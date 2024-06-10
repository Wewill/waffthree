<?php
/**
 * Footer waff / Dinard
 *
 * @package Waff
 */

$has_social_icons = Go\has_social_icons();
$has_background   = Go\has_footer_background(); // background-color: ...
$blog_description = get_bloginfo( 'description' );

// Footer bg image 
$bg_images   	= WaffTwo\Blocks\waff_get_blocks_background();
$bg_image 		= reset( $bg_images );
?>

<?php do_action( 'waff_before_footer' ); ?>

<?php dynamic_sidebar('sidebar-before'); ?>

<!-- Begin: FOOTER -->
<!-- #footer -->
<footer id="colophon" class="site-footer site-footer--waff mt-0 pt-13 pb-10 pt-md-20 pb-md-18 bg-action-1 text-light link-light contrast--dark rounded-top-4 ---- bg-image bg-cover bg-position-center-center position-relative <?php echo esc_attr( $has_background ); ?>" style="background-image: url('<?= $bg_image['url']; ?>');">
	<div class="container-fluid --px-0">

		<!-- First row -->
		<div class="row g-4">

			<!-- Col -->
			<div class="col-6 col-lg-3">
				<!-- First nav -->
				<?php if ( has_nav_menu( 'footer-1' ) || is_customize_preview() ) : ?>
					<nav class="footer-navigation footer-navigation--1" aria-label="<?php esc_attr_e( 'Primary Footer Menu', 'go' ); ?>">
						<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-1' ) ); ?></span>
						<?php
							print ( preg_replace( '/(<a )/', '<a class="nav-link" ', wp_nav_menu(
								array(
									'theme_location' => 'footer-1',
									'menu_class'     => 'footer-menu list-unstyled list-inverse list-ps-0 font-weight-semi-bold list-md',
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
			<!-- Col -->
			<div class="col-6 col-lg-3">
				<!-- Second nav -->
				<?php if ( has_nav_menu( 'footer-2' ) || is_customize_preview() ) : ?>
					<nav class="footer-navigation footer-navigation--2" aria-label="<?php esc_attr_e( 'Secondary Footer Menu', 'go' ); ?>">
						<span class="screen-reader-text footer-navigation__title"><?php echo esc_html( wp_get_nav_menu_name( 'footer-2' ) ); ?></span>
						<?php
							print ( preg_replace( '/(<a )/', '<a class="nav-link" ', wp_nav_menu(
								array(
									'theme_location' => 'footer-2',
									'menu_class'     => 'footer-menu list-unstyled list-inverse list-ps-0 font-weight-semi-bold',
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
			<!-- Col -->
			<div class="col-6 col-lg-3">
				<!-- Theme address -->
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

				<!-- Theme contact -->
				<p class="font-weight-medium">
					<?php WaffTwo\display_phone(); ?>
					<?php WaffTwo\display_email(); ?>
				</p>
				<p><small><?php WaffTwo\display_site_message(); ?></small></p>
			</div>
			<!-- Col -->
			<div class="col-6 col-lg-3">
				<!-- Logotype -->
				<div class="">
					<div class="ml-4 mb-2" data-aos="fade-down" data-aos-delay="100">
						<div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svgsign_dark_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>" width="255" height="65"></div>
						<?php /* Go\display_site_branding( array( 'description' => false ) ); */ ?>
					</div>
				</div>

				<!-- Tagline -->
				<div class="">
					<?php if ( !empty( $blog_description ) ) :
						echo '<p class="h6 f-heading font-weight-bold text-light mt-3">' . WaffTwo\waff_display_site_description() . '</p>';
					endif;	?>				
				</div>
			</div>

		</div>
		<!--End: First row -->

	</div>	
</footer>

<?php dynamic_sidebar('sidebar-after'); ?>

<?php do_action( 'waff_after_footer' ); ?>

<!-- .Credits -->
<footer class="credits container-fluid bg-color-dark text-light pt-4 pb-3 --px-3 --px-md-5">
	<div class="d-flex justify-content-between align-items-stretch">
	    <p class="font-weight-bold link-light mb-2" aria-label="<?php esc_attr_e( 'Credits Menu', 'waff' ); ?>">
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
					'add_li_class' 	 => ''
					)
			), '<a>' ) );
			?>
	
			<?php endif; ?>

		</p>
		<?php /* Go\copyright( array( 'class' => 'site-info text-sm mb-0' ) ); */ ?>
	    <p class="text-adaptive"><small>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <a href="http://www.wilhemarnoldy.fr/" class="link-light"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i> <?php printf( esc_html__( 'Designed w/ <3 by %1$s using WordPress', 'waff' ), 'Wilhem Arnoldy, WAG&amp;W' ); ?></a> <?php WaffTwo\display_privacy_statement(); ?></small></p>
	</div>
</footer>
<!-- END: .Credits -->

<!-- Begin: Modal signloginout -->
<?php get_template_part( 'partials/modal-loginout' ); ?>
