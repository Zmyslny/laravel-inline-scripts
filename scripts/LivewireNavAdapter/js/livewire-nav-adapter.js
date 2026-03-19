(function __FUNCTION_NAME__() {
  document.addEventListener("livewire:navigated", () => {
    if (localStorage.colorScheme === "__DARK__") {
      document.documentElement.classList.add("__DARK__");
    } else if (localStorage.colorScheme === "__LIGHT__") {
      document.documentElement.classList.remove("__DARK__");
    } else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
      document.documentElement.classList.add("__DARK__");
    } else {
      document.documentElement.classList.remove("__DARK__");
    }
  });
})();
