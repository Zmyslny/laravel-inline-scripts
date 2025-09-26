<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Facades;

use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;
use Illuminate\Support\Facades\Facade;

/**
 * @method static BladeInlineScriptsFacade take(RenderableScript ...$inlineScripts)
 */
class BladeInlineScriptsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'blade-inline-scripts';
    }
}
