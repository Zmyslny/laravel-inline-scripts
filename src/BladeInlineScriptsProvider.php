<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Illuminate\Support\ServiceProvider;
use Override;
use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar as BladeDirectiveRegistrarInterface;

class BladeInlineScriptsProvider extends ServiceProvider
{
    #[Override]
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
            __DIR__.'/../stubs/ThemeSwitchTwoStates/ThemeInitScript.php' => base_path('app/Blade/ThemeSwitchTwoStates/ThemeInitScript.php'),
            __DIR__.'/../stubs/ThemeSwitchTwoStates/ThemeSwitchScript.php' => base_path('app/Blade/ThemeSwitchTwoStates/ThemeSwitchScript.php'),
            __DIR__.'/../tests/js/theme-switch-two-states/theme-init.test.js' => base_path('tests/js/theme-switch-two-states/theme-init.test.js'),
            __DIR__.'/../tests/js/theme-switch-two-states/theme-switch.test.js' => base_path('tests/js/theme-switch-two-states/theme-switch.test.js'),
        ], 'theme-switch-two-states');
    }
}
