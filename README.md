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
 - color scheme switching script (two states - light/dark)
 - color scheme switching script (three states - light/dark/system)
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

# Explanation Through Example: Color scheme switch script

Modern websites often provide users with the ability to switch between light and dark themes. In such cases, you might want to remember the user's choice using `localStorage` and apply the selected theme on page load. To avoid **FOUC** (Flash of Unstyled Content), you can use _inline script_ to set the theme before the page fully loads.

The folowing example demonstrates by using **two-state** color scheme switch script (light/dark). 

**Three-state** (light/dark/system) is also available. Just replace `2-states- | ..TwoStates` with `3-states- | ..ThreeStates` in the commands and code below.

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" /></svg> **Sun** (Light mode)
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" /></svg> **Moon** (Dark mode)
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd" /></svg> **Desktop** (System preference)

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
            new InitScript(),
            new SwitchScript('d')
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

Get the available view files:
```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Select the one that suits your frontend and insert it in your template: 
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" /></svg> **Sun** (Light mode) 
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" /></svg> **Moon** (Dark mode)

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

Get the available view files:
```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Select the one that suits your frontend and insert it in your template:
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" /></svg> **Sun** (Light mode)
> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" /></svg> **Moon** (Dark mode)

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

## Bonuses

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


