<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Theme\waff_get_theme_homeslide_content as waff_get_theme_homeslide_content;

function wa_keymessages_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// No data no render.
	// if ( empty( $attributes['data'] ) ) return;

	// Unique HTML ID if available.
	$id = '';
	if ( $attributes['name'] ) {
		$id = $attributes['name'] . '-';
	} elseif (  $attributes['data']['name'] ) {
		$id = $attributes['data']['name'] . '-';
	}
	$id .= ( $attributes['id'] && $attributes['id'] !== $attributes['name']) ? $attributes['id'] : wp_generate_uuid4();
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'keymessages mt-2 mb-4 contrast--light';
	$class = $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid --px-0">

				<?php if ( mb_get_block_field( 'waff_k_subtitle' ) || mb_get_block_field( 'waff_k_title' ) ) : ?>
					<hgroup class="">
						<?php if ( mb_get_block_field( 'waff_k_subtitle' ) ) : ?>
							<h6 class="subline" style="<?= !$is_preview ?: '--color:white;' ?>"><?= mb_get_block_field( 'waff_k_subtitle' ) ?></h6>
						<?php endif; ?>
						<?php if ( mb_get_block_field( 'waff_k_title' ) ) : ?>
							<h4 class="" style="<?= !$is_preview ?: '--color:white;' ?>"><?= mb_get_block_field( 'waff_k_title' ) ?></h4>
						<?php endif; ?>
					</hgroup>
				<?php endif; ?>

				<div class="col-12 d-flex justify-content-between" style="<?= $is_preview ? 'display:flex;justify-content:space-between;' : '' ?>">

					<?php if ( waff_get_theme_homeslide_content() ) :  ?>

						<?php foreach (waff_get_theme_homeslide_content() as $contents) : ?>
							<div data-aos="fade-up">
								<span class="bullet bullet-action-1 ms-0"></span>
								<h5 class="color-action-1 small-sm animated-title"><?= esc_html($contents[0]); ?><br/>
									<?= esc_html($contents[1]); ?></h5>
							</div>
						<?php endforeach; ?>

						<style type="text/css" scoped>
							.animated-title span {
								opacity: 0;
								animation: fadeInUp 0.5s forwards ease-out;
							}

							.animated-title:not(.animated) span {
								animation: none;
							}

							@keyframes fadeInUp {
								from {
								opacity: 0;
								}
								to {
								opacity: 1;
								}
							}
						</style>

						<script type="text/javascript">
							document.addEventListener("DOMContentLoaded", function () {
								const titles = document.querySelectorAll(".animated-title");

								// Fonction pour animer un titre
								function animateTitle(title) {
									if (title.classList.contains("animated")) return; // Evite de réanimer un titre déjà animé
									title.classList.add("animated");

									const fragments = document.createDocumentFragment();
									let letterIndex = 0;

									// Parcourt chaque nœud (y compris <br>) et anime chaque caractère
									Array.from(title.childNodes).forEach((node) => {
										if (node.nodeType === Node.TEXT_NODE) {
											node.textContent.split("").forEach((char) => {
												let span = document.createElement("span");
												span.textContent = char;
												span.style.animationDelay = `${letterIndex * 0.05}s`;
												fragments.appendChild(span);
												letterIndex++;
											});
										} else if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "BR") {
											fragments.appendChild(document.createElement("br"));
										}
									});

									title.innerHTML = "";
									title.appendChild(fragments);
								}

								// Vérifier si un parent contient la classe "aos-animate"
								function checkAOSAnimation() {
									titles.forEach((title) => {
										const parent = title.closest('[data-aos]'); // Trouve le parent qui contient l'attribut "data-aos"
										if (parent && parent.classList.contains("aos-animate")) {
											animateTitle(title); // Lance l'animation lorsque le parent a la classe "aos-animate"
										}
									});
								}

								// Vérifie au moment du chargement et au moment du scroll
								checkAOSAnimation();

								// Écouter l'événement AOS pour appliquer l'animation dès qu'un parent devient visible
								document.addEventListener("aos:in", checkAOSAnimation);
							});
						</script>

						<?php else :  ?>
							<div>
								<span class="bullet bullet-action-1 ms-0"></span>
								<h5 class="color-action-1 small-sm">Lorem ipsum<br/>
									dolor sit amet</h5>
							</div>
							<div>
								<span class="bullet bullet-action-1 ms-0"></span>
								<h5 class="color-action-1 small-sm">Lorem ipsum<br/>
									dolor sit amet</h5>
							</div>
							<div>
								<span class="bullet bullet-action-1 ms-0"></span>
								<h5 class="color-action-1 small-sm">Lorem ipsum<br/>
									dolor sit amet</h5>
							</div>
							<div>
								<span class="bullet bullet-action-1 ms-0"></span>
								<h5 class="color-action-1 small-sm">Lorem ipsum<br/>
									dolor sit amet</h5>
							</div>
							<div>
								&nbsp;
							</div>
					<?php endif;  ?>

				</div>
		</div>
	</section>
	<?php
}
