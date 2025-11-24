<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Core\waff_clean_alltags as waff_clean_alltags;
use function WaffTwo\Core\waff_trim as waff_trim;
use function WaffTwo\Core\waff_HTMLToRGB as waff_HTMLToRGB;
use function WaffTwo\Core\waff_RGBToHSL as waff_RGBToHSL;
use function WaffTwo\Core\waff_get_image_id_by_url as waff_get_image_id_by_url;

function wa_sections_callback( $attributes ) {
	global $current_edition_id, $current_edition_slug;
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// if ( $is_preview )
	 	//print_r($attributes);

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
	$themeClass = 'sections-list mt-10 mb-10 contrast--dark';
	$class = $themeClass . ' ' . ( $attributes['className'] ?? '' );
	$subclass = ( $attributes['name'] ?? '' ) . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
		$subclass .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Params
	// print_r($attributes['data']);
	$show_introduction 		= (mb_get_block_field( 'waff_sl_show_introduction' ) ||
								( isset( $attributes['data']['waff_sl_show_introduction'] ) && $attributes['data']['waff_sl_show_introduction'] == 1 )
							  )?'1':'0';
	$show_parent_section 	= (mb_get_block_field( 'waff_sl_show_parent_section' ) ||
								( isset( $attributes['data']['waff_sl_show_parent_section'] ) && $attributes['data']['waff_sl_show_parent_section'] == 1 )
							  )?'1':'0';
	$show_tiny_list 		= (mb_get_block_field( 'waff_sl_show_tiny_list' ) ||
								( isset( $attributes['data']['waff_sl_show_tiny_list'] ) && $attributes['data']['waff_sl_show_tiny_list'] == 1 )
							  )?'1':'0';

	// Get sections filter (for favorite sections feature)
	$sections_in = isset( $attributes['data']['waff_sl_sections_in'] ) ? (array) $attributes['data']['waff_sl_sections_in'] : array();

	// Get edition metas
	$edition 			= mb_get_block_field( 'waff_sl_edition' ); // WP_Term Object
	$edition_id 		= (int)$attributes['data']['waff_sl_edition']; // ID
	$edition_id 		= ( isset($edition_id) && $edition_id != null && $edition_id != 0 )?$edition_id:$current_edition_id;
	$edition_name		= ( !empty($edition) && !is_wp_error($edition) )?$edition->name:get_term($edition_id)->name;
	$edition_year 		= ( !empty($edition) && !is_wp_error($edition) )?get_term_meta( $edition_id, 'wpcf-e-year', true ):'';
	if ( empty($edition_id) ) //|| is_wp_error($edition)
		echo esc_html__( 'Please choose an edition', 'waff' );

	// Get parent section by edition year
	$parent_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby' => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC',
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'number' => 1,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition_id,
			),
		),
	);
	$the_edition_section = get_terms( $parent_section_args );
	$the_edition_terms_list = array();
	if ( !empty( $the_edition_section ) && !is_wp_error( $the_edition_section ) ) :
		foreach( $the_edition_section as $term ) {
			$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
			$the_edition_terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
				(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.';"':''),
				esc_url(get_term_link($term)),
				esc_html__($term->name),
				esc_html__($term->name)
			);
		}
	endif;

	// Get all sections by edition year
	$all_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC',
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => $the_edition_section[0]->term_id,
		//'exclude' => $the_edition_section[0]->term_id,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition_id,
			),
		),
	);
	$sections = get_terms( $all_section_args );

	?>
		<?php /* #Sections list */ ?>
		<?php if ( isset( $show_introduction ) && $show_introduction == '1' ) : ?>
		<?php /* BEGIN:Introduction */ ?>
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="margin-bottom: 1.5rem!important;background-color: <?= mb_get_block_field( 'background_color' ) ?>">
			<div class="container-fluid px-0">
				<hgroup class="text-center">
					<h6 class="headline d-inline-block"><?= esc_html(mb_get_block_field( 'waff_sl_title' )) ?></h6>
					<?php if ($edition_year != '') : ?><h1 class="sections-title mt-0 mb-0 display-1"><?= $edition_year; ?></h1><?php endif; ?>
					<?php
						if ( !empty($the_edition_terms_list) ) {
							printf(
								/* translators: %s: list of categories. */
								'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
								esc_html__( 'Categorized as', 'waff' ),
								implode($the_edition_terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
							);
						}
					?>
					<h6 class="visually-hidden">Les sections de l'édition <?= $edition_name; ?> du Festival Internationnal du Film d'Amiens</h6>
				</hgroup>

				<?php if ( mb_get_block_field( 'waff_sl_leadcontent' ) ) : ?>
				<p class="lead mt-2 mt-sm-6 text-center"><?= waff_do_markdown(mb_get_block_field( 'waff_sl_leadcontent' )) ?></p>
				<?php endif; ?>

				<?php if ( mb_get_block_field( 'waff_sl_content' ) ) : ?>
				<div class="mt-1 mt-sm-3 text-center w-75 m-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_sl_content' )) ?></div>
				<?php endif; ?>
			</div>
		</section>
		<?php /* END:Introduction */ ?>
		<?php endif; ?>

		<?php /* #BEGIN: Sections tiny list */ ?>
		<?php if ( isset( $show_tiny_list ) && $show_tiny_list == '1' ) : ?>
		<section class="<?= $subclass ?> mt-0 mb-0 <?= $animation_class ?> tiny-list" <?= $data ?>>
			<div class="d-sm-flex row g-0">
		<?php endif; ?>

		<?php
		// Lightness threshold
		$lightness_threshold = 200; // Section = 200
		if ( isset( $show_parent_section ) && $show_parent_section == '1' )
			$sections = array_merge($the_edition_section, $sections);
		if ( !empty( $sections ) && !is_wp_error( $sections ) ) :
			foreach( $sections as $section ) :
				$section_id 				= $section->term_id;
				$section_color 				= get_term_meta( $section_id, 'wpcf-s-color', true );
				// Ajust lightness of section
				$section_color_class		= 'contrast--light bg-light color-dark';
				$section_title_color 		= 'color-dark link-dark';
				if ( $section_color != '' ) {
					$rgb = waff_HTMLToRGB($section_color); //, 'array'
					$hsl = waff_RGBToHSL($rgb);
					if($hsl->lightness < $lightness_threshold) {
						$section_color_class = 'contrast--dark bg-dark color-light';
						$section_title_color = 'color-light link-light';
					}
				}
				// Apply opacity for sections not in favorites (if sections_in filter is set)
				$section_opacity_style = '';
				if ( ! empty( $sections_in ) && ! in_array( $section_id, $sections_in, true ) ) {
					$section_opacity_style = 'opacity: 0.15;';
				}
				// Counts
				if ( function_exists('get_counts') )
					$counts = get_counts('section', $section_id, null);
				// Content
				$section_description 				= term_description($section_id);
				$section_content 					= get_term_meta( $section_id, 'wpcf-s-content', true );
				// Image
				$section_image 						= get_term_meta( $section_id, 'wpcf-s-image', true );
				$section_credits_image 				= get_term_meta( $section_id, 'wpcf-s-credits-image', true );
				$section_image_ID 					= waff_get_image_id_by_url($section_image);
				$featured_img_caption 				= wp_get_attachment_caption($section_image_ID); // ADD WIL
				$thumb_img 							= get_post( $section_image_ID ); // Get post by ID
				//$featured_img_description 		= $thumb_img->post_content; // Display Description >> ISSUE with description > it display the page post_content instead of image.
				if ( function_exists( 'types_render_termmeta' ) ) {
					$section_image = types_render_termmeta( 's-image', array(
						'term_id' => $section_id,
						'size' => ( isset( $show_tiny_list ) && $show_tiny_list == '1' )?'post-featured-image-xs':'post-featured-image', //post-featured-image-x2
						'alt' => esc_html($featured_img_caption),
						'style' => 'object-fit: cover; width: 100%;',
						'class' => ( isset( $show_tiny_list ) && $show_tiny_list == '1' )?'img-fluid h-100-px':'img-fluid h-sm-600-px h-600-px')
					);
				}
				$section_credits_image 				= get_term_meta( $section_id, 'wpcf-s-credits-image', true );
		?>
		<?php /* BEGIN:Sections list */ ?>
		<?php if ( isset( $show_tiny_list ) && $show_tiny_list == '0' ) : ?>
		<section class="<?= $subclass ?> mt-0 mb-0 <?= $section_color_class ?> <?= $animation_class ?>" <?= $data ?>>
			<div class="--card border-0 rounded-0 row" style="<?= (($section_color!='')?'background-color:'.$section_color.' !important;':'') ?><?= $section_opacity_style ?>">
				<?php if ( $section_image != '' ) : ?>
				<figure title="<?php echo esc_attr(sanitize_text_field($section->name)); ?>" class="h-sm-600-px h-600-px col-12 col-sm-6">
					<picture class="lazy">
					<?php /* 3800x1200 > 1900x600 */ ?>
					<?= $section_image ?>
					</picture>
					<?php if ( $featured_img_caption ) : ?>
					<figcaption class="bg-transparent text-light ms-4"><strong>© <?= waff_do_markdown(strip_tags(esc_html($featured_img_caption))); ?></strong></figcaption>
					<?php elseif ( $section_credits_image ) : ?>
					<figcaption class="bg-transparent text-light ms-4"><strong>© <?= waff_do_markdown(strip_tags(esc_html($section_credits_image))); ?></strong></figcaption>
					<?php endif; /* If captions */ ?>
				</figure>
				<?php endif; ?>
				<div class="<?= (( $section_image != '')?'col-12 col-sm-6':'col-12 p-3 h-sm-600-px h-600-px'); ?> p-5 p-sm-2 d-flex flex-column flex-sm-row justify-content-center justify-content-sm-between align-items-start align-items-sm-center" <?= (($section_image=='' && $section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?>>
					<div class="w-sm-50">
						<h2 class="--pt-4 heading-4 heading-sm card-title <?= $section_title_color ?>"><?= sanitize_text_field($section->name) ?></h2>
						<?php /* Edition */ ?>
						<?php
						if ( !empty($the_edition_terms_list) ) {
							printf(
								/* translators: %s: list of categories. */
								'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
								esc_html__( 'Categorized as', 'waff' ),
								implode($the_edition_terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
							);
						}
						?>
						<?php /* <div class="category-list d-inline"><a class="category-item">En avant</a></div> */ ?>
						<?php /* Description */ ?>
						<?php if ( strlen(strip_tags($section_description)) > 0 ) : ?>
							<p class="card-text <?= $section_title_color ?> pt-4 mb-2"><?= waff_do_markdown(waff_trim(strip_tags($section_description), 300)) ?></p>
						<?php else : ?>
							<?php echo apply_filters('the_content', waff_do_markdown(waff_trim(waff_clean_alltags($section_content), 300))); ?>
						<?php endif; ?>
					</div>
					<div class="mt-4 mt-sm-0">
						<a href="<?= get_term_link($section_id); ?>" class="card-link <?= $section_title_color ?> stretched-link pr-3 d-flex flex-column align-items-start">
							<span class="display-3 display-sm mb-n2"><?= $counts['films']; ?></span> <span>films
						<i class="icon icon-right pl-2"></i><span></a>
					</div>
				</div>
			</div>
		</section>
		<?php else: ?>
		<?php /* BEGIN:Sections list tiny */ ?>
		<div class="card border-0 rounded-0 flex-sm-equal col-4 col-sm-12" style="<?= (($section_color!='')?'background-color:'.$section_color.' !important;':'') ?><?= $section_opacity_style ?>">
			<?php if ( $section_image != '' ) : ?>
			<figure title="<?php echo esc_attr(sanitize_text_field($section->name)); ?>" class="">
				<picture class="lazy">
				<?php /* 3800x1200 > 1900x600 */ ?>
				<?= $section_image ?>
				</picture>
			</figure>
			<?php endif; ?>
			<div class="p-2 d-flex flex-column justify-content-between h-100" <?= (($section_image=='' && $section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?>>
				<h6 class="pt-2 <?= $section_title_color ?>"><?= sanitize_text_field($section->name) ?></h6>
				<div class="mt-2 mt-sm-0 pb-4">
					<a href="<?= get_term_link($section_id); ?>" class="card-link <?= $section_title_color ?> stretched-link pr-1"><?= $counts['films']; ?> films
					<i class="icon icon-right pl-2"></i></a>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
			endforeach;

		if ( isset( $show_tiny_list ) && $show_tiny_list == '1' ) : ?>
			</div>
		</section>
		<?php /* #END: Sections tiny list */ ?>
		<?php endif;

		endif;
		?>
		<?php /* END:Sections */ ?>
		<?php /* END: #Sections list */ ?>
		<?php
}
