# Livewire Nav Adapter

A script that re-applies the active color scheme after Livewire navigation (`livewire:navigated` event).

## What does this script do?

When using Livewire's SPA-style navigation, the `<html>` element's classes can be lost after a page transition. This script listens for the `livewire:navigated` event and restores the correct color scheme class based on `localStorage.colorScheme` or the system preference (`prefers-color-scheme`).

**Designed to be used together with** `ColorSchemeSwitchTwoStates` or `ColorSchemeSwitchThreeStates`.

## Usage

### Method 1: Using PHP class

Add `LivewireNavAdapter` alongside the color scheme scripts in your `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SwitchScript;
use Zmyslny\LaravelInlineScripts\Ready\LivewireNavAdapter\LivewireNavAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        BladeInlineScripts::take(
            new InitScript(),
            new SwitchScript('d'),
            new LivewireNavAdapter()
        )->registerAs('colorSchemeScripts');
    }
}
```

Then insert the directive in your template:

```blade
<html>
<head>
    ...
    @colorSchemeScripts
</head>
```

### Method 2: Direct JS file usage

**Step 1:** Publish the script:

```bash
php artisan vendor:publish --tag=livewire-nav-adapter-js
```

This copies the script to `resources/js/livewire-nav-adapter/livewire-nav-adapter.js`.

**Step 2:** Register in `AppServiceProvider`:

```php
BladeInlineScripts::takeFiles(
    [
        resource_path('js/livewire-nav-adapter/livewire-nav-adapter.js'),
        ['__DARK__' => 'dark', '__LIGHT__' => 'light'],
    ]
)->registerAs('livewireNavAdapter');
```

## File structure

```
LivewireNavAdapter/
├── LivewireNavAdapter.php          # PHP class
└── js/
    └── livewire-nav-adapter.js     # Script (inline in <head>)
```
