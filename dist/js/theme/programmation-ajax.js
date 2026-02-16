/**
 * Fonction pour recharger le contenu de la modal de programmation
 * Peut être appelée de n'importe où pour rafraîchir le contenu
 */
window.reloadProgrammationModalContent = function() {
	var programmationModalAjax = document.querySelector('#programmationModalAjax');
	if (!programmationModalAjax) {
		return;
	}

	// const durationCache = 30 * 60 * 1000; // 30 minutes
	// // const durationCache = 24 * 60 * 60 * 1000; // 24 heures
	// let programmationTimeout = localStorage.getItem('programmationTimeout');
	// if (programmationTimeout) {
	//     if (programmationTimeout >= new Date().getTime())
	// 	    useCache = true;
	// }
	// // useCache = false; // @wilhem si tu veux forcer à toujour charger

    // Access the user status from localized data
    let isUserOnline = programmationData.isUserOnline;
	let isModalFavorited = localStorage.getItem('programmation-modal-favorited') === 'true';
	console.log("isUserOnline::", isUserOnline);
	console.log("isModalFavorited::", isModalFavorited);
	if (isUserOnline && isModalFavorited) {
		var useCache = false;
		// const durationCache = 30 * 60 * 1000; // 30 minutes
		// // const durationCache = 24 * 60 * 60 * 1000; // 24 heures
		// let programmationTimeout = localStorage.getItem('programmationTimeout');
		// if (programmationTimeout) {
		//     if (programmationTimeout >= new Date().getTime())
		// 	    useCache = true;
		// }
		if (useCache) {
			programmationModalAjax.innerHTML = localStorage.getItem('programmationHtml');
		} else {
			// Vider le modal-body
			programmationModalAjax.innerHTML = '<div id="programmationModalAjax">\
				<div class="d-flex flex-center vh-70">\
					<div class="d-flex flex-column align-items-center">\
						<div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">\
							<span class="visually-hidden">Loading...</span>\
						</div>\
						<div class="subline-4 text-center d-block mt-4">Nous préparons votre grille...</div>\
					</div>\
				</div>\
			</div>';
			const data = {
				'action': 'widget_programmation_ajax_html',
				'noCache': true,
			};
			fetch('/wp-admin/admin-ajax.php', {
				method: 'POST',
				headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'Cache-Control': 'no-cache',
				},
				body: new URLSearchParams(data),
			})
			.then(response => response.text())
			.then(response => {
				programmationModalAjax.innerHTML = response;
				const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
				modal.handleUpdate();
//						localStorage.setItem('programmationFromCache', true);
//						localStorage.setItem('programmationFavorited', false);

				// Dispatch custom event to notify that AJAX HTML has been loaded
				// This allows the favorites plugin to sync UI state for logged-in users
				//jQuery(document).trigger('wacp:programmation-html-loaded');
			});
		}
	} else {
		fetch('/wp-content/uploads/programmation_ajax_cache.html', {})
		.then(response => response.text())
		.then(response => {
			programmationModalAjax.innerHTML = response;
			const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
			modal.handleUpdate();
//						localStorage.setItem('programmationFromCache', true);
//						localStorage.setItem('programmationFavorited', false);

			// Dispatch custom event to notify that HTML cache has been loaded
			// This allows the favorites plugin to sync UI state for logged-in users
			jQuery(document).trigger('wacp:programmation-html-loaded');
		});
	}
};

jQuery(document).ready(function() {
	var programmationModals = document.querySelectorAll('[aria-controls="programmationModal"]');
	programmationModals.forEach(function(programmationModal) {
		programmationModal.addEventListener('click', function () {
			var programmationModalAjax = document.querySelector('#programmationModalAjax');
			if (programmationModalAjax) {
				// Utiliser la fonction réutilisable
				window.reloadProgrammationModalContent();

				// Mettre à jour le modal Bootstrap
				const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
				modal.handleUpdate();
			}
		});
	});
});