<?php
// Todo mega menu nav
//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- Toggle External nav -->
	<div class="collapse --sticky-top position-fixed zi-5 top-0 left-0 w-100 navbar-external shadow-sm" id="navbarToggleExternalContent">
		<div class="bg-color-bg text-light px-3 px-md-6 py-4">

			<div class="d-md-none d-flex mb-2 justify-content-end align-items-end">
				<!-- Burger -->
				<button class="navbar-toggler py-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="<?php echo esc_html__( 'Toggle Menu', 'waff' ); ?>">
					<span class="navbar-toggler-icon"><span class="screen-reader-text"><?php echo esc_html__( 'Menu', 'waff' ); ?></span></span>
					<span class="navbar-close-icon my-1 mx-0 color-dark">
						<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
						</svg>
					</span>
					<span class="subline align-middle small opacity-100 d-none d-sm-inline-block">MENU</span>
				</button>
			</div>

			<div class="mt-18">
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
								'add_li_class'  	 => 'heading-4' //heading-3
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
	</div>
	<!-- END: Toogle External nav -->