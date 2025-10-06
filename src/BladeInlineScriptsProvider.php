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
            __DIR__.'/../scripts/ColorSchemeSwitchTwoStates/js/init-script.js' => resource_path('js/color-scheme-switch-two-states/init-script.js'),
            __DIR__.'/../scripts/ColorSchemeSwitchTwoStates/js/switch-script.js' => resource_path('js/color-scheme-switch-two-states/switch-script.js'),
        ], ['color-scheme-switch-2-states-js', 'color-scheme-switch-2-states-all']);

        $this->publishes([
            __DIR__.'/../tests/js/color-scheme-switch-two-states/init-script.test.js' => base_path('tests/js/color-scheme-switch-two-states/init-script.test.js'),
            __DIR__.'/../tests/js/color-scheme-switch-two-states/switch-script.test.js' => base_path('tests/js/color-scheme-switch-two-states/switch-script.test.js'),
        ], ['color-scheme-switch-2-states-js-tests', 'color-scheme-switch-2-states-all']);

        $this->publishes([
            __DIR__.'/../scripts/ColorSchemeSwitchThreeStates/js/init-script.js' => base_path('js/color-scheme-switch-three-states/init-script.js'),
            __DIR__.'/../scripts/ColorSchemeSwitchThreeStates/js/switch-script.js' => base_path('js/color-scheme-switch-three-states/switch-script.js'),
        ], ['color-scheme-switch-3-states-js', 'color-scheme-switch-3-states-all']);
    }
}
