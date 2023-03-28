<?php
/**
 * Footer waff
 *
 * @package Go
 */

$has_social_icons = Go\has_social_icons();
$has_background   = Go\has_footer_background();
?>

<?php do_action( 'waff_before_footer' ); ?>

<?php dynamic_sidebar('sidebar-before'); ?>

<!-- Begin: FOOTER -->
<footer id="colophon" class="site-footer pt-10 pb-10 color-light contrast--dark site-footer--waff <?php echo esc_attr( $has_background ); ?>">
		<div class="container-fluid px-0">
			<div class="row g-0">
				<div class="col-12">
					
					<div class="d-flex align-items-center">

						<div class="ml-4 ms-5 me-4 mb-2" data-aos="fade-down" data-aos-delay="100">
							<div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svgsign_light_url' ); ?>" width="150" height="100%"></div>
							<?php /* Go\display_site_branding( array( 'description' => false ) ); */ ?>
						</div>
						
						<?php //get_sidebar('sidebar-1'); ?>

						<?php if ( has_nav_menu( 'footer-1' ) || is_customize_preview() ) : ?>
							<nav class="nav link-light mb-1 p-2 w-75 --footer-navigation footer-navigation--1 nav-md" aria-label="<?php esc_attr_e( 'Primary Footer Menu', 'go' ); ?>">
								<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-1' ) ); ?></span>
			
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link" ', strip_tags( wp_nav_menu(
										array(
											'theme_location' => 'footer-1',
											'items_wrap'      => '%3$s',
											'container'       => false,
											'echo'            => false,
											'depth'          => '1',
										)
									), '<a><span><i><title><desc>' ) ) );
									/*wp_nav_menu(
										array(
											'theme_location' => 'footer-1',
											'menu_class'     => 'nav-link',
											'depth'          => 1,
										)
									);*/
								?>
							<!-- </nav> -->
						<?php endif; ?>

						<?php if ( !has_nav_menu( 'footer-2' ) && !has_nav_menu( 'footer-3' ) ) : ?>
							</nav>
						<?php endif; ?>

						<?php if ( has_nav_menu( 'footer-2' ) || is_customize_preview() ) : ?>
							<!-- <nav class="nav link-light mb-1 p-2 w-75 --footer-navigation footer-navigation--2" aria-label="<?php esc_attr_e( 'Secondary Footer Menu', 'go' ); ?>"> -->
								<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-2' ) ); ?></span>
			
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link link-muted" ', strip_tags( wp_nav_menu(
										array(
											'theme_location' => 'footer-2',
											'items_wrap'      => '%3$s',
											'container'       => false,
											'echo'            => false,
											'depth'          => '1',
										)
									), '<a><span><i><title><desc>' ) ) );
									/*wp_nav_menu(
										array(
											'theme_location' => 'footer-2',
											'menu_class'     => 'nav-link link-muted',
											'depth'          => 1,
										)
									);*/
								?>
							<!-- </nav> -->
						<?php endif; ?>

						<?php if ( !has_nav_menu( 'footer-3' ) ) : ?>
							</nav>
						<?php endif; ?>

						<?php if ( has_nav_menu( 'footer-3' ) || is_customize_preview() ) : ?>
							<!-- <nav class="nav link-light mb-1 p-2 w-75 --footer-navigation footer-navigation--3" aria-label="<?php esc_attr_e( 'Tertiary Footer Menu', 'go' ); ?>"> -->
								<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-3' ) ); ?></span>
			
								<?php
									print ( preg_replace( '/(<a )/', '<a class="nav-link link-muted" ', strip_tags( wp_nav_menu(
										array(
											'theme_location' => 'footer-3',
											'items_wrap'      => '%3$s',
											'container'       => false,
											'echo'            => false,
											'depth'          => '1',
										)
									), '<a><span><i><title><desc>' ) ) );
									/*wp_nav_menu(
										array(
											'theme_location' => 'footer-3',
											'menu_class'     => '',
											'depth'          => 1,
										)
									);*/
								?>
							</nav>
						<?php endif; ?>

					</div>
					
					<!-- Theme social menu -->
					<div id="socials" class="d-inline-block socials p-0 m-0 ms-5 ml-3 --ml-4 list-inline reset-fontsize">
						<?= WaffTwo\Theme\waff_get_social_menu(); ?>
					</div>
		
					<!-- Go social options -->
					<?php if ( $has_social_icons ) : ?>
						<div class="site-footer__row flex flex-column lg:flex-row justify-between lg:items-center ms-5">
							<?php Go\social_icons( array( 'class' => 'social-icons list-reset' ) ); ?>
						</div>
					<?php endif; ?>
					
				</div>
			</div>
		</div>
</footer>

<?php dynamic_sidebar('sidebar-after'); ?>

<?php do_action( 'waff_after_footer' ); ?>

<!-- .Credits -->
<footer class="credits container-fluid bg-bgcolor pt-4 pb-2">
	<div class="d-flex flex-sm-row flex-column justify-content-between align-items-stretch">
		<?php /* Go\copyright( array( 'class' => 'site-info text-sm mb-0' ) ); */ ?>
	    <p class="text-adaptive"><small>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <a href="http://www.wilhemarnoldy.fr/" class="link-black"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i> <?php printf( esc_html__( 'Designed w/ <3 by %1$s using WordPress', 'waff' ), 'Wilhem Arnoldy, WAG&amp;W' ); ?></a> <?php WaffTwo\display_privacy_statement(); ?></small></p>
	    <p class="subline link-black mb-0" aria-label="<?php esc_attr_e( 'Credits Menu', 'waff' ); ?>">
			<a class="opacity-50" href="#"><?= esc_html__('Back to top', 'waff'); ?></a> <!--  · <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?= esc_html__('Home', 'waff'); ?></a> -->
			
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
	</div>
</footer>
<!-- END: .Credits -->

<!-- .Modal Programmation -->
<?php dynamic_sidebar('sidebar-programmation'); ?>