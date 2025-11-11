jQuery(document).ready(function() {
	var programmationModals = document.querySelectorAll('[aria-controls="programmationModal"]');
	programmationModals.forEach(function(programmationModal) {
		programmationModal.addEventListener('click', function (event) {
			var programmationModalAjax = document.querySelector('#programmationModalAjax');	
			if (programmationModalAjax) {

			    // const durationCache = 30 * 60 * 1000; // 30 minutes
				// // const durationCache = 24 * 60 * 60 * 1000; // 24 heures
			    // let programmationTimeout = localStorage.getItem('programmationTimeout');
			    // if (programmationTimeout) {
				//     if (programmationTimeout >= new Date().getTime())
				// 	    useCache = true;
			    // }
				// // useCache = false; // @wilhem si tu veux forcer à toujour charger
				let isModalFavorited = localStorage.getItem('programmation-modal-favorited') === 'true';
				if (isModalFavorited) {
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
						const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
						modal.handleUpdate();
					} else {
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
						});
					}
				} else {
					fetch('/wp-content/cache/programmation_ajax_cache.html', {})
					.then(response => response.text())
					.then(response => {
						programmationModalAjax.innerHTML = response;
						const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
						modal.handleUpdate();
//						localStorage.setItem('programmationFromCache', true);
//						localStorage.setItem('programmationFavorited', false);
					});
				}
			}
		});
	});
});