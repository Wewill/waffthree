<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Blocks\waff_get_blocks_transition as waff_get_blocks_transition;
use function WaffTwo\Theme\waff_get_theme_homeslide_background as waff_get_theme_homeslide_background;

function wa_cols_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;
	// print_r($is_preview);

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
	$themeClass = 'cols mt-10 mb-0 contrast--dark text-white';
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

	// Image
	$image 			= mb_get_block_field('waff_c_image');
	$im 			= ( !empty($image) ) ? reset( $image ) : false;

	// Background image
	$bg_images 		= waff_get_blocks_transition();
	$bg_image 		= ( !empty($bg_images) ) ? reset( $bg_images ) : false;

	// Homeslide background image
	$homeslide_images = waff_get_theme_homeslide_background();
	$homeslide_image = ( !empty($homeslide_images) ) ? reset($homeslide_images) : false;

	// Get gradient color
	$gradient_color = '';
	if( mb_get_block_field( 'waff_c_gradient_color_class' ) ) {
		$gradient_color = mb_get_block_field( 'waff_c_gradient_color_class' );
	} else {
		$gradient_color = 'action-2';
	}

	?>
	<?php /* #cols */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">

		<?php if ( $bg_image && is_array($bg_image) ) : ?>
		<figure class="m-0 p-0 overflow-hidden mb-n1 z-2" <?= $is_preview ? 'style="display:none;"' : '' ?>>
			<picture class="">
				<img src="<?= $bg_image['url']; ?>" alt="Image de transition" class="img-fluid fit-image w-100">
			</picture>
		</figure>
		<?php endif; ?>

		<div class="container-fluid p-2 p-md-8 z-2 position-relative <?= mb_get_block_field( 'waff_c_image_style' ) === 'full' ? ( $bg_image && is_array($bg_image) ? 'bg-v-plain-gradient-'.$gradient_color : 'bg-v-gradient-'.$gradient_color ) : 'mask no-gradient' ?>" style="<?= !$is_preview ? '' : 'color:white; background-color:var(--go--color--secondary, --wp--preset--color--secondary);' ?>">
			<div class="row mb-10 <?= $bg_image ? 'mt-5' : '' ?>">
				<div class="col-4"></div>
				<div class="col-4 text-center">
					<h6 class="subline <?= mb_get_block_field( 'waff_c_subtitle_class' ) ?>" style="<?=!$is_preview ?: 'color:white;' ?>"><?= mb_get_block_field( 'waff_c_subtitle' ) ?></h6>
					<h2 class="text-white" style="<?= !$is_preview ?: 'color:white;' ?>"><?= mb_get_block_field( 'waff_c_title' ) ?></h2>
				</div>
				<div class="col-4 d-flex align-items-start justify-content-end">
					<?php if ( mb_get_block_field( 'waff_c_morelink' ) == 1 ) : ?>
					<a class="btn btn-action-3 btn-lg btn-transition-scale" href="<?php echo esc_url( mb_get_block_field( 'waff_c_moreurl' )); ?>"><?php esc_html_e( 'More...', 'waff' ); ?></a>
					<?php endif; ?>
				</div>
				<?php if (mb_get_block_field( 'waff_c_leadcontent' ) != "") {
					echo '<div class="col-12"><p class="lead mb-4 text-center fw-bold">'.waff_do_markdown(mb_get_block_field( 'waff_c_leadcontent' )).'</p></div>';
				} ?>
			</div>
			<div class="row <?= $bg_image && is_array($bg_image) ? 'mb-15' : 'mb-5' ?> m-gutter-l m-gutter-r" <?= $is_preview ? 'style="display:flex;"' : ''; ?>>
				<?php
				$i = 0;
				foreach( mb_get_block_field( 'waff_c_contents' ) as $content ) :
					echo '<div class="col" style="' .( $is_preview ? 'margin-right: 10px;' : '' ). '" data-aos="flip-down" data-aos-delay="'.($i*200).'" ><div class="lead">'.waff_do_markdown($content).'</div></div>';
					$i++;
				endforeach;
				?>
			</div>
			<?php if ( $im && is_array($im) && !empty($im[0]) ) : ?>
			<figure class="bg-image h-100 m-0 position-absolute">
				<?php $im[0]['alt'] = 'DR'; if ( $im[0]['alt'] || $im[0]['description'] || wp_get_attachment_caption($im[0]['ID']) ) : ?>
					<?php /* <figcaption> */ ?>
					<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2 zi-max">
						<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id  ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id  ?>">©</span>
						<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id  ?>">
							<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im[0]['ID']) ? wp_get_attachment_caption($im[0]['ID']) : esc_html($im[0]['alt'] ? $im[0]['alt'] : 'DR'); ?></strong> <?= esc_html($im[0]['description']); ?></span>
						</span>
					</figcaption>
				<?php endif; /* If captions */ ?>
			</figure>
			<?php endif; ?>
		</div>

		<?php /* Background image */ ?>
		<?php if ( $im && is_array($im) && !$is_preview ) : ?>
			<figure class="overflow-hidden h-100 w-100 position-absolute top-0 start-0 z-0" <?=  $bg_image && is_array($bg_image) ? 'style="height: calc(100% - 112px);  margin-top: 112px;"':'' ?>>
				<picture class="">
						<img src="<?= $im['full_url'] ?>" alt="<?= $im['alt'] ?>" class="img-fluid fit-image h-100 w-100">
				</picture>
			</figure>
		<?php endif; ?>

		<?php /* Background image mask : special GOLFS */ ?>
		<?php if ( $homeslide_image && mb_get_block_field( 'waff_c_image_style' ) === 'masked' ) : ?>
			<div class="position-absolute top-50 start-0 translate-middle-y h-100 w-100 no-drag d-flex">
				<div class="bg-<?= $gradient_color; ?> w-50"></div>
				<img class="h-100 bg-cover bg-position-center-center img-fluid no-drag" src="<?= $homeslide_image['url']; ?>" />
			</div>
		<?php endif; ?>


	</section>
	<?php /* END: #cols */ ?>
	<?php
}
