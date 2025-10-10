<p align="center">
    <a href="https://github.com/zmyslny/laravel-inline-scripts/actions"><img src="https://github.com/zmyslny/laravel-inline-scripts/actions/workflows/tests.yml/badge.svg"  alt="Build Status" ></a>
    <a href="https://packagist.org/packages/zmyslny/laravel-inline-scripts"><img src="https://poser.pugx.org/zmyslny/laravel-inline-scripts/v/stable.svg" alt="Latest Version"></a>
</p>

------
# Laravel Inline Scripts

A Laravel package that provides a simple way to wrap your JavaScript code stored in a file and inline it as custom Blade directive.  

Allows ✨:
- passing variables from PHP to JavaScript,
- process / modify the script in a dedicated PHP class.

Additionally - has build in **ready-to-use** scripts:
 - [two states](scripts/ColorSchemeSwitchThreeStates/README.md) - light / dark - color scheme switching script _(+ view with icons)_
 - [three states](scripts/ColorSchemeSwitchThreeStates/README.md) - light / dark / system - color scheme switching script _(+ view with icons)_
 - _more to come_

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

### What are inline scripts?

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

- **Complex script processing** : using dedicated PHP classes _(see [example bellow](#using-prepared-php-classes))_
- **Variable passing** : from PHP to JavaScript _(see [example bellow](#using-prepared-php-classes))_
- **Unit testing** : your JavaScript code using tools like Vitest or Jest _(see [bonus section](#bonus))_
- **IDE support** : with syntax highlighting and error detection in dedicated JS files _(see [example bellow](#using-js-code-directly))_

# Explanation Through Example: Color scheme switch script

Modern websites often provide users with the ability to switch between light and dark themes. In such cases, you might want to remember the user's choice using `localStorage` and apply the selected theme on page load. To avoid **FOUC** (Flash of Unstyled Content), you can use _inline script_ to set the theme before the page fully loads.

The folowing example demonstrates by using **two-state** color scheme switch script (light/dark). 

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> ![View](assets/2-states-hero-icons.gif)

**Three-state** (light/dark/system) is also available. Just replace `2-states- | ..TwoStates` with `3-states- | ..ThreeStates` in the commands and code below.


### Using prepared PHP classes

**Step 1**: Add the following to your `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SwitchScript;

class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new InitScript(), // initializes the color scheme on page load
            new SwitchScript('d') // toggles the color scheme when the 'd' key is pressed
        )->registerAs('colorSchemeScripts');
    }
}
```

**Step 2**: Insert a newly created custom Blade directive in your template:

```blade
<html>
<head>
    ... 
    @colorSchemeScripts
</head>
<body>
    ...
``` 

Now hit the `d` key to toggle between a light and dark color scheme, and your choice will be remembered on subsequent visits.

**Step 3 (optional)**: Add the view with color scheme icons to your website:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Select the one that suits your frontend and insert it in your template: 
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

### Using JS code directly

**Step 1**: Publish the built-in scripts:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-js
```

That will copy the scripts to `resources/js/color-scheme-switch-two-states/[init-script.js, switch-script.js]`.

**Step 2**: Register the scripts in your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::takeFiles(
            [
                resource_path('js/color-scheme-switch-two-states/init-script.js'),
                ['__DARK__' => 'dark'], // variables to replace in the script
            ],
            [
                resource_path('js/color-scheme-switch-two-states/switch-script.js'),
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'], // variables to replace in the script
            ]
        )->registerAs('colorSchemeScripts');
    }
}
```

**Step 3**: Insert a newly created custom Blade directive in your template:

```blade
<html>
<head>
    ... 
    @colorSchemeScripts
</head>
<body>
    ...
``` 

Now hit the `d` key to toggle between a light and dark color scheme, and your choice will be remembered on subsequent visits.

**Step 3 (optional)**: Add the view with color scheme icons to your website:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Select the one that suits your frontend and insert it in your template:
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

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

## Bonus

Unit tests for JS scripts
```bash
php artisan vendor:publish --tag=color-scheme-2-states-js-tests
# or
php artisan vendor:publish --tag=color-scheme-3-states-js-tests
```

You can also publish JS scripts, JS test files and views at once:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-all
# or
php artisan vendor:publish --tag=color-scheme-3-states-all
```

## License

This package is licensed under the MIT License.


