(function __FUNCTION_NAME__() {
  const setColorScheme = (scheme) => {
    document.documentElement.classList.toggle(
      "__DARK__",
      scheme === "__DARK__",
    );
    document.documentElement.classList.toggle(
      "__LIGHT__",
      scheme === "__LIGHT__",
    );
  };

  if (localStorage.colorScheme === "__DARK__") {
    setColorScheme("__DARK__");
  } else if (localStorage.colorScheme === "__LIGHT__") {
    setColorScheme("__LIGHT__");
  } else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
    setColorScheme("__DARK__");
  } else if (window.matchMedia("(prefers-color-scheme: light)").matches) {
    setColorScheme("__LIGHT__");
  } else {
    setColorScheme(null);
  }
})();
