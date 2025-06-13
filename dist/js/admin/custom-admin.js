/**
 * Admin javascript functions file.
 */

// document.addEventListener("DOMContentLoaded", function () {
// 	"use strict";
// 	console.log('Ready');
// });

document.addEventListener("DOMContentLoaded", function () {
  const notices = document.querySelectorAll(".notice");

  notices.forEach(function (notice) {
    if (notice.textContent.includes("Meta Box")) {
      notice.style.display = "none";
    }
  });
});