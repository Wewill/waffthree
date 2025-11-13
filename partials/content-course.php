<?php
/**
 * Template part for displaying courses
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTCOURSE</code>':'');

$prefix = 'c_';

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

<!-- #medias -->
<!-- Gallery -->
<?php $medias_gallery = rwmb_meta( $prefix . 'medias_gallery', ['size' => 'large'] ); ?>
<?php if (!empty($medias_gallery)): ?>
<h6 class="subline --text-action-1 mt-5"><?= /*translators:Galerie */ __('Gallery', 'wa-rsfp'); ?></h6>
<!-- Begin: Gallery row -->
<div class="row row-cols-sm-2 row-cols-lg-4 mt-2 mb-6 g-4">
	<?php foreach ( $medias_gallery as $image ) : ?>
		<a class="col" href="javascript:;">
			<figure id="<?= $image['ID'] ?>">
				<picture class="lazy" data-fancybox="gallery" data-loader="pic" data-src="<?= $image['full_url'] ?>">
					<!--<data-src media="(min-width: 576px)" srcset="https://placehold.co/600x600/AA0000/808080?text=1200x1200" type="image/jpeg"></data-src> -->
					<data-src media="(min-width: 150px)" srcset="<?= $image['sizes']['page-featured-image-s']['url']; ?>" type="image/jpeg"></data-src>
					<data-img src="<?= $image['url']; ?>" alt="<?= esc_html($image['alt']); ?>" class="img-fluid rounded-4 --h-300-px fit-image w-100 img-transition-scale" style="" title="<?= $image['title']; ?>"></data-img>
				</picture>
				<?php if ( $image['alt'] || $image['description'] ) : ?>
				<figcaption><strong>© <?= esc_html($image['alt']); ?></strong> <?= esc_html($image['description']); ?></figcaption>
				<?php endif; /* If captions */ ?>
			</figure>
		</a>
	<?php endforeach ?>
</div>
<!-- End: Gallery row -->
<?php endif; ?>

<!-- Videos -->
<?php $d_medias_videos 					= rwmb_meta( $prefix . 'medias_video', array(), $post->ID); ?>
<?php $d_medias_video_links 			= rwmb_meta( $prefix . 'medias_video_link', array(), $post->ID); ?>
<?php if (!empty($d_medias_videos) || !empty($d_medias_video_links)): ?>
<h6 class="subline --text-action-1 mt-5"><?= /*translators:Vidéo */ __('Video', 'wa-rsfp'); ?></h6>
<!-- Begin: Video row -->
<div class="row row-cols-sm-2 row-cols-lg-4 mt-2 mb-6 g-4">
	<?php foreach ( $d_medias_videos as $d_medias_video ) : ?>
		<a class="col" href="javascript:;">
			<figure class="wp-block-video position-relative d-flex flex-center" id="<?= $d_medias_video['ID'] ?>" data-fancybox="gallery" data-loader="pic" data-src="<?= $d_medias_video['src'] ?>">
				<img src="https://placehold.co/600x600" class="img-fluid rounded-4 fit-image w-100"/>
				<video class="position-absolute top-0 start-0 h-100 img-fluid rounded-4 h-300-px fit-image w-100 img-transition-scale" autoplay loop muted playsinline src="<?= $d_medias_video['src']; ?>"><!-- poster="<?= $d_medias_video['image']['src']; ?>" --></video>
				<?php if ( $d_medias_video['alt'] || $d_medias_video['description'] ) : ?>
				<figcaption><strong>© <?= esc_html($d_medias_video['alt']); ?></strong> <?= esc_html($d_medias_video['description']); ?></figcaption>
				<?php endif; /* If captions */ ?>
			</figure>
		</a>
	<?php endforeach ?>
	<?php foreach ( $d_medias_video_links as $d_medias_video_link ) : ?>
		<a class="col" href="javascript:;">
			<figure class="wp-block-video position-relative d-flex flex-center" id="<?= $d_medias_video['ID'] ?>" data-fancybox="gallery" data-loader="pic" data-src="<?= $d_medias_video_link ?>">
				<img src="https://placehold.co/600x600" class="img-fluid rounded-4 fit-image w-100"/>
			</figure>
		</a>
	<?php endforeach ?>
</div>
<!-- End: Video row -->
<?php endif; ?>


<!-- Files -->
<?php $d_medias_files 					= rwmb_meta( $prefix . 'medias_files', array(), $post->ID); ?>
<?php if (!empty($d_medias_files)): ?>
<h6 class="subline --text-action-1 mt-5"><?= /*translators:Files */ __('Files', 'wa-rsfp'); ?></h6>
<!-- Begin: File -->
<div class="row row-cols-sm-2 row-cols-lg-3 mt-2 mb-6 g-4">
	<?php foreach ( $d_medias_files as $d_medias_file ) : ?>
		<a class="col mx-3 rounded-4 bg-action-3 w-100-px h-100-px d-flex flex-center" href="<?= esc_html($d_medias_file['url']); ?>" target="_blank">
			<i class="bi bi-file-earmark-arrow-down h2 m-0"></i>
			<?php if ( $d_medias_file['alt'] || $d_medias_file['description'] ) : ?>
				<figcaption><strong>© <?= esc_html($d_medias_file['alt']); ?></strong> <?= esc_html($d_medias_file['description']); ?></figcaption>
			<?php endif; /* If captions */ ?>
		</a>
		<!-- <?= esc_html($d_medias_file['name']); ?> -->
	<?php endforeach ?>
</div>
<!-- End: File -->
<?php endif; ?>


<!-- #navigation -->
<?php if ( is_a( $previous_post, 'WP_Post' ) || is_a( $next_post, 'WP_Post' ) ) {  ?>
	<section id="navigation" class="mt-3 mb-3 mt-lg-10 mb-lg-10 contrast--light">
		<div class="container-fluid g-0 p-0">
			<div class="row row-cols-1 row-cols-md-3 mt-4 mb-4">

				<?php if ( is_a( $previous_post, 'WP_Post' ) ) : ?>
				<div class="col d-flex align-items-center">
					<a href="<?= esc_url(get_permalink($previous_post)) ?>"><i class="bi bi-arrow-left-short h1 text-transparent-action-2 p-4 mt-4"></i></a>
					<div class="flex-fill pe-7 pe-md-0">
						<h6 class="d-none d-md-block subline text-transparent-action-2 text-start"><?= __('Previous course', 'wa-golfs'); ?></h6>

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
						<h6 class="d-none d-md-block subline text-transparent-action-2 text-end"><?= __('Next course', 'wa-golfs'); ?></h6>

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