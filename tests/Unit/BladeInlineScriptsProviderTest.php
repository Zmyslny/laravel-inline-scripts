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

test('boot method publishes theme-switch-two-states JS files', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    $expectedJsPublishes = [];
    foreach ($published as $source => $destination) {
        if (str_contains($source, 'resources/js/theme-switch-two-states')) {
            $expectedJsPublishes[$source] = $destination;
        }
    }

    expect($expectedJsPublishes)->not->toBeEmpty();

    // Check specific JS files are published
    $sourceFiles = array_keys($expectedJsPublishes);
    $jsInitFile = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'theme-init.js'));
    $jsSwitchFile = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'theme-switch.js'));

    expect($jsInitFile)->not->toBeNull()
        ->and($jsSwitchFile)->not->toBeNull();
});

test('boot method publishes theme-switch-two-states PHP stub classes', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    $expectedClassPublishes = [];
    foreach ($published as $source => $destination) {
        if (str_contains($source, 'stubs/ThemeSwitchTwoStates')) {
            $expectedClassPublishes[$source] = $destination;
        }
    }

    expect($expectedClassPublishes)->not->toBeEmpty();

    // Check specific PHP stub files are published
    $sourceFiles = array_keys($expectedClassPublishes);
    $themeInitClass = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'ThemeInitScript.php'));
    $themeSwitchClass = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'ThemeSwitchScript.php'));

    expect($themeInitClass)->not->toBeNull()
        ->and($themeSwitchClass)->not->toBeNull();
});

test('boot method publishes theme-switch-two-states JS test files', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $published = ServiceProvider::$publishes[BladeInlineScriptsProvider::class] ?? [];

    $expectedTestPublishes = [];
    foreach ($published as $source => $destination) {
        if (str_contains($source, 'tests/js/theme-switch-two-states')) {
            $expectedTestPublishes[$source] = $destination;
        }
    }

    expect($expectedTestPublishes)->not->toBeEmpty();

    // Check specific test files are published
    $sourceFiles = array_keys($expectedTestPublishes);
    $themeInitTest = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'theme-init.test.js'));
    $themeSwitchTest = collect($sourceFiles)->first(fn ($file) => str_contains($file, 'theme-switch.test.js'));

    expect($themeInitTest)->not->toBeNull()
        ->and($themeSwitchTest)->not->toBeNull();
});

test('boot method sets up correct publish groups', function (): void {
    // Arrange
    $app = new Application();
    $provider = new BladeInlineScriptsProvider($app);

    // Act
    $provider->boot();

    // Assert
    $groups = ServiceProvider::$publishGroups ?? [];

    expect($groups)->toHaveKey('theme-switch-two-states-js')
        ->and($groups)->toHaveKey('theme-switch-two-states-classes')
        ->and($groups)->toHaveKey('theme-switch-two-states-js-tests')
        ->and($groups)->toHaveKey('theme-switch-two-states-all');

    // Verify 'theme-switch-two-states-all' includes all the files
    $allGroupFiles = $groups['theme-switch-two-states-all'] ?? [];
    $jsFiles = $groups['theme-switch-two-states-js'] ?? [];
    $classFiles = $groups['theme-switch-two-states-classes'] ?? [];
    $testFiles = $groups['theme-switch-two-states-js-tests'] ?? [];

    $expectedAllFiles = array_merge($jsFiles, $classFiles, $testFiles);

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
        } elseif (str_contains($source, 'stubs/ThemeSwitchTwoStates')) {
            expect($destination)->toContain('app/Blade/ThemeSwitchTwoStates');
        } elseif (str_contains($source, 'tests/js')) {
            expect($destination)->toContain('tests/js/theme-switch-two-states');
        }
    }
});
