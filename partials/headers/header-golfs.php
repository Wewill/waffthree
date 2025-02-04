<?php

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##HEADER</code>':'');

// Every passed args
//var_dump( $args );  

// Get page meta fields 
global $page_atts;
//print_r($page_atts);

?>

	<!-- #header -->
	<?php 
	// Setting affix
	$affix 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('normal', 'modern') ) )?'affix':'';
	$toggleaffix 	=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('full', 'fancy') ) )?'affix':'';

	// Setting fancy transparent header
	$headerbackgroundcolor 	=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'bg-transparent':'mb-0 has-bg';
	$headerborder 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'border-0':'border-top border-color-bg';
	$navbarborder 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'border-0':'border-0';
	?>

	<header id="site-header" class="site-header header masthead navbar navbar-expand-lg navbar-light classic-navbar container-fluid p-0 zi-5 <?= esc_attr($headerbackgroundcolor); ?> <?= esc_attr($headerborder); ?> <?= esc_attr($affix) ?> <?php echo esc_attr( Go\has_header_background() ); ?>" data-bs-toggle="<?= esc_attr($toggleaffix) ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader">
	
		<div class="d-flex align-items-stretch justify-content-center w-100 sticky-top">
			
			<!-- Mobile nav -->
			<!-- Burger -->
			<?php // Go\navigation_toggle(); ?>

			<button class="navbar-toggler collapsed px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				<span class="navbar-close-icon my-1 mx-0 --color-black color-dark">
				<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
				</svg>
				</span>
			</button>

			<!-- Logotype -->
			<?php //Go\display_site_branding(); ?>
			<a class="navbar-brand --w-13 --w-sm-15 d-flex align-items-center justify-content-start pt-3 pb-3 m-0 ms-0 ms-sm-5 overflow-hidden" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= get_bloginfo('description'); ?>"> <!--  Removed height="100%" : Attribute height not allowed on element a at this point. -->
			    <div class="header__logo js-replace">
				      <!-- item to replace -->
				      <div class="js-replace__item  js-replace__item--active">
				        <div class="js-replace__content">
				          <div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_dark_url' ); ?>"></div>
				        </div>
				      </div>  
				      <!-- end item to replace -->
				     
				      <!-- item to replace with -->
				      <div class="js-replace__item">
				        <div class="js-replace__content">
				          <div class="logo logo--invert"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_light_url' ); ?>" ></div>
				        </div>
				      </div>
				      <!-- end item to replace with -->
				</div>
				<div class="logo nav-logomenu position-absolute --top-0 left-0 d-none"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_dark_url' ); ?>" 	></div>
				<span class="site-title text-hide visually-hidden"><?= get_bloginfo('description'); ?></span>
			</a>
					
			<!-- Nav -->
			<div class="navbar-menu col d-flex align-items-center <?= esc_attr($navbarborder); ?>">

				<!-- Mobile nav -->
				<!-- Burger -->
				<?php // Go\navigation_toggle(); ?>

				<!-- <button class="navbar-toggler collapsed px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
					<span class="navbar-close-icon my-1 mx-0 --color-black color-dark">
					<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
					</svg>
					</span>
				</button> -->

				<!-- Desktop nav -->
				<div class="d-flex col flex-column align-items-stretch justify-content-center d-lg-block d-none pe-2 ps-2">
					
					<!-- Second line -->
					<div class="d-flex align-items-center justify-content-end" aria-label="<?php esc_attr_e( 'Primary Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement" data-aos="fade-down" data-aos-delay="200">

						<!-- Primary menu -->
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
								<?php
								wp_nav_menu(
									array(
										'container'       => false,
										'theme_location' => 'primary',
										'menu_class'     => 'nav navbar-nav main-nav primary-menu list-reset',
										'depth'          => '3',
										'add_li_class'  	 => 'nav-link font-weight-bold fw-semibold ps-4 pe-0'
										// + le cas link-muted
										// + le cas is_loggued_in
									)
								);
								?>
						<?php endif; ?>

						</div>


				</div>

			</div>
				
			<!-- Toolbar and Page title -->
			<div class="navbar-text --w-13 d-flex align-items-center justify-content-end me-3 me-sm-5 ms-3 ms-sm-5">

				<div class="d-flex col flex-column justify-content-center position-relative">

					<!-- First line -->
					<div class="d-flex flex-column flex-sm-row align-items-center justify-content-end">
						<?php Go\search_toggle(); ?>
						<?php //Go\WooCommerce\woocommerce_cart_link(); ?>
						<?php WaffTwo\waff_night_toggle(); ?>
						<?php WaffTwo\Theme\waff_get_languages(); ?>
					</div>

					<!-- Second line -->
					<!-- <div class="text-end mt-2 pe-3 d-lg-block d-none">
						<?php
							if ( !is_front_page() ) { 
								if ( !is_singular( array('post') ) ) {
									echo '<span class="text-end">';
									single_post_title();
									echo '</span>';
								}
							}
						?>
					</div> -->

			</div>
		</div>	

		<?php get_template_part( 'partials/modal-search' ); ?>
		
	</header>
	<!-- END: #header -->