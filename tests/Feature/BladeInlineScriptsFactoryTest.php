<?php

declare(strict_types=1);

use Zmyslny\LaravelInlineScripts\BladeInlineScriptsFactory;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;

uses(Tests\TestCase::class);

test('"takeFiles" creates BladeInlineScripts instance from {path, replacements} tuples', function (): void {
    // Arrange
    $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test';
    if ( ! is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }

    $file = $tmpDir.DIRECTORY_SEPARATOR.'my-func.js';
    $contents = "function __FUNCTION_NAME__(){return '__VALUE__';}";
    file_put_contents($file, $contents);

    $file2 = $tmpDir.DIRECTORY_SEPARATOR.'another-func.js';
    $contents2 = "function anotherFunc(){return '__VALUE__';}";
    file_put_contents($file2, $contents2);

    $factory = new BladeInlineScriptsFactory();

    // Act
    $inline = $factory->takeFiles([$file, ['__VALUE__' => 'foo']], [$file2, ['__VALUE__' => 'baz']]);
    $code = $inline->renderScriptTag()->toHtml();

    // Assert
    expect($code)
        ->toContain('function myFunc()')
        ->toContain('function anotherFunc()')
        ->and($code)->toContain("return 'baz'")
        ->and($code)->toContain("return 'foo'");
});

test('"takeFiles" creates BladeInlineScripts instance from only paths', function (): void {
    // Arrange
    $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test';
    if ( ! is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }

    $file = $tmpDir.DIRECTORY_SEPARATOR.'my-func.js';
    $contents = "function __FUNCTION_NAME__(){return '__VALUE__';}";
    file_put_contents($file, $contents);

    $file2 = $tmpDir.DIRECTORY_SEPARATOR.'another-func.js';
    $contents2 = "function anotherFunc(){return 'bar';}";
    file_put_contents($file2, $contents2);

    $factory = new BladeInlineScriptsFactory();

    // Act
    $inlineScripts = $factory->takeFiles($file, $file2);
    $code = $inlineScripts->renderScriptTag()->toHtml();

    // Assert
    expect($code)
        ->toContain('function myFunc()')
        ->toContain('function anotherFunc()')
        ->and($code)->toContain("return '__VALUE__'")
        ->and($code)->toContain("return 'bar'");
});

test('"takeFile" creates BladeInlineScripts instance from one path and its placeholders', function (): void {
    // Arrange
    $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test';
    if ( ! is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }

    $file = $tmpDir.DIRECTORY_SEPARATOR.'my-func.js';
    $contents = "function __FUNCTION_NAME__(){return '__VALUE__';}";
    file_put_contents($file, $contents);

    $factory = new BladeInlineScriptsFactory();

    // Act
    $inlineScripts = $factory->takeFile($file, ['__VALUE__' => 'foo']);
    $code = $inlineScripts->renderScriptTag()->toHtml();

    // Assert
    expect($code)->toContain('function myFunc()')
        ->and($code)->toContain("return 'foo'");

});

test('"takeFile" creates BladeInlineScripts instance from only one path', function (): void {
    // Arrange
    $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test';
    if ( ! is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }

    $file = $tmpDir.DIRECTORY_SEPARATOR.'my-func.js';
    $contents = "function __FUNCTION_NAME__(){return '__VALUE__';}";
    file_put_contents($file, $contents);

    $factory = new BladeInlineScriptsFactory();

    // Act
    $inlineScripts = $factory->takeFile($file);
    $code = $inlineScripts->renderScriptTag()->toHtml();

    // Assert
    expect($code)->toContain('function myFunc()')
        ->and($code)->toContain("return '__VALUE__'");
});

test('"take" can create BladeInlineScripts instance from RenderableScript objects', function (): void {
    // Arrange
    $script1 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function myFunc(){return 'foo';}";
        }

        public function getName(): string
        {
            return 'myFunc';
        }
    };
    $script2 = new class implements RenderableScript
    {
        public function render(): string
        {
            return "function anotherFunc(){return 'bar';}";
        }

        public function getName(): string
        {
            return 'anotherFunc';
        }
    };

    $factory = new BladeInlineScriptsFactory();

    // Act
    $inlineScripts = $factory->take($script1, $script2);
    $code = $inlineScripts->renderScriptTag()->toHtml();

    // Assert
    expect($code)
        ->toContain('function myFunc()')
        ->and($code)->toContain('function anotherFunc()');
});
