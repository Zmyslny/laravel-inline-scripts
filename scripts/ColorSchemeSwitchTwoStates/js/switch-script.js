(function __FUNCTION_NAME__() {
  window.inlineScripts = window.inlineScripts || {};

  const setColorScheme = (scheme) => {
    document.documentElement.classList.toggle("__DARK__", scheme === "__DARK__");
  };

  window.inlineScripts.toggleColorScheme = function () {
    const isDark = document.documentElement.classList.contains("__DARK__");

    if (isDark) {
      setColorScheme("__LIGHT__");
      localStorage.colorScheme = "__LIGHT__";
    } else {
      setColorScheme("__DARK__");
      localStorage.colorScheme = "__DARK__";
    }
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

      window.inlineScripts.toggleColorScheme();
    }
  });
})();
