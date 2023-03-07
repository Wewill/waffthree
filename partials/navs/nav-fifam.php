<?php

//var_dump( $args );  // Everything
$page_atts = $args;

?>
	<!-- main nav -->
	<div class="collapse fixed-top --zi-1000" id="navbarToggleExternalContent">
		<div class="container-fluid bg-light --bg-white text-dark vh-100 --pt-250-px pt-200-px">
			<div class="row g-1 justify-content-between vh-100">
				
				<div class="col-12 d-block d-md-none d-flex mb-2 bd-navbar justify-content-between align-items-start">
					<!-- Burger -->
					<button class="navbar-toggler py-1 text-left text-start w-40" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="<?php echo esc_html__( 'Toggle Menu', 'waff' ); ?>">
						<span class="navbar-toggler-icon"><span class="screen-reader-text"><?php echo esc_html__( 'Menu', 'waff' ); ?></span></span>
						<span class="navbar-close-icon my-1 mx-0 color-dark">
							<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							  <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
							</svg>
						</span>
						<span class="subline align-middle small opacity-100 d-none d-sm-inline-block">MENU</span>
					</button>
					<div class="logo mx-auto pl-1"><img src="<?= get_stylesheet_directory_uri() ?>/dist/images/logotype_fifam_seul_dark.svg" width="33.33" height="40"></div>
					<div class="text-right text-end w-40 py-2"></div>
				</div>
				
				<?php if ( has_nav_menu( 'primary' ) ) : ?>
	
				<div class="col-6 col-md-5 vh-50">
					<nav id="main-nav" class="ml-0 ml-md-2 ms-0 ms-md-2" aria-label="<?php esc_attr_e( 'Primary Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<!-- <ul class="list-unstyled link-dark">
							<li class="heading-3 active"><a href="">L'édition</a></li>
							<li class="heading-3"><a href="">Le festival</a></li>
							<li class="heading-3"><a href="">Pratique</a></li>
							<li class="heading-3 link-muted"><a href="">Pro &amp; presse</a></li>
							<li class="heading-3 link-muted"><a href="">Partenaires</a></li>
						</ul> -->
	
						<?php
						wp_nav_menu(
							array(
								'container'       => false,
								'theme_location' => 'primary',
								'menu_class'     => 'main-nav list-unstyled link-dark',
								'depth'          => '1',
								'add_li_class'  	 => 'heading-3'
								// + le cas link-muted
								// + le cas is_loggued_in
							)
						);
						?>
	
					</nav>
				</div>
				<div class="col-6 col-md-4 mt-0 mt-md-3 vh-50">
					<nav id="sub-nav" class="ml-0 ml-md-2 ms-0 ms-md-2" aria-label="<?php esc_attr_e( 'Primary Sub Menu', 'waff' ); ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<!--<ul class="list-unstyled link-dark">
							<li class="active"><a href="">La compétition</a></li>
							<li><a href="">Jurys</a></li>
							<li><a href="">Les sections</a></li>
							<li><a href="">Tous les films</a></li>
							<li class="link-muted"><a href="">Exposition</a></li>
							<li class="link-muted"><a href="">Actualités</a></li>
						</ul>-->
						
						<?php
						wp_nav_menu(
							array(
								'container'       => false,
								'theme_location' => 'primary',
								'menu_class'     => 'sub-nav list-unstyled link-dark',
								'depth'          => '3',
								'add_li_class'  	 => ''
								// + le cas link-muted
								// + le cas is_loggued_in
							)
						);
						?>
						
					</nav>
				</div>
	
				<?php endif; ?>
				
				<div class="col-12 col-md-3 mt-0 mt-md-3 vh-25">
					<div class="row">
						<div class="col-6 col-md-12 d-none d-md-block">
							<?= WaffTwo\Theme\waff_get_edition_badge(); ?>
							<!-- <p><small>#TODO 125 films<br/>#TODO 35 invités</small></p> -->
						</div>
						
						<div class="col-12 col-md-12 col-contact">

							<!-- Contacts infos -->
							<div class="subline medium mt-sm-5 mt-0 color-dark"><?php WaffTwo\display_site_message(); ?></div>
							<div class="mt-1">
								<small>
									<?php WaffTwo\display_company_address(); ?>
									<?php WaffTwo\display_phone(); ?>
								</small>
							</div>

							<!-- Contact -->
							<p class="mb-0 mt-sm-5 mt-1"><a class="underline semibold link-dark" href="/contactez-nous/">Contactez-nous</a></p>
							
							<!-- Theme social menu -->
							<div id="socials" class="socials ml-auto ms-auto p-0 mt-2 list-inline reset-fontsize">
								<?= WaffTwo\Theme\waff_get_social_menu('color-dark'); ?>
							</div>
							
						</div>
					</div>
				</div>

				
			</div>
		</div>
	</div>
	<!-- END: main nav -->