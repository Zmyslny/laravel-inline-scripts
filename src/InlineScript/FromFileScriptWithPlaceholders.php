<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\InlineScript;

use Illuminate\Support\Str;
use Override;
use Zmyslny\LaravelInlineScripts\Contracts\ScriptWithPlaceholders;

abstract class FromFileScriptWithPlaceholders extends FromFileScript implements ScriptWithPlaceholders
{
    /**
     * @return array<string,string>
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    #[Override]
    public function render(): string
    {
        $content = parent::render();

        return strtr($content, array_merge([
            '__FUNCTION_NAME__' => $this->getFunctionName(),
        ], $this->getPlaceholders()));
    }

    public function getFunctionName(): string
    {
        return $this->convertFileNameToFunctionName();
    }

    protected function convertFileNameToFunctionName(): string
    {
        return Str::camel($this->fileName);
    }
}
