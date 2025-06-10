<?php
/**
 * Template part for displaying films
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTCOMPETITIONS</code>':'');

$csv_departures_url 	= get_post_meta(get_the_ID(), 'c_competition_departures', true);
$csv_results_brut_url 	= get_post_meta(get_the_ID(), 'c_competition_results_brut', true);
$csv_results_net_url 	= get_post_meta(get_the_ID(), 'c_competition_results_net', true);

function get_empty_departures_block() {
	return '<div class="container my-5 px-0 px-md-10 px-lg-15">
		<div class="p-5 text-center bg-body-tertiary rounded-4">
			<i class="bi bi-calendar-event mt-2 mb-0 h2 text-muted muted d-inline-block"></i>
			<h6 class="text-muted muted fw-semibold">' . __('No departures yet! Please come back', 'waff') . '</h6>
			<p class="col-lg-8 mx-auto text-muted muted fs-sm">' . __('There is no departure defined yet. Please contact us to get more informations.', 'waff') . '</p>
			<div class="d-inline-flex gap-2 mb-2">
				<button class="d-inline-flex align-items-center btn btn-secondary btn-lg px-4 rounded-pill" type="button">' . __('Sign-up', 'waff') . ' <i class="bi bi-arrow-right-short ms-2"></i></button> <button class="btn btn-outline-secondary btn-lg px-4 rounded-pill fw-bold" type="button">' . __('Contact-us', 'waff') . '</button>
			</div>
		</div>
	</div>';
}
function get_empty_results_block() {
	return '<div class="container my-5 px-0 px-md-10 px-lg-15">
		<div class="p-5 text-center bg-body-tertiary rounded-4">
			<i class="bi bi-list-columns-reverse mt-2 mb-0 h2 text-muted muted d-inline-block"></i>
			<h6 class="text-muted muted fw-semibold">' . __('No results yet! Please come back', 'waff') . '</h6>
			<p class="col-lg-8 mx-auto text-muted muted fs-sm">' . __('There is no departure defined yet. Please contact us to get more informations.', 'waff') . '</p>
			<div class="d-inline-flex gap-2 mb-2">
				<!--<button class="d-inline-flex align-items-center btn btn-secondary btn-lg px-4 rounded-pill" type="button">Contact us <i class="bi bi-arrow-right-short ms-2"></i></button>--> <button class="btn btn-outline-secondary btn-lg px-4 rounded-pill fw-bold" type="button">' . __('Contact-us', 'waff') . '</button>
			</div>
		</div>
	</div>';
}
function get_ended_signup_block() {
	return '<div class="container my-5 px-0 px-md-10 px-lg-15">
		<div class="p-5 text-center bg-body-tertiary rounded-4">
			<i class="bi bi-trophy mt-2 mb-0 h2 text-muted muted d-inline-block"></i>
			<h6 class="text-muted muted fw-semibold">' . __('It\'s done', 'waff') . '</h6>
			<p class="col-lg-8 mx-auto text-muted muted fs-sm">' . __('Les inscriptions pour cette compétition sont terminées. Please contact us to get more informations.', 'waff') . '</p>
			<div class="d-inline-flex gap-2 mb-2">
				<!--<button class="d-inline-flex align-items-center btn btn-secondary btn-lg px-4 rounded-pill" type="button">Contact us <i class="bi bi-arrow-right-short ms-2"></i></button>--> <button class="btn btn-outline-secondary btn-lg px-4 rounded-pill fw-bold" type="button">' . __('Contact-us', 'waff') . '</button>
			</div>
		</div>
	</div>';
}


?>
<!-- Begin: SINGLE COMPETITIONS -->
<article <?php post_class(); ?> id="competitions-<?php the_ID(); ?>">

	<?php
	if ( is_search() || ( get_theme_mod( 'blog_excerpt', false ) && is_home() ) ) {
		echo "SEARCH / EXTRAIT ( remettre le title ? )";
		the_excerpt();
	} else {
		ob_start();
		the_content();
		$content = ob_get_clean();
		$content = preg_replace('/<p([^>]+)?>/', '<p$1 class="lead">', $content, 1);
		echo $content;
	}
	wp_link_pages(
		array(
			'before' => '<nav class="post-nav-links" aria-label="' . esc_attr__( 'Page', 'go' ) . '"><span class="label">' . __( 'Pages:', 'go' ) . '</span>',
			'after'  => '</nav>',
		)
	);
	?>




	<!-- Departures -->
	<section id="departures">
		<h3><?= __('Departures', 'waff'); ?></h3>
		<?php		
		if ($csv_departures_url) {
			$csv = file_get_contents($csv_departures_url);
			if ($csv) {
				echo '<div id="departures-wrapper" data-fetch="'.$csv_departures_url.'"></div>
				<script>
					document.addEventListener("DOMContentLoaded", function () {
						loadDatatable("'. esc_url($csv_departures_url) .'", "departures-wrapper", "0,1,2,6,7,8");
					});
				</script>';
		} else {
				echo get_empty_departures_block();
			}
		} else {
			echo get_empty_departures_block();
		}
		?>
	</section>
	
	<!-- Results -->
	<section id="results">
		<h3><?= __('Results', 'waff'); ?></h3>
		<?php
			if ($csv_results_brut_url) {
				$csv = file_get_contents($csv_results_brut_url);
				if ($csv) {
					echo '<h6>Brut</h6>';
					echo '<div id="results-wrapper" data-fetch="'.$csv_results_brut_url.'"></div>
					<script>
						document.addEventListener("DOMContentLoaded", function () {
							loadDatatable("'. esc_url($csv_results_brut_url) .'", "results-wrapper", "6,5,7,8,13,14");
						});
					</script>';
				} else {
					echo get_empty_results_block();
				}
			} else {
				echo get_empty_results_block();
			}
		?>
		<?php
			if ($csv_results_net_url) {
				$csv = file_get_contents($csv_results_net_url);
				if ($csv) {
					echo '<h6>Net</h6>';
					echo '<div id="results-net-wrapper" data-fetch="'.$csv_results_net_url.'"></div>
					<script>
						document.addEventListener("DOMContentLoaded", function () {
							loadDatatable("'. esc_url($csv_results_net_url) .'", "results-net-wrapper", "6,5,7,8,13,14");
						});
					</script>';
				} else {
					echo get_empty_results_block();
				}
			} else {
				echo get_empty_results_block();
			}
		?>
	</section>

	<!-- Results -->
	<section id="sign-up">
		<h3><?= __('Sign-up', 'waff'); ?></h3>

		<div class="row">
			<?php
			if (empty($csv_results_brut_url) || empty($csv_results_net_url)) {
			?>
				<div class="col-3">
					<div class="d-flex flex-column align-items-center justify-content-center p-2 py-md-3 px-md-4 py-xl-4 px-xl-5 bg-body rounded-4 shadow">
						<div class="d-lg-flex d-inline-block align-items-center px-1 px-lg-0">
							<i class="bi bi-house-heart flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-2"></i>
							<div>
								<h6 class="fw-bold text-action-3 my-2 my-lg-3">Inscriptions ouvertes</h6>
								<p class="mb-0 small-lg">#TODO Lorem ipsum dolor sit amet</p>
							</div>
						</div>

						<div class="d-none d-lg-flex align-items-center justify-content-center py-2 py-md-4 py-xl-5">
							<span class="bullet bullet-action-2 ms-0"></span>
						</div>

						<div class="d-lg-flex d-inline-block align-items-center px-1 px-lg-0">
							<i class="bi bi-highlighter flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-2"></i>
							<div>
								<h6 class="fw-bold text-action-3 my-2 my-lg-3">Remplissez vos coordonnées</h6>
								<p class="mb-0 small-lg">#TODO Lorem ipsum dolor sit amet</p>
							</div>
						</div>

						<div class="d-none d-lg-flex align-items-center justify-content-center py-2 py-md-4 py-xl-5">
							<span class="bullet bullet-action-2 ms-0"></span>
						</div>

						<div class="d-lg-flex d-inline-block align-items-center">
							<i class="bi bi-cloud-arrow-down flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-2"></i>
							<div>
								<h6 class="fw-bold text-action-3">Attendez la confirmation</h6>
								<p class="mb-0 small-lg">#TODO Lorem ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-8 offset-1">
					<?php
					if (shortcode_exists('ws_form')) {
						add_filter('wsf_pre_render_2', function($form) {
							$field = wsf_form_get_field($form, 8);
							if ($field) $field->meta->default_value = get_the_title() . ' (#' . get_the_ID() . ')';
							return $form;
						});
						echo do_shortcode('[ws_form id="2"]');
					}
					?>
				</div>
			<?php
			} else {
				echo get_ended_signup_block();
			}
			?>
		</div>
	</section>

	<!-- Datatables -->
	<script>
		function loadDatatable(url, id, columnsToKeep = "4, 5, 6, 7, 8, 9, 10") {
			// console.log('loadDatatable', url, id, columnsToKeep);

			// Transform columnsToKeep into an array of integers if it's a string
			if (typeof columnsToKeep === "string") {
				columnsToKeep = columnsToKeep.split(',').map(s => parseInt(s.trim(), 10));
			}

			// Fetch the CSV file
			fetch(url)
				.then(response => response.text())
				.then(csvText => {
					const rows = csvText.split('\n').map(row => row.split(';'));
					const columns = rows[0];
					const data = rows.slice(1);

					// If columnsToKeep is empty, use all columns
					let filteredColumns, filteredData;
					if (!columnsToKeep || columnsToKeep.length === 0) {
						filteredColumns = columns;
						filteredData = data;
					} else {
						filteredColumns = columnsToKeep.map((index, i) => {
							if (i === 3) { // Assuming column 7 is the fourth column in the filtered array
								return {
									name: columns[index],
									formatter: (cell) => gridjs.html(`<b>${cell}</b>`)
								};
							}
							return columns[index];
						});
						filteredData = data.map(row => columnsToKeep.map(index => row[index]));
					}

								 console.log('loadDatatable :: data', filteredColumns, filteredData, columnsToKeep);


					new gridjs.Grid({
						search: true,
						pagination: {
							enabled: true,
							limit: 20
						},
						sort: true,
						resizable: true,
						columns: filteredColumns,
						data: filteredData,
						//
						className: {
							td: 'my-td-class',
							table: 'table table-hover --table-dark ht-tm-element',
							search: 'w-30',
							pagination: 'd-flex align-items-center justify-content-between',
							paginationButton: 'btn btn-sm btn-action-2 rounded-0',
							paginationButtonNext: 'rounded-end-2 ',
							paginationButtonCurrent: 'btn-action-3',
							paginationButtonPrev: 'rounded-start-2',
							paginationSummary: 'muted',
							error: 'color-danger',
						},
						//
						language: {
							search: {
								placeholder: 'Recherche...',
							},
							sort: {
								sortAsc: "Trier la colonne dans l'ordre croissant",
								sortDesc: "Trier la colonne dans l'ordre décroissant",
							},
							pagination: {
								previous: 'Précédent',
								next: 'Suivant',
								navigate: (page, pages) => `Page ${page} de ${pages}`,
								page: (page) => `Page ${page}`,
								showing: 'Affichage de',
								of: 'sur',
								to: 'à',
								results: 'résultats',
							},
							loading: 'Chargement...',
							noRecordsFound: 'Aucun résultat trouvé',
							error: 'Une erreur est survenue lors de la récupération des données',
						},
						//
					}).render(document.getElementById(id));
				});

		}
	</script>

	<?php
	// Meta si post ? 
	if ( is_singular() ) {
		Go\post_meta( get_the_ID(), 'single-bottom' );
	}
	?>

</article>
<!-- End: SINGLE COMPETITIONS -->