<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar;
use Zmyslny\LaravelInlineScripts\BladeInlineScripts;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;

uses(Tests\TestCase::class);

it('resolves BladeDirectiveRegistrar from IoC by default on construct', function (): void {
    // Arrange & Act
    $inline = new BladeInlineScripts();

    // Assert
    $resolvedFromContainer = app(BladeDirectiveRegistrar::class);

    expect($inline->getBladeRegistrar())
        ->toBeInstanceOf(BladeDirectiveRegistrar::class)
        ->toBe($resolvedFromContainer);
});

it('allows setting BladeDirectiveRegistrar via setBladeRegistrar and returns self', function (): void {
    // Arrange
    $customRegistrar = new class implements BladeDirectiveRegistrar
    {
        public function register(string $name, callable $renderer): void {}
    };

    $inline = new BladeInlineScripts();

    // Act
    $result = $inline->setBladeRegistrar($customRegistrar);

    // Assert
    expect($result)
        ->toBe($inline)
        ->and($inline->getBladeRegistrar())
        ->toBe($customRegistrar);
});

it('allows setting a custom scriptTagId, uses it when rendering and returns self', function (): void {
    // Arrange
    $inline = new BladeInlineScripts();

    // Act
    $result = $inline->setScriptTagId('my-custom-id');
    $html = $inline->renderScriptTag()->toHtml();

    // Assert
    expect($result)
        ->toBe($inline)
        ->and($inline->getScriptTagId())->toBe('my-custom-id')
        ->and($html)->toContain('id="my-custom-id"');
});

it('auto-generates scriptTagId by joining script names and adding a hash when not manually set', function (): void {
    // Arrange
    $script1 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Alpha(){return 'A';}";
        }

        public function getName(): string
        {
            return 'Alpha';
        }
    };

    $script2 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Beta(){return 'B';}";
        }

        public function getName(): string
        {
            return 'Beta';
        }
    };

    $inline = new BladeInlineScripts($script1, $script2);

    // Act
    $id = $inline->getScriptTagId();
    $html = $inline->renderScriptTag()->toHtml();

    // Assert
    expect($id)
        ->toMatch('/^alpha-beta-[0-9a-f]{8}$/')
        ->and($html)->toContain('id="'.$id.'"');
});

it('wraps scripts code inside a single {script} tag with the computed id', function (): void {
    // Arrange
    $script1 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Alpha(){return 'A';}";
        }

        public function getName(): string
        {
            return 'Alpha';
        }
    };

    $script2 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Beta(){return 'B';}";
        }

        public function getName(): string
        {
            return 'Beta';
        }
    };

    $inline = new BladeInlineScripts($script1, $script2);

    // Act
    $id = $inline->getScriptTagId();
    $html = $inline->renderScriptTag()->toHtml();

    // Assert
    expect($html)
        ->toContain('<script id="'.$id.'">')
        ->toContain("function Alpha(){return 'A';}")
        ->toContain(PHP_EOL)
        ->toContain("function Beta(){return 'B';}")
        ->toContain('</script>');
});

test('getScriptsCombinedCode returns empty string when no scripts are provided', function (): void {
    // Arrange
    $inline = new BladeInlineScripts();

    // Act
    $code = $inline->getScriptsCombinedCode();

    // Assert
    expect($code)->toBe('');
});

test('getScriptsCombinedCode concatenates script outputs with PHP_EOL and preserves order', function (): void {
    // Arrange
    $script1 = new class implements RenderableScript
    {
        public function render(): string
        {
            return 'console.log("one")';
        }

        public function getName(): string
        {
            return 'One';
        }
    };

    $script2 = new class implements RenderableScript
    {
        public function render(): string
        {
            return 'console.log("two")';
        }

        public function getName(): string
        {
            return 'Two';
        }
    };

    $inline = new BladeInlineScripts($script1, $script2);

    // Act
    $code = $inline->getScriptsCombinedCode();

    // Assert
    expect($code)->toBe('console.log("one")'.PHP_EOL.'console.log("two")');
});

test('getScriptsCombinedCode caches the result so scripts are rendered only once', function (): void {
    // Arrange
    $renders = ['a' => 0, 'b' => 0];

    $scriptA = new class($renders) implements RenderableScript
    {
        public function __construct(private array &$renders) {}

        public function render(): string
        {
            $this->renders['a']++;

            return 'A()';
        }

        public function getName(): string
        {
            return 'A';
        }
    };

    $scriptB = new class($renders) implements RenderableScript
    {
        public function __construct(private array &$renders) {}

        public function render(): string
        {
            $this->renders['b']++;

            return 'B()';
        }

        public function getName(): string
        {
            return 'B';
        }
    };

    $inline = new BladeInlineScripts($scriptA, $scriptB);

    // Act
    $first = $inline->getScriptsCombinedCode();
    $second = $inline->getScriptsCombinedCode();

    // Assert
    expect($first)->toBe('A()'.PHP_EOL.'B()')
        ->and($second)->toBe($first)
        ->and($renders['a'])->toBe(1)
        ->and($renders['b'])->toBe(1);
});

test('doNotAddHashToScriptId disables hash addition to script id', function (): void {
    // Arrange
    $script1 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Alpha(){return 'A';}";
        }

        public function getName(): string
        {
            return 'Alpha';
        }
    };

    $script2 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function Beta(){return 'B';}";
        }

        public function getName(): string
        {
            return 'Beta';
        }
    };

    $inline = new BladeInlineScripts($script1, $script2);

    // Act
    $inline->doNotAddHashToScriptId();

    // Assert
    $id = $inline->getScriptTagId();
    $html = $inline->renderScriptTag()->toHtml();

    expect($id)
        ->toBe('alpha-beta')
        ->and($html)->toContain('id="alpha-beta"');
});

test('registerAs allows register renderable script under custom blade directive', function (): void {
    // Arrange
    $registrar = app(\Zmyslny\LaravelInlineScripts\BladeDirectiveRegistrar::class);

    $script = new class implements RenderableScript
    {
        public function render(): string
        {
            return 'console.log("test myDirective directive");';
        }

        public function getName(): string
        {
            return 'TestScript';
        }
    };

    $inline = new BladeInlineScripts($script);
    $inline->setBladeRegistrar($registrar);

    // Act
    $inline->registerAs('myDirective');

    // Assert
    $isRegistered = isset($registrar->blade->getCustomDirectives()['myDirective']);
    expect($isRegistered)->toBeTrue();

    $result = ($registrar->blade->getCustomDirectives()['myDirective'])();
    expect($result)->toBeString()
        ->toContain('<script id=')
        ->toContain('console.log("test myDirective directive");')
        ->toContain('</script>');
});
