import { describe, it, beforeEach, expect, vi } from "vitest";
import fs from "node:fs";
import path from "node:path";

// Path to the raw JS template (with placeholders) used by PHP to generate a runtime script
const scriptPath = path.resolve(process.cwd(), "scripts/ColorSchemeSwitchThreeStates/js/init-script.js");

const DEFAULT_DARK = "dark";
const DEFAULT_LIGHT = "light";

function runThemeInitScript({
  dark = DEFAULT_DARK,
  light = DEFAULT_LIGHT,
  functionName = "themeTypeInit",
  matchMediaDarkMatches = false,
  matchMediaLightMatches = false,
} = {}) {
  // Load the template and substitute placeholders like PHP does
  let src = fs.readFileSync(scriptPath, "utf8");
  src = src.replaceAll("__FUNCTION_NAME__", functionName).replaceAll("__DARK__", dark).replaceAll("__LIGHT__", light);

  // Mock matchMedia if not present
  const mm = vi.fn().mockImplementation((query) => ({
    matches:
      (matchMediaDarkMatches && query.includes(`(prefers-color-scheme: ${dark})`)) ||
      (matchMediaLightMatches && query.includes(`(prefers-color-scheme: ${light})`)),
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

// IIFE = Immediately Invoked Function Expression
describe("ThemeInitScript.js IIFE behavior (three states)", () => {
  beforeEach(() => {
    // Reset DOM classes and storage before each test
    document.documentElement.className = "";
    // JSDOM provides localStorage per origin
    try {
      localStorage.clear();
    } catch {
      // Provide a tiny fallback mock if needed
      const store = new Map();
      // @ts-ignore
      global.localStorage = {
        getItem: (k) => (store.has(k) ? store.get(k) : null),
        setItem: (k, v) => store.set(k, String(v)),
        removeItem: (k) => store.delete(k),
        clear: () => store.clear(),
      };
    }
  });

  it("adds dark class when localStorage.colorScheme is dark", () => {
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(false);
  });

  it("adds light class when localStorage.colorScheme is light", () => {
    localStorage.setItem("colorScheme", DEFAULT_LIGHT);

    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });

  it("adds dark class when no localStorage.colorScheme and prefers-color-scheme: dark", () => {
    // No localStorage.colorScheme
    runThemeInitScript({ matchMediaDarkMatches: true, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(false);
  });

  it("adds light class when no localStorage.colorScheme and prefers-color-scheme: light", () => {
    // No localStorage.colorScheme
    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: true });

    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });

  it("adds no class when no localStorage.colorScheme and no preference", () => {
    // No localStorage.colorScheme and matchMedia false
    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(false);
  });

  it("is idempotent when executed twice", () => {
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    expect(() => {
      runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });
      runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });
    }).not.toThrow();

    // classList does not duplicate tokens; just ensure it is present
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
  });

  it("removes light class when switching to dark", () => {
    // First set light
    document.documentElement.classList.add(DEFAULT_LIGHT);
    localStorage.setItem("colorScheme", DEFAULT_DARK);

    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(false);
  });

  it("removes dark class when switching to light", () => {
    // First set dark
    document.documentElement.classList.add(DEFAULT_DARK);
    localStorage.setItem("colorScheme", DEFAULT_LIGHT);

    runThemeInitScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });

    expect(document.documentElement.classList.contains(DEFAULT_LIGHT)).toBe(true);
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
  });
});
