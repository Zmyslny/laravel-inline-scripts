<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Compilers\BladeCompiler;
use Zmyslny\LaravelInlineScripts\BladeDirectiveRegistrar;
use Zmyslny\LaravelInlineScripts\Exceptions\BladeInlineScriptsException;

uses(Tests\TestCase::class);

it('registers a Blade directive and uses renderer return string', function (): void {
    // Arrange
    $captured = null;
    $blade = Mockery::mock(BladeCompiler::class);
    $blade->shouldReceive('directive')
        ->once()
        ->with('inlineScripts', Mockery::on(function ($closure) use (&$captured) {
            $captured = $closure;

            return is_callable($closure);
        }))
        ->andReturnNull();

    $registrar = new BladeDirectiveRegistrar($blade);

    // Act
    $registrar->register('inlineScripts', fn (): string => 'console.log("ok");');

    // Assert
    expect($captured)
        ->toBeCallable()
        ->and($captured())->toBe('console.log("ok");');
});

it('registers a Blade directive and converts Htmlable to HTML', function (): void {
    // Arrange
    $captured = null;
    $blade = Mockery::mock(BladeCompiler::class);
    $blade->shouldReceive('directive')
        ->once()
        ->with('inlineHtml', Mockery::on(function ($closure) use (&$captured) {
            $captured = $closure;

            return is_callable($closure);
        }))
        ->andReturnNull();

    $registrar = new BladeDirectiveRegistrar($blade);

    $htmlable = new class implements Htmlable
    {
        public function toHtml(): string
        {
            return '<script>console.log("htmlable")</script>';
        }
    };

    // Act
    $registrar->register('inlineHtml', fn () => $htmlable);

    // Assert
    expect($captured)
        ->toBeCallable()
        ->and($captured())->toBe('<script>console.log("htmlable")</script>');
});

it('casts non-string renderer result to string', function (): void {
    // Arrange
    $captured = null;
    $blade = Mockery::mock(BladeCompiler::class);
    $blade->shouldReceive('directive')
        ->once()
        ->with('number', Mockery::on(function ($closure) use (&$captured) {
            $captured = $closure;

            return is_callable($closure);
        }))
        ->andReturnNull();

    $registrar = new BladeDirectiveRegistrar($blade);

    // Act
    $registrar->register('number', fn () => 123);

    // Assert
    expect($captured)
        ->toBeCallable()
        ->and($captured())->toBe('123');
});

it('throws when directive name is empty', function (): void {
    // Arrange
    $blade = Mockery::mock(BladeCompiler::class);
    $registrar = new BladeDirectiveRegistrar($blade);

    // Assert
    expect(fn () => $registrar->register('', fn () => 'x'))
        ->toThrow(BladeInlineScriptsException::class, 'Directive name cannot be empty.');
});
