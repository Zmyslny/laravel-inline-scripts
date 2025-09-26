<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Contracts;

interface ScriptWithPlaceholders
{
    /**
     * @return array<string,string>
     */
    public function getPlaceholders(): array;
}
