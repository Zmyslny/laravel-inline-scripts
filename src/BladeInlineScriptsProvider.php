<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Illuminate\Support\ServiceProvider;
use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar as BladeDirectiveRegistrarInterface;

class BladeInlineScriptsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('blade-inline-scripts', BladeInlineScriptsFactory::class);

        $this->app->singleton(BladeDirectiveRegistrarInterface::class, BladeDirectiveRegistrar::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../resources/js/theme-switch-two-states/theme-init.js' => resource_path('js/theme-switch-two-states/theme-init.js'),
            __DIR__.'/../resources/js/theme-switch-two-states/theme-switch.js' => resource_path('js/theme-switch-two-states/theme-switch.js'),
        ], ['theme-switch-2-states-js', 'theme-switch-2-states-all']);

        $this->publishes([
            __DIR__.'/../tests/js/theme-switch-two-states/theme-init.test.js' => base_path('tests/js/theme-switch-two-states/theme-init.test.js'),
            __DIR__.'/../tests/js/theme-switch-two-states/theme-switch.test.js' => base_path('tests/js/theme-switch-two-states/theme-switch.test.js'),
        ], ['theme-switch-2-states-js-tests', 'theme-switch-2-states-all']);
    }
}
