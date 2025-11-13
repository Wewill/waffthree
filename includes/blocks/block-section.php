<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Core\waff_HTMLToRGB as waff_HTMLToRGB;
use function WaffTwo\Core\waff_RGBToHSL as waff_RGBToHSL;

function wa_section_callback( $attributes ) {
	global $current_edition_slug, $current_edition_films_are_online;
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// if ( $is_preview )
	// 	print_r($attributes);

	// No taxonomy no render.
	if ( ! taxonomy_exists( 'section' ) ) {
		//if ( $is_preview ) {
			echo '<div class="alert alert-dismissible alert-danger fade show" role="alert"><strong>Heads up!</strong> The <strong>section</strong> taxonomy does not exist. Please check your configuration. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
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
	$themeClass = 'section-slideshow caroussel mt-10 mb-10 bg-dark contrast--dark color-light';
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

	$section 				= mb_get_block_field( 'waff_ss_section' );
	$section_id 			= $attributes['data']['waff_ss_section'];
	$section_slug 			= $section[0]->slug;
	$use_section_color 		= (mb_get_block_field( 'waff_ss_section_color' ))?'1':'0';

	// Do not show if option is selected and edition is offline
	$showonly_when_edition_is_online 		= (mb_get_block_field( 'waff_ss_showonly_when_edition_is_online' ))?'1':'0';
	if ( $showonly_when_edition_is_online == '1' && $current_edition_films_are_online == false ) return;

	if ( $section_id != "" ) :

		// Get term section
		$html_section = '';
		if ( ! empty( $section ) && ! is_wp_error( $section ) ) {
			$html_section .= '<div class="section-list d-inline cat-links">';
			foreach($section as $s) {
				$s_color 	= get_term_meta( $s->term_id, 'wpcf-s-color', true );
				$s_edition 	= get_term_meta( $s->term_id, 'wpcf-select-edition', true );
				$html_section .= sprintf('<a href="%s" class="section-item" %s title="%s">%s</a>',
					esc_url(get_term_link($s, 'section')),
					(( $s_color!='' )?'style="background-color:'.$s_color.';border-color:'.$s_color.'"':''),
					esc_html__($s->name),
					esc_html__($s->name)
				);
			}
			$html_section .= '</div>';
		}

		$section_color 						= get_term_meta( $section_id, 'wpcf-s-color', true );
		$section_color_class				= 'contrast--light card-dark color-dark';
		if ( $use_section_color == '1' && $section_color != '' ) {
			$rgb = waff_HTMLToRGB($section_color);
			$hsl = waff_RGBToHSL($rgb);
			if($hsl->lightness < 200) {
				$section_color_class 		= 'contrast--dark card-light color-light';
			}
		}

		// print_r($section);
		// print_r($section[0]);
		// print_r($section_id);
		// print_r($section_slug);
		// echo $section_color;
		// echo $section_color_class;

		global $attributes;

		// Exemple : 
		// <div class="card film-card flex-row flex-wrap col-10 bg-custom my-0 border-0 h-600-px shadow-sm card-white p-0" style="background-color: #d54100 !important;">
		// 	<!-- Film -->
		// 	<div class="card-featured overflow-hidden w-50 float-start">
		// 		<a class="d-flex flex-column flex-wrap align-items-start justify-content-middle h-100 w-100 bg-secondary">
		// 			<img data-srcset="img/carousel/1-1200x1200.jpg 2x, img/carousel/1-600x600.jpg" data-lazy="img/carousel/1-600x600.jpg" data-sizes="" class="w-100 h-600-px fit-image" alt="">
		// 		</a>
		// 	</div>
		// 	<div class="card-body p-4 d-flex flex-column justify-content-between w-50 h-100">
		// 		<div>
		// 			<h5 class="card-title mb-0"><a href="#" class="text-link">Arguments</a> <span class="length">108'</span></h5>
		// 			<div class="section-list"><a class="section-item" style="color: #d54100 !important;">Coups de coeur</a></div>
		// 		</div>
		// 		<div>
		// 			<div class="category-list"><a class="category-item">Long-métrage</a><a class="category-item">Documentaire</a></div>
		// 			<p class="card-text d-none d-sm-block"><small>Ce documentaire est consacré aux derniers témoignages des résistants déportés NN, rescapés du camp de concentration de Natzweiler-Struthof. C'est aussi l'histoire de ce camp, le seul installé en France, l'un des plus meurtriers du système nazi. C'est le récit des arrestations de ces jeunes résistants et de leurs souffrances vécues pendant leur déportation.</small></p>
		// 		</div>
		// 		<div>
		// 			<div class="room-list">
		// 				<a class="room-item">Petit théâtre</a>
		// 				<a class="parentroom-item">Maison de la culture d'Amiens</a>
		// 			</div>
		// 			<a href="#" class="card-link link-black stretched-link pt-2 d-block"><i class="icon icon-arrow"></i></a>
		// 		</div>
		// 	</div>
		// </div>

		$attributes = array(
			'wrapper' 		=> 'div', // div / li
			'title_wrapper' => 'h4', // h5 / h6
			// section + projection : div
			// Related-sections : li
			'parent' 		=> 'film', // film / projection
			// section : film
			// Projection in fiche film : projection
			// Related-sections : film
			'class' 		=> 'card film-card carousel-card flex-row flex-wrap col-md-10 h-sm-600-px h-600-px bg-light my-0 p-0 border-0 shadow-sm '.$section_color_class,
			// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
			// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
			// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
			// Carousel : card film-card flex-row flex-wrap col-10 bg-custom my-0 border-0 h-600-px shadow-sm card-white p-0
			'image_class' => 'w-100 h-600-px fit-image',
			// section : w-100 h-280-px fit-image
			// Projection in fiche film : w-100 h-600-px fit-image
			// Related-sections : w-100 --h-100 h-200-px fit-image
			// Carousel : w-100 h-600-px  fit-image
			'image_width' => 'w-50 float-start',
			// section : w-60
			// Projection in fiche film : w-50 float-left
			// Related-sections : w-150-px
			// Carousel : w-50 float-start
			'body_width' => 'w-50 h-100',
			// section : w-40
			// Projection in fiche film : w-50 h-100
			// Related-sections : w-250-px
			// Projection in fiche film : w-50 h-100
			'show_sections' => 'false', // string = false / true
			'show_cats' 	=> 'true', // string = false / true
			'show_excerpt' 	=> 'true', // string = false / true
			'excerpt_length' => '400',
			// section = room : 100
			// Projection in fiche film : 80
			// Related-sections : 60
			'show_rooms' 	=> 'true', // string = false / true
			'items' 		=> '', // string = @film_projection.parent / empty
			// Parent items
			// Color
			'film_color'	=> (($use_section_color=='1' && $section_color != '')?$section_color:''),
			// Animation
			'has_animation' => (($animation_class != '')?'false':'true'),
		);
		?>
		<?php /* #Carrousel section */ ?>
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?>>
			<div class="carousel-header container px-0 text-light">
				<div class="row">
					<div class="col-11 p-4">
						<hgroup id="carousel-title" class="py-4">
							<?= $html_section ?>
							<h2 class="color-light"><?= mb_get_block_field( 'waff_ss_title' ) ?></h2>
						</hgroup>
					</div>
					<div class="col-1 p-4 position-relative">
						<div class="slick-carousel-arrows"><button class="slick-prev slick-arrow" aria-label="Previous" type="button" style="">Previous</button><button class="slick-next slick-arrow" aria-label="Next" type="button" style="">Next</button></div>
					</div>
				</div>
			</div>
			<div class="carousel-items text-dark">
				<?php /* Slick slide */ ?>
				<div class="slick-carousel card-deck w-100 m-0">

					<?php /* FILM-CARD CALLED BY A VIEW */ ?>
					<?php
					$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
					$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?66660:0;
					if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
						$view_id = 0;
					// Then, print if we found results
					// 66660 = films-section
					$args = array(
						'id' => $view_id,
						'wpvsection' => $section_slug,
						'wpvedition' => $current_edition_slug,
					);
					echo render_view( $args );
					?>
					<?php /* FIN FILM CARD */ ?>

				</div>
				<?php /* End : Slick slide */ ?>
			</div>
		</section>
		<?php /* END: #Carrousel section */ ?>
		<?php
	endif;
}
