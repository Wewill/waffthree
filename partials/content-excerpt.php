<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Waff
 */

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Core\waff_clean_alltags as waff_clean_alltags;
use function WaffTwo\Core\waff_trim as waff_trim;

if ( get_post_type(get_the_ID()) === 'post' ) {
	// Get post meta fields
	$excerpt_atts['post_color'] 			= rwmb_meta( '_waff_bg_color_metafield', $args, get_the_ID() );
	$excerpt_atts['post_color_class']		= ( $excerpt_atts['post_color'] )?'style="background-color:'.$excerpt_atts['post_color'].'!important;"':'';
	$excerpt_atts['article_class']			= ( $excerpt_atts['post_color'] )?'f-w-gutter p-gutter-sm-r p-gutter-sm-l has-color excerpt':'excerpt';	
}

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTEXCERPT</code>':'');
?>

<article <?php post_class($excerpt_atts['article_class']);?> id="post-<?php the_ID();?>" <?= $excerpt_atts['post_color_class'] ?>>

	<?php if ( is_singular() && has_post_thumbnail() ) : ?>
		<div class="post__thumbnail">
			<a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>">
				<?php the_post_thumbnail();?>
			</a>
		</div>
	<?php endif;?>

	<header class="entry-header m-auto px">
		<?php
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', esc_html_x( 'Featured', 'post', 'go' ) );
		}

		if ( is_singular() ) :
			the_title( '<h1 class="post__title entry-title m-0">', '</h1>' );
		else :
			if ( get_post_type(get_the_ID()) === 'film' ) : 
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Film', 'post', 'go' ) );
				$film_french_title 	= get_post_meta( get_the_ID(), 'wpcf-f-french-operating-title', true );
				$film_length 		= get_post_meta( get_the_ID(), 'wpcf-f-movie-length', true );
				if ( $film_french_title != "" ) {
					the_title( 
						sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">%s</a> <span class="length light">%s\'</span> <span class="subline-4 text-muted mb-1">', esc_url(get_permalink()), $film_french_title, $film_length ), 
						'</span></h2>'
					);
				} else {
					the_title( 
						sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), 
						sprintf('</a> <span class="length light">%s\'</span></h2>', $film_length) 
					);
				}
			elseif ( get_post_type(get_the_ID()) === 'jury' ) : 
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Jury', 'post', 'go' ) );
				the_title( sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
			elseif ( get_post_type(get_the_ID()) === 'farm' ) : 
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Farm', 'post', 'go' ) );
				the_title( sprintf( 'TODOFARM# <h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
			elseif ( get_post_type(get_the_ID()) === 'structure' ) : 
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Structure', 'post', 'go' ) );
				the_title( sprintf( 'TODOSTRUCTURE# <h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
			elseif ( get_post_type(get_the_ID()) === 'operation' ) :
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Operation', 'post', 'go' ) );
				// Get operation content
				$o_more_description 		= get_post_meta( get_the_ID(), 'o_more_description', true );
				$o_general_links 			= get_post_meta( get_the_ID(), 'o_general_links', true );
				$o_media_url 				= get_the_post_thumbnail_url( get_the_ID(), 'medium' );
				$o_media_thumbnail_url		= get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
				$o_image = $o_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$o_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';

				printf('<div class="card my-2 border-0">
						<div class="row g-0 align-items-center">
							<div class="col-md-3 order-first">
								%s
							</div>
							<div class="col-md-9">
								<div class="card-body">', 
					$o_image
				);
				WaffTwo\waff_entry_meta_header();
				printf('
									%s
									<p class="card-text fs-sm mb-0">%s</p>
									<p class="card-text --mt-n2"><small class="text-body-secondary">%s</small></p>
								</div>
							</div>
						</div>
						</div>', 
					the_title( sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h5>', false),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$o_more_description,
						15,
						' &hellip;'
					),
					WaffTwo\Core\waff_implode_nonempty($o_general_links, '<br/>')
				);

			elseif ( get_post_type(get_the_ID()) === 'directory' ) : 
				// Get directory content
				// $d_general_subtitle 		= get_post_meta( get_the_ID(), 'd_general_subtitle', true );
				$d_general_introduction 	= get_post_meta( get_the_ID(), 'd_general_introduction', true );
				// $d_identity_location 	= get_post_meta( get_the_ID(), 'd_identity_location', true );
				$d_media_url 				= get_the_post_thumbnail_url( get_the_ID(), 'medium' );
				$d_media_thumbnail_url		= get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
				$d_image = $d_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$d_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';
				
				$d_last_updated =  __('Last update') . " " . human_time_diff(get_post_time('U'), current_time('timestamp')) . " " . __('ago');


				printf('<div class="card my-2 border-0">
						<div class="row g-0 align-items-center">
							<div class="col-md-3 order-first">
								%s
							</div>
							<div class="col-md-9">
								<div class="card-body">', 
					$d_image
				);
				WaffTwo\waff_entry_meta_header();
				printf('
									%s
									<p class="card-text fs-sm mb-0">%s</p>
									<p class="card-text --mt-n2"><small class="text-body-secondary">%s</small></p>
								</div>
							</div>
						</div>
						</div>', 
					the_title( sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h5>', false),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$d_general_introduction,
						15,
						' &hellip;'
					),
					$d_last_updated
				);
			else :
				the_title( sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
			endif;
		endif;

		//DEBUG
		echo ((true === WAFF_DEBUG)?'<code> ##META'.is_singular().'</code>':'');

		WaffTwo\waff_post_meta( get_the_ID(), 'top' );
		?>
	</header>


	<?php if ( get_post_type(get_the_ID()) !== 'operation' && get_post_type(get_the_ID()) !== 'directory') : ?>
	<div class="<?php Go\content_wrapper_class( 'content-area__wrapper' );?>">
		<div class="content-area entry-content">
			<?php 
				if ( get_post_type(get_the_ID()) === 'film' ) { 
					$film_punchline_french 			= get_post_meta( get_the_ID(), 'wpcf-f-punchline-french', true );
					$film_punchline_english 		= get_post_meta( get_the_ID(), 'wpcf-f-punchline-english', true );
					$film_short_synopsis_french 	= get_post_meta( get_the_ID(), 'wpcf-f-short-synopsis-french', true );
					$film_short_synopsis_english 	= get_post_meta( get_the_ID(), 'wpcf-f-short-synopsis-english', true );
					$film_synopsis_french 			= get_post_meta( get_the_ID(), 'wpcf-f-synopsis-french', true );
					$film_synopsis_english 			= get_post_meta( get_the_ID(), 'wpcf-f-synopsis-english', true );
					if ( $film_punchline_french !== '' ) {
						printf('<p class="--lead --light pt-0 pb-4 punchlines"><!-- French : f-punchline-french -->%s<!-- .sep -->	· <!-- English : f-punchline-english --><em class="italics">%s</em></p>',
							$film_punchline_french, $film_punchline_english
						);
					} else {
						printf('<p class="--lead --light pt-0 pb-4 punchlines"><!-- French : f-punchline-french -->%s<!-- .sep -->	· <!-- English : f-punchline-english --><em class="italics">%s</em></p>',
							waff_do_markdown(waff_trim(waff_clean_alltags( $film_short_synopsis_french !== '' ? $film_short_synopsis_french:$film_synopsis_french), 150)),
							waff_do_markdown(waff_trim(waff_clean_alltags( $film_short_synopsis_english !== '' ? $film_short_synopsis_english:$film_synopsis_english), 150)),
						);
					}
				} else {
					the_excerpt();
				}
			?>
		</div>
	</div>
	<?php endif;?>

</article>
