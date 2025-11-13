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

function wa_film_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// if ( $is_preview )
	// 	print_r($attributes);

	// No post type no render.
	if ( ! post_type_exists( 'film' ) ) {
		//if ( $is_preview ) {
			echo '<div class="alert alert-dismissible alert-danger fade show" role="alert"><strong>Heads up!</strong> The <strong>film</strong> post type does not exist. Please check your configuration. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
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
	$themeClass = 'single-film --alignwide contrast--light';
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

	$film_ID = mb_get_block_field( 'waff_sf_film' );
	if ( $film_ID != "" ) :
		$promotted 	= (mb_get_block_field( 'waff_sf_promotted' ))?'1':'0';

		$promote 	= get_post_meta($film_ID, 'wpcf-f-promote', true);
		$film_color = rwmb_meta( 'waff_film_color', array(), $film_ID );
		$film_color_class = 'contrast--light card-dark';
		if ( ($promotted=='1' || $promote=='1') && isset($film_color) && $film_color != '' ) {
			$rgb = waff_HTMLToRGB($film_color);
			$hsl = waff_RGBToHSL($rgb);
			if($hsl->lightness < $lightness_threshold)
				$film_color_class = 'contrast--dark card-light';
		}
		global $attributes;
		$attributes = array(
			'wrapper' 		=> 'div', // div / li
			'title_wrapper' => (($promotted=='1' || $promote=='1')?'h2':'h5'), // h5 / h6
			// section + projection : div
			// Related-sections : li
			'parent' 		=> 'film', // film / projection
			// section : film
			// Projection in fiche film : projection
			// Related-sections : film
			'class' 		=> 'card film-card flex-row flex-wrap '.(($promotted=='1' || $promote=='1')?'col-md-12 h-520-px':'col-md-6 h-280-px').' bg-light my-2 border-0 shadow-sm '.$film_color_class,
			// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
			// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
			// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
			'image_class' => '--w-100 '.(($promotted=='1' || $promote=='1')?'h-520-px':'h-280-px').' fit-image',
			// section : w-100 h-280-px fit-image
			// Projection in fiche film : w-100 h-600-px fit-image
			// Related-sections : w-100 --h-100 h-200-px fit-image
			'image_width' => 'w-60',
			// section : w-60
			// Projection in fiche film : w-50 float-left
			// Related-sections : w-150-px
			'body_width' => 'w-40',
			// section : w-40
			// Projection in fiche film : w-50 h-100
			// Related-sections : w-250-px
			'show_sections' => 'false', // string = false / true
			'show_cats' 	=> 'true', // string = false / true
			'show_excerpt' 	=> 'true', // string = false / true
			'excerpt_length' => '100',
			// section = room : 100
			// Projection in fiche film : 80
			// Related-sections : 60
			'show_rooms' 	=> 'false', // string = false / true
			'items' 		=> '', // string = @film_projection.parent / empty
			// Parent items
			// Color
			'film_color'	=> ((($promotted=='1' || $promote=='1') && $film_color != '')?$film_color:''),
			// Animation
			'has_animation' => (($animation_class != '')?'false':'true'),
		);
		$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
		$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?54057:44405;
		if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
			$view_id = 670;
		?>
		<?php /* #Single Film */ ?>
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?>>
			<?php /*  <div class="container-fluid px-0">
				<div class="row g-0 align-items-center py-2 offset-md-2"> */ ?>
					<?php echo render_view_template( $view_id, $film_ID ); // ID de la vue Film card / film-card ?>
				<?php /*  </div>
			</div> */ ?>
		</section>
		<?php /* END: #Single Film */ ?>
		<?php
	endif;
}
