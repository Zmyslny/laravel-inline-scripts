import { describe, it, beforeEach, expect } from "vitest";
import fs from "node:fs";
import path from "node:path";

// Path to the raw JS template (with placeholders) used by PHP to generate a runtime script
const scriptPath = path.resolve(process.cwd(), "resources/js/theme-switch-two-states/theme-switch.js");

const DEFAULT_TOGGLE_KEY = "d";
const DEFAULT_DARK = "dark";
const DEFAULT_LIGHT = "light";

function runThemeSwitchScript({
  dark = DEFAULT_DARK,
  light = DEFAULT_LIGHT,
  functionName = "themeSwitch",
  toggleKey = DEFAULT_TOGGLE_KEY,
} = {}) {
  // Load the template and substitute placeholders like PHP does
  let src = fs.readFileSync(scriptPath, "utf8");
  src = src
    .replaceAll("__FUNCTION_NAME__", functionName)
    .replaceAll("__DARK__", dark)
    .replaceAll("__LIGHT__", light)
    .replaceAll("__TOGGLE_KEY__", toggleKey);

  // Execute the IIFE in global scope

  const fn = new Function(src);
  fn();
}

function dispatchKeydown(key, { ctrlKey = false, altKey = false, metaKey = false } = {}) {
  const event = new window.KeyboardEvent("keydown", {
    key,
    ctrlKey,
    altKey,
    metaKey,
    bubbles: true,
    cancelable: true,
  });

  document.dispatchEvent(event);
}

// IIFE = Immediately Invoked Function Expression
describe("ThemeSwitchScript.js IIFE behavior", () => {
  beforeEach(() => {
    document.documentElement.className = "";
    try {
      localStorage.clear();
    } catch {
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

  it("toggles dark class and updates localStorage on default key press", () => {
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
    expect(localStorage.getItem("theme")).toBe(null);

    runThemeSwitchScript();
    dispatchKeydown(DEFAULT_TOGGLE_KEY);

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    expect(localStorage.getItem("theme")).toBe(DEFAULT_DARK);

    // press again to toggle off
    dispatchKeydown(DEFAULT_TOGGLE_KEY);
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
    expect(localStorage.getItem("theme")).toBe(DEFAULT_LIGHT);
  });

  it("does nothing when modifier keys are pressed", () => {
    runThemeSwitchScript();

    dispatchKeydown(DEFAULT_TOGGLE_KEY, { ctrlKey: true });
    dispatchKeydown(DEFAULT_TOGGLE_KEY, { altKey: true });
    dispatchKeydown(DEFAULT_TOGGLE_KEY, { metaKey: true });

    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
    expect(localStorage.getItem("theme")).toBe(null);
  });

  it("uses a custom toggle key", () => {
    runThemeSwitchScript({ toggleKey: "t" });

    // wrong key - no toggle
    dispatchKeydown(DEFAULT_TOGGLE_KEY);
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);

    // correct custom key - toggles
    dispatchKeydown("t");
    expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    expect(localStorage.getItem("theme")).toBe(DEFAULT_DARK);
  });
});
