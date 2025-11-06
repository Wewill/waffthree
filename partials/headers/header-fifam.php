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
	?>

	<header id="site-header" class="masthead navbar navbar-light bd-navbar fancy-navbar bg-transparent container-fluid <?= esc_attr($affix) ?> <?php echo esc_attr( Go\has_header_background() ); ?>" data-bs-toggle="<?= esc_attr($toggleaffix) ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader">
		
		<nav class="d-flex align-items-start justify-content-between w-100 sticky-top">
			<!-- Burger -->
			<button class="navbar-toggler collapsed --text-left text-start w-40" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="<?php echo esc_html__( 'Toggle Menu', 'waff' ); ?>">
				<span class="navbar-toggler-icon"><span class="screen-reader-text"><?php echo esc_html__( 'Menu', 'waff' ); ?></span></span>
				<span class="navbar-close-icon my-1 mx-0 --color-black color-dark">
					<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					  <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
					</svg>
				</span>
				<span class="subline align-middle small opacity-100 d-none d-sm-inline-block">MENU</span>
			</button>
			
			<!-- Logotype -->
			<?php //Go\display_site_branding(); ?>
			<a class="navbar-brand p-0 m-0 overflow-hidden m-auto position-relative" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= get_bloginfo('description'); ?>" height="140">
			    <div class="header__logo js-replace">
				      <!-- item to replace -->
				      <div class="js-replace__item  js-replace__item--active">
				        <div class="js-replace__content">
				          <div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/logotype_fifam_dark.svg" width="90" height="140"></div>
				        </div>
				      </div>  
				      <!-- end item to replace -->
				     
				      <!-- item to replace with -->
				      <div class="js-replace__item">
				        <div class="js-replace__content">
				          <div class="logo logo--invert"><img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/logotype_fifam_white.svg" width="90" height="140"></div>
				        </div>
				      </div>
				      <!-- end item to replace with -->
				</div>
				<div class="logo nav-logomenu position-absolute top-0 left-0 d-none"><img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/logotype_fifam_dark.svg" width="90" height="140"></div>
				<span class="text-hide visually-hidden"><?= get_bloginfo('description'); ?></span>
			</a>
			
			<!-- Page title -->
			<span class="navbar-text page-title --pr-3 text-end w-40 d-flex align-items-center justify-content-end" style="max-height: 35px;">
				
				<?php
				if ( !is_front_page() ) { 
					if ( !is_singular( array('post', 'film') ) ) {
						echo '<span class="d-sm-inline d-none">';
						single_post_title(); 
						echo '</span>';
					}
				}
				?>
				
				<?php Go\search_toggle(); ?>
				<?php // Go\WooCommerce\woocommerce_cart_link(); ?>
				<?php // Go\navigation_toggle(); ?>
	
				<?php WaffTwo\waff_night_toggle(); ?>

				<!-- If exists add here account menu walker -->
				<?php if ( has_nav_menu( 'account' ) || is_customize_preview() ) : ?>
					<div class="font-weight-bold account-nav" aria-label="<?php esc_attr_e( 'Account Menu', 'go' ); ?>">	
						<?php
							/*print ( preg_replace( '/(<a )/', '<a class="nav-link text-action-2" ', strip_tags( wp_nav_menu(
								array(
									'theme_location' => 'account',
									'items_wrap'      => '%3$s',
									'container'       => false,
									'echo'            => false,
									'depth'          => '2',
								)
							), '<a><span><i><title><desc>' ) ) );*/
							wp_nav_menu(
								array(
									'theme_location' => 'account',
									'menu_class'     => 'nav-link subline fs-5',
									'depth'          => 2,
								)
							);
						?>
					</div>
				<?php endif; ?>

				<style type="text/css">
					.account-nav {
						display: inline-block;
						padding: .75rem;
						/* position: relative; */
					}
					.account-nav ul {
						margin: 0;
						padding: 0;
						list-style: none;
					}
					.account-nav ul li {
						display: inline-block;
					}
					.account-nav ul li a {
						color: var(--color-black);
						text-decoration: none;	
					}
					.account-nav ul li a i {
						position: relative;
						color: black;
						top: 1px;
					}
					.account-nav ul li ul.sub-menu {
						position: absolute;
						top: 32px;
						right: 10px;
					}
					.account-nav ul li ul.sub-menu li {
						margin-left:.5rem;
					}
					.account-nav ul li ul.sub-menu {
						opacity: 0;
						transition: all .3s ease-in-out;
					}
					.account-nav ul li:hover ul.sub-menu {
						opacity: 1;
					} 
				</style>
	
			</span>
		</nav>	

		<?php get_template_part( 'partials/modal-search' ); ?>
	
	</header>
	<!-- END: #header -->