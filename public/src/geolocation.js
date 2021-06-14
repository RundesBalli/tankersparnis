"use strict";

let showPosition = function(position){
    document.getElementById("geo").value = position.coords.latitude + " " + position.coords.longitude;
    document.getElementById("geoButton").value = "Erfolgreich gefunden";
    document.getElementById("geoButton").disabled = true;
};

let getLocation = () => (navigator.geolocation && navigator.geolocation.getCurrentPosition(showPosition));

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("geoButton").addEventListener("click", getLocation);
});

/**
 * Danke an NullDev
 * https://github.com/NullDev
 */
