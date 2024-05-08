<?php
// Todo mega menu nav
//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- Toggle External nav -->
	<div class="collapse --sticky-top position-fixed zi-5 top-0 left-0 w-100 navbar-external shadow-sm" id="navbarToggleExternalContent">
		<div class="bg-color-bg text-light px-3 px-md-6 py-6 pt-20">
			<h6 class="subline d-inline">Menu</h6>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav id="main-nav" class="mt-4" aria-label="<?php esc_attr_e( 'Primary Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<?php
					wp_nav_menu(
						array(
							'container'       => false,
							'theme_location' => 'primary',
							'menu_class'     => 'main-nav list-unstyled link-light',
							'depth'          => '3',
							'add_li_class'  	 => 'heading-3'
							// + le cas link-muted
							// + le cas is_loggued_in
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<!-- Theme social menu -->
			<div class="socials ml-auto ms-auto p-0 mt-4 list-inline reset-fontsize">
				<?= WaffTwo\Theme\waff_get_social_menu('color-dark'); ?>
			</div>

		</div>	
	</div>
	<!-- END: Toogle External nav -->