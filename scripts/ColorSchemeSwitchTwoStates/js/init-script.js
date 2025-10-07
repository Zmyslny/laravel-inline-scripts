(function __FUNCTION_NAME__() {
  if (localStorage.colorScheme === "__DARK__") {
    document.documentElement.classList.add("__DARK__");
  } else if (localStorage.colorScheme === "__LIGHT__") {
    // do nothing
  } else if (window.matchMedia("(prefers-color-scheme: __DARK__)").matches) {
    document.documentElement.classList.add("__DARK__");
  }
})();
