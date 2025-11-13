<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Blocks\waff_get_blocks_pattern as waff_get_blocks_pattern;

function wa_insights_callback( $attributes ) {
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
	if ( mb_get_block_field( 'waff_i_blockmargin' ) == 1 ) {
		$blockmargin = 'mt-lg-10 mb-lg-10 mt-5 mb-5';
	} else {
		$blockmargin = 'mt-0 mb-0';
	}

	$themeClass = 'insights '.$blockmargin.' contrast--light bg-image bg-cover bg-position-center-center position-relative';
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
	$image 					= mb_get_block_field('waff_i_image');

	// // Background image
	// $bg_images = WaffTwo\Blocks\waff_get_blocks_background();
	// $bg_image = reset( $bg_images );

	// Pattern image
	$pat_images = waff_get_blocks_pattern();
	$pat_image 		= ( !empty($pat_images) ) ? reset( $pat_images ) : false;

	// Background color
	$bg_color 		= mb_get_block_field('waff_i_bg_color') ? mb_get_block_field('waff_i_bg_color') : 'bg-color-layout';

	?>
	<?php /* #Insights */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">
		<div class="container-fluid px-0 position-relative">
			<div class="row">
				<div class="col-12 col-lg-8 ps-4 pe-4 ps-lg-10 pe-lg-10" ---data-aos="fade-left" style="<?= !$is_preview ?: 'display:inline-block; width:49%;' ?>">

					<h6 class="subline <?= mb_get_block_field( 'waff_i_subtitle_class' ) ?>"><?= mb_get_block_field( 'waff_i_subtitle' ) ?></h6>
					<hgroup class="pt-8 pb-4 d-flex justify-content-between align-items-center">
						<h2><?= mb_get_block_field( 'waff_i_title' ) ?></h2>
						<?php if ( mb_get_block_field( 'waff_i_morelink' ) == 1 ) : ?>
						<a class="btn btn-action-2 btn-lg btn-transition-scale" href="<?php echo esc_url( mb_get_block_field( 'waff_i_moreurl' )); ?>"><?php esc_html_e( 'More...', 'waff' ); ?></a>
						<?php endif; ?>
					</hgroup>

					<p class="lead mb-3"><?= waff_do_markdown(mb_get_block_field( 'waff_i_leadcontent' )) ?></p>

					<div class="row row-cols-1 row-cols-md-3 mb-3 text-center" <?= $is_preview ? 'style="display:flex;"' : ''; ?>>
						<?php
						foreach( mb_get_block_field( 'waff_i_lists' ) as $list ) :
							echo sprintf('<div class="col">
								<div class="card mb-4 rounded-3 --shadow-sm border-0 text-start %s text-color-main">
									%s
									<div class="card-body">
										<h1 class="card-title %s %s %s">%s<small class="text-body-secondary fw-light">%s</small></h1>
										<p class="mt-3 mb-2">%s</p>
										%s
									</div>
								</div>
							</div>',
							$list[4]?$list[4]:'bg-color-layout',
							$list[0]?'<div class="card-header py-3"><h4 class="my-0 fw-normal">'.$list[0].'</h4></div>':'',
							$list[4]?'heading-2 mt-2':'',
							$list[6]?$list[6]:'', // TextColor
							$list[7]?$list[7]:'fw-medium', // FontWeight
							$list[1],
							$list[2],
							$list[3],
							$list[5]?'<a href="'.$list[5].'" class="w-100 btn btn-lg '.($list[4]=='bg-action-1'?'btn-outline-light':'btn-primary').'">En savoir plus...</a>':''
							);
						endforeach;
						?>
					</div>

				</div>

				<div class="col-12 col-lg-4 <?= $bg_color; ?> --bg-color-layout <?= $attributes['align'] === 'full' ? 'rounded-start-4':'rounded-4'; ?> d-flex align-items-end justify-content-end ---- bg-position-center-center bg-repeat" ---data-aos="fade-left" style="<?= !$is_preview ?: 'display:inline-block; width:49%;' ?> background-image: url('<?= $pat_image['url']; ?>');">

					<?php /* Figure */ ?>
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="p-0 <?= $attributes['align'] === 'full' ? 'rounded-start-4':'rounded-4'; ?> contrast--light h-80 w-80 overflow-hidden" data-aos="fade-left" data-aos-delay="200">
								<picture class="">
									<img src="<?= $im['full_url'] ?>" alt="<?= esc_html($im['alt']); ?>" class="img-fluid rounded-4 w-100 h-100 fit-image img-transition-scale">
								</picture>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<?php /* <figcaption> */ ?>
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>

				</div>

			</div>
		</div>
	</section>
	<?php /* END: #Insights */ ?>
	<?php

}
