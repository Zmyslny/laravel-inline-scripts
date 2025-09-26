<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\BladeInlineScripts;
use Zmyslny\LaravelInlineScripts\Examples\ThemeInitScript;
use Zmyslny\LaravelInlineScripts\Examples\ThemeSwitchScript;
use Zmyslny\LaravelInlineScripts\BladeInlineScriptsFacade;

uses(Tests\TestCase::class);

test('"take" can create BladeInlineScripts instance', function (): void {
    // Arrange & Act
    $instance = BladeInlineScriptsFacade::take(
        new ThemeSwitchScript,
        new ThemeInitScript
    );

    // Assert
    expect($instance)->toBeInstanceOf(BladeInlineScripts::class);
});
