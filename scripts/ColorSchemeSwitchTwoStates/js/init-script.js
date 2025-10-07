(function __FUNCTION_NAME__() {
  const setColorScheme = (scheme) => {
    document.documentElement.classList.toggle("__DARK__", scheme === "__DARK__");
  };

  if (localStorage.colorScheme === "__DARK__") {
    setColorScheme("__DARK__");
  } else if (localStorage.colorScheme === "__LIGHT__") {
    setColorScheme("__LIGHT__");
  } else if (window.matchMedia("(prefers-color-scheme: __DARK__)").matches) {
    setColorScheme("__DARK__");
  }
})();
