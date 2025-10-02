# Laravel Inline Scripts

A Laravel package that provides a simple way to wrap your JavaScript code stored in a file and inline it as custom Blade directive.  

Allows ✨:
- passing variables from PHP to JavaScript,
- process / modify the script in a dedicated PHP class.

Extra - build in **ready-to-use** scripts:
 - theme switching script
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

Use the Blade directive in your template to inline the scripts:

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

This package makes it much more convenient by allowing you to keep inline scripts in separate JavaScript files, which enables:

- **Complex script processing** using dedicated PHP classes _(see example below)_
- **Variable passing** from PHP to JavaScript _(see example below)_
- **Unit testing** your JavaScript code using tools like Vitest or Jest _(see extra section below)_
- **Better code organization** and maintainability
- **IDE support** with syntax highlighting and error detection in dedicated JS files

# Explanation Through Example: Theme switch script

Modern websites often provide users with the ability to switch between light and dark themes. In such cases, you might want to remember the user's choice using `localStorage` and apply the selected theme on page load. To avoid **FOUC** (Flash of Unstyled Content), you can use _inline script_ to set the theme before the page fully loads.

**Step 1**: Publish the built-in theme switch scripts:

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
                ['__DARK__' => 'dark', '__LIGHT__' => 'light'], // variables to replace in the script
            ],
            [
                resource_path('js/theme-switch-two-states/theme-switch.js'),
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'], // variables to replace in the script
            ]
        )->registerAs('themeSwitchScripts');
    }
}
```

**Step 3**: Use the Blade directive in your template:

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

You can create a custom PHP class to process or modify your JavaScript code before inlining it. For example, you might want to minify the script or add some dynamic content.

Create a custom PHP processor class implementing the `RenderableScript` or `ScriptWithPlaceholders` interface and register it using the `BladeInlineScripts::take()` method.

We have prepared abstract base implementations for each of the interfaces:
```php
abstract class FromFile implements RenderableScript
abstract class FromFileWithPlaceholders implements ScriptWithPlaceholders
```

To show them in action, we have created PHP processors extending the base classes for the theme switch scripts:

**Step 1**: Publish the built-in theme switch scripts with the PHP processor:

```bash
php artisan vendor:publish --tag=theme-switch-2-states-php
```

That will copy the scripts to `resources/js/theme-switch-two-states/[theme-init.js, theme-switch.js]` and the processors classes to `app/Blade/ThemeSwitchTwoStates/[ThemeInitScript.php, ThemeSwitchScript]`.

**Step 2**: Register the scripts in your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new ThemeInitScript(),
            new ThemeSwitchScript('d')        
        )->registerAs('themeSwitchScripts');
    }
}
```

**Step 3**: Use the Blade directive in your template as previously shown.

Now hit the `d` key to toggle theme.

## Extra - get the tests for the built-in scripts and PHP processors

```bash
php artisan vendor:publish --tag=theme-switch-2-states-js-tests
php artisan vendor:publish --tag=theme-switch-2-states-php-tests
```

You can also publish all the assets mentioned at once:

```bash
php artisan vendor:publish --tag=theme-switch-2-states-all
```

## License

This package is licensed under the MIT License.


