<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Illuminate\Support\Facades\Facade;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;

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
