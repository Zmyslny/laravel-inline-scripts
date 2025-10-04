<p align="center">
    <a href="https://github.com/Zmyslny/laravel-inline-scripts/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/Zmyslny/laravel-inline-scripts/tests.yml?branch=main&label=Tests%201.x"></a>
</p>

------
# Laravel Inline Scripts

A Laravel package that provides a simple way to wrap your JavaScript code stored in a file and inline it as custom Blade directive.  

Allows ✨:
- passing variables from PHP to JavaScript,
- process / modify the script in a dedicated PHP class.

Additionally - has build in **ready-to-use** scripts:
 - theme switching script (two states - light/dark)
 - _more coming later_

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

### What are Inline Scripts?

Inline scripts are JavaScript code blocks embedded directly into HTML documents. Traditionally, developers manually write these scripts as strings in the `<head>` section or at the end of the `<body>` section:

```html
...
<script>
    // Traditional inline script
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
    });
</script>
</head>
```

This package makes it much more convenient by allowing you to keep inline scripts in separate JavaScript files, which enables ✨:

- **Complex script processing** using dedicated PHP classes _(see example below)_
- **Variable passing** from PHP to JavaScript _(see example below)_
- **Unit testing** your JavaScript code using tools like Vitest or Jest _(see bonus section below)_
- **Better code organization** and maintainability
- **IDE support** with syntax highlighting and error detection in dedicated JS files

# Explanation Through Example: Theme switch script

Modern websites often provide users with the ability to switch between light and dark themes. In such cases, you might want to remember the user's choice using `localStorage` and apply the selected theme on page load. To avoid **FOUC** (Flash of Unstyled Content), you can use _inline script_ to set the theme before the page fully loads.

### Using prepared PHP classes

Add the following to your `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates\SwitchScript;

class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new InitScript(),
            new SwitchScript('d')
        )->registerAs('themeSwitchScripts');
    }
}
```

and insert a newly created custom Blade directive in your template:

```blade
<html>
<head>
    ... 
    @themeSwitchScripts
</head>
<body>
    ...
``` 

Now hit the `d` key to toggle between light and dark themes, and your choice will be remembered on subsequent visits.

### Using JS code directly

**Step 1**: Publish the built-in scripts:

```bash
php artisan vendor:publish --tag=theme-switch-2-states-js
```

That will copy the scripts to `resources/js/theme-switch-two-states/[theme-init.js, theme-switch.js]`.

`theme-init.js` - initializes the theme based on the user's previous choice stored in `localStorage`.  
`theme-switch.js` - a function to toggle between light and dark themes by hitting a selected KEY and saves the choice in `localStorage`.

**Step 2**: Register the scripts in your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::takeFiles(
            [
                resource_path('js/theme-switch-two-states/theme-init.js'),
                ['__DARK__' => 'dark'], // variables to replace in the script
            ],
            [
                resource_path('js/theme-switch-two-states/theme-switch.js'),
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'], // variables to replace in the script
            ]
        )->registerAs('themeSwitchScripts');
    }
}
```

**Step 3**: Insert a newly created custom Blade directive in your template:

```blade
<html>
<head>
    ... 
    @themeSwitchScripts
</head>
<body>
    ...
``` 

Now hit the `d` key to toggle between light and dark themes, and your choice will be remembered on subsequent visits.

## Advanced Usage: Custom Script Processing

Want to do more advanced processing of your JavaScript code before inlining it?

Create a PHP class:
- that implements the `RenderableScript` interface - using it you can fetch / prepare / create JS code in any way you want;
- and place it in `BladeInlineScripts::take(...)` method.

Use interface `ScriptWithPlaceholders` for scripts with placeholders to be replaced with variables.

Want to load JS code from a file? Extend the abstract class `FromFile` or `FromFileWithPlaceholders`.

```php
abstract class FromFile implements RenderableScript;

abstract class FromFileWithPlaceholders implements ScriptWithPlaceholders;
```

## Bonus - Unit tests for JS scripts

```bash
php artisan vendor:publish --tag=theme-switch-2-states-js-tests
```

You can also publish JS code with test files at once:

```bash
php artisan vendor:publish --tag=theme-switch-2-states-all
```

## License

This package is licensed under the MIT License.


