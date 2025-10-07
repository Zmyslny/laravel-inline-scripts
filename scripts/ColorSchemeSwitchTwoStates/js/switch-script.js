(function __FUNCTION_NAME__() {
  window.inlineScripts = window.inlineScripts || {};

  window.inlineScripts.toggleColorScheme = function () {
    const isDark = document.documentElement.classList.toggle("__DARK__");

    localStorage.colorScheme = isDark ? "__DARK__" : "__LIGHT__";
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
