# Laravel Inline Scripts
<p>
    <a href="https://github.com/zmyslny/laravel-inline-scripts/actions"><img src="https://github.com/zmyslny/laravel-inline-scripts/actions/workflows/tests.yml/badge.svg"  alt="Build Status" ></a>
    <a href="https://packagist.org/packages/zmyslny/laravel-inline-scripts"><img src="https://poser.pugx.org/zmyslny/laravel-inline-scripts/v/stable.svg" alt="Latest Version"></a>
</p>

A Laravel package that provides a simple way to wrap your JavaScript code stored in a file and inline it as custom Blade directive.  

Allows ✨:
- passing variables from PHP to JavaScript,
- process / modify the script in a dedicated PHP class.

Additionally - has build in **ready-to-use** scripts (built using this package):
 - [ColorSchemeSwitchThreeStates](scripts/ColorSchemeSwitchTwoStates/README.md) - light / dark - color scheme switching script _(+ view with icons)_
 - [ColorSchemeSwitchTwoStates](scripts/ColorSchemeSwitchThreeStates/README.md) - light / dark / system - color scheme switching script _(+ view with icons)_
 - [LivewireNavAdapter](scripts/LivewireNavAdapter/README.md) - color scheme navigation state adapter for Livewire

### Requirements

- PHP 8.2 or newer
- Laravel 9.x or newer (did not test with older versions)

## 🚀 Quick Start

Install the package via Composer:

```bash
composer require zmyslny/laravel-inline-scripts
```

Register a custom Blade directive for your JavaScript in your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::takeFiles(
            resource_path('js/your-first-script.js'),
            resource_path('js/your-second-script.js'),
            ...
        )->registerAs('myInlineScripts');
    }
}
```

Use the custom Blade directive in your template to inline the scripts:

```blade
<html>
<head>
    ...
    
    @myInlineScripts
</head>
<body>
    ...
```

Done.

## Using Built-in Scripts

The package includes ready-to-use scripts. For example, to add a color scheme switcher:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SwitchScript;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        BladeInlineScripts::take(
            new InitScript(),
            new SwitchScript('d') // toggle dark & light modes with 'd' key
        )->registerAs('colorScheme');
    }
}
```

Then use in your template:

```blade
@colorScheme
```

See the [Color Scheme Switch](scripts/ColorSchemeSwitchTwoStates/README.md), [Color Scheme Three States](scripts/ColorSchemeSwitchThreeStates/README.md), and [LivewireNavAdapter](scripts/LivewireNavAdapter/README.md) documentation for full details and customization options.

## Advanced Usage

For more advanced script processing, create a PHP class that implements the `RenderableScript` interface to prepare or transform your JavaScript code. Use the abstract class `FromFile` to load scripts from files, or `FromFileWithPlaceholders` to include placeholder replacement (e.g., `__VARIABLE__` → value).

Register your custom scripts using `BladeInlineScripts::take(...)`.

For a complete working example with detailed setup instructions, see the [Color Scheme Switch](scripts/ColorSchemeSwitchTwoStates/README.md) documentation.

## License

This package is licensed under the MIT License.


