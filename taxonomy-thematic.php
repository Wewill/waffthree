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
	$thematic_content 					= get_term_meta( $thematic_id, 't_general_content', true ); 
	$thematic_image 					= get_term_meta( $thematic_id, 't_general_image', true ); 
	$thematic_color 					= get_term_meta( $thematic_id, 't_general_color', true ); 
	$thematic_bgcolor					= (( $thematic_color != '' )?'style="background-color:'.$thematic_color.'"':'');


	?> 
	<!-- Header -->
	<section class="mt-n10 --mb-10 --mb-lg-7 contrast--dark f-w" id="thematichero">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center h-550-px"><!-- .vh-100 hack-->

				<div class="col-lg-5 overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-5 ps-6 pt-20 aos-init aos-animate" data-aos="fade-left" <?= $thematic_bgcolor?>>
						
						<hgroup>
							<h6 class="subline text-white">Thématique</h6>						
							<?php if ( strlen(strip_tags($current_thematic_name)) > 0 ) : ?>
							<h3 class="my-3 text-white border-white border-2 border rounded-4 p-2"><?php echo WaffTwo\Core\waff_do_markdown( WaffTwo\Core\waff_clean_tags(strip_tags($current_thematic_name)) ); ?></h3>
							<?php endif; ?>
							<!-- <div class="thematic-list d-inline-block"><a class="thematic-item" tabindex="-1">Sous-thématique ?</a></div> -->
						</hgroup>
						
						<div class="text-white">
							<?php if ( strlen(strip_tags($thematic_description)) > 0 ) : ?>
							<p class="fw-bold text-white"><?php echo WaffTwo\Core\waff_do_markdown( WaffTwo\Core\waff_clean_tags(strip_tags($thematic_description)) ); ?></p>
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
					<div class="col-lg bg-color-layout h-100 ---- img-shifted shift-right aos-init aos-animate" data-aos="fade-down" data-aos-delay="200">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('https://picsum.photos/720/900');"></div>
					</div>
			
					<!-- Mouse down -->
					<!-- <div class="scroll-downs position-absolute bottom-0 start-45 mb-4">
						<div class="mousey">
							<div class="scroller"></div>
						</div>
					</div> -->

			</div>
		</div>
	</section>

	<!-- Content -->
	<?php if ( strlen(strip_tags($thematic_content)) > 0 ) : ?>
		<div class="content taxonomy-content mt-5 mb-10">
		<?php echo apply_filters('the_content', WaffTwo\Core\waff_do_markdown($thematic_content)); ?>
		</div>
	<?php endif; ?>

	<!-- Related posts -->
	<section class="contrast--light f-w">
	<div class="container-fluid --px-0">
	<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3">
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
 