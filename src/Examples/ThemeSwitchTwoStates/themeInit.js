(function __FUNCTION_NAME__() {
    if (localStorage.theme === "__DARK__") {
        document.documentElement.classList.add("__DARK__");
    } else if (localStorage.theme === "__LIGHT__") {
        document.documentElement.classList.add("__LIGHT__");
    } else if (window.matchMedia("(prefers-color-scheme: __DARK__)").matches) {
        document.documentElement.classList.add("__DARK__");
    }
})();
