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
  } else if (window.matchMedia("(prefers-color-scheme: __DARK__)").matches) {
    setColorScheme("__DARK__");
  }
})();
