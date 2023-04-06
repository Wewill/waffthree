<?php
// Todo mega menu nav
//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- Toggle External nav -->
	<div class="collapse sticky-top navbar-external border-top border-bottom border-transparent-color-silver" id="navbarToggleExternalContent">
		<div class="bg-color-light text-dark px-4 py-6">
			<h6 class="headline d-inline">Menu</h5>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav id="main-nav" class="mt-4" aria-label="<?php esc_attr_e( 'Primary Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<?php
					wp_nav_menu(
						array(
							'container'       => false,
							'theme_location' => 'primary',
							'menu_class'     => 'main-nav list-unstyled link-dark',
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
			<div id="socials" class="socials ml-auto ms-auto p-0 mt-4 list-inline reset-fontsize">
				<?= WaffTwo\Theme\waff_get_social_menu('color-dark'); ?>
			</div>

		</div>	
	</div>
	<!-- END: Toogle External nav -->