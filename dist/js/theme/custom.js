"use strict"; // Apply strict mode to the entire script

/**
 * Theme javascript functions file.
 */

document.addEventListener("DOMContentLoaded", function () {
  "use strict";

  var html = document.documentElement,
    nightToggle = document.getElementById("night-mode-toggle"),
    nightActive = "night-mode";

  /* Night Mode */
  if (nightToggle) {
    nightToggle.addEventListener("click", function () {
      if (html.classList.contains(nightActive)) {
        html.classList.remove(nightActive);
        localStorage.setItem("night-mode", "false");
      } else {
        html.classList.add(nightActive);
        localStorage.setItem("night-mode", "true");
      }
    });
  } else {
    console.info("Night mode toggle button not found.");
  }
});
