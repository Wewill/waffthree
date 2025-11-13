<?php
/**
 * Footer waff / Dinard
 *
 * @package Waff
 */

$has_social_icons = Go\has_social_icons();
$has_background   = Go\has_footer_background();
$blog_description = get_bloginfo( 'description' );

?>

<?php do_action( 'waff_before_footer' ); ?>

<?php dynamic_sidebar('sidebar-before'); ?>

<!-- Begin: FOOTER -->
<footer id="colophon" class="site-footer --mt-10 --mt-md-10 mt-0 pt-0 contrast--light site-footer--waff <?php echo esc_attr( $has_background ); ?>">
	<div class="container-fluid px-0">

		<!-- First row -->
		<div class="row g-0 border-top">

			<!-- Logotype -->
			<div class="col-12 col-sm-6 px-3 px-md-5 py-md-7 py-4 pt-5 border-end">
				<div class="" data-aos="fade-down" data-aos-delay="100">
					<div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svgsign_dark_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>" width="50" height="60"></div>
					<?php /* Go\display_site_branding( array( 'description' => false ) ); */ ?>
				</div>
			</div>

			<!-- Tagline -->
			<div class="col-12 col-sm-6 px-3 px-md-5 py-md-7 py-4">
				<?php if ( !empty( $blog_description ) ) :
					echo '<p class="h3 f-heading font-weight-bold text-action-1">' . WaffTwo\waff_display_site_description() . '</p>';
				endif;	?>				
			</div>

		</div>

		<!-- Second row -->
		<div class="row g-0 border-bottom">
			<div class="col-12 col-sm-6 px-3 px-md-5 py-md-7 py-4 border-end">

				<div class="row">
					<div class="col-6">
						<span class="bullet bullet-action-1 ml-0 ms-0"></span>
						
						<?php WaffTwo\display_company_address(array('class' => 'font-weight-bold')); ?>

						<!-- Theme social menu -->
						<div id="socials" class="d-inline-block socials p-0 m-0 list-inline reset-fontsize">
							<?= WaffTwo\Theme\waff_get_social_menu('color-dark'); ?>
						</div>

						<!-- Go social options -->
						<?php if ( $has_social_icons ) : ?>
							<div class="site-footer__row flex flex-column lg:flex-row justify-between lg:items-center">
								<?php Go\social_icons( array( 'class' => 'social-icons list-reset' ) ); ?>
							</div>
						<?php endif; ?>
													
					</div>

					<div class="col-6">
						<span class="bullet bullet-action-1 ml-0 ms-0"></span>
						<p class="font-weight-medium">
							<?php WaffTwo\display_phone(); ?>
							<?php WaffTwo\display_email(); ?>
						</p>
						<p><small><?php WaffTwo\display_site_message(); ?></small></p>
					</div>
				</div>

			</div>
			<div class="col-12 col-sm-6 px-3 px-md-5 py-md-7 py-4 pb-5">

				<div class="row">
					<div class="col-6">
						<span class="bullet bullet-action-1 ml-0 ms-0"></span>
						<?php if ( has_nav_menu( 'footer-1' ) || is_customize_preview() ) : ?>
							<nav class="footer-navigation footer-navigation--1" aria-label="<?php esc_attr_e( 'Primary Footer Menu', 'go' ); ?>">
								<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-1' ) ); ?></span>
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link" ', wp_nav_menu(
										array(
											'theme_location' => 'footer-1',
											'menu_class'     => 'footer-menu list-unstyled font-weight-semi-bold list-md',
											'depth'          => 1,
											'echo'		 	 => false,
											'container'		 => false,
											'add_li_class' 	 => ''
											)
									) ) );
								?>
							</nav>
						<?php endif; ?>							
					</div>

					<div class="col-6">
						<span class="bullet bullet-action-1 ml-0 ms-0"></span>
						<?php if ( has_nav_menu( 'footer-2' ) || is_customize_preview() ) : ?>
							<nav class="footer-navigation footer-navigation--2" aria-label="<?php esc_attr_e( 'Secondary Footer Menu', 'go' ); ?>">
								<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-2' ) ); ?></span>
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link" ', wp_nav_menu(
										array(
											'theme_location' => 'footer-2',
											'menu_class'     => 'footer-menu list-unstyled font-weight-semi-bold',
											'depth'          => 1,
											'echo'		 	 => false,
											'container'		 => false,
											'add_li_class' 	 => ''
											)
									) ) );
								?>
							</nav>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>

	</div>	
</footer>

<?php dynamic_sidebar('sidebar-after'); ?>

<?php do_action( 'waff_after_footer' ); ?>

<!-- .Credits -->
<footer class="credits container-fluid bg-action-2 pt-4 pb-3 px-3 px-md-5">
	<div class="d-flex justify-content-between align-items-stretch">
	    <p class="font-weight-bold link-black mb-2" aria-label="<?php esc_attr_e( 'Credits Menu', 'waff' ); ?>">
			<a class="font-weight-normal" href="#"><?= esc_html__('Back to top', 'waff'); ?></a> <!--  · <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?= esc_html__('Home', 'waff'); ?></a> -->
			
			<?php if ( has_nav_menu( 'credits' ) ) : ?>
	
			<?php
			print( strip_tags( wp_nav_menu(
				array(
					'before' 		  => ' 	· ',
					'items_wrap'      => '%3$s',
					'container'       => false,
					'echo'            => false,
					'theme_location' => 'credits',
					//'menu_class'     => 'credits-menu',
					'depth'          => '0',
				)
			), '<a>' ) );
			?>
	
			<?php endif; ?>

		</p>
		<?php /* Go\copyright( array( 'class' => 'site-info text-sm mb-0' ) ); */ ?>
	    <p class="text-adaptive"><small>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <a href="http://www.wilhemarnoldy.fr/" class="link-black"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i> <?php printf( esc_html__( 'Designed w/ <3 by %1$s using WordPress', 'waff' ), 'Wilhem Arnoldy, WAG&amp;W' ); ?></a> <?php WaffTwo\display_privacy_statement(); ?></small></p>
	</div>
</footer>
<!-- END: .Credits -->

<!-- .Modal Programmation -->
<?php dynamic_sidebar('sidebar-programmation'); ?>