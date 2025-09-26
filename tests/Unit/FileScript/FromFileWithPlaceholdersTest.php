<?php

declare(strict_types=1);


use Zmyslny\LaravelInlineScripts\FileScript\FromFileWithPlaceholders;

uses(Tests\TestCase::class);

it('computes function name from file name using camelCase by default', function (): void {
    // Arrange
    $script = new class extends FromFileWithPlaceholders
    {
        protected string $fileName = 'some-function_name-file';

        protected string $fileDirectory;

        public function __construct()
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct();
        }
    };

    // Act
    $fn = $script->getFunctionName();

    // Assert
    expect($fn)->toBe('someFunctionNameFile');
});

it('replaces __FUNCTION_NAME__ placeholder with computed function name on render', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-placeholders-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'my-awesome-script';
    $path = $dir.DIRECTORY_SEPARATOR.$fileName.'.js';
    file_put_contents($path, 'function __FUNCTION_NAME__() { return true; }');

    $script = new class($fileName, $dir) extends FromFileWithPlaceholders
    {
        public function __construct(private string $name, private string $dir)
        {
            parent::__construct($this->name, $this->dir);
        }
    };

    // Act
    $output = $script->render();

    // Assert
    expect($output)->toBe('function myAwesomeScript() { return true; }');
});

it('applies custom placeholders provided by getPlaceholders() in addition to __FUNCTION_NAME__', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-placeholders-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'with-custom-placeholders';
    $path = $dir.DIRECTORY_SEPARATOR.$fileName.'.js';
    file_put_contents($path, "/* __BANNER__ */\nfunction __FUNCTION_NAME__() { return '__CUSTOM__'; }");

    $script = new class($fileName, $dir) extends FromFileWithPlaceholders
    {
        public function __construct(private string $name, private string $dir)
        {
            parent::__construct($this->name, $this->dir);
        }

        public function getPlaceholders(): array
        {
            return [
                '__BANNER__' => 'Generated',
                '__CUSTOM__' => 'ok',
            ];
        }
    };

    // Act
    $output = $script->render();

    // Assert
    expect($output)->toBe("/* Generated */\nfunction withCustomPlaceholders() { return 'ok'; }");
});

it('allows overriding default __FUNCTION_NAME__ placeholder', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-placeholders-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'original-name';
    $path = $dir.DIRECTORY_SEPARATOR.$fileName.'.js';
    file_put_contents($path, 'const x = "__FUNCTION_NAME__";');

    $script = new class($fileName, $dir) extends FromFileWithPlaceholders
    {
        public function __construct(private string $name, private string $dir)
        {
            parent::__construct($this->name, $this->dir);
        }

        public function getFunctionName(): string
        {
            return 'shouldBeOverridden';
        }

        public function getPlaceholders(): array
        {
            // This should override the default '__FUNCTION_NAME__' replacement
            return ['__FUNCTION_NAME__' => 'OVERRIDDEN'];
        }
    };

    // Act
    $output = $script->render();

    // Assert
    expect($output)->toBe('const x = "OVERRIDDEN";');
});
