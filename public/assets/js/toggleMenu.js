"use strict";

/**
 * Toggle Mobile menu
 * @returns {any}
 */
let toggleMenu = function(){
    const x = document.getElementById("navbar");
    x.classList.toggle("responsive");
};


document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("toggle").addEventListener("click", toggleMenu);
});

/**
 * Thanks to NullDev!
 * https://github.com/NullDev
 */
