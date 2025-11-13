<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;

function wa_breaking_callback( $attributes ) {
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
	if ( mb_get_block_field( 'waff_b_blockmargin' ) == 1 ) {
		$blockmargin = 'mt-0 mb-lg-10 mb-5';
	} else {
		$blockmargin = 'mt-0 mb-0';
	}

	$themeClass = 'breaking '.$blockmargin.' contrast--dark';
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
	$image_1 					= mb_get_block_field('waff_b_image_1');
	$image_2 					= mb_get_block_field('waff_b_image_2');

	// Get gradient style
	switch( mb_get_block_field( 'waff_b_gradient_style' ) ) {
		case 'inverse':
			$gradient_style = 'bg-v-inverse-gradient';
			break;
		case 'smooth':
			$gradient_style = 'bg-v-smooth-gradient';
			break;
		case 'plain':
			$gradient_style = 'bg-v-plain-gradient';
			break;
		case 'vertical':
			$gradient_style = 'bg-v-gradient';
			break;
		case 'horizontal':
			$gradient_style = 'bg-gradient';
			break;
		case 'transparent':
			$gradient_style = 'bg-transparent';
			break;
		case 'no':
			$gradient_style = '';
			break;
		default:
			$gradient_style = 'bg-v-inverse-gradient';
			break;
	}

	// Get gradient color
	$gradient_color = '';
	if( mb_get_block_field( 'waff_b_gradient_color_class' ) ) {
		$gradient_color = mb_get_block_field( 'waff_b_gradient_color_class' );
	} else {
		$gradient_color = 'action-2';
	}

	?>
	<?php /* #Breaking */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">

				<div class="col-md-6 h-500-px bg-color-layout img-shifted --shift-right rounded-bottom-4 rounded-bottom-right-0 md-rounded-0" data-aos="fade-down" data-aos-delay="0" style="<?=!$is_preview ? '' : 'display:inline-block; width:49%' ?>">

					<?php /* Figure */ ?>
					<figure class="bg-image h-100 m-0 position-absolute">
					<?php if ( count($image_1) > 0 && !$is_preview ) : ?>
						<?php foreach ( $image_1 as $im ) : ?>
								<div class="bg-image bg-cover bg-position-center-center z-0" style="background-image: url('<?= $im['full_url'] ?>');"></div>
								<div class="bg-image <?= $gradient_style; ?>-<?= $gradient_color; ?> z-1"></div>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<?php /* figcaption */ ?>
									<figcaption class="top-0 bottom-auto d-flex align-items-center bg-transparent pt-2 ps-2 zi-5">
										<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapseA-<?= $id  ?>" role="button" aria-expanded="false" aria-controls="collapseA-<?= $id  ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapseA-<?= $id  ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</figure>

					<div class="card bg-transparent border-0 text-white --h-100 px-4 py-4 px-md-8 py-md-6 d-flex flex-column justify-content-between align-items-start z-2 <?= $is_preview ? '' : 'h-100' ?>">
						<h6 class="subline d-inline <?= mb_get_block_field( 'waff_b_label_1_class' ) ?>"><?= mb_get_block_field( 'waff_b_label_1' ) ?></h6>
						<div class="w-100">
							<div class="w-100 w-lg-50">
								<p class="card-date fw-bold <?= mb_get_block_field( 'waff_b_subtitle_1_class' )?mb_get_block_field( 'waff_b_subtitle_1_class' ):'text-transparent-color-layout'; ?> mt-1 mb-0"><?= mb_get_block_field( 'waff_b_subtitle_1' ) ?></p>
								<h2 class="card-title"><a href="#" class="stretched-link link-white"><?= mb_get_block_field( 'waff_b_title_1' ) ?></a></h2>
							</div>
							<div class="card-text fw-bold"><?= waff_do_markdown(mb_get_block_field( 'waff_b_content_1' )) ?></div>
							<?php if ( mb_get_block_field( 'waff_b_morelink_1' ) == 1 ) : ?>
							<a class="btn btn-action-3 btn-lg mt-4 btn-transition-scale" href="<?php echo esc_url( mb_get_block_field( 'waff_b_moreurl_1' )); ?>"><?php esc_html_e( 'More...', 'waff' ); ?></a>
							<?php endif; ?>
						</div>
					</div>


				</div>
				<div class="col-md-6 h-500-px bg-color-layout img-shifted --shift-right rounded-bottom-4 rounded-bottom-left-0 md-rounded-bottom-4" data-aos="fade-down" data-aos-delay="400" style="<?=!$is_preview ? '' : 'display:inline-block; width:49%' ?>">

					<?php /* Figure */ ?>
					<figure class="bg-image h-100 m-0 position-absolute">
					<?php if ( count($image_2) > 0 && !$is_preview ) : ?>
						<?php foreach ( $image_2 as $im ) : ?>
								<div class="bg-image bg-cover bg-position-center-center z-0" style="background-image: url('<?= $im['full_url'] ?>');"></div>
								<div class="bg-image <?= $gradient_style; ?>-<?= $gradient_color; ?> z-1"></div>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<?php /* figcaption */ ?>
									<figcaption class="top-0 bottom-auto d-flex align-items-center bg-transparent pt-2 ps-2 zi-5">
										<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapseB-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapseB-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapseB-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</figure>

					<div class="card bg-transparent border-0 text-white --h-100 px-4 py-4 px-md-8 py-md-6 d-flex flex-column justify-content-between align-items-start z-2 <?= $is_preview ? '' : 'h-100' ?>">
						<h6 class="subline d-inline <?= mb_get_block_field( 'waff_b_label_2_class' ) ?>"><?= mb_get_block_field( 'waff_b_label_2' ) ?></h6>
						<div class="w-100">
							<div class="w-100 w-lg-50">
								<p class="card-date fw-bold <?= mb_get_block_field( 'waff_b_subtitle_2_class' )?mb_get_block_field( 'waff_b_subtitle_2_class' ):'text-transparent-color-layout'; ?> mt-1 mb-0"><?= mb_get_block_field( 'waff_b_subtitle_2' ) ?></p>
								<h3 class="card-title"><a href="#" class="stretched-link link-white"><?= mb_get_block_field( 'waff_b_title_2' ) ?></a></h3>
							</div>
							<div class="card-text fw-bold"><?= waff_do_markdown(mb_get_block_field( 'waff_b_content_2' )) ?></div>
							<?php if ( mb_get_block_field( 'waff_b_morelink_2' ) == 1 ) : ?>
							<a class="btn btn-action-3 btn-lg mt-4 btn-transition-scale" href="<?php echo esc_url( mb_get_block_field( 'waff_b_moreurl_2' )); ?>"><?php esc_html_e( 'More...', 'waff' ); ?></a>
							<?php endif; ?>
						</div>
					</div>


				</div>

			</div>
		</div>
	</section>
	<?php /* END: #Breaking */ ?>
	<?php

}
