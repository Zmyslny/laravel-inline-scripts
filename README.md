# Laravel Inline Scripts

A Laravel package that provides a simple way to wrap your JavaScript code stored in a file and inline it as custom Blade directive. 

Additionally, allows you to pass variables from PHP to JavaScript easily or process the script in a dedicated PHP class.

## Requirements

- PHP 8.2 or newer
- Laravel 9.x or newer (did not test with older versions)

## 🚀 Quick Start

Install the package via Composer:

```bash
composer require zmyslny/laravel-inline-scripts
```

Register a custom Blade directive for your JavaScript file or files, typically in a service provider `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
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

## What are Inline Scripts?

Inline scripts are JavaScript code blocks embedded directly into HTML documents. Traditionally, developers manually write these scripts as strings in the `<head>` section or at the end of the `<body>` section:

```html
<script>
    // Traditional inline script
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
    });
</script>
```

This package makes it much more convenient by allowing you to keep inline scripts in separate JavaScript files, which enables:

- **Unit testing** your JavaScript code using tools like Vitest or Jest _(see the example section below)_
- **Better code organization** and maintainability
- **IDE support** with syntax highlighting and error detection
- **Variable passing** from PHP to JavaScript

### Example Usage with Blade

Instead of writing JavaScript directly in your Blade template:

```blade
<!-- Old way -->
<script>
    const userId = {{ auth()->id() }};
    const theme = '{{ $theme }}';
    // ... more JavaScript code
</script>
```

You can now use:

```blade
<!-- New way -->
@myInlineScript
```

## Usage Example: Theme Switch Scripts

The package includes example scripts for theme switching functionality (dark/light theme) with user preference persistence in localStorage, available under the "theme-switch-2-states-all" tag.

### Publishing Theme Switch Scripts

To use the included theme switch scripts, publish them:

```bash
php artisan vendor:publish --tag="theme-switch-2-states-all"
```

### Adding as JavaScript Files

After publishing, you can include the scripts in your Blade templates:

```blade
@inlineScript('theme-switch-two-states')
```

### Adding as PHP Classes

You can also use the theme switch functionality through PHP classes:

```php
use LaravelInlineScripts\ThemeSwitch\ThemeSwitchScript;

// In your controller
$themeSwitchScript = new ThemeSwitchScript([
    'defaultTheme' => 'light',
    'storageKey' => 'user-theme-preference'
]);

return view('your-view', ['themeScript' => $themeSwitchScript]);
```

Then in your Blade template:

```blade
{!! $themeScript->render() !!}
```

The theme switch scripts provide:
- Automatic theme detection based on user's system preference
- Toggle functionality between dark and light themes
- Persistence of user choice in localStorage
- Smooth theme transitions

## 📖 License

This package is licensed under the MIT License.


