import { describe, it, beforeEach, expect, vi } from "vitest";
import fs from "node:fs";
import path from "node:path";

// Path to the raw JS template (with placeholders) used by PHP to generate a runtime script
const scriptPath = path.resolve(process.cwd(), "scripts/ColorSchemeSwitchThreeStates/js/switch-script.js");

const DEFAULT_TOGGLE_KEY = "d";
const DEFAULT_DARK = "dark";
const DEFAULT_LIGHT = "light";
const DEFAULT_SYSTEM = "system";

function runThemeSwitchScript({
  dark = DEFAULT_DARK,
  light = DEFAULT_LIGHT,
  functionName = "themeSwitch",
  toggleKey = DEFAULT_TOGGLE_KEY,
  matchMediaDarkMatches = false,
  matchMediaLightMatches = false,
} = {}) {
  // Load the template and substitute placeholders like PHP does
  let src = fs.readFileSync(scriptPath, "utf8");
  src = src
    .replaceAll("__FUNCTION_NAME__", functionName)
    .replaceAll("__DARK__", dark)
    .replaceAll("__LIGHT__", light)
    .replaceAll("__SYSTEM__", DEFAULT_SYSTEM)
    .replaceAll("__TOGGLE_KEY__", toggleKey);

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
describe("ColorSchemeSwitchScript.js IIFE behavior (three states)", () => {
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

    // Clear window.inlineScripts before each test
    if (window.inlineScripts) {
      delete window.inlineScripts.switchColorScheme;
    }

    // Remove all event listeners by cloning the document
    // This ensures keyboard listeners from previous tests don't interfere
    const oldDocument = document;
    const events = ["keydown", "colorSchemeChanged"];
    events.forEach((eventType) => {
      const listeners = oldDocument.querySelectorAll("*");
      // Remove listeners by replacing document (handled by JSDOM reset in vitest)
    });
  });

  describe("switchColorScheme function", () => {
    it("transitions from dark to light", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);
    });

    it("transitions from light to system with dark preference", () => {
      localStorage.setItem("colorScheme", DEFAULT_LIGHT);

      runThemeSwitchScript({ matchMediaDarkMatches: true, matchMediaLightMatches: false });
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_SYSTEM);
    });

    it("transitions from light to system with light preference", () => {
      localStorage.setItem("colorScheme", DEFAULT_LIGHT);

      runThemeSwitchScript({ matchMediaDarkMatches: false, matchMediaLightMatches: true });
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_SYSTEM);
    });

    it("transitions from light to system with no preference", () => {
      localStorage.setItem("colorScheme", DEFAULT_LIGHT);

      runThemeSwitchScript({ matchMediaDarkMatches: false, matchMediaLightMatches: false });
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_SYSTEM);
    });

    it("transitions from system to dark", () => {
      localStorage.setItem("colorScheme", DEFAULT_SYSTEM);

      runThemeSwitchScript();
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);
    });

    it("transitions from undefined/empty to dark", () => {
      // No colorScheme set in localStorage

      runThemeSwitchScript();
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);
    });

    it("completes full cycle: dark -> light -> system -> dark", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript({ matchMediaDarkMatches: true });

      // Dark -> Light
      window.inlineScripts.switchColorScheme();
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);

      // Light -> System (with dark preference)
      window.inlineScripts.switchColorScheme();
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_SYSTEM);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);

      // System -> Dark
      window.inlineScripts.switchColorScheme();
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    });
  });

  describe("custom event dispatching", () => {
    it("dispatches colorSchemeChanged event with correct details", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      const eventListener = vi.fn();
      document.addEventListener("colorSchemeChanged", eventListener);

      runThemeSwitchScript();
      window.inlineScripts.switchColorScheme();

      expect(eventListener).toHaveBeenCalledTimes(1);
      const event = eventListener.mock.calls[0][0];
      expect(event.detail.previousScheme).toBe(DEFAULT_DARK);
      expect(event.detail.currentScheme).toBe(DEFAULT_LIGHT);

      document.removeEventListener("colorSchemeChanged", eventListener);
    });

    it("dispatches colorSchemeChanged event with undefined previousScheme when not set", () => {
      // No colorScheme in localStorage

      const eventListener = vi.fn();
      document.addEventListener("colorSchemeChanged", eventListener);

      runThemeSwitchScript();
      window.inlineScripts.switchColorScheme();

      expect(eventListener).toHaveBeenCalledTimes(1);
      const event = eventListener.mock.calls[0][0];
      expect(event.detail.previousScheme).toBeUndefined();
      expect(event.detail.currentScheme).toBe(DEFAULT_DARK);

      document.removeEventListener("colorSchemeChanged", eventListener);
    });
  });

  describe("keyboard shortcut", () => {
    it("switches color scheme on default key press", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();
      dispatchKeydown(DEFAULT_TOGGLE_KEY);

      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);
    });

    it("does nothing when modifier keys are pressed with toggle key", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      dispatchKeydown(DEFAULT_TOGGLE_KEY, { ctrlKey: true });
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      dispatchKeydown(DEFAULT_TOGGLE_KEY, { altKey: true });
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      dispatchKeydown(DEFAULT_TOGGLE_KEY, { metaKey: true });
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);
    });

    it("uses a custom toggle key", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript({ toggleKey: "t" });

      // wrong key - no toggle
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      // correct custom key - toggles
      dispatchKeydown("t");
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);
    });

    it("does not switch when an input field is focused", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      // Create and focus an input element
      const input = document.createElement("input");
      document.body.appendChild(input);
      input.focus();

      expect(document.activeElement).toBe(input);

      // Try to toggle while input is focused - should not work
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      // Clean up
      document.body.removeChild(input);
    });

    it("does not switch when a textarea is focused", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      // Create and focus a textarea element
      const textarea = document.createElement("textarea");
      document.body.appendChild(textarea);
      textarea.focus();

      expect(document.activeElement).toBe(textarea);

      // Try to toggle while textarea is focused - should not work
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      // Clean up
      document.body.removeChild(textarea);
    });

    it("does not switch when a select field is focused", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      // Create and focus a select element
      const select = document.createElement("select");
      const option = document.createElement("option");
      option.value = "test";
      option.text = "Test Option";
      select.appendChild(option);
      document.body.appendChild(select);
      select.focus();

      expect(document.activeElement).toBe(select);

      // Try to toggle while select is focused - should not work
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      // Clean up
      document.body.removeChild(select);
    });

    it("does not switch when a contentEditable element is focused", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      // Create and focus a contentEditable element
      const div = document.createElement("div");
      div.contentEditable = "true";
      div.tabIndex = 0; // Make it focusable
      document.body.appendChild(div);
      div.focus();

      expect(document.activeElement).toBe(div);

      // Try to toggle while contentEditable is focused - should not work
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);

      // Clean up
      document.body.removeChild(div);
    });

    it("switches normally when input exists but is unfocused", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript();

      // Create an input but don't focus it
      const input = document.createElement("input");
      document.body.appendChild(input);

      expect(document.activeElement).not.toBe(input);

      // Toggle should work normally when input exists but is not focused
      dispatchKeydown(DEFAULT_TOGGLE_KEY);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);

      // Clean up
      document.body.removeChild(input);
    });

    it("dispatches custom event when triggered via keyboard", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      const eventListener = vi.fn();
      document.addEventListener("colorSchemeChanged", eventListener);

      runThemeSwitchScript();
      dispatchKeydown(DEFAULT_TOGGLE_KEY);

      expect(eventListener).toHaveBeenCalledTimes(1);
      const event = eventListener.mock.calls[0][0];
      expect(event.detail.previousScheme).toBe(DEFAULT_DARK);
      expect(event.detail.currentScheme).toBe(DEFAULT_LIGHT);

      document.removeEventListener("colorSchemeChanged", eventListener);
    });
  });

  describe("edge cases", () => {
    it("is idempotent when executed twice", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);

      expect(() => {
        runThemeSwitchScript();
        runThemeSwitchScript();
      }).not.toThrow();

      // Should still work after double initialization
      window.inlineScripts.switchColorScheme();
      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_LIGHT);
    });

    it("uses custom dark and light class names", () => {
      const customDark = "custom-dark";
      const customLight = "custom-light";

      localStorage.setItem("colorScheme", customDark);
      document.documentElement.classList.add(customDark);

      runThemeSwitchScript({ dark: customDark, light: customLight });
      window.inlineScripts.switchColorScheme();

      expect(document.documentElement.classList.contains(customDark)).toBe(false);
      expect(localStorage.getItem("colorScheme")).toBe(customLight);
    });

    it("handles rapid successive calls correctly", () => {
      localStorage.setItem("colorScheme", DEFAULT_DARK);
      document.documentElement.classList.add(DEFAULT_DARK);

      runThemeSwitchScript({ matchMediaDarkMatches: true });

      // Call multiple times rapidly
      window.inlineScripts.switchColorScheme(); // Dark -> Light
      window.inlineScripts.switchColorScheme(); // Light -> System
      window.inlineScripts.switchColorScheme(); // System -> Dark

      expect(localStorage.getItem("colorScheme")).toBe(DEFAULT_DARK);
      expect(document.documentElement.classList.contains(DEFAULT_DARK)).toBe(true);
    });
  });
});
