jQuery(document).ready(function() {
	var programmationModals = document.querySelectorAll('[aria-controls="programmationModal"]');
	programmationModals.forEach(function(programmationModal) {
		programmationModal.addEventListener('click', function (event) {
			var programmationModalAjax = document.querySelector('#programmationModalAjax');	
			if (programmationModalAjax) {
			    const data = {
				'action': 'widget_programmation_ajax_html',
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
				// programmationModalAjax.parentNode.insertBefore(programmationModalAjax.querySelector(':scope > div'), programmationModalAjax);
				// programmationModalAjax.remove();
				const modal = bootstrap.Modal.getOrCreateInstance(programmationModal);
				modal.handleUpdate();
				// modal.dispose(); 
				// modal.show(); 
			    });
			}
		});
	});
});
