# Color Scheme Switch - Two States (Light/Dark)

A script for switching between two color schemes: light and dark.

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> ![View](/../assets/2-states-hero-icons.gif)

## What does this script do?

The script provides functionality for switching between light and dark themes on a website, while preserving user preferences in `localStorage`. It consists of three parts:

### 1. InitScript - Theme initialization on page load

**File:** `init-script.js`

The script runs **before the page renders** (inline in `<head>`) to avoid **FOUC** (Flash of Unstyled Content).

**Behavior:**
- Checks `localStorage.colorScheme` and if the user has previously selected a theme, applies it
- If there's no saved preference, checks system settings (`prefers-color-scheme`)
- Adds the `dark` class to `document.documentElement` when dark theme is active

### 2. SwitchScript - Theme switching

**File:** `switch-script.js`

The script enables switching between themes via keyboard shortcut (default key `d`).

**Behavior:**
- Exposes the `window.inlineScripts.switchColorScheme()` function for programmatic theme switching
- Listens for keyboard shortcut (default `d`) and switches the theme
- Saves the selected preference in `localStorage`
- Intelligently ignores key presses when focus is on input, textarea, select fields or elements with contentEditable

### 3. View with color scheme icons

A Blade view with light/dark theme icons that call the `window.inlineScripts.switchColorScheme()` function when clicked.

## Usage

### Method 1: Using PHP classes

**Step 1:** Add to your `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SwitchScript;

class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new InitScript(), // initializes the theme on page load
            new SwitchScript('d') // switches the theme when 'd' key is pressed
        )->registerAs('colorSchemeScripts');
    }
}
```

**Step 2:** Insert the Blade directive in your template:

```blade
<html>
<head>
    ... 
    @colorSchemeScripts
</head>
<body>
    ...
``` 

**Step 3:** Add the view with theme icons:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Select the appropriate view and insert it in your template:
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

### Method 2: Direct JS file usage

**Step 1:** Publish the built-in scripts:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-js
```

This will copy the scripts to `resources/js/color-scheme-switch-two-states/[init-script.js, switch-script.js]`.

**Step 2:** Register the scripts in `AppServiceProvider`:

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
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'],
            ]
        )->registerAs('colorSchemeScripts');
    }
}
```

**Step 3:** Insert the Blade directive in your template (same as in Method 1).

## Configuration

### Changing the toggle key

By default, the script uses the `d` key to switch themes. You can change this by passing a different key to the `SwitchScript` constructor:

```php
new SwitchScript('t') // use 't' key instead of 'd'
```

**Requirements:** The key must be a single lowercase letter (a-z).

### Changing CSS class names

By default, the script adds the `dark` class to the `<html>` element. You can change this by editing the values in the `SchemeTypeEnum` enum:

```php
enum SchemeTypeEnum: string
{
    case DARK = 'dark';
    case LIGHT = 'light';
}
```

Or when using direct JS files, by changing the placeholders:

```php
['__DARK__' => 'dark-mode', '__LIGHT__' => 'light-mode']
```

## Programmatic theme switching

The script exposes a function for programmatic theme switching:

```javascript
window.inlineScripts.switchColorScheme();
```

You can use it, for example, in a button click handler:

```html
<button onclick="window.inlineScripts.switchColorScheme()">
    Toggle theme
</button>
```

## File structure

```
ColorSchemeSwitchTwoStates/
├── InitScript.php              # PHP class for theme initialization
├── SwitchScript.php            # PHP class for theme switching
├── SchemeTypeEnum.php          # Enum with theme values (dark/light)
├── js/
│   ├── init-script.js          # Initialization script (inline in <head>)
│   └── switch-script.js        # Switching script (inline in <head>)
└── view/
    ├── hero-icons-tailwind.blade.php        # View with icons (Blade + Tailwind)
    └── hero-icons-tailwind-alpine.blade.php # View with icons (Alpine + Tailwind)
```

## How does it work?

1. **On page load:** `init-script.js` checks the saved preference or system preferences and immediately applies the appropriate theme
2. **Switching:** After pressing the keyboard shortcut or clicking the icons, `switch-script.js` toggles the `dark` class and saves the choice in `localStorage`
3. **Subsequent visits:** On the next page load, `init-script.js` will read the saved preference and apply it before the page renders

## Testing

You can publish unit tests for the JS scripts:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-js-tests
```

Or publish everything at once (JS scripts, tests, and views):

```bash
php artisan vendor:publish --tag=color-scheme-2-states-all
```

## Difference between Two States and Three States

- **Two States:** Switching between light and dark theme (2 states)
- **Three States:** Switching between light, dark, and system theme (3 states)

If you need an "auto/system" option, use the Three States variant.
