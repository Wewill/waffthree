<?php

//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- .pre-header-->
	<div class="pre-header bg-color-dark text-white">
	
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				
				<!-- Col -->
				<div class="col-12 col-xl-10">
					<!-- Flex -->
					<div class="d-flex justify-content-between--- align-items-center">
						<div class="mr-2 me-2 --ml-3 --ms-3 m-gutter-l flash-title headline text-nowrap "><?= esc_html__( 'Breaking', 'waff' ) ?><!-- Le flash--> <span class="sr-only"><?= esc_html__( 'Breaking news live from festival', 'waff' ) ?></span></div> <!-- <?= esc_html__( 'Read More', 'waff' ) ?> -->
						<ul id="flash" class="w-70 p-1 p-sm-2 mb-0" style="display: none;">
							<?php $flashes = new WP_Query( array( 'post_type' => 'flash', 'posts_per_page' => 20 ) ); ?>
	
							<?php while ( $flashes->have_posts() ) : $flashes->the_post(); ?>
							
							    <?php 
							    	$id 		= $post->ID;
							    	$prefix 	= 'waff_flash_';
							    	$content 	= rwmb_meta( $prefix . 'content' , array(), $id);
							    	$url		= rwmb_meta( $prefix . 'url' , array(), $id);
							    	$color 		= rwmb_meta( $prefix . 'color' , array(), $id);
							    	$style 		= ( $color )?'style="color:'.$color.'!important;"':'';
							    ?>
								
								<li class="flash-item text-truncate" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="bottom" title="<?php strip_tags(WaffTwo\Core\waff_do_markdown(the_title())); ?>" data-bs-content="<?= strip_tags(WaffTwo\Core\waff_do_markdown($content)); ?>" <?= $style; ?>><i class="fas fa-plus"></i> <strong><u><?php WaffTwo\Core\waff_do_markdown(the_title()); ?></u></strong> <?= WaffTwo\Core\waff_do_markdown($content); ?><?= ( $url )?' · <a href="'.$url.'" class="link-light subline" '.$style.'>'.esc_attr__( 'Read More', 'waff' ).'</a>':''; ?></li>
	
							<?php endwhile; wp_reset_postdata(); ?>
						</ul>

						<?php if ( has_nav_menu( 'social' ) ) : ?>
	
						<div id="socials" class="d-inline-block--- socials ml-auto ms-auto p-0 mb-0 mr-2 me-2 list-inline d-none d-sm-block reset-fontsize" aria-label="<?php esc_attr_e( 'Social Menu', 'waff' ); ?>">
							
							<?= WaffTwo\Theme\waff_get_social_menu(); ?>
							
						</div>
						
						<?php endif; ?>
						
					</div>
					<!-- End Flex -->
				</div>
				
				<!-- Col -->
				<div class="col-auto col-xl-2 d-none d-xl-block bg-primary--- bg-action-1 text-center text-light link-light">
					<div class="p-2"><a href="" class="prog-title headline link" data-bs-toggle="modal" data-bs-target="#programmationModal" aria-expanded="false" aria-controls="programmationModal"><i class="fas fa-bolt px-1 d-none"></i> <?= esc_html__( 'Programmation', 'waff' ) ?></a></div>
				</div>	
			
			</div> 
		</div>	
	</div>	
	<!-- END: .pre-header-->