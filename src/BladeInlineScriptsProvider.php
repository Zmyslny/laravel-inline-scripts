<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar as BladeDirectiveRegistrarInterface;
use Illuminate\Support\ServiceProvider;
use Override;

class BladeInlineScriptsProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(BladeDirectiveRegistrarInterface::class, BladeDirectiveRegistrar::class);
    }
}