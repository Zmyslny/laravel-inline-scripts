<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Zmyslny\LaravelInlineScripts\Exceptions\FromFileException;
use Zmyslny\LaravelInlineScripts\FileScript\FromFile;

uses(Tests\TestCase::class);

it('initializes default Filesystem instance when none provided', function (): void {
    // Arrange
    $script = new class extends FromFile
    {
        protected string $fileName = 'dummy';

        protected string $fileDirectory;

        protected string $fileExtension = 'js';

        public function __construct()
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct();
        }
    };

    // Act
    $filesystem = $script->getFileSystem();

    // Assert
    expect($filesystem)->toBeInstanceOf(Filesystem::class);
});

it('does not throw exception when fileName is neither set on the class nor passed to constructor', function (): void {
    // Arrange & Act
    $action = fn () => new class extends FromFile
    {
        protected string $fileDirectory;

        public function __construct()
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct();
        }
    };

    // Assert
    expect($action)->not->toThrow(FromFileException::class);
});

it('does not throw exception when fileDirectory is neither set on the class nor passed to constructor', function (): void {
    // Arrange & Act
    $action = fn () => new class extends FromFile
    {
        protected string $fileName = 'dummy';

        public function __construct()
        {
            parent::__construct();
        }
    };

    // Assert
    expect($action)->not->toThrow(FromFileException::class);
});

it('defaults scriptFileExtension to js when not set on the class nor passed to constructor', function (): void {
    // Arrange
    $script = new class extends FromFile
    {
        protected string $fileName = 'dummy';

        protected string $fileDirectory;

        // Note: We do NOT set $scriptFileExtension here nor pass it to parent
        public function __construct()
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct();
        }
    };

    // Act
    $extension = $script->getFileExtension();

    // Assert
    expect($extension)->toBe('js');
});

it('returns scriptFileName by default from getName', function (): void {
    // Arrange
    $script = new class extends FromFile
    {
        protected string $fileName = 'dummy-name';

        protected string $fileDirectory;

        public function __construct()
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct();
        }
    };

    // Act
    $name = $script->getName();

    // Assert
    expect($name)->toBe('dummy-name');
});

it('returns scriptFileDirectory set via constructor from getFileDirectory', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'custom-inline-dir';

    $script = new class($dir) extends FromFile
    {
        protected string $fileName = 'dummy';

        public function __construct(private string $dir)
        {
            parent::__construct(null, $this->dir);
        }
    };

    // Act
    $returned = $script->getFileDirectory();

    // Assert
    expect($returned)->toBe($dir);
});

it('returns scriptFileExtension set via constructor from getFileExtension', function (): void {
    // Arrange
    $extension = 'ts';

    $script = new class($extension) extends FromFile
    {
        protected string $fileName = 'dummy';

        protected string $fileDirectory;

        public function __construct(private string $ext)
        {
            $this->fileDirectory = sys_get_temp_dir();
            parent::__construct(null, $this->fileDirectory, $this->ext);
        }
    };

    // Act
    $returned = $script->getFileExtension();

    // Assert
    expect($returned)->toBe($extension);
});

it('builds file path in getFilePath from constructor-provided parts', function (): void {
    // Arrange
    $directory = 'inline-scripts-directory';
    $fileName = 'my-script';
    $extension = 'mjs';

    $script = new class($fileName, $directory, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Act
    $path = $script->getFilePath();

    // Assert
    $expected = 'inline-scripts-directory'.DIRECTORY_SEPARATOR.$fileName.'.'.$extension;
    expect($path)->toBe($expected);
});

it('returns false from isFilePathValid when file does not exist at path', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'non-existent-script';
    $extension = 'js';

    $script = new class($fileName, $dir, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Sanity check: ensure file truly does NOT exist
    $path = $script->getFilePath();
    if (file_exists($path)) {
        unlink($path);
    }

    // Act
    $valid = $script->isFilePathValid();

    // Assert
    expect($valid)->toBeFalse();
});

it('returns false from isFilePathValid when path points to a directory, not a file', function (): void {
    // Arrange
    $base = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test-'.uniqid();
    $dirPath = $base.DIRECTORY_SEPARATOR.'folder.js';
    mkdir($dirPath, 0777, true); // Create a directory that looks like a file name

    $fileName = 'folder';
    $extension = 'js';

    $script = new class($fileName, $base, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Act
    $valid = $script->isFilePathValid();

    // Assert
    expect($valid)->toBeFalse();
});

it('returns true from isFilePathValid when path points to an existing file', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'real-script';
    $extension = 'js';
    $fullPath = $dir.DIRECTORY_SEPARATOR.$fileName.'.'.$extension;
    file_put_contents($fullPath, "console.log('ok');");

    $script = new class($fileName, $dir, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Act
    $valid = $script->isFilePathValid();

    // Assert
    expect($valid)->toBeTrue();
});

test('render throws exception when file path is invalid', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'missing-script';
    $extension = 'js';

    $script = new class($fileName, $dir, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Act
    $action = fn () => $script->render();

    // Assert
    expect($action)->toThrow(FromFileException::class, 'Script file not found');
});

test('render returns file contents when file path is valid', function (): void {
    // Arrange
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'inline-scripts-test-'.uniqid();
    if ( ! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = 'existing-script';
    $extension = 'js';
    $fullPath = $dir.DIRECTORY_SEPARATOR.$fileName.'.'.$extension;
    $expected = "console.log('hello from file');";
    file_put_contents($fullPath, $expected);

    $script = new class($fileName, $dir, $extension) extends FromFile
    {
        public function __construct(
            private string $myfileName,
            private string $myDirectory,
            private string $myExtension
        ) {
            parent::__construct($this->myfileName, $this->myDirectory, $this->myExtension);
        }
    };

    // Act
    $rendered = $script->render();

    // Assert
    expect($rendered)->toBe($expected);
});
