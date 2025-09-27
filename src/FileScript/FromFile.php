<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\FileScript;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;
use Zmyslny\LaravelInlineScripts\Exceptions\FromFileException;

abstract class FromFile implements RenderableScript
{
    protected string $fileName;

    protected string $fileDirectory;

    protected string $fileExtension = 'js';

    protected Filesystem $files;

    protected string $filePath;

    /**
     * @throws Throwable
     */
    public function __construct(
        ?string $fileName = null,
        ?string $fileDirectory = null,
        ?string $fileExtension = null,
        ?Filesystem $files = null,
    ) {
        if (filled($fileName)) {
            $this->fileName = $fileName;
        }
        if (filled($fileDirectory)) {
            $this->fileDirectory = $fileDirectory;
        }
        if (filled($fileExtension)) {
            $this->fileExtension = $fileExtension;
        }

        $this->files = $files ?? app(Filesystem::class);
    }

    public function getFileSystem(): Filesystem
    {
        return $this->files;
    }

    /**
     * @throws Throwable
     */
    public function getName(): string
    {
        return $this->fileName;
    }

    public function getFileDirectory(): string
    {
        return $this->fileDirectory;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @throws Throwable
     */
    public function getFilePath(): string
    {
        if (isset($this->filePath)) {
            return $this->filePath;
        }

        $this->filePath = sprintf(
            '%s%s.%s',
            Str::finish($this->getFileDirectory(), DIRECTORY_SEPARATOR),
            $this->getFileName(),
            $this->getFileExtension()
        );

        return $this->filePath;
    }

    /**
     * @throws Throwable
     */
    public function isFilePathValid(): bool
    {
        $path = $this->getFilePath();

        return $this->files->exists($path) && $this->files->isFile($path);
    }

    /**
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws FileNotFoundException
     */
    public function render(): string
    {
        $path = $this->getFilePath();

        if ( ! $this->isFilePathValid()) {
            throw new FromFileException(sprintf('Script file not found: %s', $path));
        }

        return (string) $this->files->get($path);
    }
}
