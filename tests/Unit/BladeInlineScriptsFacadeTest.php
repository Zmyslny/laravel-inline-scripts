<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\BladeInlineScriptsCore;
use Zmyslny\LaravelInlineScripts\BladeInlineScriptsFacade;
use Zmyslny\LaravelInlineScripts\ThemeSwitchTwoStates\ThemeInitScript;
use Zmyslny\LaravelInlineScripts\ThemeSwitchTwoStates\ThemeSwitchScript;

uses(Tests\TestCase::class);

test('"take" can create BladeInlineScripts instance', function (): void {
    // Arrange & Act
    $instance = BladeInlineScriptsFacade::take(
        new ThemeSwitchScript,
        new ThemeInitScript
    );

    // Assert
    expect($instance)->toBeInstanceOf(BladeInlineScriptsCore::class);
});
