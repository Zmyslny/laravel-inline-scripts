<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates\SchemeTypeEnum;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates\SwitchScript;

uses(Tests\TestCase::class);

it('points to a real file', function (): void {
    // Arrange
    $script = new SwitchScript();

    // Act
    $isValid = $script->isFilePathValid();

    // Assert
    expect($isValid)->toBeTrue();
});

it('throws an exception when the $key value being set is empty', function (): void {
    // Arrange
    $script = new SwitchScript();

    // Act & Assert
    expect(fn () => $script->setKey(''))
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', SwitchScript::KEY_PATTERN));
});

it('throws an exception when the $key value being set is longer than one letter', function (): void {
    // Arrange
    $script = new SwitchScript();

    // Act & Assert
    expect(fn () => $script->setKey('too'))
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', SwitchScript::KEY_PATTERN));
});

it('throws an exception when the $key is a single letter but does not match KEY_PATTERN', function (): void {
    // Arrange
    $script = new SwitchScript();

    // Act & Assert
    expect(fn () => $script->setKey('A'))
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', SwitchScript::KEY_PATTERN));
});

it('sets $key correctly for a valid value', function (): void {
    // Arrange
    $script = new SwitchScript();

    // Act
    $script->setKey('t');

    // Assert
    expect($script->getKey())->toBe('t');
});

it('sets $key correctly for a valid value through constructor', function (): void {
    // Arrange & Act
    $script = new SwitchScript('t');

    // Assert
    expect($script->getKey())->toBe('t');
});

it('returns proper values from getPlaceholders()', function (): void {
    // Arrange
    $script = new SwitchScript();
    $script->setKey('k');

    // Act
    $placeholders = $script->getPlaceholders();

    // Assert
    expect($placeholders)->toBe([
        '__TOGGLE_KEY__' => 'k',
        '__DARK__' => SchemeTypeEnum::DARK->value,
        '__LIGHT__' => SchemeTypeEnum::LIGHT->value,
    ]);
});
