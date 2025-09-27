<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\Examples\ThemeSwitchTwoStates\ThemeSwitchScript;
use Zmyslny\LaravelInlineScripts\Examples\ThemeSwitchTwoStates\ThemeTypeEnum;

uses(Tests\TestCase::class);

it('points to a real file', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();

    // Act
    $isValid = $script->isFilePathValid();

    // Assert
    expect($isValid)->toBeTrue();
});

it('throws an exception when the $key value being set is empty', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();

    // Act & Assert
    expect(fn () => $script->key = '')
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', ThemeSwitchScript::KEY_PATTERN));
});

it('throws an exception when the $key value being set is longer than one letter', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();

    // Act & Assert
    expect(fn () => $script->key = 'too')
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', ThemeSwitchScript::KEY_PATTERN));
});

it('throws an exception when the $key is a single letter but does not match KEY_PATTERN', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();

    // Act & Assert
    expect(fn () => $script->key = 'A')
        ->toThrow(InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', ThemeSwitchScript::KEY_PATTERN));
});

it('sets $key correctly for a valid value', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();

    // Act
    $script->key = 't';

    // Assert
    expect($script->key)->toBe('t');
});

it('sets $key correctly for a valid value through constructor', function (): void {
    // Arrange & Act
    $script = new ThemeSwitchScript('t');

    // Assert
    expect($script->key)->toBe('t');
});

it('returns proper values from getPlaceholders()', function (): void {
    // Arrange
    $script = new ThemeSwitchScript();
    $script->key = 'k';

    // Act
    $placeholders = $script->getPlaceholders();

    // Assert
    expect($placeholders)->toBe([
        '__TOGGLE_KEY__' => 'k',
        '__DARK__' => ThemeTypeEnum::DARK->value,
        '__LIGHT__' => ThemeTypeEnum::LIGHT->value,
    ]);
});
