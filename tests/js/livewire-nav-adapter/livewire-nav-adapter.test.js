import { describe, it, beforeEach, expect, vi } from "vitest";
import fs from "node:fs";
import path from "node:path";

// Path to the raw JS template (with placeholders) used by PHP to generate a runtime script
const scriptPath = path.resolve(process.cwd(), "scripts/LivewireNavAdapter/js/livewire-nav-adapter.js");

const DEFAULT_DARK = "dark";
const DEFAULT_LIGHT = "light";

/**
 * Create a Proxy-based localStorage mock that supports property access
 * (e.g. localStorage.colorScheme) as required by the Web Storage spec.
 * Node's built-in localStorage does not support this, so we need a mock.
 */
function createLocalStorageMock() {
  const store = new Map();

  return new Proxy(
    {
      getItem: (k) => (store.has(k) ? store.get(k) : null),
      setItem: (k, v) => store.set(k, String(v)),
      removeItem: (k) => store.delete(k),
      clear: () => store.clear(),
      get length() {
        return store.size;
      },
    },
    {
      get(target, prop) {
        if (prop in target) return target[prop];
        return store.has(prop) ? store.get(prop) : undefined;
      },
      set(target, prop, value) {
        if (prop in target) {
          target[prop] = value;
        } else {
          store.set(prop, String(value));
        }
        return true;
      },
    },
  );
}

function runNavAdapterScript({
  dark = DEFAULT_DARK,
  light = DEFAULT_LIGHT,
  functionName = "livewireNavAdapter",
  matchMediaDarkMatches = false,
} = {}) {
  // Load the template and substitute placeholders like PHP does
  let src = fs.readFileSync(scriptPath, "utf8");
  src = src.replaceAll("__FUNCTION_NAME__", functionName).replaceAll("__DARK__", dark).replaceAll("__LIGHT__", light);

  // Mock matchMedia if not present
  const mm = vi.fn().mockImplementation((query) => ({
    matches: matchMediaDarkMatches && query.includes(`(prefers-color-scheme: ${dark})`),
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  }));

  Object.defineProperty(window, "matchMedia", {
    writable: true,
    configurable: true,
    value: mm,
  });

  // Execute the IIFE in global scope
  // Using Function constructor to avoid ESM import side effects
  const fn = new Function(src);
  fn();
}

function dispatchLivewireNavigated() {
  const event = new Event("livewire:navigated", { bubbles: true });
  document.dispatchEvent(event);
}

// IIFE = Immediately Invoked Function Expression
describe("LivewireNavAdapter.js IIFE behavior", () => {
  beforeEach(() => {
    // Reset DOM classes and storage before each test
    document.documentElement.className = "";

    // Replace global localStorage with a Proxy-based mock that supports
    // property access (localStorage.colorScheme), which the scripts rely on.
    const mock = createLocalStorageMock();
    Object.defineProperty(globalThis, "localStorage", {
      writable: true,
      configurable: true,
      value: mock,
    });
  });

  it("adds dark class when localStorage.colorScheme is dark", () => {
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    runNavAdapterScript({ matchMediaDarkMatches: false });
    dispatchLivewireNavigated();

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
  });

  it("removes dark class when localStorage.colorScheme is light", () => {
    document.documentElement.classList.add(DEFAULT_DARK);
    localStorage.setItem("colorScheme", DEFAULT_LIGHT);

    runNavAdapterScript({ matchMediaDarkMatches: false });
    dispatchLivewireNavigated();

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });

  it("adds dark class when no localStorage.colorScheme and prefers-color-scheme: dark", () => {
    // No localStorage.colorScheme
    runNavAdapterScript({ matchMediaDarkMatches: true });
    dispatchLivewireNavigated();

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
  });

  it("does not add dark class when no localStorage.colorScheme and no dark preference", () => {
    // No localStorage.colorScheme and matchMedia false
    runNavAdapterScript({ matchMediaDarkMatches: false });
    dispatchLivewireNavigated();

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });

  it("does nothing before livewire:navigated event is dispatched", () => {
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    runNavAdapterScript();
    // No dispatchLivewireNavigated() call

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });

  it("is idempotent when executed twice", () => {
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    expect(() => {
      runNavAdapterScript();
      runNavAdapterScript();
    }).not.toThrow();

    dispatchLivewireNavigated();

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
  });

  it("reacts to multiple livewire:navigated events", () => {
    runNavAdapterScript({ matchMediaDarkMatches: false });

    // First navigation with dark preference
    localStorage.setItem("colorScheme", DEFAULT_DARK);
    dispatchLivewireNavigated();
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);

    // Second navigation with light preference
    localStorage.setItem("colorScheme", DEFAULT_LIGHT);
    dispatchLivewireNavigated();
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });
});
