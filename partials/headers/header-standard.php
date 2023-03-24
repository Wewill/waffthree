<?php

//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- #site-header -->
	<header id="site-header" class="site-header header relative <?php echo esc_attr( Go\has_header_background() ); ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader">

		<div class="header__inner flex items-center justify-between h-inherit w-full relative">

			<div class="header__extras">
				<?php Go\search_toggle(); ?>
				<?php Go\WooCommerce\woocommerce_cart_link(); ?>
			</div>

			<div class="header__title-nav flex items-center flex-nowrap">

				<?php Go\display_site_branding(); ?>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>

					<nav id="header__navigation" class="header__navigation" aria-label="<?php esc_attr_e( 'Horizontal', 'go' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">

						<div class="header__navigation-inner">
							<?php
							wp_nav_menu(
								array(
									'menu_class'     => 'primary-menu list-reset',
									'theme_location' => 'primary',
								)
							);
							?>
						</div>

					</nav>

				<?php endif; ?>

			</div>

			<?php Go\navigation_toggle(); ?>

		</div>

		<?php get_template_part( 'partials/modal-search' ); ?>

	</header>
	<!-- END: #site-header -->