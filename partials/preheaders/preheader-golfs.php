<?php

//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- .pre-header-->
	<div class="pre-header bg-color-bg color-action-2">
	
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				
				<!-- Col -->
				<div class="col-12">
					<!-- Flex -->
					<div class="d-flex justify-content-between align-items-center">
						<div class="ms-5 me-2 --m-gutter-l flash-title headline font-weight-bold text-nowrap badge badge-sm rounded-pill bg-color-bg color-color-main d-none d-sm-block border border-2 border-color-main"><?= esc_html__( 'Breaking', 'waff' ) ?><!-- Le flash--> <span class="sr-only"><?= esc_html__( 'Breaking news', 'waff' ) ?></span></div> <!-- <?= esc_html__( 'Read More', 'waff' ) ?> -->
						
						<div class="flex-fill d-none d-sm-block">
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
								
								<li class="flash-item text-truncate" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="bottom" title="<?php WaffTwo\Core\waff_do_markdown(the_title()); ?>" data-bs-content="<?= WaffTwo\Core\waff_do_markdown($content); ?>" <?= $style; ?>><i class="fas fa-plus"></i> <strong><u><?php WaffTwo\Core\waff_do_markdown(the_title()); ?></u></strong> <?= WaffTwo\Core\waff_do_markdown($content); ?><?= ( $url )?' · <a href="'.$url.'" class="link-light subline" '.$style.'>'.esc_attr__( 'Read More', 'waff' ).'</a>':''; ?></li>
	
							<?php endwhile; wp_reset_postdata(); ?>
							</ul>
						</div>

						<!-- Menu footer 2 -->
						<div class="d-flex me-5 --justify-content-between align-items-center">
						<?php if ( has_nav_menu( 'preheader-1' ) || is_customize_preview() ) : ?>
							<nav class="nav font-weight-bold preheader-navigation--1 lh-xs mt-2 mb-2 small-sm" aria-label="<?php esc_attr_e( 'Preheader Menu', 'go' ); ?>">
								<span class="screen-reader-text preheader-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'preheader-1' ) ); ?></span>
			
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link text-action-2" ', strip_tags( wp_nav_menu(
										array(
											'theme_location' => 'preheader-1',
											'items_wrap'      => '%3$s',
											'container'       => false,
											'echo'            => false,
											'depth'          => '1',
										)
									), '<a><span><i><title><desc>' ) ) );
									/*wp_nav_menu(
										array(
											'theme_location' => 'footer-1',
											'menu_class'     => 'nav-link',
											'depth'          => 1,
										)
									);*/
								?>
							</nav>
						<?php endif; ?>

						<!-- Social menu -->
						<?php if ( has_nav_menu( 'social' ) ) : ?>
							<div class="socials ml-auto ms-auto p-0 mb-0 mr-2 me-2 list-inline --d-none --d-sm-block reset-fontsize" aria-label="<?php esc_attr_e( 'Social Menu', 'waff' ); ?>">
								<?= WaffTwo\Theme\waff_get_social_menu(""); // Overriding color-light class ?>
							</div>
						<?php endif; ?>
						</div>

						
					</div>
					<!-- End Flex -->
				</div>
							
			</div> 
		</div>	
	</div>	
	<!-- END: .pre-header-->