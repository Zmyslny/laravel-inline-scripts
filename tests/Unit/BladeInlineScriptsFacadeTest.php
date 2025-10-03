<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\BladeInlineScriptsCore;
use Zmyslny\LaravelInlineScripts\BladeInlineScriptsFacade;
use Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates\SwitchScript;

uses(Tests\TestCase::class);

test('"take" can create BladeInlineScripts instance', function (): void {
    // Arrange & Act
    $instance = BladeInlineScriptsFacade::take(
        new SwitchScript,
        new InitScript
    );

    // Assert
    expect($instance)->toBeInstanceOf(BladeInlineScriptsCore::class);
});
