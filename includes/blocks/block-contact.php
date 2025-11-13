<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;

function wa_contact_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	if ( $is_preview === true ) {
		?>
		<section style="text-align: center; padding-left: 20%; padding-right: 20%;">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-contact.svg" class="img-fluid" />
		</section>
		<?php
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
	$themeClass = 'contact mb-10 container-fluid contrast--light';
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

	$gallery = mb_get_block_field( 'waff_c_gallery' );
	$images = array();
	$index = 0;

	foreach($gallery as $im) {
		if ( $im['full_url' ] != '')
			$images[] = $im;
		else
			$images[] = $images[$index-1];
		$index++;
	}

	?>
	<?php /* #Contact */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> mt-0 mb-0 bg-bgcolor" style="height: 100%;">
		<div class="row f-w g-0">
				<div class="col-8 bg-primary" style="height: 540px;">

					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[0]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[0]['alt'] || $images[0]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[0]['alt']); ?></strong> <?= esc_html($images[0]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>
					</figure>

				</div>
				<div class="col-4"></div>

				<div class="col-4"></div>
				<div class="col-4"></div>
				<div class="col-4 bg-action-1" style="height: 780px;margin-top: -250px;">

					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[1]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[1]['alt'] || $images[1]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[1]['alt']); ?></strong> <?= esc_html($images[1]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>
					</figure>
					<?php /*  <figure class="img-shifted shift-right h-100">
						<img src="<?php echo $images[1]['sizes']['post-featured-image-m'] ?>"
							 srcset="<?php echo $images[1]['srcset'] ?>"
							 alt="<?= esc_html($images[1]['alt']); ?>"$
							 class="img-fluid h-100" style="object-fit: cover; height: 100%;"
						>
						<?php $im['alt'] = 'DR'; if ( $images[1]['alt'] || $images[0]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[1]['alt']); ?></strong> <?= esc_html($images[1]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>
					</figure>  */ ?>

				</div>

				<div class="col-4 bg-action-3" style="height: 540px;margin-top: -160px;">
					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[2]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[2]['alt'] || $images[2]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[2]['alt']); ?></strong> <?= esc_html($images[2]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>
					</figure>
				</div>
				<div class="col-4"></div>
				<div class="col-4"></div>


				<div class="col-4"></div>
				<div class="col-8 bg-secondary" style="height: 540px;">
					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[3]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[3]['alt'] || $images[3]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[3]['alt']); ?></strong> <?= esc_html($images[3]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>
					</figure>
				</div>
		</div>
	</section>

	<?php /* Begin: Contact content */ ?>
	<section id="contact-section-<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="margin-top: -1820px; position: relative;">
		<div class="row f-w">
				<div class="col-10 col-md-8 offset-1 offset-md-2 p-4 p-md-5 <?= mb_get_block_field( 'waff_c_first_color_class' ) && mb_get_block_field( 'waff_c_first_color_class' ) !== '' ? mb_get_block_field( 'waff_c_first_color_class' ) : 'bg-action-2' ?> <?= mb_get_block_field( 'waff_c_rounded' ) ? 'rounded-top-4' :'' ?>" style="height: 370px;">
					<div class="row">
						<div class="col-12 col-md-6 ">
							<h2 class="heading-4 mb-5 mb-md-0"><?= waff_do_markdown(mb_get_block_field( 'waff_c_first_title' )) ?></h2>
						</div>
						<div class="col-12 col-md-6">
							<div class="row">
								<?= mb_get_block_field( 'waff_c_first_content' ); ?>
							</div>
						</div>
					</div>

				</div>
				<div class="col-10 col-md-8 offset-1 offset-md-2 p-4 p-md-5 <?= mb_get_block_field( 'waff_c_second_color_class' ) && mb_get_block_field( 'waff_c_second_color_class' ) !== '' ? mb_get_block_field( 'waff_c_second_color_class' ) : 'bg-secondary' ?>" style="height: 370px;">

					<div class="row">
						<div class="col-12 col-md-6 ">
							<h2 class="heading-4 mb-5 mb-md-0"><?= waff_do_markdown(mb_get_block_field( 'waff_c_second_title' )) ?></h2>
						</div>
						<div class="col-12 col-md-6">
							<div class="row">
								<?= mb_get_block_field( 'waff_c_second_content' ); ?>
							</div>
						</div>
					</div>


				</div>
				<div class="col-10 col-md-8 offset-1 offset-md-2 <?= mb_get_block_field( 'waff_c_form_color_class' ) ?: 'bg-light' ?> p-4 p-md-5 <?= mb_get_block_field( 'waff_c_rounded' ) ? 'rounded-bottom-4' :'' ?>" style="height: 740px;">
					<?php
					$form_id = mb_get_block_field( 'waff_c_form' );
					$ws_form_id = mb_get_block_field( 'waff_c_ws_form' );
					if ( $form_id ) {
						echo do_shortcode('[gravityform id="'.$form_id.'" title="false" description="false" ajax="true" field_values=""]');
					}
					if ( $ws_form_id ) {
						echo do_shortcode('[ws_form id="'.$ws_form_id.'"]');
					}
					?>
				</div>
		</div>
	</section>
	<div class="clear clearfix"></div>
	<?php /* End: Contact content */ ?>

	<?php /* END: #Contact */ ?>
	<?php

}
