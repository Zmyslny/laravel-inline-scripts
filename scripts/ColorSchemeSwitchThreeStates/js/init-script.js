(function __FUNCTION_NAME__() {
  if (localStorage.theme === "__DARK__") {
    document.documentElement.classList.remove("__LIGHT__");
    document.documentElement.classList.add("__DARK__");
  } else if (localStorage.theme === "__LIGHT__") {
    document.documentElement.classList.add("__LIGHT__");
    document.documentElement.classList.remove("__DARK__");
  } else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
    document.documentElement.classList.remove("__LIGHT__");
    document.documentElement.classList.add("__DARK__");
  } else if (window.matchMedia("(prefers-color-scheme: light)").matches) {
    document.documentElement.classList.add("__LIGHT__");
    document.documentElement.classList.remove("__DARK__");
  }
})();
