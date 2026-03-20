# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel package that provides a simple way to wrap JavaScript code stored in files and inline it as a custom Blade directive. Allows passing variables from PHP to JavaScript and processing scripts in dedicated PHP classes.

## Development Commands

### Testing

```bash
composer test               # Run full test suite (type coverage, formatting, static analysis, unit tests, linting)
composer test:unit         # Run Pest unit tests in parallel
composer test:unit:co      # Run unit tests with coverage (requires 100% coverage)
composer test:type-co      # Run Pest type coverage (requires 100% coverage)
composer test:stan         # Run PHPStan static analysis
composer test:format:php   # Check PHP code formatting with Pint
composer test:format:js    # Check JavaScript formatting
composer test:refa         # Check Rector refactoring suggestions (dry-run)
composer test:lint         # Check JavaScript linting
composer test:js           # Run JavaScript tests with Vitest
```

### Code Formatting & Fixes

```bash
composer format:php        # Format PHP code with Pint
composer format:js         # Format and fix JavaScript
composer refa              # Apply Rector refactoring suggestions
composer lint              # Lint and fix JavaScript
```

### Single Test Execution

```bash
./vendor/bin/pest --path tests/Unit/BladeInlineScriptsTest.php
./vendor/bin/pest tests/Unit/BladeInlineScriptsTest.php --filter "testName"
npm run test:js -- --reporter=verbose
```

## Project Structure

### Core Architecture (`src/`)

**BladeInlineScriptsFactory** - Factory for creating script instances:
- `take(...$scripts)` - Register RenderableScript objects
- `takeFile()` / `takeFiles()` - Load scripts from files with optional placeholder replacement

**BladeInlineScriptsCore** - Core rendering logic:
- Accepts RenderableScript objects via constructor
- `registerAs(name)` - Register a custom Blade directive by name
- Generates unique script tag IDs with content hash
- Combines multiple scripts into a single `<script>` tag

**Script Loading Hierarchy**:
- `RenderableScript` interface - Any object that can `render()` and has a `getName()`
- `FromFile` abstract class - Load script from a file path
- `FromFileWithPlaceholders` - Extend FromFile to replace placeholders (e.g., `__DARK__` → `'dark'`)

**BladeDirectiveRegistrar** - Registers custom Blade directives via `@directiveName` syntax

### Ready-Made Scripts (`scripts/`)

Three production-ready script modules using the package's own architecture:

1. **ColorSchemeSwitchTwoStates** - Light/dark theme switching
2. **ColorSchemeSwitchThreeStates** - Light/dark/system theme switching
3. **LivewireNavAdapter** - Adapts navigation state for Livewire

Each includes:
- PHP script classes (InitScript, SwitchScript) implementing RenderableScript
- JavaScript files in `js/` directory
- Vitest test files in `tests/js/`
- Blade views for UI components in `view/` directory
- Publishable assets via `php artisan vendor:publish --tag=...`

### Testing Structure (`tests/`)

**Unit Tests**: Located in `tests/Unit/` and test subdirectories matching script modules
- Uses Pest framework with Orchestra Testbench for Laravel context
- TestCase extends `Orchestra\Testbench\TestCase` with BladeInlineScriptsProvider registered
- Runs with parallel execution by default

**Architecture Tests**: `tests/Architecture/` - Validates code structure (e.g., no leftover files)

**JavaScript Tests**: `tests/js/` - Vitest test files for corresponding scripts
- Mirrors script module structure
- Run via `npm run test:js`

## Key Concepts

### Script Rendering Flow

1. User registers scripts via `BladeInlineScripts::take()` or `::takeFiles()`
2. Returns `BladeInlineScriptsCore` instance
3. User calls `->registerAs('directiveName')`
4. Core registers Blade directive with Laravel
5. In Blade template, `@directiveName` renders all scripts into a single `<script id="...">` tag
6. Script tag ID is deterministic, based on script names + hash of combined content

### Placeholder Replacement Pattern

For `takeFiles()`, pass tuples to replace variables:
```php
[
    [resource_path('js/script.js'), ['__VAR__' => 'value']],
    [resource_path('js/other.js'), []]
]
```

Placeholders are replaced in `FromFileWithPlaceholders::render()` via simple string replacement.

### Service Provider Registration

`BladeInlineScriptsProvider` binds:
- Singleton `BladeInlineScriptsFactory` to container key `'blade-inline-scripts'`
- Singleton `BladeDirectiveRegistrar` interface to its implementation
- Publishable assets via `php artisan vendor:publish --tag=...` for color-scheme scripts

## Testing Patterns

- Use `Orchestra\Testbench\TestCase` for tests requiring Laravel context
- Mock filesystem via `Illuminate\Filesystem\Filesystem` when testing FromFile classes
- Verify rendered HTML contains expected `<script>` tag structure
- Test placeholder replacement by asserting replaced values appear in render output
- JavaScript tests use Vitest with jsdom environment for DOM testing

## Code Quality Standards

- PHP 8.2+ syntax required (strict_types=1 on all files)
- 100% code coverage required for PHP code
- 100% type coverage required for PHP code
- PHPStan analysis (strict level implied)
- Rector refactoring suggestions must be addressed
- Pint formatting (PSR-12 style)
- ESLint + Prettier for JavaScript (Tailwind sort plugin included)
- All tests must pass before merging
