(function __FUNCTION_NAME__() {
  window.inlineScripts = window.inlineScripts || {};

  window.inlineScripts.switchColorScheme = function() {
    if (document.documentElement.classList.contains('__DARK__')) {
      document.documentElement.classList.remove('__DARK__');
      document.documentElement.classList.add('__LIGHT__');

      localStorage.theme = '__LIGHT__';
    } else if (document.documentElement.classList.contains('__LIGHT__')) {
      document.documentElement.classList.remove('__LIGHT__');

      localStorage.removeItem('theme');
    } else {
      document.documentElement.classList.add('__DARK__');

      localStorage.theme = '__DARK__';
    }
  };

  document.addEventListener("keydown", (event) => {
    const activeElement = document.activeElement;

    const isInputFocused =
      activeElement && (["INPUT", "TEXTAREA", "SELECT"].includes(activeElement.tagName) || activeElement.contentEditable === "true");

    if (!isInputFocused && event.key === "__TOGGLE_KEY__" && !event.ctrlKey && !event.altKey && !event.metaKey) {
      event.preventDefault();

      window.inlineScripts.switchColorScheme();
    }
  });
})();
