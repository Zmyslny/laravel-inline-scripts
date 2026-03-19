<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates\SchemeTypeEnum;
use Zmyslny\LaravelInlineScripts\Ready\LivewireNavAdapter\LivewireNavAdapter;

uses(Tests\TestCase::class);

it('points to a real file', function (): void {
    // Arrange
    $script = new LivewireNavAdapter();

    // Act
    $isValid = $script->isFilePathValid();

    // Assert
    expect($isValid)->toBeTrue();
});

it('returns proper values from getPlaceholders()', function (): void {
    // Arrange
    $script = new LivewireNavAdapter();

    // Act
    $placeholders = $script->getPlaceholders();

    // Assert
    expect($placeholders)->toBe([
        '__DARK__' => SchemeTypeEnum::DARK->value,
        '__LIGHT__' => SchemeTypeEnum::LIGHT->value,
    ]);
});
