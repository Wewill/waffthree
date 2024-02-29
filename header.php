<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="site-content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Go
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="apple-touch-icon" href="dist/images/apple-touch-icon.png">
	<link rel="icon" type="image/x-icon" href="dist/images/favicon.ico">
	<link rel="canonical" href="<?php bloginfo( 'url' ); ?>">
	<?php
	/** WWW VS DEV : Desactivation du referencement */
	$subdomain = substr($_SERVER['SERVER_NAME'],0,3);
	if ( $subdomain ==  'dev' ) { echo '<meta name="robots" content="noindex">'; }
	?>
	<?php wp_head(); ?>
</head>

<body
	<?php
	$body_class = get_body_class();
	if ( Go\AMP\is_amp() ) {
		?>
		aria-expanded="false"
		[aria-expanded]="mainNavMenuExpanded ? 'true' : 'false'"
		[class]="'<?php echo esc_attr( implode( ' ', $body_class ) ); ?>' + ( mainNavMenuExpanded ? ' menu-is-open' : '' )"
		<?php
	}
	?>
	class="<?php echo esc_attr( implode( ' ', $body_class ) ); ?>"
	data-aos-easing="ease" 
	data-aos-duration="400" 
	data-aos-delay="0"	
>

	<?php
	if ( true === WAFF_DEBUG ) {
		echo "<code>";
		echo " #SINGULAR";
		echo is_singular();
		echo " #ARCHIVE";
		echo is_archive();
		echo " #TAX";
		echo is_tax();
		echo " #SEARCH";
		echo is_search();
		echo " #ATTACHMENT";
		echo is_attachment();
		echo " #AUTHOR";
		echo is_author();
		echo " #IS_FRONTPAGE";
		echo is_front_page();
		echo " #IS_HOME";
		echo is_home();
		echo " #IS_PAGE";
		echo is_page();
		echo " #IS_SINGLE";
		echo is_single();


		echo " #IS_BLOG_PAGE";
		print(is_home() && !is_front_page());


		echo " #IS_HOME_PAGE";
		print(is_home() || is_front_page());

		echo " #SHOWINFRONT";
		print('page' == get_option( 'show_on_front' ));

		echo "</code>";
	}

	global $post, $page_atts;
	$page_atts = array();

	// Check if blog
	$is_blog =  (is_home() && !is_front_page())?true:false;

	// Check if home
	$is_home =  (is_home() || is_front_page())?true:false;

	$args = array();
	$ID = ( true === $is_blog)?get_queried_object_id():$post->ID;
	
	$page_atts['page_mode_class'] 		= ( true === apply_filters('waff_is_page_dark', false) || true === is_404())?'bg-color-dark text-white contrast--dark':'contrast--light';
	
	// MAIN Post classes
	if ( defined('WAFF_PARTIALS') && 'rsfp' === WAFF_PARTIALS ) : 
		// Type always transparent header 
		$page_atts['post_class'] 			= 'pt-5 pt-md-10 pb-4 pb-sm-10 container-fluid'; //'mt-10 mb-10 --pt-10 --pb-4 pb-sm-10'
		$page_atts['post_class'] 			= ( true === is_singular('page') && !has_post_thumbnail() )?'is-page pt-15 pt-md-20  pt-md-10 pb-4 pb-sm-10 container-fluid':$page_atts['post_class'];
		$page_atts['post_class'] 			= ( is_singular() && true === is_front_page() )?'is-home pb-4 pb-sm-10 container-fluid':$page_atts['post_class'];
	else : 
		// Type plain header 
		$page_atts['post_class'] 			= '--pt-10 pt-5 pt-md-10 pb-4 pb-sm-10 container-fluid'; //'mt-10 mb-10 --pt-10 --pb-4 pb-sm-10'
		$page_atts['post_class'] 			= ( true === is_singular('page') )?'is-page --pt-10 pt-5 pt-md-10 pb-4 pb-sm-10 container-fluid':$page_atts['post_class'];
		$page_atts['post_class'] 			= ( is_singular() && true === is_front_page() )?'is-home pb-4 pb-sm-10 container-fluid':$page_atts['post_class'];
	endif;
	
	if ( true === WAFF_HAS_LEFTSTYLE_BLOG ) :
		$page_atts['post_class'] 			= ( true === is_singular('post') )?'is-post mt-0 --mt-10 --mb-10 pt-10 container-fluid container-10 container-left':$page_atts['post_class'];
		$page_atts['post_class'] 			= ( true === is_home() && false === is_front_page() )?'is-blog pt-4 pb-4 pb-sm-10 container-fluid container-10 container-left':$page_atts['post_class'];
	else : 
		$page_atts['post_class'] 			= ( true === is_singular('post') )?'is-post mt-0 --mt-10 --mb-10 pt-10 container-fluid':$page_atts['post_class'];
		$page_atts['post_class'] 			= ( true === is_home() && false === is_front_page() )?'is-blog pt-4 pb-4 pb-sm-10 container-fluid':$page_atts['post_class'];
	endif;

	// MAIN Extra post classes 
	$page_atts['post_type_class'] 		= ( true === is_singular() )?'single-'.get_post_type():'site-content'; 
	$page_atts['post_type_class'] 	   .= ( true === is_tax() )?' single-'.$wp_query->get_queried_object()->taxonomy:''; 
	$page_atts['forcewide_class'] 		= ( true === is_search() )?'is-wide':''; 
	$page_atts['forcewide_class'] 		= ( true === is_404() )?'is-404 bg-dark':''; 

	if ( is_singular('directory') )  
		$page_atts['post_class'] 		= 'is-directory p-0 pb-4 pb-sm-10 container-fluid'; 

	if ( '' != WAFF_BLOG_STYLE )
		$page_atts['post_class'] 			.= ' is-blog-style-'.WAFF_BLOG_STYLE; 

	// Get post meta fields
	//$prefix = 'waff_post_';
	//$page_atts = array();
	//$page_atts['post_color'] 			= rwmb_meta( $prefix . 'color', $args, $post->ID );

	// Get page meta fields 
	if ( is_singular(array('post', 'page')) || true === $is_blog ) {
		// Get page meta fields
		$prefix = 'waff_page_';
		$page_atts['title'] 				= rwmb_meta( $prefix . 'title', $args, $ID );
		$page_atts['subtitle'] 				= rwmb_meta( $prefix . 'subtitle', $args, $ID );
		$page_atts['anchors'] 				= rwmb_meta( $prefix . 'anchors', $args, $ID );
		$page_atts['header_style'] 			= rwmb_meta( $prefix . 'header_style', $args, $ID );
		$page_atts['header_image'] 			= rwmb_meta( $prefix . 'header_image', $args, $ID );
		$page_atts['header_color'] 			= rwmb_meta( $prefix . 'header_color', $args, $ID );
		$page_atts['header_image_style'] 	= rwmb_meta( $prefix . 'header_image_style', $args, $ID );
		$page_atts['advanced_class'] 		= rwmb_meta( $prefix . 'advanced_class', $args, $ID ); // Add Dinard

		// Get post meta fields
		$page_atts['post_color'] 			= rwmb_meta( '_waff_bg_color_metafield', $args, $ID );
		$page_atts['post_color_class']		= ( isset($page_atts['post_color']) )?'style="background-color:'.$page_atts['post_color'].'!important;"':'';

		// Override classes 
		$page_atts['post_class'] 	   		= ( isset($page_atts['advanced_class']) && $page_atts['advanced_class'] != '' )?' ' . $page_atts['advanced_class'] . ' ':$page_atts['post_class']; 
	}

	// Force some page headers
	if ( is_tax('room') ) {
		$page_atts['header_style'] = 'modern';
	}

	?>

	<?php wp_body_open(); ?>

	<?php WaffTwo\browser_notice(); ?>	

	<div id="page" class="site">

		<a class="skip-link screen-reader-text" href="#site-content"><?php esc_html_e( 'Skip to content', 'go' ); ?></a>

		<!-- Begin: HEADER -->				

		<?php if ( true === WAFF_HAS_PREHEADER ) : ?>
		<!-- Preheader -->
		<?php  get_template_part( 'partials/preheaders/preheader-'.WAFF_PARTIALS ); ?>
		<?php endif; ?>
		
		<!-- Nav / before -->
		<?php  if ( defined('WAFF_PARTIALS') && 'fifam' === WAFF_PARTIALS ) { get_template_part( 'partials/navs/nav-'.WAFF_PARTIALS ); } ?>
		
		<!-- Header -->
		<?php  get_template_part( 'partials/headers/header-'.WAFF_PARTIALS ); //get_template_part( 'partials/headers/header-standard'); // Initial header ?>

		<!-- Nav / after -->
		<?php  if ( defined('WAFF_PARTIALS') && 'dinard' === WAFF_PARTIALS ) { get_template_part( 'partials/navs/nav-'.WAFF_PARTIALS ); } ?>
		<?php  if ( defined('WAFF_PARTIALS') && 'diag' === WAFF_PARTIALS ) { get_template_part( 'partials/navs/nav-'.WAFF_PARTIALS ); } ?>
		<?php  if ( defined('WAFF_PARTIALS') && 'rsfp' === WAFF_PARTIALS ) { get_template_part( 'partials/navs/nav-'.WAFF_PARTIALS ); } ?>

		<?php if ( true === WAFF_HAS_ADVERT ) : ?>
		<!-- Preheader -->
		<?php  get_template_part( 'partials/adverts/advert-'.WAFF_PARTIALS ); ?>
		<?php endif; ?>
		
		<!-- Homeslide -->
		<?php if ( true === WAFF_HAS_HOMESLIDE ) : ?>
		<?php if ( defined('WAFF_PARTIALS') && !is_home() && is_front_page() ) { get_template_part( 'partials/homeslides/homeslide-'.WAFF_PARTIALS); } ?>
		<?php endif; ?>

		<!-- Page titles -->
		<?php if ( true === $is_blog ) : 					get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'blog'); ?>
		<?php elseif ( is_singular('film') ) : 				get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'film'); ?>
		<?php elseif ( is_singular('jury') ) : 				get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'jury'); ?>
		<?php elseif ( is_singular('partenaire') ) : 		get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'partenaire'); ?>
		<?php elseif ( is_singular('projection') ) : 		get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'projection'); ?>
		<?php elseif ( is_singular('post') ) : 				get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'post'); ?>
		<?php elseif ( is_singular('directory') ) : 		get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'directory'); ?>
		<?php elseif ( is_tax('section') ) : 				get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'section'); ?>
		<?php elseif ( is_tax('room') ) : 					get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS, '', 'room'); ?>
		<?php elseif ( is_page() && false === $is_home ) :  get_template_part( 'partials/pagetitles/pagetitle-'.WAFF_PARTIALS); ?>
		<?php else: 										print("<!-- ::is-home --> "); ?>
		<?php endif; ?>	
		
		<!-- End: HEADER -->				

		<!-- Begin: MAIN -->
		<main id="main" class="<?= $page_atts['post_type_class'] ?> site-main <?= $page_atts['forcewide_class'] ?> <?= $page_atts['post_class'] ?> <?= $page_atts['page_mode_class'] ?> is-formatted" role="main" <?= (isset($page_atts['post_color_class']))?$page_atts['post_color_class']:'' ?>> 
		<!-- Remove .is-formatted to remove margins / paddings / styling -->