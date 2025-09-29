(function __FUNCTION_NAME__() {
  document.addEventListener("keydown", (event) => {
    const activeElement = document.activeElement;

    const isInputFocused =
      activeElement && (["INPUT", "TEXTAREA", "SELECT"].includes(activeElement.tagName) || activeElement.isContentEditable);

    if (!isInputFocused && event.key === "__TOGGLE_KEY__" && !event.ctrlKey && !event.altKey && !event.metaKey) {
      event.preventDefault();

      const isDark = document.documentElement.classList.toggle("__DARK__");

      localStorage.theme = isDark ? "__DARK__" : "__LIGHT__";
    }
  });
})();
