<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Blocks\waff_get_blocks_background as waff_get_blocks_background;


function wa_mission_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;
	//print_r($is_preview);

	// if ( $is_preview )
	// 	print_r($attributes);

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
	if ( mb_get_block_field( 'waff_m_blockmargin' ) == 1 ) {
		$blockmargin = 'mt-lg-10 mb-lg-10 mt-5 mb-5';
	} else {
		$blockmargin = 'mt-n10 mb-0';
	}

	$themeClass = 'mission '.$blockmargin.' pt-10 pb-10 contrast--light bg-image bg-cover bg-position-center-center position-relative';
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
	$image 					= mb_get_block_field('waff_m_image');

	// No image no render in preview.
	if ( $is_preview && ( empty($image) || count($image) === 0 ) ) {
		echo '<div class="alert alert-dismissible alert-error fade show" role="alert"><strong>Heads up!</strong> Please select at least one image for this block. <button aria-label="Close" class="btn-close" data-dismiss="alert" type="button"></button></div>';
		return;
	}

	// Background color
	$bg_color_aligned 		= mb_get_block_field('waff_m_bg_color') ? mb_get_block_field('waff_m_bg_color') : 'bg-action-1';
	$bg_color_shifted 		= mb_get_block_field('waff_m_bg_color') ? mb_get_block_field('waff_m_bg_color') : 'bg-action-2';

	// print_r(mb_get_block_field('waff_m_alignment'));
	// print_r(mb_get_block_field('waff_m_position'));
	// Alignment
	switch(mb_get_block_field('waff_m_alignment')) {
		case 'aligned':
			$r_alignment 	= '';
			$b_alignment 	= $bg_color_aligned . ' col-12 col-lg-4 --bg-action-1 h-850-px h-lg-850-px'; // col-4
			//$f_alignment 	= 'align-items-end';
			$i_alignment 	= 'h-700-px h-lg-700-px'; // h-700-px
			break;
		case 'shifted':
			$r_alignment 	= 'vh-100';
			$b_alignment 	= $bg_color_shifted . 'col-lg-5 col-xl-5 --bg-action-2 vh-75';
			$f_alignment 	= 'lg-vh-75 h-100'; // h-100 pose soucis.
			$i_alignment 	= 'vh-75 --h-100';
			break;
	}

	// Position
	switch(mb_get_block_field('waff_m_position')) {
		case 'top':
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-lg-end align-items-start' : 'align-items-start';
			$f_position 	= 'top-0';
			$aos_position 	= 'fade-up';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-start'; }
			break;
		case 'center':
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-lg-center align-items-start' : 'align-items-end';
			$f_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'top-50 end-0 translate-middle-y lg-transform-0' : '--bottom-0 top-50 translate-middle-y';
			$aos_position 	= 'fade-up';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-center'; }
			break;
		case 'bottom':
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-start' : 'align-items-end';
			$f_position 	= 'bottom-0';
			$aos_position 	= 'fade-down';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-end'; }
			break;
	}

	// Responsive
	$b_position 	.= ' ---- position-absolute position-lg-relative top-0 left-0 w-100';

	// Background image
	$bg_images 		= waff_get_blocks_background();
	$bg_image 		= ( !empty($bg_images) ) ? reset( $bg_images ) : false;

	?>
	<?php /* #Mission */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>; background-image: url('<?= $bg_image['url']; ?>');">
		<div class="container-fluid px-0 position-relative">
			<div class="row g-0 <?= $b_position; ?> <?= $r_alignment; ?>" <?= $is_preview ? 'style="display:none;"' : '' ?>>
				<div class="<?= $b_alignment; ?> <?= $attributes['align'] === 'full' ? 'rounded-end-4':'rounded-4'; ?>" --data-aos="fade-left" --data-aos-delay="100"></div>
			</div>
			<div class="row <?= $f_alignment; ?> w-100 ---- position-lg-absolute <?= $f_position; ?> left-0">
				<?php /* Col 1 */ ?>
				<div class="col-2 d-none d-lg-block" ---data-aos="fade-left"></div>

				<?php /* Col 2 */ ?>
				<?php /* Figure */ ?>
				<?php if ( count($image) > 0 ) : ?>
					<?php foreach ( $image as $im ) : ?>
						<?php if ( ! $is_preview ) : ?>
						<figure class="col-10 col-lg-4 p-0 rounded-4 contrast--light <?= $i_alignment; ?> overflow-hidden position-relative mb-10 --mb-md-10 mb-lg-0" data-aos="<?= $aos_position; ?>" data-aos-delay="200" style="<?= $is_preview ? 'float:left; width:49%;' : '' ?>">
							<picture class="">
								<img src="<?= $im['full_url'] ?>" alt="<?= esc_html($im['alt']); ?>" class="img-fluid rounded-4 <?= $i_alignment; ?> fit-image w-100 img-transition-scale">
							</picture>
							<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
								<?php /* figcaption */ ?>
								<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
									<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
									<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
										<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
									</span>
								</figcaption>
							<?php endif; /* If captions */ ?>
						</figure>
						<?php else: /* If is_preview */ ?>
							<img src="<?= $im['sizes']['medium']['url'] ?>" alt="<?= esc_html($im['alt']); ?>" class="" style="border-radius:1rem;">
						<?php endif; /* If is_preview */ ?>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php /* Col 3 */ ?>
				<?php /* Begin: Content */ ?>
				<div class="col-12 col-lg-6 col-xl-5 ps-5 d-flex flex-column justify-content-between --align-items-end --- mt-10 mt-sm-0" data-aos="fade-left" data-aos-delay="400" style="<?= $is_preview ? 'float:right; width:49%;' : '' ?>">
					<div>
						<h6 class="subline <?= mb_get_block_field( 'waff_m_subtitle_class' ) ?>"><?= mb_get_block_field( 'waff_m_subtitle' ) ?></h6>
						<h2><?= mb_get_block_field( 'waff_m_title' ) ?></h2>
						<p class="lead mb-3"><?= waff_do_markdown(mb_get_block_field( 'waff_m_leadcontent' )) ?></p>
						<?= waff_do_markdown(mb_get_block_field( 'waff_m_content' )) ?>
					</div>

					<div>
						<div class="row row-cols-2 row-cols-sm-3 row-cols-lg-2 g-4 py-5" style="<?= $is_preview ? 'display:grid;grid-template-columns: repeat(2, 1fr);grid-template-rows: repeat(2, 1fr);gap: 5px;' : '' ?>">
							<?php
							foreach( mb_get_block_field( 'waff_m_lists' ) as $list ) :
								echo sprintf('<div class="col d-flex align-items-center">
									<i class="%s flex-shrink-0 me-3 h4"></i>
									<div>
										<h6 class="fw-bold">%s</h6>
										<p>%s</p>
									</div>
								</div>',
								$list[2],
								$list[0],
								$list[1]
								);
							endforeach;
							?>
						</div>

						<?php if ( mb_get_block_field( 'waff_m_morelink' ) == 1 ) : ?>
						<a class="btn btn-action-2 btn-lg btn-transition-scale" href="<?php echo esc_url( mb_get_block_field( 'waff_m_moreurl' )); ?>"><?php esc_html_e( 'More...', 'waff' ); ?></a>
						<?php endif; ?>

					</div>
				</div>
				<?php /* End: Content */ ?>

				<?php if ($is_preview) { echo '<div class="clear clearfix"></div>'; } ?>
			</div>
		</div>
	</section>
	<?php /* END: #Mission */ ?>
	<?php

}
