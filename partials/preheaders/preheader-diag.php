<?php

//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- .pre-header-->
	<div class="pre-header bg-light color-action-2">
	
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				
				<!-- Col -->
				<div class="col-12">
					<!-- Flex -->
					<div class="d-flex justify-content-between--- align-items-center">
						<div class="ms-5 me-2 --m-gutter-l flash-title --headline font-weight-bold text-nowrap badge badge-sm rounded-pill bg-color-gray text-light"><?= esc_html__( 'Breaking', 'waff' ) ?><!-- Le flash--> <span class="sr-only"><?= esc_html__( 'Breaking news live from festival', 'waff' ) ?></span></div> <!-- <?= esc_html__( 'Read More', 'waff' ) ?> -->
						<ul id="flash" class="w-70 p-2 mb-0" style="display: none;">
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
								
								<li class="flash-item text-truncate" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="bottom" title="<?php the_title(); ?>" data-bs-content="<?= $content; ?>" <?= $style; ?>><i class="fas fa-plus"></i> <strong><u><?php the_title(); ?></u></strong> <?= $content; ?><?= ( $url )?' · <a href="'.$url.'" class="link-light subline" '.$style.'>'.esc_attr__( 'Read More', 'waff' ).'</a>':''; ?></li>
	
							<?php endwhile; wp_reset_postdata(); ?>
						</ul>

						@todo menu 3 + choose best social

						<?php if ( has_nav_menu( 'social' ) ) : ?>
							<div id="socials" class="d-inline-block--- socials ml-auto ms-auto p-0 mb-0 mr-2 me-2 list-inline d-none d-sm-block reset-fontsize" aria-label="<?php esc_attr_e( 'Social Menu', 'waff' ); ?>">
								<?= WaffTwo\Theme\waff_get_social_menu(); ?>
							</div>
						<?php endif; ?>

						<!-- Social menu -->
						<?php if ( has_nav_menu( 'social' ) ) : ?>
							<div id="socials" class="socials ml-auto ms-auto p-0 mb-0 mr-2 me-2 list-inline d-none d-sm-block reset-fontsize" aria-label="<?php esc_attr_e( 'Social Menu', 'waff' ); ?>">
								<?= WaffTwo\Theme\waff_get_social_menu(); ?>
								<span class="bullet bullet-action-1"></span>
							</div>
						<?php endif; ?>

						
					</div>
					<!-- End Flex -->
				</div>
							
			</div> 
		</div>	
	</div>	
	<!-- END: .pre-header-->