<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Zmyslny\LaravelInlineScripts\InlineScript\FromFileScriptWithPlaceholders;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;
use Throwable;

class BladeInlineScriptsFactory
{
    public function take(RenderableScript ...$scripts): BladeInlineScripts
    {
        return new BladeInlineScripts(...$scripts);
    }

    /**
     * @param  array<string,string>  $placeholders
     *
     * @throws Throwable
     */
    public function takeFile(string $path, array $placeholders = []): BladeInlineScripts
    {
        [$dir, $filename, $extension] = $this->extractedPath($path);

        $instance = $this->prepareFileScriptInstance(
            filename: $filename,
            directory: $dir,
            extension: $extension,
            placeholders: $placeholders
        );

        return $this->take($instance);
    }

    /**
     * @param  string|array{0:string,1:array<string,string>}  ...$paths
     *
     * @throws Throwable
     */
    public function takeFiles(array|string ...$paths): BladeInlineScripts
    {
        $instances = [];

        foreach ($paths as $pathOrTuple) {
            $path = is_array($pathOrTuple) ? (string) $pathOrTuple[0] : $pathOrTuple;
            $replacements = is_array($pathOrTuple) ? (array) $pathOrTuple[1] : [];

            [$dir, $filename, $extension] = $this->extractedPath($path);

            $instances[] = $this->prepareFileScriptInstance(
                filename: $filename,
                directory: $dir,
                extension: $extension,
                placeholders: $replacements
            );
        }

        return $this->take(...$instances);
    }

    /**
     * @return string[]
     */
    protected function extractedPath(string $path): array
    {
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = (string) (in_array(pathinfo($path, PATHINFO_EXTENSION), ['', '0'], true) ? 'js' : pathinfo($path, PATHINFO_EXTENSION));

        return [$dir, $filename, $extension];
    }

    /**
     * @param  array<string,string>  $placeholders
     */
    protected function prepareFileScriptInstance(
        string $filename,
        string $directory,
        string $extension,
        array $placeholders = []
    ): FromFileScriptWithPlaceholders {
        return new class($filename, $directory, $extension, $placeholders) extends FromFileScriptWithPlaceholders
        {
            /**
             * @param  array<string,string>  $placeholders
             */
            public function __construct(
                ?string $fileName = null,
                ?string $fileDirectory = null,
                ?string $fileExtension = null,
                /** @var array<string,string> */ private readonly array $placeholders = []
            ) {
                parent::__construct($fileName, $fileDirectory, $fileExtension);
            }

            /**
             * @return array<string,string>
             */
            public function getPlaceholders(): array
            {
                return $this->placeholders;
            }
        };
    }
}
