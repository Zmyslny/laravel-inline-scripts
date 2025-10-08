(function __FUNCTION_NAME__() {
  window.inlineScripts = window.inlineScripts || {};

  const setColorScheme = (scheme) => {
    document.documentElement.classList.toggle(
      "__DARK__",
      scheme === "__DARK__",
    );
  };

  window.inlineScripts.switchColorScheme = function () {
    const previousScheme = localStorage.colorScheme;

    // Cycle: Dark -> Light -> System -> Dark -> ...
    if (localStorage.colorScheme === "__DARK__") {
      setColorScheme("__LIGHT__");
      localStorage.colorScheme = "__LIGHT__";
    } else if (localStorage.colorScheme === "__LIGHT__") {
      // System preference
      if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        setColorScheme("__DARK__");
      } else if (window.matchMedia("(prefers-color-scheme: light)").matches) {
        setColorScheme("__LIGHT__");
      }
      localStorage.colorScheme = "__SYSTEM__";
    } else {
      setColorScheme("__DARK__");
      localStorage.colorScheme = "__DARK__";
    }

    const event = new CustomEvent("colorSchemeChanged", {
      detail: {
        previousScheme: previousScheme,
        currentScheme: localStorage.colorScheme,
      },
    });

    document.dispatchEvent(event);
  };

  document.addEventListener("keydown", (event) => {
    const activeElement = document.activeElement;

    const isInputFocused =
      activeElement &&
      (["INPUT", "TEXTAREA", "SELECT"].includes(activeElement.tagName) ||
        activeElement.contentEditable === "true");

    if (
      !isInputFocused &&
      event.key === "__TOGGLE_KEY__" &&
      !event.ctrlKey &&
      !event.altKey &&
      !event.metaKey
    ) {
      event.preventDefault();

      window.inlineScripts.switchColorScheme();
    }
  });
})();
