<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Illuminate\Support\Facades\Facade;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;

/**
 * @method static BladeInlineScriptsCore take(RenderableScript ...$scripts)
 * @method static BladeInlineScriptsCore takeFile(string $path, array<string, string> $placeholders = [])
 * @method static BladeInlineScriptsCore takeFiles(array< string, array<string,string> >|string ...$paths)
 */
class BladeInlineScriptsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'blade-inline-scripts';
    }
}
