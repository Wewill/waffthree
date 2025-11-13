<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Core\waff_get_image_id_by_url as waff_get_image_id_by_url;

function wa_awards_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	//print_r($attributes);
	//global $current_edition_id, $current_edition_films_are_online;

	// No taxonomy no render.
	if ( ! taxonomy_exists( 'award' ) ) {
		//if ( $is_preview ) {
			echo '<div class="alert alert-dismissible alert-danger fade show" role="alert"><strong>Heads up!</strong> The <strong>award</strong> taxonomy does not exist. Please check your configuration. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
		//}
		return;
	}

	// No data no render.
	if ( empty( $attributes['data'] ) ) return;

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
	$themeClass = 'awards mt-5 mt-sm-10 mb-5 mb-sm-10 contrast--light';
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

	// Get terms awards
	$master_awards = array();
	$master_awards_id = array();
	$awards = array();
	$awards_id = array();

	// Master awards
	$master_awards_args = array(
		'taxonomy' => 'award',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC',
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'wpcf-a-master',
				'compare' => '=',
				'value' => '1',
			),
			array(
				'key' => 'wpcf-a-hide-in-block',
				'compare' => '!=',
				'value' => '1',
			),
		),
	);
	$master_awards = get_terms( $master_awards_args );
	if ( ! empty( $master_awards ) && ! is_wp_error( $master_awards ) ) {
        $master_awards_id = wp_list_pluck( $master_awards, 'term_id' );
	}
	//echo "blocks.php:: master awards IDs"; print_r($master_awards_id);

	// Awards
	$awards_args = array(
		'taxonomy' => 'award',
		'posts_per_page' => -1,
		'orderby'  => array( 'term_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC',
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'wpcf-a-master',
				'compare' => '=',
				'value' => '0',
			),
			array(
				'key' => 'wpcf-a-hide-in-block',
				'compare' => '!=',
				'value' => '1',
			),
		),
	);
	$awards = get_terms( $awards_args );
	if ( ! empty( $awards ) && ! is_wp_error( $awards ) ) {
		$awards_id = wp_list_pluck( $awards, 'term_id' );
	}
	//echo "blocks.php:: awards IDs"; print_r($awards_id);

	// Get posts
	$master_awards_films = array();
	$awards_films = array();
	$edition = (int)$attributes['data']['waff_a_edition'];
	/*if ( !isset($edition) )
		echo esc_html__( 'Please choose an edition', 'waff' );*/
	$edition = ( isset($edition) && $edition != null && $edition != 0 )?$edition:$current_edition_id;

	// Master awards Films
	foreach( $master_awards_id as $a_id ) {
		$master_awards_films_args = array(
			'post_type' => 'film',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			// In edition
			'tax_query' => array(
				'relation' => 'AND',
				array (
					'taxonomy' => 'award',
					'field' => 'term_id',
					'terms' => $a_id, //array_values($mai)
				),
				array (
					'taxonomy' => 'edition',
					'field' => 'term_id',
					'terms' => array($edition),
				),
			),
			// Order by
			'orderby'  => array( 'menu_order' => 'DESC', 'date' => 'DESC' ),
		);
		// Filter to order by taxonomy
		//add_filter('posts_orderby', __NAMESPACE__ . "\\edit_posts_orderby_award" );
		//add_filter('posts_clauses', __NAMESPACE__ . "\\edit_posts_orderby_award_clauses", 10, 2);
		$master_awards_films[] = get_posts( $master_awards_films_args );
	}
	//remove_filter('posts_orderby',  __NAMESPACE__ . "\\edit_posts_orderby_award" );
	//echo "blocks.php:: Films IDs"; echo '<pre>'.print_r($master_awards_films, true).'</pre>';

	// Awards Films
	foreach( $awards_id as $a_id ) {
		$awards_films_args = array(
			'post_type' => 'film',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			// In edition
			'tax_query' => array(
				'relation' => 'AND',
				array (
					'taxonomy' => 'award',
					'field' => 'term_id',
					'terms' => $a_id, //array_values($awards_id),
				),
				array (
					'taxonomy' => 'edition',
					'field' => 'term_id',
					'terms' => array($edition),
				),
			),
			// Order by
			'orderby'  => array( 'term_order' => 'DESC', 'menu_order' => 'DESC', 'date' => 'DESC' ), //'term_taxonomy_id' => 'DESC',
		);
		// Filter to order by taxonomy
		//add_filter('posts_orderby', __NAMESPACE__ . "\\edit_posts_orderby_award" );
		$awards_films[] = get_posts( $awards_films_args );
	}
	//remove_filter('posts_orderby',  __NAMESPACE__ . "\\edit_posts_orderby_award" );
	//echo "blocks.php:: Films IDs"; echo '<pre>'.print_r($awards_films, true).'</pre>';

	$empty_awards = '';
	if (
		( empty( $master_awards_films ) || is_wp_error( $master_awards_films ) )
		&&
		( empty( $awards_films ) || is_wp_error( $awards_films ) )
	) {
		$empty_awards = esc_html__( 'Be patient ! Awards has not been published yet', 'waff' );
	}

	// Get edition year
	$edition_year 			= get_term_meta( $edition, 'wpcf-e-year', true );
	$edition_name 			= get_term( $edition )->name;

	// Get section by edition year
	$all_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC',
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition,
			),
		),
	);
	$the_edition_section = get_terms( $all_section_args );
	$terms_list = array();
	if ( ! empty( $the_edition_section ) ) :
		foreach( $the_edition_section as $term ) {
			$term_link = get_term_link($term);
			if ( is_wp_error( $term_link ) ) continue;

			$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
			$terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
				(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.';"':''),
				esc_url($term_link),
				esc_html__($term->name),
				esc_html__($term->name)
			);
		}
	endif;

	$display = mb_get_block_field( 'waff_a_display' );
	$display_master_awards 	= ( $display == 2 )?false:true;
	$display_awards 		= ( $display == 1 )?false:true;

	?>
	<?php /* #Awards */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<hgroup class="text-center">
				<h6 class="headline d-inline-block"><?= mb_get_block_field( 'waff_a_title' ) ?></h6>
				<h1 class="award-title mt-0 mb-0 display-1"><?= $edition_year; ?></h1>
				<?php
					if ( $terms_list ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				?>
				<h6 class="visually-hidden">Le palmarès de l'édition <?= $edition_name; ?> du Festival Internationnal du Film d'Amiens</h6>
			</hgroup>

			<?php if ( mb_get_block_field( 'waff_a_leadcontent' ) ) : ?>
			<p class="lead mt-2 mt-sm-6 text-center"><?= waff_do_markdown(mb_get_block_field( 'waff_a_leadcontent' )) ?></p>
			<?php endif; ?>

			<?php if ( mb_get_block_field( 'waff_a_content' ) ) : ?>
			<div class="mt-1 mt-sm-3 text-center w-75 m-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_a_content' )) ?></div>
			<?php endif; ?>

			<?php /* Empty */ ?>
			<?php if ( $empty_awards != '' ) : ?>
			<div class="alert text-center">
				<p><?= $empty_awards ?></p>
			</div>
			<?php endif; ?>

			<?php /* Master awards */ ?>
			<?php if ( ! empty( $master_awards_films ) && ! is_wp_error( $master_awards_films )	&& $display_master_awards == true ) : ?>
			<section class="text-center">
				<hr class="vertical-separator h-80-px mt-2 mb-2 bg-transparent" size></hr>
				<p class="headline d-inline-block mx-auto"><?= esc_html__( 'Master awards', 'waff' ); ?></p>
			</section>

			<?php print(wa_awards_get_films($master_awards_films, true)); ?>

			<?php endif; // END:: if Master awards ?>

			<?php /* Awards */ ?>
			<?php if ( ! empty( $awards_films ) && ! is_wp_error( $awards_films ) && $display_awards == true ) : ?>
			<section class="text-center">
				<hr class="vertical-separator h-80-px mt-2 mb-2 bg-transparent" size></hr>
				<p class="headline d-inline-block mx-auto d-none"><?= esc_html__( 'Awards', 'waff' ); ?></p>
			</section>

			<?php print(wa_awards_get_films($awards_films, false)); ?>

			<?php endif; // END:: if Awards ?>

			<?php /* More button */ ?>
			<?php if ( mb_get_block_field( 'waff_a_morelink' ) == 1 ) : ?>
			<div class="--d-grid --gap-2 mt-2 mt-sm-6 mb-2 mb-sm-6 text-center">
				<a class="btn btn-outline-dark mt-4" href="<?php echo esc_url( mb_get_block_field( 'waff_a_moreurl' )); ?>"><?php esc_html_e( 'All the awards', 'waff' ); ?></a>
			</div>
			<?php endif; ?>

		</div>
	</section>
	<?php /* END: #Awards */ ?>
	<?php
}

function wa_awards_get_films( $films, $master = true ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	$html = '<div class="row award-list ' . (($master == true)?'master-':'') . 'awards">';
	$counter=0;
	$idx=0;
	//echo"<pre>".print_r($films, true)."</pre>";

	// Count array films
	array_walk_recursive($films, function($value, $key) use (&$counter) {
		$counter++;
		//echo print_r($value,true) . " : " . $counter;
	 }, $counter);
	 //echo "counter : " . $counter;

	foreach($films as $_films) { // Foreach 1
		foreach($_films as $film) { // Foreach 2 Object/Array in array fix
			$idx++;
			$f_id						= $film->ID;
			$f_title 					= (( $film->post_title )?get_the_title($f_id):$film->post_title);
			$f_french_operating_title 	= get_post_meta( $f_id, 'wpcf-f-french-operating-title', true );
			$f_movie_length 			= get_post_meta( $f_id, 'wpcf-f-movie-length', true );
			$f_author 					= get_post_meta( $f_id, 'wpcf-f-author', true );
			$f_author 					= esc_html($f_author['firstname']) . ' <strong>' . esc_html($f_author['lastname']) . '</strong>';
			$f_director_contact 		= get_post_meta( $f_id, 'wpcf-c-e-director-contact', true );
			$f_color 					= get_post_meta( $f_id, 'film-color', true );

			$featured_img_caption = '';
			$featured_img_description = '';

			$f_image = '';
			// Featured image
			$featured_img_urls = array();
			if ( has_post_thumbnail($f_id) ) {
				$post_featured_sizes = array(
					'thumbnail',
					'post-featured-image-s',
					'post-featured-image-s-x2',
					'post-featured-image-m',
					'post-featured-image-m-x2',
				);
				$featured_img_id     		= get_post_thumbnail_id($f_id);
				$featured_img_url_full 		= get_the_post_thumbnail_url($f_id);
				foreach ($post_featured_sizes as $size) {
					$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
					$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full;
				}
				$alt = get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
				$featured_img_caption = wp_get_attachment_caption($featured_img_id); // ADD WIL
				$thumb_img = get_post( $featured_img_id ); // Get post by ID
				$featured_img_description =  $thumb_img->post_content; // Display Description
				// Render image
				$f_image = sprintf('<figure class="w-100 %s fit-image" %s>
						<picture class="lazy">
						<data-src media="(min-width: 399px)"
								srcset="%s 2x,
										%s" type="image/jpeg"></data-src>
						<data-src media="(min-width: 149px)"
								srcset="%s 2x,
										%s" type="image/jpeg"></data-src>
						<data-img src="%s" class="w-100 img-fluid %s fit-image" alt="%s"></data-img>
						</picture>
						%s
					</figure>',
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_description )?'title="'.esc_html($featured_img_description).'"':'title="'.esc_html($f_title).'"',
					$featured_img_urls['post-featured-image-s-x2'],
					$featured_img_urls['post-featured-image-s'],
					$featured_img_urls['post-featured-image-xs-x2'],
					$featured_img_urls['post-featured-image-xs'],
					$featured_img_urls['thumbnail'],
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_caption )?esc_html($featured_img_caption):esc_html($f_title),
					( $featured_img_caption || $featured_img_description )?'<figcaption><strong>© '. esc_html($featured_img_caption) .'</strong> '. esc_html($featured_img_description) .'</figcaption>':'',
				);
			}

			// Poster image
			$f_poster = '';
			$f_poster 								= get_post_meta( $f_id, 'wpcf-f-film-poster', true ); // Medium large
			if ( $master == true && $f_poster != '' ) {
				$f_poster 							= get_post_meta( $f_id, 'wpcf-f-film-poster', true ); // Medium large
				$f_poster_ID 						= waff_get_image_id_by_url($f_poster);
				$featured_img_caption 				= wp_get_attachment_caption($f_poster_ID); // ADD WIL
				$thumb_img 							= get_post($f_poster_ID); // Get post by ID
				$featured_img_description 			= $thumb_img->post_content; // Display Description
				if ( function_exists( 'types_render_field' ) ) {
					$f_poster 						= types_render_field( 'f-film-poster',
						array( 'item' => $f_id, 'size' => 'medium_large', 'alt' => esc_html($featured_img_caption), 'class' => 'w-100 img-fluid fit-image ' . (($master == true)?'h-340-px':'h-200-px' ) )
					); //'width' => '28', 'height' => '28', 'proportional' => 'false',
				}
				// Render image
				$f_image = sprintf('<figure class="w-100 %s fit-image" %s>
						<picture class="lazy">
							%s
						</picture>
						%s
					</figure>',
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_description )?'title="'.esc_html($featured_img_description).'"':'title="'.esc_html($f_title).'"',
					$f_poster,
					( $featured_img_caption || $featured_img_description )?'<figcaption><strong>© '. esc_html($featured_img_caption) .'</strong> '. esc_html($featured_img_description) .'</figcaption>':'',
				);
			}

			// Get terms
			$f_sections 				= get_the_terms( $f_id, 'section' );
			$html_f_section = '';
			if ( ! empty( $f_sections ) && ! is_wp_error( $f_sections ) ) {
				$html_f_section .= ($master == false)?'<div class="section-list d-inline cat-links">':'';
				foreach($f_sections as $f_section) {
					$f_section_color 	= get_term_meta( $f_section->term_id, 'wpcf-s-color', true );
					$f_section_edition 	= get_term_meta( $f_section->term_id, 'wpcf-select-edition', true );

					if ($master == true) {
						$f_section_link = get_term_link($f_section->slug, 'section');
						if ( ! is_wp_error( $f_section_link ) ) {
							$html_f_section .= sprintf('<a href="%s" %s class="dot-section" data-bs-toggle="tooltip" data-bs-container=".modal" data-title="%s" data-original-title="" title="">•</a>',
								esc_url($f_section_link),
								(( $f_section_color != '' )?'style="color: '.$f_section_color.';"':''),
								esc_html__($f_section->name)
							);
						}
					} else {
						$f_section_link = get_term_link($f_section, 'section');
						if ( ! is_wp_error( $f_section_link ) ) {
							$html_f_section .= sprintf('<a href="%s" class="section-item" %s title="%s">%s</a>',
								esc_url($f_section_link),
								(( $f_section_color!='' )?'style="background-color:'.$f_section_color.';border-color:'.$f_section_color.'"':''),
								esc_html__($f_section->name),
								esc_html__($f_section->name)
							);
						}
					}
				}
				$html_f_section .= ($master == false)?'</div>':'';
			}

			// Get award
			$f_awards 				= get_the_terms( $f_id, 'award' );
			$f_award_light_img = '';
			$f_award_dark_img = '';
			$f_award_name = '';
			$html_award_img = '';
			$html_award_title = '';
			$f_awards_count = count($f_awards);
			if ( ! empty( $f_awards ) && ! is_wp_error( $f_awards ) ) foreach ($f_awards as $f_award) {
				$f_award_name 		= $f_award->name;
				$f_awards_is_master = get_term_meta( $f_award->term_id, 'wpcf-a-master', true );
				if ( $f_awards_is_master == (int)$master) {
					$f_award_light_img 	= get_term_meta( $f_award->term_id, 'wpcf-a-light-image', true );
					$f_award_dark_img 	= get_term_meta( $f_award->term_id, 'wpcf-a-dark-image', true );
					// print_r('MASTER:'.(int)$master);
					// print_r('IS_MASTER:'.$f_awards_is_master);
					$html_award_img 	.= '<img src="'.(($master == true)?$f_award_light_img:$f_award_dark_img).'" class="w-100 '.(($f_awards_count>2)?'mw-80-px':'mw-180-px').' fit-image mb-4 mb-sm-3" alt="'.(($f_award_name)?$f_award_name:__( 'Award', 'waff' )).'">';
					$html_award_title 	.= '<h6>'.(($f_award_name)?$f_award_name:__( 'Award', 'waff' )).'</h6>';
				}
				//if ( in_array( $f_award->term_id, $master_awards) )
					//print_r($f_award->term_id);
			}
			// print_r($html_award_img);
			// print_r($html_award_title);
			//var_dump($f_award_light_img);
			//print_r($counter);//count($films)

			$html .= '<div class="col-12 col-md-' . ( ($master == true)?ceil(12/$counter):3 ) . ' award-item ' . (($master == true)?'master-':'') . 'award">';

			// Print film / <img src="%s" class="w-100 %s fit-image" alt="%s">
			$html .= sprintf('<div class="card film-card flex-row flex-wrap bg-color-dark my-2 border-0 %s" %s data-film-id="%d" data-aos="flip-up" data-aos-delay="%d">
				<?php /* Film */ ?>
				<div class="card-featured overflow-hidden %s">
					<a href="%s" class="d-flex flex-column flex-wrap align-items-start justify-content-middle h-100 w-100 bg-bgcolor-lighten">
						%s
					</a>
				</div>
				<div class="card-body %s d-flex flex-column justify-content-center text-center %s">
					<div>
						%s
						%s
						<h5 class="card-title mb-0"><a href="%s" class="text-link">%s</a> %s</h5>
						%s
					</div>
					<div class="pt-3">
						<a %s class="card-text">%s</a>
					</div>
					<div>
						<a href="%s" class="card-link link-black stretched-link pt-2 d-none"><i class="icon icon-arrow"></i></a>
					</div>
				</div>
				<?php /* Ribbon */ ?>
				<div class="ribbon-wrapper d-none"><div class="ribbon">Special</div></div>
			</div>',
				($master == true)?'h-340-px shadow-lg card-light':'h-400-px shadow-sm card-dark',
				( $f_color != '' )?'style="background-color:#'.$f_color.';"':'style="background:'.(($master == true)?'black':'white').';"',
				esc_attr( $f_id ),
				$idx*100,
				//
				($master == true)?'w-45':'h-50',
				esc_url(get_permalink( $f_id )),
				$f_image,
				//
				(count($films) > 3)?'p-3':'p-4',
				($master == true)?'w-55':'h-50',
				( $f_award_light_img != '' && $f_award_dark_img != '' )?$html_award_img:'',
				( $f_award_light_img == '' && $f_award_dark_img == '' )?$html_award_title:'',
				esc_url(get_permalink( $f_id )),
				( $f_french_operating_title != '' )?$f_french_operating_title.' <span class="muted">('.$f_title.')</span>':$f_title,
				( $f_movie_length != '' )?'<span class="length">'.$f_movie_length.'\'</span>':'',
				$html_f_section,
				( $f_director_contact != null || $f_director_contact != '' )?'href="'.get_permalink( $f_director_contact ).'"':'',
				$f_author,
				esc_url(get_permalink( $f_id )),
			);

			$html .= '</div>';
		} // END: Foreach 2
	} // END: Foreach
	$html .= '</div>';

	return $html;
}
