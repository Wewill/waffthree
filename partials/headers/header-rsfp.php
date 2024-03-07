<?php

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##HEADER</code>':'');

// Every passed args
//var_dump( $args );  

// Get page meta fields 
global $page_atts;
// print_r($page_atts);

?>

	<!-- #header -->
	<?php 
	// Setting affix == reduced header 
	$affix 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('modern') ) )?'affix':''; 
	$toggleaffix 	=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('normal', 'full', 'fancy') ) )?'affix':'';

	// Setting sticky from mods == sticky header 
	$sticky 		=  ( !get_theme_mod( 'sticky_header', false ) )?'position-relative':''; 

	// Setting fancy transparent header
	$headerbackgroundcolor 	=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'bg-transparent':'has-bg'; 	
	// $headerborder 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'border-transparent':'border-bottom border-transparent-color-silver'; 	
	// $navbarborder 			=  ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) )?'border-transparent':'--border-start --border-end border-transparent-color-silver'; 	
	$headerborder = '';
	$navbarborder = '';
	?>

	<header id="site-header" class="site-header header masthead navbar navbar-light classic-navbar bg-transparent container-fluid p-0 zi-5 <?= esc_attr($headerbackgroundcolor); ?> <?= esc_attr($headerborder); ?> <?= esc_attr($sticky) ?> <?= esc_attr($affix) ?> <?php echo esc_attr( Go\has_header_background() ); ?>" data-bs-toggle="<?= esc_attr($toggleaffix) ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader">

		<nav class="w-100 sticky-top row g-0">
			<!-- Filled col 1 -->
			<div class="col-10 col-md-6 col-lg-5 bg-white d-flex align-items-center justify-content-center">

				<!-- Logotype -->
				<?php //Go\display_site_branding(); ?>
				<a class="navbar-brand d-flex align-items-center justify-content-center ps-4 ps-md-5 m-0 overflow-hidden" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= get_bloginfo('description'); ?>"> <!--  Removed height="100%" : Attribute height not allowed on element a at this point. -->
					<div class="header__logo js-replace">
						<!-- item to replace -->
						<div class="js-replace__item  js-replace__item--active">
							<div class="js-replace__content">
							<div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_dark_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>"></div>
							</div>
						</div>  
						<!-- end item to replace -->
						
						<!-- item to replace with -->
						<div class="js-replace__item">
							<div class="js-replace__content">
							<div class="logo logo--invert"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_light_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>"></div>
							</div>
						</div>
						<!-- end item to replace with -->
					</div>
					<div class="logo nav-logomenu position-absolute top-0 start-0 d-none"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_dark_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>"></div>
					<span class="site-title text-hide visually-hidden"><?= get_bloginfo('description'); ?></span>
				</a>

				<!-- Social menu -->
				<?php if ( has_nav_menu( 'social' ) ) : ?>
					<div class="socials ml-auto ms-auto p-0 mb-0 mr-2 me-2 list-inline d-none d-sm-block reset-fontsize" aria-label="<?php esc_attr_e( 'Social Menu', 'waff' ); ?>">
						<?= WaffTwo\Theme\waff_get_social_menu('color-white text-action-3'); ?>
					</div>
				<?php endif; ?>

			</div>
			<!-- end: Filled col 1 -->

			<!-- Filled col 2 -->
			<div class="col-2 col-md-1 col-lg-1 bg-action-3 d-flex align-items-center justify-content-center rounded-end-4 rounded-top-right-0 rounded-md-0">

				<!-- Mobile nav -->
				<!-- Burger .navbar-toggler -->
				<?php // Go\navigation_toggle(); ?>
				<button class="navbar-toggler collapsed px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
					<span class="navbar-close-icon my-1 mx-0 color-dark">
					<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
					</svg>
					</span>
				</button>


			</div>
			<!-- end: Filled col 2 -->

			<!-- Transparent col 3 / Hidden xs sm -->
			<!-- Nav -->
			<div class="navbar-menu col-12 col-md-5 col-lg-6 d-none d-md-flex d-flex align-items-center justify-content-end <?= esc_attr($navbarborder); ?>">

				<!-- Desktop nav .navbar-nav-->
				<div class="d-flex flex-column align-items-stretch justify-content-center d-lg-block d-none ps-5 pe-0">
					<!-- First line -->
					<div class="d-flex align-items-center justify-content-between" aria-label="<?php esc_attr_e( 'Primary Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<!-- Primary menu -->
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<?php
							wp_nav_menu(
								array(
									'container'       => false,
									'theme_location' => 'primary',
									'menu_class'     => 'nav navbar-nav main-nav flex-row primary-menu list-reset',
									'depth'          => '3',
									'add_li_class'  	 => 'nav-link font-weight-bold --ps-4 --pe-0 px-4'
									// + le cas link-muted
									// + le cas is_loggued_in
								)
							);
							?>
						<?php endif; ?>
					</div>
					<!-- Second line -->
					<div class="navbar-text d-flex visually-hidden">
						<span class="d-flex align-items-center justify-content-end edition-wrapper overflow-hidden position-relative" itemscope itemtype="http://schema.org/Organization">
							<span class="edition-number bg-white text-secondary pe-1 --font-weight-bold zi-4"><?php WaffTwo\waff_display_site_blogname(); ?></span>
							<span class="edition-dates text-sm text-secondary --font-weight-semi-bold"><span class="bullet bullet-color-light"></span> <?php WaffTwo\waff_display_site_description(); ?></span>
						</span>
						<?php // Go\display_site_branding( array( 'description' => true ) ); ?>
					</div>
				</div>
				
				<!-- Toolbar and Page title -->
				<div class="navbar-text d-flex align-items-center justify-content-center ps-0 pe-2 pe-sm-4">
					<div class="d-flex col flex-column justify-content-center position-relative">

						<!-- First line -->
						<div class="d-flex flex-column flex-sm-row align-items-center justify-content-end">
							<?php Go\search_toggle(); ?>
							<?php //Go\WooCommerce\woocommerce_cart_link(); ?>
							<?php WaffTwo\waff_night_toggle(); ?>
							<?php WaffTwo\Theme\waff_get_languages(); ?>
						</div>
						<!-- Second line -->
						<div class="text-end mt-2 pe-3 visually-hidden">
							<?php
								if ( !is_front_page() ) { 
									if ( !is_singular( array('post', 'film') ) ) { 
										echo '<span class="text-end">';
										single_post_title(); 
										echo '</span>';
									}
								}
							?>
						</div>
					</div>
				</div>

			</div>
			<!-- end: Transparent col 3 -->
		</nav>	

		<?php get_template_part( 'partials/modal-search' ); ?>
		
	</header>
	<!-- END: #header -->