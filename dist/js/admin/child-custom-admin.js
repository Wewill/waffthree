/**
 * Admin javascript functions file.
 */

"use strict";

/* Document Ready */
document.addEventListener("DOMContentLoaded", function () {
	// Hide custom page options fields (from meta box io)
	if (!document.body.classList.contains('role-administrator')) {
		var field = document.querySelector('#page-options .rwmb-meta-box #waff_page_advanced_class');
		if (field) {
			field.disabled = true;
			var closestField = field.closest('.rwmb-field');
			if (closestField) {
				closestField.style.opacity = '0.7';
			}
		}
	}
});

// Hide Meta Box notices
document.addEventListener("DOMContentLoaded", function () {
  const notices = document.querySelectorAll(".notice");

  notices.forEach(function (notice) {
    if (notice.textContent.includes("Meta Box")) {
      notice.style.display = "none";
    }
  });
});