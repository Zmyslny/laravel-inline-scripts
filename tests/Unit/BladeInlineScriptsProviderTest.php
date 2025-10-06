<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Zmyslny\LaravelInlineScripts\BladeDirectiveRegistrar;
use Zmyslny\LaravelInlineScripts\BladeInlineScriptsFactory;
use Zmyslny\LaravelInlineScripts\BladeInlineScriptsProvider;
use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar as BladeDirectiveRegistrarInterface;

uses(Tests\TestCase::class);

test('BladeInlineScriptsProvider extends ServiceProvider', function (): void {
    // Arrange & Act
    $provider = new BladeInlineScriptsProvider(app());

    // Assert
    expect($provider)->toBeInstanceOf(ServiceProvider::class);
});

test('register method binds blade-inline-scripts as singleton to BladeInlineScriptsFactory', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->register();

    // Assert
    expect($app->bound('blade-inline-scripts'))->toBeTrue()
        ->and($app->isShared('blade-inline-scripts'))->toBeTrue();

    $instance1 = $app->make('blade-inline-scripts');
    $instance2 = $app->make('blade-inline-scripts');

    expect($instance1)
        ->toBeInstanceOf(BladeInlineScriptsFactory::class)
        ->and($instance1)->toBe($instance2);
});

test('register method binds BladeDirectiveRegistrarInterface to BladeDirectiveRegistrar as singleton', function (): void {
    // Arrange
    $app = app(); // Use the Laravel application instance from TestCase
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->register();

    // Assert
    expect($app->bound(BladeDirectiveRegistrarInterface::class))->toBeTrue()
        ->and($app->isShared(BladeDirectiveRegistrarInterface::class))->toBeTrue();

    $instance1 = $app->make(BladeDirectiveRegistrarInterface::class);
    $instance2 = $app->make(BladeDirectiveRegistrarInterface::class);

    expect($instance1)
        ->toBeInstanceOf(BladeDirectiveRegistrar::class)
        ->and($instance1)->toBe($instance2);
});

test('boot method publishes color-scheme-switch-two-states JS files', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    $expectedJsPublishes = [];
    foreach ($published as $source => $destination) {
        if (str_contains($source, '/scripts/ColorSchemeSwitchTwoStates/js')) {
            $expectedJsPublishes[$source] = $destination;
        }
    }

    expect($expectedJsPublishes)->not->toBeEmpty();

    // Check that the specific physical JS files are published and exist
    $sourceFiles = array_keys($expectedJsPublishes);

    $jsInitFile = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'init-script.js'));
    $jsSwitchFile = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'switch-script.js'));

    expect($jsInitFile)->not->toBeNull()
        ->and($jsSwitchFile)->not->toBeNull();

    // Verify that the source files actually exist physically
    expect(file_exists((string) $jsInitFile))->toBeTrue('init-script.js source file must exist')
        ->and(file_exists((string) $jsSwitchFile))->toBeTrue('switch-script.js source file must exist');
});

test('boot method publishes color-scheme-switch-two-states JS test files', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    $expectedTestPublishes = [];
    foreach ($published as $source => $destination) {
        if (str_contains($source, 'tests/js/color-scheme-switch-two-states')) {
            $expectedTestPublishes[$source] = $destination;
        }
    }

    expect($expectedTestPublishes)->not->toBeEmpty();

    // Check specific test files are published
    $sourceFiles = array_keys($expectedTestPublishes);
    $initScriptTest = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'init-script.test.js'));
    $switchScriptTest = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'switch-script.test.js'));

    expect($initScriptTest)->not->toBeNull()
        ->and($switchScriptTest)->not->toBeNull();

    // Verify that the source test files actually exist physically
    expect(file_exists((string) $initScriptTest))->toBeTrue('init-script.test.js source file must exist')
        ->and(file_exists((string) $switchScriptTest))->toBeTrue('switch-script.test.js source file must exist');
});

test('boot method sets up correct publish groups', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $groups = ServiceProvider::$publishGroups ?? [];

    expect($groups)->toHaveKey('theme-switch-2-states-js')
        ->and($groups)->toHaveKey('theme-switch-2-states-js-tests')
        ->and($groups)->toHaveKey('theme-switch-2-states-all');

    // Verify 'theme-switch-2-states-all' includes all the files
    $allGroupFiles = $groups['theme-switch-2-states-all'] ?? [];
    $jsFiles = $groups['theme-switch-2-states-js'] ?? [];
    $jsTestFiles = $groups['theme-switch-2-states-js-tests'] ?? [];

    $expectedAllFiles = array_merge($jsFiles, $jsTestFiles);

    expect(count($allGroupFiles))->toBe(count($expectedAllFiles));
});

test('published files have correct destination paths', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    foreach ($published as $source => $destination) {
        if (str_contains($source, 'resources/js')) {
            expect($destination)->toContain('resources/js/theme-switch-two-states');
        } elseif (str_contains($source, 'tests/js')) {
            expect($destination)->toContain('tests/js/theme-switch-two-states');
        } else {
            expect(false)->toBeTrue(); // Fail the test if an unexpected file is found
        }
    }
});
