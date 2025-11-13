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

// if ( get_post_type(get_the_ID()) === 'competitions' || get_post_type(get_the_ID()) === 'course' )
	$excerpt_atts['article_class'] .= ' border-0 p-2 m-0';

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTEXCERPT</code>':'');
?>

<article <?php post_class($excerpt_atts['article_class']);?> id="post-<?php the_ID();?>">

	<?php if ( is_singular() && has_post_thumbnail() ) : ?>
		<div class="post__thumbnail">
			<a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>">
				<?php the_post_thumbnail();?>
			</a>
		</div>
	<?php endif;?>

	<!--<header class="entry-header m-auto px">-->
		<?php
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', esc_html_x( 'Featured', 'post', 'waff' ) );
		}

		if ( is_singular() ) :
			the_title( '<h1 class="post__title entry-title m-0">', '</h1>' );
		else :
			if ( get_post_type(get_the_ID()) === 'film' ) : 
				print('<div class="card overflow-hidden rounded-2 bg-color-light border-0 h-100 p-4 --mb-4">');
				printf( '<h6 class="mb-0 muted"><i class="bi bi-film"></i> %s</h6> ', esc_html_x( 'Film', 'post', 'waff' ) );
				$film_french_title 	= get_post_meta( get_the_ID(), 'wpcf-f-french-operating-title', true );
				$film_length 		= get_post_meta( get_the_ID(), 'wpcf-f-movie-length', true );
				if ( $film_french_title != "" ) {
					the_title( 
						sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">%s</a> <span class="length light">%s\'</span> <span class="subline-4 text-muted mb-1">', esc_url(get_permalink()), $film_french_title, $film_length ), 
						'</span></h3>'
					);
				} else {
					the_title( 
						sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), 
						sprintf('</a> <span class="length light">%s\'</span></h3>', $film_length) 
					);
				}
				// Metas 
				print( '<div>' . WaffTwo\waff_post_meta( get_the_ID(), 'top', true ) . '</div>' );
				print('<br/>');

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
				print('<!-- </div> -->');
			elseif ( get_post_type(get_the_ID()) === 'jury' ) : 
				// printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Jury', 'post', 'waff' ) );
				// the_title( sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
				// the_excerpt();
				printf('<div class="card overflow-hidden rounded-2 bg-light-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					sprintf( '<h6 class="mb-2 muted subline"><i class="bi bi-award-fill"></i> %s</h6> ', esc_html_x( 'Jury', 'post', 'waff' ) ),
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
			elseif ( get_post_type(get_the_ID()) === 'farm' ) : 
				// printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Farm', 'post', 'go' ) );
				// the_title( sprintf( 'TODOFARM# <h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
				// the_excerpt();
				printf('<div class="card overflow-hidden rounded-2 bg-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					sprintf( '<h6 class="mb-2 muted subline">%s</h6>', esc_html_x( 'Farm', 'post', 'waff' ) ),
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
			elseif ( get_post_type(get_the_ID()) === 'structure' ) : 
				// printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Structure', 'post', 'go' ) );
				// the_title( sprintf( 'TODOSTRUCTURE# <h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>' );
				// the_excerpt();
				printf('<div class="card overflow-hidden rounded-2 bg-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					sprintf( '<h6 class="mb-2 muted subline">%s</h6>', esc_html_x( 'Structure', 'post', 'waff' ) ),
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
			elseif ( get_post_type(get_the_ID()) === 'operation' ) :
				printf( '<h6 class="mb-0 muted">%s</h6>', esc_html_x( 'Operation', 'post', 'waff' ) );
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
						<!-- </div> -->', 
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
				
				$time_diff = human_time_diff(get_post_time('U'), current_time('timestamp'));
				$d_last_updated = sprintf(__('Last update %s ago', 'waff'), $time_diff);

				printf('<div class="card my-2 border-0">
						<div class="row g-0 align-items-start">
							<div class="col-2 col-md-3 order-first">
								%s
							</div>
							<div class="col-10 col-md-9">
								<div class="card-body pt-0 pb-0">', 
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
						<!-- </div> -->', 
					the_title( sprintf( '<h5 class="post__title entry-title card-title mt-2"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h5>', false),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$d_general_introduction,
						15,
						' &hellip;'
					),
					$d_last_updated
				);
			elseif ( get_post_type(get_the_ID()) === 'competitions' ) : 
				// Use constants defined by plugin wa-golfs
				$stateColors = STATE_COLORS;
				$stateLabels = STATE_LABELS;
				
				$c_introduction 			= get_post_meta( get_the_ID(), 'c_introduction', true );
				$c_media_url 				= get_the_post_thumbnail_url( get_the_ID(), 'medium' );
				$c_media_thumbnail_url		= get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
				$c_image = $c_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$c_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px w-150-px h-auto"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';

				$time_diff = human_time_diff(get_post_time('U'), current_time('timestamp'));
				$c_last_updated = sprintf(__('Last update %s ago', 'waff'), $time_diff);

				$c_date 					= get_post_meta( get_the_ID(), 'c_date', true );
				$c_state 					= get_post_meta( get_the_ID(), 'c_state', true );

				$c_competition_departures 	= get_post_meta( get_the_ID(), 'c_competition_departures', true );
				$c_competition_results_brut = get_post_meta( get_the_ID(), 'c_competition_results_brut', true );
				$c_competition_results_net 	= get_post_meta( get_the_ID(), 'c_competition_results_net', true );

				$competition_date = get_post_meta(get_the_ID(), 'c_date', true); 
				$competition_date_string = wp_kses(
					sprintf(
						'<time datetime="%1$s">%2$s</time>',
						esc_attr($competition_date),
						sprintf(
							__('<strong>Le %1$s</strong>, à %2$s', 'waff'),
							date_i18n(get_option('date_format'), strtotime($competition_date)),
							date_i18n(get_option('time_format'), strtotime($competition_date))
						)
					),
					array_merge(
						wp_kses_allowed_html('post'),
						array(
							'time' => array(
								'datetime' => true,
							),
						)
					)
				);

				printf('<div class="card border-0 p-4 h-100" style="background-color:var(--waff-action-3-lighten-3);">
						<div class="d-flex g-0 align-items-center">
							<div class="w-150-px order-first">
								%s
							</div>
							<div class="w-100">
								<div class="card-body">', 
					$c_image
				);
				WaffTwo\waff_entry_meta_header();
				printf('
									%s
									<span class="fs-xs">%s %s %s</span>
									%s
									%s
									<p class="card-text fs-sm mb-0">%s</p>
									<p class="card-text --mt-n2 mt-3"><small class="text-body-secondary">%s</small></p>
								</div>
							</div>
						</div>
						<!-- </div> -->', 
					sprintf( '<h6 class="mb-2 muted subline text-action-3">%s</h6>', esc_html_x( 'Competitions', 'post', 'waff' ) ),
					sprintf( '<span class="state-label" style="color:%s;"><span class="dot" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%%; vertical-align: 2px; margin-left: 2px; background-color:%s;"></span> %s</span>',
						esc_attr( $stateColors[$c_state]['textColor'] ),
						esc_attr( $stateColors[$c_state]['textColor'] ),
						esc_html( $stateLabels[$c_state]['label'] )
					),
					$c_competition_departures?'<i class="bi bi-person-lines-fill ms-1"></i> Départs':'',
					$c_competition_results_brut || $c_competition_results_net? '<i class="bi bi-check-circle-fill ms-1"></i> Résultats':'',
					the_title( sprintf( '<h4 class="post__title entry-title m-0 lh-1 mt-2 mb-1 fw-bold" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h4>', false),
					sprintf( '<p class="competition-date --muted mb-0"><i class="bi bi-calendar-event"></i> %s</p>', $competition_date_string),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$c_introduction,
						15,
						' &hellip;'
					),
					$c_last_updated
				);

			elseif ( get_post_type(get_the_ID()) === 'course' ) : 

				$c_media_url 				= get_the_post_thumbnail_url( get_the_ID(), 'large' );
				// $c_media_thumbnail_url		= get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
				// $c_image = $c_media_thumbnail_url ? '<div class="d-flex flex-center rounded-4 bg-color-layout overflow-hidden"><img decoding="async" src="'.$c_media_thumbnail_url.'" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px"></div>' : '<div class="d-flex flex-center rounded-4 bg-color-layout"><img decoding="async" src="https://placehold.co/300x300/white/white" class="img-fluid fit-image rounded-4 img-transition-scale --h-100-px --w-100-px op-0"><i class="position-absolute bi bi-image text-action-3"></i></div>';

				$time_diff = human_time_diff(get_post_time('U'), current_time('timestamp'));
				$c_last_updated = sprintf(__('Last update %s ago', 'waff'), $time_diff);

				$c_number_of_strokes = get_post_meta( get_the_ID(), 'c_number_of_strokes', true );
				$c_handicap = get_post_meta( get_the_ID(), 'c_handicap', true );
				$c_green = get_post_meta( get_the_ID(), 'c_green', true );
				$c_altitude = get_post_meta( get_the_ID(), 'c_altitude', true );
				$content = get_the_content();
				$content = wp_trim_words( $content, 10, '...' ); // Limit content to 200 characters


				printf('<div class="col mb-4 bg-color-layout-2 p-4 rounded-2 h-100">
							<div class="card c-card overflow-hidden rounded-4 shadow-lg border-0 mb-4 ---- bg-cover bg-position-center-center min-h-400-px" style="background-image: url(\'%s\');">
								<div class="d-flex flex-column justify-content-between h-100 p-4 pb-3 text-white text-shadow-1">
									<div class="d-flex justify-content-between p-4 rounded-4 fw-bold framefilter">
										%s
										%s
										%s
										%s
									</div>
								</div>
								<a href="%s" class="stretched-link"></a>
							</div>', 
						$c_media_url,
						( $c_number_of_strokes ) ? sprintf( '<p class="mb-0">Par | %s</p>', esc_html( $c_number_of_strokes )) : '',
						( $c_handicap ) ? sprintf( '<p class="mb-0">Handicap | %s</p>', esc_html( $c_handicap )) : '',
						( $c_green ) ? sprintf( '<p class="mb-0">Green | %s</p>', esc_html( $c_green )) : '',
						( $c_altitude ) ? sprintf( '<p class="mb-0">Altitude | %s</p>', esc_html( $c_altitude )) : '',
						esc_url(get_permalink()),
				);
				WaffTwo\waff_entry_meta_header();
				printf('
							%s
							%s
							<p class="card-text fs-sm mb-0">%s</p>
							<p class="card-text --mt-n2"><small class="text-body-secondary">%s</small></p>
						<!-- </div> -->', 
					sprintf( '<h6 class="mb-2 muted subline">%s</h6>', esc_html_x( 'Course', 'post', 'waff' ) ),
					the_title( sprintf( '<h4 class="post__title entry-title m-0 lh-1 mb-2 text-dark fw-normal mb-3" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h4>', false ),
					wp_trim_words(
						get_the_excerpt() != ''?get_the_excerpt():$content,
						15,
						' &hellip;'
					),
					$c_last_updated
				);
			elseif ( get_post_type(get_the_ID()) === 'testimony' ) : 

				printf('<div class="card overflow-hidden rounded-2 bg-action-1 border-0 h-100 p-4 --mb-4">
					%s
					<div class="text-dark default subline-3 lh-base h2">« %s »</div>
					<!-- </div> -->',
					sprintf( '<h6 class="mb-2 muted subline text-black">%s</h6>', esc_html_x( 'Testimony', 'post', 'waff' ) ),
					// the_title( sprintf( '<h2 class="post__title entry-title m-0 lh-1 mb-2" style="margin-left: -2px !important;"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h2>', false),
					wp_trim_words(
						get_the_excerpt(),
						15,
						' &hellip;'
					),

				);
			// Standard post		
			elseif ( get_post_type(get_the_ID()) === 'page' ) :
				printf('<div class="card overflow-hidden rounded-2 bg-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					sprintf( '<h6 class="mb-2 muted subline">%s</h6>', esc_html_x( 'Page', 'post', 'waff' ) ),
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
			elseif ( get_post_type(get_the_ID()) === 'post' ) :
				printf('<div class="card overflow-hidden rounded-2 bg-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					sprintf( '<h6 class="mb-2 muted subline">%s</h6>', esc_html_x( 'Post', 'post', 'waff' ) ),
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
			// Default
			else :
				printf('<div class="card overflow-hidden rounded-2 bg-color-layout border-0 h-100 p-4 --mb-4" %s>
						%s
						%s
						%s
					<!-- </div> -->',
					$excerpt_atts['post_color_class'],
					the_title( sprintf( '<h3 class="post__title entry-title m-0 lh-1 mb-4"><a href="%s" rel="bookmark">', esc_url(get_permalink()) ), '</a></h3>', false),
					WaffTwo\waff_post_meta( get_the_ID(), 'top', true ),
					get_the_excerpt()
				);
				// //DEBUG
				// echo ((true === WAFF_DEBUG)?'<code> ##META'.is_singular().'</code>':'');
				// WaffTwo\waff_post_meta( get_the_ID(), 'top' );
			endif;
			print('</div>');
		endif;
		?>
	<!-- </header> -->

</article>
