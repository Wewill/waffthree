<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;

function wa_partners_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	$partner_post_type 	= ( post_type_exists('partenaire') )?'partenaire':'partner'; // Depreciated WAFFTWO V1
	$partner_category 	= ( post_type_exists('partenaire') )?'partenaire-category':'partner-category'; // Depreciated WAFFTWO V1
	$partner_field 		= ( post_type_exists('partenaire') )?'p-link':'waff_partner_link'; // Depreciated WAFFTWO V1
	$partner_field 		= ( defined('WAFF_THEME') && 'RSFP' === WAFF_THEME && post_type_exists('partner') )?'p_general_link':$partner_field; // Special RSFP

	// print_r($attributes);
	// if ( $is_preview )
	// 	print_r($attributes);

	// No post type no render.
	if ( ! post_type_exists( $partner_post_type ) ) {
		//if ( $is_preview ) {
			echo '<div class="alert alert-dismissible alert-danger fade show" role="alert"><strong>Heads up!</strong> The <strong>partner | partenaire</strong> post type does not exist. Please check your configuration. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
		//}
		return;
	}


	global $current_edition_id, $current_edition_films_are_online;

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
	$themeClass = 'partners mt-1 mb-1 contrast--light';
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
		<div class="container-fluid px-0">

				<?php
				$categories 	= array();
				$posttype 		= $partner_post_type;
				//$categories 	= $attributes['data']['waff_pn_categories'];
				$categories 	= mb_get_block_field( 'waff_pn_categories' );
				// print_r($categories);

				foreach($categories as $category) {

					//$term = get_term_by( 'id', $category, $partner_category );
					if ( !empty($category) ) {
						?>

						<p class="subline mt-6 mb-2"><?= $category->name; ?></p>
						<hr/>
						<div class="row g-0 align-items-top">

						<?php
					}

					if ( $current_edition_id !== NULL && taxonomy_exists( 'edition') ) {
						$args = array(
							'numberposts' 		=> -1, // No limit
							'post_status' 		=> 'publish', // Show only the published posts
							'orderby'			=> 'post_date',
							'order'				=> 'DESC',
							'post_type'			=> $posttype,
							// Limit to selected cats and edition
							'tax_query' => array(
								array(
									'taxonomy' => 'edition',
									'field' => 'term_id',
									'terms' => $current_edition_id,
									'include_children' => false
								),
								array(
									'taxonomy' => $partner_category,
									'field' => 'term_id',
									'terms' => $category->term_id, // Fixed from DINARD SEPT23
									'operator' => 'IN'
								),
							)
						);
					} else {
						$args = array(
							'numberposts' 		=> -1, // No limit
							'post_status' 		=> 'publish', // Show only the published posts
							'orderby'			=> 'post_date',
							'order'				=> 'DESC',
							'post_type'			=> $posttype,
							// Limit to selected cats and edition
							'tax_query' => array(
								array(
									'taxonomy' => $partner_category,
									'field' => 'term_id',
									'terms' => $category->term_id, // Fixed from DINARD SEPT23
									'operator' => 'IN'
								),
							)
						);
					}

					$partners = get_posts($args);

					foreach( $partners as $post ) :

						$id 		= (( $post->ID )?$post->ID:get_the_ID());

						// DEPRECIATED WAFFTWO V.1 = FIFAM : p-link / DINARD : waff_partner_link
						if ( post_type_exists('partenaire') )
						$link 		= types_render_field( $partner_field, array('id' => $id) );
						else
						$link 		= get_post_meta( $id, $partner_field, true );

						// Post Thumbnail
						$featured_img_urls = array();
						$partenaire_featured_sizes = array(
							//'full',
							'medium_large', // 768px
							'medium', // 300px
							'partenaire-featured-image', // 150px
							'partenaire-featured-image-x2', // 200px
						);
						$selected_featured_sizes = $partenaire_featured_sizes;
						if ( has_post_thumbnail($post) ) {  //is_singular() &&
							$featured_img_id     		= get_post_thumbnail_id($post);
							$featured_img_url_full 		= get_the_post_thumbnail_url($post);
							foreach ($selected_featured_sizes as $size) {
								$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
								$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full;
							}
						}
						$featured_img_caption = $post->post_title;

					?>

						<div id="p-<?= $id ?>" class="col-3 col-sm-2 partner-slide-item d-inline-block p-2 p-sm-4">
							<a href="<?= esc_url($link) ?>" class="color-black link link-dark" title="<?php echo $post->post_title; ?>">
								<figure title="<?php echo esc_attr($featured_img_description); ?>">
									<picture class="lazy">
									<?php // Breakpoint : xl ?>
									<data-src media="(min-width: 767px)"
											srcset="<?= $featured_img_urls['medium_large']; ?>" type="image/jpeg"></data-src>
									<?php // Breakpoint : lg ?>
									<data-src media="(min-width: 299px)"
											srcset="<?= $featured_img_urls['medium']; ?>" type="image/jpeg"></data-src>
									<?php // Breakpoint : sm ?>
									<data-src media="(min-width: 149px)"
											srcset="<?= $featured_img_urls['partenaire-featured-image']; ?>" type="image/jpeg"></data-src>
									<data-img src="<?= $featured_img_urls['partenaire-featured-image']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid" style="object-fit: cover; width: 100%;"></data-img>
									</picture>
									<?php // Sizes : print_r($featured_img_urls); ?>
								</figure>
							</a>
						</div>

					<?php endforeach;

					?>
					</div>
					<?php
				}
			?>
		</div>
	</section>
	<?php
}
