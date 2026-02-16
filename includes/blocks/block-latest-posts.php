<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\waff_entry_meta_header as waff_entry_meta_header;

function wa_latest_posts_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// if ( $is_preview )
	// 	print_r($attributes);

	if ( $is_preview === true ) {
		?>
		<section style="text-align: center; padding-left: 20%; padding-right: 20%;">
		<?php
		switch(mb_get_block_field( 'waff_lp_style' )) {
			case 'normal':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-normal.svg" class="img-fluid" />
				<?php
				break;
			case 'magazine':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-magazine.svg" class="img-fluid" />
				<?php
				break;
			case 'bold':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-bold.svg" class="img-fluid" />
				<?php
				break;
			case 'classic':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-classic.svg" class="img-fluid" />
				<?php
				break;
			default:
				echo '<div class="alert alert-dismissible alert-notice fade show" role="alert"><strong>Heads up!</strong> No style choosen yet. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
				break;
		}
		?>
		</section>
		<?php
		return;
	}

	// print_r($attributes);

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
	//$themeClass = 'featured mt-10 mb-10 contrast--dark fix-vh-50';
	$themeClass = 'featured mt-md-5 mb-md-5 mt-2 mb-2 contrast--dark fix-vh-50'; // Responsive issue fix
	if ( mb_get_block_field( 'waff_lp_style' ) == 'normal') $themeClass = 'mt-2 mb-2 contrast--light';
	if ( mb_get_block_field( 'waff_lp_style' ) == 'classic') $themeClass = 'mt-2 mb-4 contrast--light overflow-visible';
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

	if ( mb_get_block_field( 'waff_lp_style' ) == null ) :
	?>
		<div class="alert alert-dark"><p><?php _e('Please define style to continue', 'waff'); ?></p></div>
	<?php
	endif;

	$meta_queries 	= array();
	$sticky_posts 	= array();
	$categories 	= array();
	$categories_id	= array();
	// mb_get_block_field / mb_the_block_field
	$limit 			= esc_attr(mb_get_block_field( 'waff_lp_limit' ));
	// $morelink 		= esc_attr(mb_get_block_field( 'waff_lp_morelink' ));
	$posttype 		= esc_attr(mb_get_block_field( 'waff_lp_posttype' ));
	$meta 			= esc_attr(mb_get_block_field( 'waff_lp_meta' ));
	$containsvideo 	= esc_attr(mb_get_block_field( 'waff_lp_containsvideo' ));

	if ( $containsvideo == 1 ) {
		$meta_queries[] = array(
			'relation' => 'OR',
			array(
				'key'     => 'd_medias_video',
				'value'   => '',
				'compare' => '!=',
			),
			array(
				'key'     => 'd_medias_video_link',
				'value'   => '',
				'compare' => '!=',
			),
		);
	}

	if ( $posttype === 'post' ) {
		//$categories 	= $attributes['data']['waff_lp_categories'];
		$categories 	= mb_get_block_field( 'waff_lp_categories' );
		$categories_id  = wp_list_pluck($categories, 'term_id');
	}

	$sticky_posts_option = get_option('sticky_posts');

	if (!empty($sticky_posts_option)) {
		$sticky_posts = get_posts(array(
			'post_type'			=> $posttype,
			'numberposts' 		=> $limit,
			'post_status' 		=> 'publish', // Show only the published posts
			'orderby'			=> 'post_date',
			'order'				=> 'DESC',
			// Only the sticky ones !
			'post__in'  		=> $sticky_posts_option,
			'ignore_sticky_posts' => true,
			// Limit to selected cats
			'category'			=> $categories_id,
			'meta_query'		=> $meta_queries,
		));
	} else {
		$sticky_posts = get_posts(array(
			'post_type'			=> $posttype,
			'numberposts' 		=> 1,
			'post_status' 		=> 'publish', // Show only the published posts
			'orderby'			=> 'post_date',
			'order'				=> 'DESC',
			// No limit to sticky if not, only the last one if featured
			// Limit to selected cats
			'category'			=> $categories_id,
			'meta_query'		=> $meta_queries,
		));
	}

	$args = array(
		'post_type'			=> $posttype,
		'numberposts' 		=> $limit,
		'post_status' 		=> 'publish', // Show only the published posts
		'orderby'			=> 'post_date',
		'order'				=> 'DESC',
		// All but not sticky !
		'post__not_in'  	=> get_option( 'sticky_posts' ),
		// Limit to selected cats
		'category'			=> $categories_id,
		'meta_query'		=> $meta_queries,
	);

	$all_posts = array(
		'post_type'			=> $posttype,
		'numberposts' 		=> $limit,
		'post_status' 		=> 'publish', // Show only the published posts
		'orderby'			=> 'post_date',
		'order'				=> 'DESC',
		// Limit to selected cats
		'category'			=> $categories_id,
		'meta_query'		=> $meta_queries,
	);

	if ( mb_get_block_field( 'waff_lp_style' ) === 'normal' ) :
	?>
	<?php /* #Latest / Normal style */ ?>
	<section id="<?= $id ?>" class="normal-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid">

			<span class="bullet bullet-action-2 ml-0"></span>
			<h5><?= mb_get_block_field( 'waff_lp_title' ) ?></h5>

			<div class="row row-cols-1 row-cols-md-<?= ($limit != '')?$limit:3; ?> mt-4 mb-4">

			<?php
				$index = 0;
				$recent_posts = get_posts($all_posts);
				//$recent_posts = array_merge($sticky_posts, $recent_posts);

				foreach( $recent_posts as $post_item ) :
					// Set up global post data in loop
					// setup_postdata($GLOBALS['post'] =& $post_item); //$GLOBALS['post'] =& $post_item

					$post_id 				= esc_attr($post_item->ID);
					// $post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_id );
					// $post_color				= ($post_color!='')?$post_color:'#444444'; //00ff97
					// $rgb_post_color			= waff_HTMLToRGB($post_color, 'array'); // , 'array' ICI Bug ??
					$the_categories 		= get_the_category($post_id);
					$excerpt = '';
					$the_excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
					$the_content = wp_strip_all_tags(get_the_content('...', true, $post_id));

					// RSFP
					$d_general_introduction 	= wp_strip_all_tags(get_post_meta($post_id, 'd_general_introduction', true)); // RSFP only
					$d_medias_videos 			= get_post_meta( $post_id, 'd_medias_video', true ); // RSFP only
					$d_medias_video_links 		= get_post_meta( $post_id, 'd_medias_video_link', true ); // RSFP only
					$d_have_videos 				= !empty($d_medias_videos) || !empty($d_medias_video_links); // RSFP only

					// echo $post_id;
					// echo $the_content;
					$the_content = ( $d_general_introduction !== '' )?$d_general_introduction:$the_content;
					$excerpt = ( $the_excerpt !== '' )?$the_excerpt:$the_content;
					if ( strlen($excerpt) > 140 ) {
						$excerpt = substr($excerpt, 0, 140);
						$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
					}
					// if ( $index > $limit ) { continue; }
					$index++;

					?>
				<div class="col">
					<div class="card mb-1 mb-md-2 border-0" id="<?= $post_id; ?>" data-aos="fade-up" data-aos-delay="<?= $index*100; ?>">
						<div class="row g-0">
							<div class="col-2 col-md-4 position-relative mh-150-px">
								<div class="img_holder position-relative">
									<img src="<?php echo get_the_post_thumbnail_url($post_id, 'thumbnail'); ?>" class="img-fluid rounded-4">
									<?php if ($containsvideo == 1 || $d_have_videos) : ?>
										<div class="position-absolute top-50 --h-100 w-100 btn_holder">
											<span class="btn --action-3 color-light play play-xs d-flex flex-center"><i class="bi bi-play-fill h5 m-0 p-0 ms-1 mt-1"></i></span>
										</div>
									<?php endif; ?>
								</div> <?php /* #Added RSFP */ ?>
							</div>
							<div class="col-10 col-md-8">
								<div class="card-body py-0">

									<?php if ( ! empty( $the_categories ) ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
									<?php if ( $meta == 1 ) : ?>
										<?= waff_entry_meta_header($post_item); ?>
									<?php endif; ?>

									<h5 class="card-title mt-2">
										<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link">
											<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
											<?= $post_item->post_title ?>
										</a>
									</h5>

									<p class="card-text fs-sm mb-0"><?= $excerpt; ?></p>

									<?php if ( $meta == 1 ) : ?>
										<p class="card-text --mt-n2"><small class="text-body-secondary"><?= get_the_date('j F Y', $post_id); ?></small></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach;
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>

			</div> <?php /* END: .row */ ?>

		</div>
	</section>
	<?php /* END: #Latest / Normal style */ ?>
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'classic' ) :
	?>
	<?php /* #Latest / Classic style */ ?>
	<section id="<?= $id ?>" class="classic-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid">
			<div class="row row-cols-1 row-cols-md-2 mt-4 mb-4">

				<?php /* Right col */ ?>
				<div class="col mb-4 mb-md-0">

				<?php
					if ( !empty( $sticky_posts ) && is_array( $sticky_posts ) && count( $sticky_posts ) > 1 ) {
						$sticky_posts = array_slice($sticky_posts, 0, 1); // Only the first sticky post
					}

					foreach( $sticky_posts as $post_item ) :
						$post_id 				= esc_attr($post_item->ID);
						$the_categories 		= get_the_category($post_id);

						// Post Color
						$post_color 				= rwmb_meta('_waff_bg_color_metafield', array(), $post_id);
						$post_color 				= ($post_color != '') ? $post_color : 'var(--waff-action-2)';
						$rgb_post_color				= waff_HTMLToRGB($post_color);
						$post_title_color 			= 'text-white';
						// Check if the color is dark or light
						if ( $post_color && $post_color != '' && $post_color != 'var(--waff-action-2)' ) { // Si $post_color n'est pas vide
							$hsl = waff_RGBToHSL($rgb_post_color); // Accepte un INTEGER
							if($hsl->lightness > $lightness_threshold) {
								$post_title_color 			= 'text-dark';
							}
						}
						// Post Thumbnail
						$thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
						$background_style = $thumbnail_url ? "background-image: url('$thumbnail_url');" : "background-color: $post_color;";

						?>
					<div class="card min-h-250-px h-100 overflow-hidden rounded-4 shadow-lg border-0 ---- bg-cover bg-position-center-center"
					id="<?= $post_id; ?>"
					style="<?= $background_style; ?>" data-aos="fade-right">
						<div class="card-img-overlay <?= $thumbnail_url ? 'bg-gradient-action-2' : '' ?>">
							<div class="d-flex flex-column justify-content-between h-100 p-4 pb-3 text-white text-shadow-1">
								<div>
									<h6 class="subline text-action-1"><?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?> <?php esc_html_e( 'Featured', 'waff' ); ?></h6>
									<?php if ( ! empty( $the_categories ) && $meta ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
								</div>
								<h2 class="text-white mt-auto mb-8">
									<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link link-white">
										<?= $post_item->post_title ?>
									</a>
								</h5>
								<ul class="d-flex list-unstyled m-0">
									<li class="me-auto subline"><?php esc_html_e( 'Read more', 'waff' ); ?> <i class="bi bi-chevron-right"></i></li>
									<li class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <small><?= get_the_date('j F Y', $post_id); ?></small></li>
								</ul>
							</div>
						</div>
					</div>
					<?php endforeach;
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>

				</div>

				<?php /* Left col */ ?>
				<div class="col">
					<style scoped>
						.card.r-card {
							height: 250px;
						}

						@media (min-width: 768px) {
							.card.r-card {
								height:16vw !important;
							}
						}
					</style>
					<?php /* Grid rows */ ?>
					<div class="row row-cols-1 row-cols-md-2 g-4">
					<?php
						$index=0;
						$recent_posts = get_posts($args);
						foreach( $recent_posts as $post_item ) :
							$post_id 				= esc_attr($post_item->ID);
							$the_categories 		= get_the_category($post_id);
							$index++;
							?>
						<div class="col">
							<div class="card r-card h-250-px overflow-hidden rounded-4 shadow-lg border-0 ---- bg-cover bg-position-center-center"
							id="<?= $post_id; ?>"
							style="background-image: url('<?= get_the_post_thumbnail_url($post_id, 'large'); ?>');" data-aos="fade-left" data-aos-delay="<?= $index*200; ?>">
								<div class="card-img-overlay bg-gradient-action-2">
									<div class="d-flex flex-column justify-content-between h-100 p-4 pb-3 text-white text-shadow-1">
										<div>
											<?php if ( ! empty( $the_categories ) && $meta ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
										</div>
										<h5 class="text-white">
											<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link link-white">
												<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
												<?= $post_item->post_title ?>
											</a>
										</h5>
										<ul class="d-flex list-unstyled m-0">
											<li class="me-auto subline"><?php esc_html_e( 'Read more', 'waff' ); ?> <i class="bi bi-chevron-right"></i></li>
											<li class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <small><?= get_the_date('j F Y', $post_id); ?></small></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach;
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>

			</div> <?php /* END: .row */ ?>

		</div>
	</section>
	<?php /* END: #Latest / Classic style */ ?>
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'magazine' ) :
	?>
	<?php /* #Featured / Magazine style */ ?>
	<section id="<?= $id ?>" class="magazine-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid px-0">
		<div class="row g-0 align-items-top">

			<?php

			$index = 0;
			$recent_posts = get_posts($args);
			//$recent_posts = array_merge($sticky_posts, $recent_posts);

			if ( count($sticky_posts) > 0 ) :
				foreach( $sticky_posts as $post_item ) :
					$post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_item->ID );
					//$rgb_post_color			= waff_HTMLToRGB($post_color, 'array');
					$the_categories 		= get_the_category($post_item->ID);
					$excerpt 	= get_the_excerpt($post_item->ID);
					$excerpt 	= force_balance_tags( html_entity_decode( wp_trim_words( htmlentities($excerpt), 15, '...' ) ) );
					$sticky_post_date = wp_kses(
						sprintf(
							'<time datetime="%1$s">%2$s</time>',
							esc_attr( get_the_date( DATE_W3C, $post_item->ID ) ),
							( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'M jS Y', strtotime( get_the_date( DATE_W3C, $post_item->ID ) )  ) : get_the_date('j F Y', $post_item->ID)
						),
						array_merge(
							wp_kses_allowed_html( 'post' ),
							array(
								'time' => array(
									'datetime' => true,
								),
							)
						)
					);
					?>
					<?php /* BEGIN: Sticky post */ ?>
					<div class="col-md-6 p-4" data-aos="fade-right">
						<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_title' ) ?></h6>
					</div>
					<div class="col-md-6 p-0 vh-75 img-shifted shift-right overflow-visible" data-aos="fade-left" data-aos-delay="200">
						<?php /* Images */ ?>
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_item->ID, 'large'); ?>');"></div>

						<?php /* Content */ ?>
						<div class="card bg-transparent h-100 p-4 border-0 rounded-0 d-flex flex-column justify-content-center n-ms-50 w-100">
							<h6 class="display d-inline text-action-1 f-14 text-center">
							<?php if ( ! empty( $the_categories ) ) { echo '<a class="text-action-1" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . ' <span class="bullet bullet-action-1 w-25"></span></a>'; } ?>
							</h6>
							<div class="main-post my-5">
								<h1 class="card-title light display-2"><a href="<?php echo get_permalink($post_item->ID) ?>" class="stretched-link"><?php echo $post_item->post_title ?></a></h1>
								<p class="card-date mt-2 mb-0">
									<?= is_sticky( $post_item->ID ) ? '<i class="bi bi-pin"></i>' : '' ?>
									<?php echo $sticky_post_date; ?>
								</p>
								<p class="card-text d-none"><?php echo $excerpt; ?></p>
							</div>
							<p class="text-action-2 pb-0 mb-0"><i class="icon icon-plus"></i> <?php esc_html_e('Read more', 'waff'); ?></p>
						</div>
					</div>
					<?php /* END: Sticky post */ ?>
					<?php
				endforeach;
			endif;

			if ( count($recent_posts) > 0 ) :
				// $oldest_post_date = get_the_date('j F Y', $recent_posts[0]->ID);
				$oldest_post_date = wp_kses(
					sprintf(
						'<time datetime="%1$s">%2$s</time>',
						esc_attr( get_the_date( DATE_W3C, $recent_posts[0]->ID ) ),
						( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'M jS Y', strtotime( get_the_date( DATE_W3C, $recent_posts[0]->ID ) )  ) : get_the_date('j F Y', $recent_posts[0]->ID)
					),
					array_merge(
						wp_kses_allowed_html( 'post' ),
						array(
							'time' => array(
								'datetime' => true,
							),
						)
					)
				);
				?>
				<?php /* BEGIN: Posts */ ?>
				<div class="col-md-3 p-4" data-aos="fade-right">
					<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_subtitle' ) ?></h6>
				</div>
				<div class="col-md-9 p-4 bold text-action-2" data-aos="fade-left">
					<?php echo $oldest_post_date; ?> <span class="bullet bullet-action-1"></span> <span class="color-action-1"><?php esc_html_e('Now', 'waff'); ?></span>
				</div>
				<?php /* END: Posts */ ?>
				<div class="col-md-12 p-0" data-aos="fade-down" data-aos-delay="400">
				<div class="list-group list-group-horizontal-lg list-group-flush rounded-0">
					<?php
					// Lightness threshold
					$lightness_threshold = 130;

					foreach( $recent_posts as $post_item ) :
						$post_color 						= rwmb_meta('_waff_bg_color_metafield', $args, $post_item->ID );
						$rgb_post_color						= waff_HTMLToRGB($post_color); // TODO , 'array' ICI BUG ? // 'array'
						$post_color_class					= 'contrast--light';
						$post_title_color 					= 'color-dark';

						// print_r($rgb_post_color);

						// Check if the color is dark or light
						if ( $post_color && $post_color != '' ) { // Si $post_color n'est pas vide
							$hsl = waff_RGBToHSL($rgb_post_color); // Accepte un INTEGER
							if($hsl->lightness < $lightness_threshold) {
								$post_color_class 			= 'contrast--dark';
								$post_title_color 			= 'color-dark';	// Here, this is the same because we do need to handle the hover state l:547
							}
						}


						$the_categories = get_the_category($post_item->ID);

						/*$excerpt = '';
						$excerpt = wp_strip_all_tags(get_the_excerpt($post_item->ID));
						if ( strlen($excerpt) > 160 ) {
							$excerpt = substr($excerpt, 0, 160);
							$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
						}*/
						$excerpt 	= get_the_excerpt($post_item->ID);
						$excerpt 	= force_balance_tags( html_entity_decode( wp_trim_words( htmlentities($excerpt), 15, '...' ) ) );

						if ( $index > $limit ) { continue; }
						?>
							<div id="post-<?php echo esc_attr($post_item->ID); ?>"
								class="<?= $post_color_class ?> <?= ( $post_color != '' && $post_color_class != 'contrast--light' )?'has-hover-background':''; ?> --vh-50 lg-vh-50 mh-380-px min-h-180-px p-4 p-sm-5 border border-start-0 border-transparent-color-silver list-group-item list-group-item-light list-group-item-action d-flex flex-column align-items-start justify-content-between">
								<div>
									<?php if ( ! empty( $the_categories ) ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>

									<?php if ( $meta == 1 ) : ?>
									<?= waff_entry_meta_header($post_item); ?>
									<?php else : ?>
									<p class="<?= $post_title_color ?> text-muted list-date mt-1 mb-0 d-none"><?php echo get_the_date('j F Y', $post_item->ID); ?></p>
									<?php endif; ?>

									<h3 class="<?= $post_title_color ?> mb-1 mt-0"><?php echo $post_item->post_title ?></h3>
									<p class="text-action-2 list-text f-14"><?php echo $excerpt; ?></p>
								</div>
								<a href="<?php echo get_permalink($post_item->ID) ?>" class="stretched-link text-action-2 pb-0 mb-0"><i class="icon icon-plus"></i> <?php esc_html_e('Read more', 'waff'); ?></a>
							</div>
						<?php if ( $post_color != '' ) : ?>
							<style> .list-group #post-<?php echo esc_attr($post_item->ID); ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; }</style>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				</div>
				<?php endif; ?>
		</div>
		</div>
	</section>
	<?php /* END: #Featured / Magazine style */ ?>
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'bold' ) :
	?>
	<?php /* #Featured / Bold style */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
		<div class="row g-0 align-items-top">
			<div class="col-md-2 p-4" data-aos="fade-right">
				<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_title' ) ?></h6>
			</div>
			<?php
			$index = 0;
			$recent_posts = get_posts($args);
			$recent_posts = array_merge($sticky_posts, $recent_posts);

			foreach( $recent_posts as $post_item ) :
				$post_id 				= esc_attr($post_item->ID);
				$post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_id );
				$post_color				= ($post_color!='')?$post_color:'#444444'; // 444444 //00ff97 > Gray blending color if no post custom color
				$rgb_post_color			= waff_HTMLToRGB($post_color, 'array'); // , 'array' ICI Bug ??
				$the_categories 		= get_the_category($post_id);
				$excerpt = '';
				$excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
				if ( strlen($excerpt) > 160 ) {
					$excerpt = substr($excerpt, 0, 160);
					$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
				}
				if ( ++$index === 1 ) {
					?>
					<?php /* First */ ?>
					<div class="col-md-5 p-0 vh-50 bg-dark img-shifted shift-right nofilter-hover" data-aos="fade-down" data-aos-delay="200">
						<?php /* Duotone filter : blended w/ custom post color */ ?>
						<?php /* https://yoksel.github.io/svg-gradient-map/ */ ?>
						<svg class="duotone-filters position-absolute" xmlns="http://www.w3.org/2000/svg">
							<filter id="duotone_featured_<?= $post_id; ?>" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
								<feColorMatrix type="matrix" values="1 0 0 0 0
							1 0 0 0 0
							1 0 0 0 0
							0 0 0 1 0" in="SourceGraphic" result="colormatrix"></feColorMatrix>
								<feComponentTransfer in="colormatrix" result="componentTransfer">
									<feFuncR type="table" tableValues="0 <?= $rgb_post_color[0]/255; ?>"/>
									<feFuncG type="table" tableValues="0 <?= $rgb_post_color[1]/255; ?>"/>
									<feFuncB type="table" tableValues="0 <?= $rgb_post_color[2]/255; ?>"/>
									<feFuncA type="table" tableValues="0 1"/>
								</feComponentTransfer>
								<feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"></feBlend>
							</filter>
						</svg>
						<?php /* Images */ ?>
						<div class="bg-image bg-cover bg-position-top-center image--origin filter--<?= $post_id; ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>');"></div>
						<div class="bg-image bg-cover bg-position-top-center image--filtered filter--<?= $post_id; ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>');"></div>
						<?php /* Content */ ?>
						<div class="card bg-transparent text-light h-100 p-4 border-0 rounded-0 d-flex flex-column justify-content-between">
							<h6 class="display d-inline --action-2 f-14 link-light">
							<?php if ( ! empty( $the_categories ) ) { echo '<a href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
							</h6>
							<div class="main-post">
								<p class="card-date text-light mt-1 mb-0">
									<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
									<?php echo get_the_date('j F Y', $post_id); ?>
								</p>
								<h2 class="card-title w-60"><a href="<?php echo get_permalink($post_id) ?>" class="stretched-link link-light"><?php echo $post_item->post_title ?></a></h2>
							</div>
							<p class="card-text"><?php echo $excerpt; ?></p>
						</div>
					</div>
					<style scoped>
						#<?= $id ?> .bg-image {
							transition: opacity .25s;
						}

						#<?= $id ?> img.filter--<?= $post_id; ?>,
						#<?= $id ?> .bg-image.filter--<?= $post_id; ?>.image--filtered  {
							-webkit-filter: url(#duotone_featured_<?= $post_id; ?>);
							-moz-filter: url(#duotone_featured_<?= $post_id; ?>);
							-o-filter: url(#duotone_featured_<?= $post_id; ?>);
							-ms-filter: url(#duotone_featured_<?= $post_id; ?>);
							filter: url(#duotone_featured_<?= $post_id; ?>);
						}

						#<?= $id ?> .nofilter-hover:hover img {
							-webkit-filter: none;
							-moz-filter: none;
							-o-filter: none;
							-ms-filter: none
							filter: none;
						}

						#<?= $id ?> .nofilter-hover:hover .bg-image.image--filtered {
							opacity:0;
						}
					</style>
					<?php /* END : First */ ?>
					<?php
					continue;
				}
				if ( $index === 2 ) {
					?>
				<div class="col-md-5 p-0 min-vh-50 min-h-50" data-aos="fade-down" data-aos-delay="400">
				<div class="list-group list-group-flush h-100 rounded-0">
					<?php /* Second */ ?>
					<a id="post-<?= $post_id; ?>" href="<?php echo get_permalink($post_id) ?>" class="list-group-item list-group-item-dark list-group-item-action d-flex flex-column align-items-start justify-content-start h-55 pr-0 overflow-hidden nofilter-hover">
						<p class="list-date text-muted mt-2 mb-0"><?php echo get_the_date('j F Y', $post_id); ?></p>
						<div class="d-flex w-100 justify-content-between ">
							<div class="second-post">
								<h6 class="normal mb-3 mt-0"><?php echo $post_item->post_title ?></h6>
								<small><?php echo $excerpt; ?></small>
							</div>
							<?php echo get_the_post_thumbnail($post_id, 'thumbnail', array( 'class' => 'img-fluid responsive float-right pl-2 max-w-50 filter--'.$post_id )); ?>
						</div>
					</a>
					<style scoped>
						.list-group a#post-<?= $post_id; ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; }
						/* #featured-<?= $id ?> .bg-image {
							transition: opacity .25s;
						}

						#featured-<?= $id ?> img.filter--<?= $post_id; ?>,
						#featured-<?= $id ?> .bg-image.filter--<?= $post_id; ?>.image--filtered  {
							-webkit-filter: url(#duotone_featured_<?= $post_id; ?>);
							-moz-filter: url(#duotone_featured_<?= $post_id; ?>);
							-o-filter: url(#duotone_featured_<?= $post_id; ?>);
							-ms-filter: url(#duotone_featured_<?= $post_id; ?>);
							filter: url(#duotone_featured_<?= $post_id; ?>);
						}

						#featured-<?= $id ?> .nofilter-hover:hover img {
							-webkit-filter: none;
							-moz-filter: none;
							-o-filter: none;
							-ms-filter: none
							filter: none;
						}

						#featured-<?= $id ?> .nofilter-hover:hover .bg-image.image--filtered {
							opacity:0;
						} */
					</style>
					<?php /* END : Second */ ?>
					<?php
					continue;
				}
				if ( $index > $limit ) { continue; }
				?>
				<a id="post-<?= $post_id; ?>" href="<?php echo get_permalink($post_id) ?>" class="third-posts list-group-item list-group-item-dark list-group-item-action d-flex flex-column align-items-start justify-content-center h-15">
						<p class="list-date text-muted mt-1 mb-0"><?php echo get_the_date('j F Y', $post_id); ?></p>
						<h6 class="normal mb-1 mt-0"><?php echo $post_item->post_title ?></h6>
				</a>
				<style scoped>
					.list-group a#post-<?= $post_id; ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; }
				</style>
			<?php endforeach; ?>

				</div>
			</div>
		</div>
	</div>
	</section>
	<?php /* END: #Featured / Bold style */ ?>
	<?php
	endif;
}
