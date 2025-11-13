<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Theme\waff_get_edition_badge as waff_get_edition_badge;

function wa_edito_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

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
	//$themeClass = 'edito mt-10 mb-10 contrast--light';
	//$themeClass = 'edito mt-10 mb-10 contrast--light fix-vh-100';
	//$marginClass = ( mb_get_block_field( 'waff_e_remove_topmargin' ) ) ? 'mt-0 mb-md-10 mb-5' : 'mt-md-10 mb-md-10 mt-5 mb-5';
	$remove_top = mb_get_block_field( 'waff_e_remove_topmargin' );
	$remove_bottom = mb_get_block_field( 'waff_e_remove_bottommargin' );

	// Build margin classes based on top/bottom removal flags
	$mt_class = $remove_top ? 'mt-0' : 'mt-md-10 mt-5';
	$mb_class = $remove_bottom ? 'mb-0' : 'mb-md-10 mb-5';
	$marginClass = $mt_class . ' ' . $mb_class;

	$themeClass = 'edito contrast--light fix-vh-100'; // Responsive issue fix
	$class = $themeClass . ' ' . $marginClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	$image = mb_get_block_field('waff_e_image');

	$hide_center_column				= (mb_get_block_field( 'waff_e_hide_center_column' ))?'1':'0';

	if ( mb_get_block_field( 'waff_e_framed' ) == 0 || mb_get_block_field( 'waff_e_framed' ) == null ) :
	?>
	<?php /* #Edito / Normal version */ ?>
	<section id="<?= $id ?>" class="fix-vh-100 <?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				<div class="<?= ( $hide_center_column != '1' )?'col-md-5':'col-md-6'; ?> d-flex flex-column justify-content-center min-h-100" data-aos="fade-right" <?= $is_preview?'style="float:left;"':''; ?>>
					<div class="p-4">
						<?php if ( mb_get_block_field( 'waff_e_editionbadge' ) == 1 ) echo waff_get_edition_badge(); ?>
					</div>
					<div class="p-4">
						<h2 class="mb-2"><?= mb_get_block_field( 'waff_e_title' ) ?></h2>
					</div>
					<div class="p-4">
						<article class="edito">
							<p class="lead mb-5"><span class="h6 headline d-inline"><?= mb_get_block_field( 'waff_e_subtitle' ) ?></span> <?= waff_do_markdown(mb_get_block_field( 'waff_e_leadcontent' )) ?></p>
							<?= waff_do_markdown(mb_get_block_field( 'waff_e_content' )) ?>
							<?php if ( mb_get_block_field( 'waff_e_morelink' ) == 1 ) : ?>
								<a class="btn btn-outline-dark mt-4" href="<?php echo esc_url( mb_get_block_field( 'waff_e_moreurl' )); ?>"><?php esc_html_e('Discover...', 'waff'); ?></a>
							<?php endif; ?>
						</article>
					</div>
				</div>
				<?php if ( $hide_center_column != '1' ) : ?><div class="col-md-2 d-none d-md-block bg-secondary" <?= $is_preview?'style="display:none;"':''; ?>></div><?php endif; ?>
				<div class="<?= ( $hide_center_column != '1' )?'col-md-5':'col-md-6'; ?> overflow-hidden bg-bgcolor" data-aos="fade-left" <?= $is_preview?'style="float:right;"':''; ?>>
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="img-shifted shift-right vh-100" <?= $is_preview?'style="margin:0;"':''; ?>>
								<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $im['full_url'] ?>');">
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
								<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
									<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
									<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
										<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
									</span>
								</figcaption>
								<?php endif; /* If captions */ ?>
								</div>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php /* END: #Edito / Normal version */ ?>
	<?php
	endif;

	if ( mb_get_block_field( 'waff_e_framed' ) == 1 ) :
	?>
	<?php /* #Edito / Framed version */ ?>
	<section id="<?= $id ?>" class="<?= ( mb_get_block_field( 'waff_e_fit' ) == 1 )?'':'fix-vh-75' ?> px-4 px-sm-8 px-md-10 <?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid border border-transparent-color-silver px-0">
			<div class="row g-0 align-items-center" <?= $is_preview?'style="border:1px solid silver;display:flex;"':''; ?>>
				<div class="col-md-6 overflow-hidden bg-bgcolor" data-aos="fade-left" <?= $is_preview?'style="float:left;"':''; ?>>
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="<?= ( mb_get_block_field( 'waff_e_fit' ) == 1 )?'':'img-shifted shift-right vh-75' ?>" <?= $is_preview?'style="margin:0;"':''; ?>>
								<?php if ( mb_get_block_field( 'waff_e_fit' ) == 1 ) : ?>
									<img class="w-100" src="<?php echo $im['full_url'] ?>" />
									<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
									<?php endif; /* If captions */ ?>
								<?php else : ?>
									<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $im['full_url'] ?>');">
									<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-color-main p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
									<?php endif; /* If captions */ ?>
									</div>
								<?php endif; ?>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="col-md-6 d-flex flex-column align-items-center justify-content-center text-center min-h-100 px-4" data-aos="fade-right" <?= $is_preview?'style="float:right;text-align: center;"':''; ?>>
					<div class="p-3">
						<?php if ( mb_get_block_field( 'waff_e_editionbadge' ) == 1 ) echo waff_get_edition_badge(); ?>
					</div>
					<div class="p-3">
						<h2 class="mb-2"><?= mb_get_block_field( 'waff_e_title' ) ?></h2>
					</div>
					<div class="p-3">
						<article class="edito">
							<p class="lead mb-5"><span class="h6 headline d-inline"><?= mb_get_block_field( 'waff_e_subtitle' ) ?></span> <?= waff_do_markdown(mb_get_block_field( 'waff_e_leadcontent' )) ?></p>
							<?= waff_do_markdown(mb_get_block_field( 'waff_e_content' )) ?>
							<?php if ( mb_get_block_field( 'waff_e_morelink' ) == 1 ) : ?>
								<a class="btn btn-outline-dark mt-4" href="<?php echo esc_url( mb_get_block_field( 'waff_e_moreurl' )); ?>"><?php esc_html_e('Discover...', 'waff'); ?></a>
							<?php endif; ?>
						</article>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php /* END: #Edito / Framed version */ ?>
	<?php
	endif;

}
