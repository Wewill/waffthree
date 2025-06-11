<?php
/**
 * Template part for displaying farms
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTFARM</code>':'');

// Get the previous post in the same category
$previous_post = get_adjacent_post(false, '', true);

// Get the next post in the same category
$next_post = get_adjacent_post(false, '', false);

?>
<!-- Begin: SINGLE COURSE-->
<article <?php post_class(); ?> id="course-<?php the_ID(); ?>">

	<?php
	if ( is_search() || ( get_theme_mod( 'blog_excerpt', false ) && is_home() ) ) {
		echo "SEARCH / EXTRAIT ( remettre le title ? )";
		the_excerpt();
	} else {
		the_content();
	}
	wp_link_pages(
		array(
			'before' => '<nav class="post-nav-links" aria-label="' . esc_attr__( 'Page', 'go' ) . '"><span class="label">' . __( 'Pages:', 'go' ) . '</span>',
			'after'  => '</nav>',
		)
	);
	?>

	<?php
	if ( is_singular() ) {
		Go\post_meta( get_the_ID(), 'single-bottom' );
	}
	?>

</article>
<!-- End: SINGLE COURSE-->

<!-- Belongs to -->
<?php

	if ( function_exists( 'get_belongs' ) ) {
		$post_id = get_the_ID();
		$datas = get_belongs('farm', $post_id);
		if ( $datas[$post_id] ) {
			foreach ( $datas[$post_id] as $directory_post ) {
				// $directory_post_obj = get_post( $directory_post['ID'] );

				// Get directory content
				$d_general_introduction 	= get_post_meta( $directory_post['ID'], 'd_general_introduction', true );
				$d_media_url 				= get_the_post_thumbnail_url( $directory_post['ID'], 'medium' );
				// $d_media_thumbnail_url		= get_the_post_thumbnail_url( $directory_post['ID'], 'thumbnail' );
				$d_image = $d_media_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$d_media_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';
				
				$time_diff = human_time_diff(get_post_time('U'), current_time('timestamp'));
				$d_last_updated = sprintf(__('Last update %s ago', 'waff'), $time_diff);

				printf('<div class="card my-2 border-0">
						<div class="row g-0 align-items-center">
							<div class="col-md-3 order-first">
								%s
							</div>
							<div class="col-md-9">
								<div class="card-body">', 
					$d_image
				);
				WaffTwo\waff_entry_meta_header($directory_post['ID']);
				printf('
									%s
									<p class="card-text fs-sm mb-0">%s</p>
									<p class="card-text --mt-n2"><small class="text-body-secondary">%s</small></p>
								</div>
							</div>
						</div>
						<!-- </div> -->', 
					sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" rel="bookmark">%s</a></h5>', esc_url(get_permalink($directory_post['ID'])), get_the_title($directory_post['ID'])),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$d_general_introduction,
						15,
						' &hellip;'
					),
					$d_last_updated
				);

			}
		}
	}
?>

<!-- #navigation -->
<?php if ( is_a( $previous_post, 'WP_Post' ) || is_a( $next_post, 'WP_Post' ) ) {  ?>
	<section id="navigation" class="mt-3 mb-3 mt-lg-10 mb-lg-10 contrast--light">
		<div class="container-fluid g-0 p-0">
			<div class="row row-cols-1 row-cols-md-3 mt-4 mb-4">

				<?php if ( is_a( $previous_post, 'WP_Post' ) ) : ?>
				<div class="col d-flex align-items-center">
					<a href="<?= esc_url(get_permalink($previous_post)) ?>"><i class="bi bi-arrow-left-short h1 text-transparent-action-2 p-4 mt-4"></i></a>
					<div class="flex-fill pe-7 pe-md-0">
						<h6 class="d-none d-md-block subline text-transparent-action-2 text-start"><?= __('Previous', 'waff'); ?></h6>

						<?php // Get directory card content
						$previous_post_media_url 				= get_the_post_thumbnail_url( $previous_post, 'medium' );
						$previous_post_media_thumbnail_url		= get_the_post_thumbnail_url( $previous_post, 'thumbnail' );
						$previous_post_image = $previous_post_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 --bg-color-layout overflow-hidden"><img decoding="async" src="'.$previous_post_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';
						// $previous_post_last_updated =  __('Last update') . " " . human_time_diff(get_post_time('U', $previous_post), current_time('timestamp')) . " " . __('ago');

						printf('<div class="card my-2 border-0">
								<div class="row g-0 align-items-center">
									<div class="col-3 order-first">
										%s
									</div>
									<div class="col-9">
										<div class="card-body">', 
							$previous_post_image
						);
						WaffTwo\waff_entry_meta_header($previous_post);
						printf('
											%s
											<p class="card-text fs-sm mb-0">%s</p>
										</div>
									</div>
								</div>
								</div>', 
							sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" class="stretched-link" rel="bookmark">%s</a></h5>', 
								esc_url(get_permalink($previous_post)), 
								get_the_title( $previous_post )),
							wp_trim_words(
								get_the_excerpt($previous_post) != ''?get_the_excerpt($previous_post):get_post_meta( $previous_post, 'd_general_introduction', true ),
								15,
								' &hellip;'
							),
							// $previous_post_last_updated
						);?>
					</div>
				</div>
				<?php endif; ?>

				<div class="col"></div>

				<?php if ( is_a( $next_post, 'WP_Post' ) ) : ?>
				<div class="col d-flex align-items-center">
					<div class="flex-fill ps-7 ps-md-0">
						<h6 class="d-none d-md-block subline text-transparent-action-2 text-end"><?= __('Next', 'waff'); ?></h6>

						<?php // Get directory card content
						$next_post_media_url 				= get_the_post_thumbnail_url( $next_post, 'medium' );
						$next_post_media_thumbnail_url		= get_the_post_thumbnail_url( $next_post, 'thumbnail' );
						$next_post_image = $next_post_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$next_post_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';
						// $next_post_last_updated =  __('Last update') . " " . human_time_diff(get_post_time('U', $next_post), current_time('timestamp')) . " " . __('ago');

						printf('<div class="card my-2 border-0">
								<div class="row g-0 align-items-center">
									<div class="col-3 order-first">
										%s
									</div>
									<div class="col-9">
										<div class="card-body">', 
							$next_post_image
						);
						WaffTwo\waff_entry_meta_header($next_post);
						printf('
											%s
											<p class="card-text fs-sm mb-0">%s</p>
										</div>
									</div>
								</div>
								</div>', 
							sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" class="stretched-link" rel="bookmark">%s</a></h5>', 
								esc_url(get_permalink($next_post)), 
								get_the_title( $next_post )),
							wp_trim_words(
								get_the_excerpt($next_post) != ''?get_the_excerpt($next_post):get_post_meta( $next_post, 'd_general_introduction', true ),
								15,
								' &hellip;'
							),
							// $next_post_last_updated
						);?>
					</div>
					<a href="<?= esc_url(get_permalink($next_post)) ?>"><i class="bi bi-arrow-right-short h1 text-transparent-action-2 p-4 mt-4"></i></a>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</section>
<?php } ?>