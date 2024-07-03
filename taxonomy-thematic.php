<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package Waff
 */

 get_header();

 //Go\page_title();
 
 if ( have_posts() ) {

	$current_thematic 					= get_queried_object();
	$thematic_id 						= get_queried_object_id();
	$current_thematic_name 				= $current_thematic->name;
	$current_thematic_slug 				= $current_thematic->slug;
	$current_thematic_count 			= $current_thematic->count;	
	// print_r($current_thematic);

	$thematic_description 				= term_description($thematic_id);
	$t_general_content 					= get_term_meta( $thematic_id, 't_general_content', true ); 
	$t_general_image 					= get_term_meta( $thematic_id, 't_general_image', true ); 
	$t_general_image_meta 				= WaffTwo\Core\waff_get_attachment($t_general_image);
	$t_general_image_url 				= wp_get_attachment_image_url(
		$t_general_image,
		'large' // full
	);
	$t_general_image_thumbnail_url 		= wp_get_attachment_image_url(
		$t_general_image,
		'thumbnail' // medium = 300x300
	);

	$t_general_color 					= get_term_meta( $thematic_id, 't_general_color', true ); 
	$thematic_bgcolor					= (( $t_general_color != '' )?'style="background-color:'.$t_general_color.'"':'style="background-color:var(--waff-color-accent-2);"');

	?> 
	<!-- Header -->
	<section class="mt-n10 --mb-10 --mb-lg-7 contrast--dark f-w" id="thematichero">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center h-550-px"><!-- .vh-100 hack-->

				<div class="col-lg overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-5 ps-6 pt-20 aos-init aos-animate" data-aos="fade-left" <?= $thematic_bgcolor?>>
						
						<hgroup>
							<h6 class="subline text-white">Thématique</h6>						
							<?php if ( strlen(strip_tags($current_thematic_name)) > 0 ) : ?>
							<h3 class="my-3 text-white border-white border-2 border rounded-4 p-2"><?php echo WaffTwo\Core\waff_do_markdown( WaffTwo\Core\waff_clean_alltags($current_thematic_name) ); ?></h3>
							<?php endif; ?>
							<!-- <div class="thematic-list d-inline-block"><a class="thematic-item" tabindex="-1">Sous-thématique ?</a></div> -->
						</hgroup>
						
						<div class="text-white">
							<?php if ( strlen(strip_tags($thematic_description)) > 0 ) : ?>
							<p class="fw-bold text-white"><?php echo WaffTwo\Core\waff_do_markdown( WaffTwo\Core\waff_clean_alltags($thematic_description) ); ?></p>
							<?php endif; ?>
						</div>
						
						<!-- Begin: CTA no shadow  -->
						<div class="d-flex align-items-center justify-content-center py-4 px-5 bg-body rounded-4 shadow d-none">
							<div class="d-flex align-items-center">
								<i class="bi bi-bootstrap flex-shrink-0 me-3 h2 text-action-1"></i>
								<div>
									<h6 class="fw-bold text-action-1">Lorem ipsum</h6>
									<p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
								</div>
							</div>

							<div class="d-flex align-items-center justify-content-center px-5">
								<span class="bullet bullet-action-2 ml-0"></span>
							</div>

							<div class="d-flex align-items-center">
								<i class="bi bi-bootstrap flex-shrink-0 me-3 h2"></i>
								<div>
									<h6 class="fw-bold">Lorem ipsum</h6>
									<p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
								</div>
							</div>
						</div>
						<!-- End: CTA -->

					</div>

					<!-- Image -->
					<?php if ( $t_general_image ) : ?>
						<div class="col-lg-7 bg-color-layout h-100 ---- img-shifted shift-right aos-init aos-animate" data-aos="fade-down" data-aos-delay="200">
							<figure class="" id="<?= $t_general_image ?>">
								<picture class="lazy" data-src="<?= $t_general_image_url ?>">
									<data-src media="(min-width: 150px)" srcset="<?= $t_general_image_url; ?>" type="image/jpeg"></data-src>
									<data-img src="<?= $t_general_image_thumbnail_url; ?>" alt="<?= esc_html($t_general_image_meta['alt']); ?>" class="img-fluid --rounded-4 rounded-top-4 rounded-top-left-0 --shadow-lg h-550-px fit-image w-100" style="" title="<?= $t_general_image_meta['title']; ?>"></data-img>
								</picture>
								<?php if ( $t_general_image_meta['alt'] || $t_general_image_meta['caption'] || $t_general_image_meta['description'] ) : ?>
								<figcaption><strong>© <?= esc_html($t_general_image_meta['alt']); ?></strong> <?= esc_html($t_general_image_meta['caption']); ?> <?= esc_html($t_general_image_meta['description']); ?></figcaption>
								<?php endif; /* If captions */ ?>
							</figure>
						</div>
					<?php endif; ?>
			
					<!-- Mouse down -->
					<!-- <div class="scroll-downs position-absolute bottom-0 start-45 mb-4">
						<div class="mousey">
							<div class="scroller"></div>
						</div>
					</div> -->

			</div>
		</div>
	</section>

	<!-- Parent and child terms --> 
	<?php 
	$all_child_terms = WaffTwo\get_all_child_terms($thematic_id, 'thematic');

	// Display all descendant terms
	if (!empty($all_child_terms) && !is_wp_error($all_child_terms)) {
		echo '<h6 class="subline mt-5 mb-0">Les sous-thématiques</h6><div class="d-flex flex-wrap gap-3 my-3 attribute-list">'; //@TODO à traduire
		foreach ($all_child_terms as $child_term) {
			printf('<a href="%s" class="fs-4 m-0 text-white border-0 rounded-4 px-3 py-2 d-inline-block" %s>%s</a>',
				esc_url(get_term_link($child_term)),
				$thematic_bgcolor,
				esc_html($child_term->name),
			);
		}
		echo '</div>';
	}
	?> 

	<?php 
	$all_parent_terms = WaffTwo\get_all_parent_terms($thematic_id, 'thematic');

	// Display all descendant terms
	if (!empty($all_parent_terms) && !is_wp_error($all_parent_terms)) {
		echo '<h6 class="subline mt-5 mb-0">Les thématiques</h6><div class="d-flex flex-wrap gap-3 my-3 attribute-list">'; //@TODO à traduire
		foreach ($all_parent_terms as $parent_term) {
			printf('<a href="%s" class="fs-4 m-0 text-white border-0 rounded-4 px-3 py-2 d-inline-block" %s>%s</a>',
				esc_url(get_term_link($parent_term)),
				$thematic_bgcolor,
				esc_html($parent_term->name),
			);
		}
		echo '</div>';
	}
	?> 


	<!-- Content -->
	<?php if ( strlen(strip_tags($t_general_content)) > 0 ) : ?>
		<div class="content taxonomy-content mt-5 mb-10">
		<?php echo apply_filters('the_content', WaffTwo\Core\waff_do_markdown($t_general_content)); ?>
		</div>
	<?php endif; ?>

	<!-- Related posts -->
	<section class="contrast--light f-w">
	<div class="container-fluid --px-0">
	<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mt-8">
	<?php
	 // Start the Loop.
	 while ( have_posts() ) :
		 the_post();
		 ?>
		<div class="col">
			<?php get_template_part( 'partials/content', 'excerpt' ); ?>
		</div>
		 <?php
	 endwhile;
	 // End the Loop.
	 ?>
	</div>
	</div>
	</section>
 
	<?php
	 // Previous/next page navigation.
	 get_template_part( 'partials/pagination' );
 
 } else {
 
	 // If no content, include the "No posts found" template.
	 get_template_part( 'partials/content', 'none' );
 }
 
 get_footer();
 