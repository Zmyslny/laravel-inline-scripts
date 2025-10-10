# Color Scheme Switch - Three States (Light/Dark/System)

A script for switching between three color schemes: light, dark, and system preference.

> **Icons used** (from [HeroIcons](https://heroicons.com)):
>
> ![View](/../assets/3-states-hero-icons.gif)

## What does this script do?

The script provides functionality for switching between light, dark, and system themes on a website, while preserving user preferences in `localStorage`. It consists of two parts:

### 1. InitScript - Theme initialization on page load

**File:** `init-script.js`

The script runs **before the page renders** (inline in `<head>`) to avoid **FOUC** (Flash of Unstyled Content).

**Behavior:**
- Checks `localStorage.colorScheme` and if the user has previously selected a theme (light or dark), applies it
- If there's no saved preference (system mode), checks system settings (`prefers-color-scheme`)
- Adds the `dark` class to `document.documentElement` when dark theme is active

### 2. SwitchScript - Theme switching

**File:** `switch-script.js`

The script enables cycling between themes via keyboard shortcut (default key `d`).

**Behavior:**
- Exposes the `window.inlineScripts.switchColorScheme()` function for programmatic theme switching
- Listens for keyboard shortcut (default `d`) and cycles through themes: **Dark → Light → System → Dark → ...**
- Saves the selected preference in `localStorage` (or removes it for system mode)
- Intelligently ignores key presses when focus is on input, textarea, select fields or elements with contentEditable
- Dispatches a custom `colorSchemeChanged` event when the theme changes

### 3. View with color scheme icons

A Blade view with light/dark/system theme icons that call the `window.inlineScripts.switchColorScheme()` function when clicked.

## Usage

### Method 1: Using PHP classes

**Step 1:** Add to your `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates\SwitchScript;

class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new InitScript(), // initializes the theme on page load
            new SwitchScript('d') // cycles through themes when 'd' key is pressed
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
php artisan vendor:publish --tag=color-scheme-3-states-views
```

Select the appropriate view and insert it in your template:
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-three-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-three-states/hero-icons-tailwind-alpine.blade.php`

### Method 2: Direct JS file usage

**Step 1:** Publish the built-in scripts:

```bash
php artisan vendor:publish --tag=color-scheme-3-states-js
```

This will copy the scripts to `resources/js/color-scheme-switch-three-states/[init-script.js, switch-script.js]`.

**Step 2:** Register the scripts in `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::takeFiles(
            [
                resource_path('js/color-scheme-switch-three-states/init-script.js'),
                ['__DARK__' => 'dark'], // variables to replace in the script
            ],
            [
                resource_path('js/color-scheme-switch-three-states/switch-script.js'),
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'],
            ]
        )->registerAs('colorSchemeScripts');
    }
}
```

**Step 3:** Insert the Blade directive in your template (same as in Method 1).

## Configuration

### Changing the toggle key

By default, the script uses the `d` key to cycle through themes. You can change this by passing a different key to the `SwitchScript` constructor:

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
    case SYSTEM = 'system';
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

## Custom event: colorSchemeChanged

The script dispatches a custom event whenever the theme changes, which you can listen to:

```javascript
document.addEventListener('colorSchemeChanged', (event) => {
    console.log('Previous scheme:', event.detail.previousScheme);
    console.log('Current scheme:', event.detail.currentScheme);
});
```

This is useful if you need to perform additional actions when the theme changes.

## File structure

```
ColorSchemeSwitchThreeStates/
├── InitScript.php              # PHP class for theme initialization
├── SwitchScript.php            # PHP class for theme switching
├── SchemeTypeEnum.php          # Enum with theme values (dark/light/system)
├── js/
│   ├── init-script.js          # Initialization script (inline in <head>)
│   └── switch-script.js        # Switching script (inline in <head>)
└── view/
    ├── hero-icons-tailwind.blade.php        # View with icons (Blade + Tailwind)
    └── hero-icons-tailwind-alpine.blade.php # View with icons (Alpine + Tailwind)
```

## How does it work?

1. **On page load:** `init-script.js` checks the saved preference or system preferences and immediately applies the appropriate theme
2. **Switching:** After pressing the keyboard shortcut or clicking the icons, `switch-script.js` cycles through themes in the order: **Dark → Light → System → Dark**
3. **System mode:** When in system mode, `localStorage.colorScheme` is removed, and the theme follows the operating system's preference
4. **Subsequent visits:** On the next page load, `init-script.js` will read the saved preference (or system settings if no preference is saved) and apply it before the page renders

## Theme cycling explained

The three-state variant cycles through themes in the following order:

1. **Dark** - Explicitly sets dark theme (saves `"dark"` in localStorage)
2. **Light** - Explicitly sets light theme (saves `"light"` in localStorage)
3. **System** - Follows system preference (removes the value from localStorage)

When in system mode, the script checks `prefers-color-scheme` media query to determine which theme to apply.

## Testing

You can publish unit tests for the JS scripts:

```bash
php artisan vendor:publish --tag=color-scheme-3-states-js-tests
```

Or publish everything at once (JS scripts, tests, and views):

```bash
php artisan vendor:publish --tag=color-scheme-3-states-all
```

## Difference between Two States and Three States

- **Two States:** Switching between light and dark theme (2 states)
- **Three States:** Cycling between light, dark, and system theme (3 states)

The three-state variant is useful when you want to give users the option to follow their operating system's color scheme preference, in addition to explicitly choosing light or dark mode.
