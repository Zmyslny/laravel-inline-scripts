(function __FUNCTION_NAME__() {
  const setColorScheme = (scheme) => {
    document.documentElement.classList.toggle(
      "__DARK__",
      scheme === "__DARK__",
    );
  };

  const storedScheme = localStorage.getItem("colorScheme");

  if (storedScheme === "__DARK__") {
    setColorScheme("__DARK__");
  } else if (storedScheme === "__LIGHT__") {
    // do nothing, default is light
  } else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
    setColorScheme("__DARK__");
  } else if (window.matchMedia("(prefers-color-scheme: light)").matches) {
    // do nothing, default is light
  } else {
    setColorScheme(null);
  }
})();
