<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SchemeTypeEnum;

uses(Tests\TestCase::class);

it('points to a real file', function (): void {
    // Arrange
    $script = new InitScript();

    // Act
    $isValid = $script->isFilePathValid();

    // Assert
    expect($isValid)->toBeTrue();
});

it('returns proper values from getPlaceholders()', function (): void {
    // Arrange
    $script = new InitScript();

    // Act
    $placeholders = $script->getPlaceholders();

    // Assert
    expect($placeholders)->toBe([
        '__DARK__' => SchemeTypeEnum::DARK->value,
        '__LIGHT__' => SchemeTypeEnum::LIGHT->value,
    ]);
});
