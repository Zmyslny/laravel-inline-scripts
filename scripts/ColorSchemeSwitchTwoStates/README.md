# Color Scheme Switch - Two States (Light/Dark)

Skrypt do przełączania między dwoma motywami kolorystycznymi: jasnym (light) i ciemnym (dark).

## Co robi ten skrypt?

Skrypt zapewnia funkcjonalność przełączania między jasnym i ciemnym motywem na stronie internetowej, z zachowaniem preferencji użytkownika w `localStorage`. Składa się z dwóch części:

### 1. InitScript - Inicjalizacja motywu przy ładowaniu strony

**Plik:** `init-script.js`

Skrypt uruchamia się **przed wyrenderowaniem strony** (inline w `<head>`), aby uniknąć efektu **FOUC** (Flash of Unstyled Content).

**Działanie:**
- Sprawdza `localStorage.colorScheme` i jeśli użytkownik wcześniej wybrał motyw, go stosuje
- Jeśli brak zapisanej preferencji, sprawdza systemowe ustawienia (`prefers-color-scheme`)
- Dodaje klasę `dark` do `document.documentElement` gdy motyw ciemny jest aktywny

### 2. SwitchScript - Przełączanie motywu

**Plik:** `switch-script.js`

Skrypt umożliwia przełączanie między motywami poprzez skrót klawiszowy (domyślnie klawisz `d`).

**Działanie:**
- Udostępnia funkcję `window.inlineScripts.switchColorScheme()` do programowego przełączania motywu
- Nasłuchuje skrótu klawiszowego (domyślnie `d`) i przełącza motyw
- Zapisuje wybraną preferencję w `localStorage`
- Inteligentnie ignoruje naciśnięcia klawisza gdy fokus jest na polach input, textarea, select lub elementach z contentEditable

## Użycie

### Metoda 1: Przy użyciu klas PHP (zalecane)

**Krok 1:** Dodaj do swojego `AppServiceProvider`:

```php
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\InitScript;
use Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates\SwitchScript;

class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::take(
            new InitScript(), // inicjalizuje motyw przy ładowaniu strony
            new SwitchScript('d') // przełącza motyw po naciśnięciu klawisza 'd'
        )->registerAs('colorSchemeScripts');
    }
}
```

**Krok 2:** Wstaw dyrektywę Blade w swoim szablonie:

```blade
<html>
<head>
    ... 
    @colorSchemeScripts
</head>
<body>
    ...
``` 

**Krok 3 (opcjonalnie):** Dodaj widok z ikonami motywów:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-views
```

Wybierz odpowiedni widok i wstaw go w swoim szablonie:
- Blade + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind.blade.php`
- Livewire/Alpine + TailwindCss + Hero icons: `../views/color-scheme-switch-two-states/hero-icons-tailwind-alpine.blade.php`

### Metoda 2: Bezpośrednie użycie plików JS

**Krok 1:** Opublikuj wbudowane skrypty:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-js
```

Skopiuje to skrypty do `resources/js/color-scheme-switch-two-states/[init-script.js, switch-script.js]`.

**Krok 2:** Zarejestruj skrypty w `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider 
{
    public function boot(): void 
    {
        BladeInlineScripts::takeFiles(
            [
                resource_path('js/color-scheme-switch-two-states/init-script.js'),
                ['__DARK__' => 'dark'], // zmienne do zastąpienia w skrypcie
            ],
            [
                resource_path('js/color-scheme-switch-two-states/switch-script.js'),
                ['__DARK__' => 'dark', '__LIGHT__' => 'light', '__TOGGLE_KEY__' => 'd'],
            ]
        )->registerAs('colorSchemeScripts');
    }
}
```

**Krok 3:** Wstaw dyrektywę Blade w swoim szablonie (tak samo jak w Metodzie 1).

## Konfiguracja

### Zmiana klawisza przełączania

Domyślnie skrypt używa klawisza `d` do przełączania motywów. Możesz to zmienić przekazując inny klawisz do konstruktora `SwitchScript`:

```php
new SwitchScript('t') // użyj klawisza 't' zamiast 'd'
```

**Wymagania:** Klawisz musi być pojedynczą małą literą (a-z).

### Zmiana nazw klas CSS

Domyślnie skrypt dodaje klasę `dark` do elementu `<html>`. Możesz to zmienić edytując wartości w enumie `SchemeTypeEnum`:

```php
enum SchemeTypeEnum: string
{
    case DARK = 'dark';
    case LIGHT = 'light';
}
```

Lub przy użyciu bezpośrednich plików JS, zmieniając placeholdery:

```php
['__DARK__' => 'dark-mode', '__LIGHT__' => 'light-mode']
```

## Programowe przełączanie motywu

Skrypt udostępnia funkcję do programowego przełączania motywu:

```javascript
window.inlineScripts.switchColorScheme();
```

Możesz jej użyć np. w obsłudze kliknięcia przycisku:

```html
<button onclick="window.inlineScripts.switchColorScheme()">
    Przełącz motyw
</button>
```

## Struktura plików

```
ColorSchemeSwitchTwoStates/
├── InitScript.php              # Klasa PHP do inicjalizacji motywu
├── SwitchScript.php            # Klasa PHP do przełączania motywu
├── SchemeTypeEnum.php          # Enum z wartościami motywów (dark/light)
├── js/
│   ├── init-script.js          # Skrypt inicjalizacji (inline w <head>)
│   └── switch-script.js        # Skrypt przełączania (inline w <head>)
└── view/
    ├── hero-icons-tailwind.blade.php        # Widok z ikonami (Blade + Tailwind)
    └── hero-icons-tailwind-alpine.blade.php # Widok z ikonami (Alpine + Tailwind)
```

## Jak to działa?

1. **Przy ładowaniu strony:** `init-script.js` sprawdza zapisaną preferencję lub preferencje systemowe i natychmiast aplikuje odpowiedni motyw
2. **Przełączanie:** Po naciśnięciu skrótu klawiszowego, `switch-script.js` przełącza klasę `dark` i zapisuje wybór w `localStorage`
3. **Następne wizyty:** Przy kolejnym ładowaniu strony, `init-script.js` odczyta zapisaną preferencję i zastosuje ją przed wyrenderowaniem strony

## Testowanie

Możesz opublikować testy jednostkowe dla skryptów JS:

```bash
php artisan vendor:publish --tag=color-scheme-2-states-js-tests
```

Lub opublikować wszystko naraz (skrypty JS, testy i widoki):

```bash
php artisan vendor:publish --tag=color-scheme-2-states-all
```

## Różnica między Two States a Three States

- **Two States:** Przełączanie między jasnym i ciemnym motywem (2 stany)
- **Three States:** Przełączanie między jasnym, ciemnym i systemowym motywem (3 stany)

Jeśli potrzebujesz opcji "auto/system", użyj wariantu Three States.
