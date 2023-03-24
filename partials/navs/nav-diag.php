<?php
// Todo mega menu nav
//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- Toggle External nav -->
	<div class="collapse sticky-top navbar-external border-top border-bottom border-transparent-color-silver" id="navbarToggleExternalContent">
		<div class="bg-color-light text-dark px-4 py-6">
			<h6 class="headline d-inline">Menu</h5>

			<!-- <ul class="nav main-nav navbar-nav link-light">
			<li class="nav-item active"><a href="#" class="nav-link font-weight-bold lead">Le festival</a></li>
			<li class="nav-item"><a href="#" class="nav-link font-weight-bold lead">L'édition</a></li>
			<li class="nav-item"><a href="#" class="nav-link font-weight-bold lead">Informations pratique</a></li>
			<li class="nav-item"><a href="#" class="nav-link font-weight-bold lead">Pro/Presse</a></li>
			<li class="nav-item disabled"><a href="#" class="nav-link font-weight-bold lead">Boutique</a></li>
			<li class="nav-item font-weight-bold lead dropdown">
				<ul class="submenu">
				<li><a class="nav-item" href="#">Action</a></li>
				<li><a class="nav-item" href="#">Another action</a></li>
				<li><a class="nav-item" href="#">Something else here</a></li>
				</ul>
			</li>
			</ul>-->

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