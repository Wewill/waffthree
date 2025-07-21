<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package WaffTwo\Theme
 */

namespace WaffTwo\Theme;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	
	// Register Custom Post Types
	require_once get_theme_file_path( 'includes/admin/custom_post_types.php' );

	// Adds custom order for * post type 
	require_once get_theme_file_path( 'includes/admin/custom_post_menuorder.php' );

	// Set meta boxes
	require_once get_theme_file_path( 'includes/admin/custom_metaboxes.php' );

	// Adds custom fields to menus 
	require_once get_theme_file_path( 'includes/admin/custom_menu_fields.php' );

	// Adds widgets 
	require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-categories.php' );
	require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-newsletter.php' );
	require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-partners.php' );
	require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-contact.php' );
	require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-twocols.php' );
	if( true === WAFF_ISFILM_VERSION ) {
		require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-section.php' );
		//require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-programmation.php' );
		require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-programmation-ajax.php' ); //#43 New ajax version 
		require_once get_theme_file_path( 'includes/admin/widgets/custom-wp-widget-counter.php' );
	}

	// Custom Medias sizes by post types 
	require_once get_theme_file_path( 'includes/admin/custom_media_sizes.php');
	
//	include_once( get_stylesheet_directory().'/path/to/file.php' );

	// Actions 

	// Enqueue custom sctipts and styles
	add_action( 'wp_enqueue_scripts', 		$n( 'waff_child_enqueue_styles' ) );
	add_action( 'wp_enqueue_scripts', 		$n( 'waff_child_enqueue_scripts' ), 90 );
	add_action( 'admin_enqueue_scripts', 	$n( 'waff_child_admin_scripts' ), 90 );
	add_filter( 'style_loader_tag', 		$n( 'waff_add_style_attributes' ), 10, 2 ); //#42
	add_filter( 'script_loader_tag', 		$n( 'waff_add_script_attributes' ), 10, 2 ); //#42

	// Custom inline scripts
	add_action( 'wp_enqueue_scripts', 		$n( 'waff_localstorage_scripts'), 110 );
	add_action( 'wp_head', 					$n( 'waff_mailchimp_scripts'), 999 );

	// Init
	add_action( 'after_setup_theme', 		$n( 'waff_setup') );
	add_action( 'widgets_init', 			$n( 'waff_widgets_init') );
	//add_filter( 'theme_page_templates', $n( 'waff_remove_page_templates' );
	
	// Stop cache SC
	add_shortcode('stop_cache', 			$n( 'waff_shortcode_no_cache') );
	
	// Remove comments 
	add_action('init', 						$n( 'waff_remove_comment_support' ), 100);
	add_action('init', 						$n( 'waff_handle_custom_login' ), 110);
	
	// Custom social icons Walker 
	add_filter( 'walker_nav_menu_start_el', $n( 'waff_nav_menu_social_icons' ), 20, 4 );

	// Adds a page option for theme in settings
	add_filter( 'mb_settings_pages', 		$n( 'waff_add_theme_setting_page' ) );
	add_filter( 'rwmb_meta_boxes',  		$n( 'waff_add_theme_custom_fields_to_setting_page' ) );

}

/**
 * Setup options 
 */

 function waff_add_theme_setting_page( $settings_pages ) {
	$settings_pages[] = [
		'menu_title' => __( 'Theme', 'waff' ),
		'id'         => 'theme-settings',
		'parent'     => 'options-general.php',
		'class'      => 'custom_css',
		'style'      => 'no-boxes',
		// 'message'    => __( 'Custom message', 'waff' ), // Saved custom message
		'customizer' => true,
		'icon_url'   => 'dashicons-admin-generic',
	];

	return $settings_pages;
}


function waff_add_theme_custom_fields_to_setting_page( $meta_boxes ) {
	$prefix = 'waff_';

	$meta_boxes[] = [
		'id'             => 'theme-settings-fields',
		'settings_pages' => ['theme-settings'],
		'fields'         => [
			[
				'name'            => __( 'Homeslide background', 'waff' ),
				'id'              => $prefix . 'homeslide_background',
				'type'            => 'image_advanced',
			],
			[
				'name'            => __( 'Homeslide content', 'wa-rsfp' ),
				'id'              => $prefix . 'homeslide_content',
                'type'       => 'text_list',
                'options'    => [
                    'Ligne 1' => 'Ligne 1',
                    'Ligne 2' => 'Ligne 2',
                ],
                'clone'      => true,
                'sort_clone' => true,
                'max_clone'  => 5,
				// 'options'         => $this->posts_options_callback(),
			],
		],
	];

	return $meta_boxes;
}

function waff_get_theme_homeslide_background() {
	$prefix = 'waff_';
	return rwmb_meta( $prefix . 'homeslide_background', [ 'size' => 'full', 'limit' => 1, 'object_type' => 'setting' ], 'theme-settings' );
}

function waff_get_theme_homeslide_content() {
	$prefix = 'waff_';
	return rwmb_meta( $prefix . 'homeslide_content', [ 'object_type' => 'setting' ], 'theme-settings' );
}


/* LOAD PARENT THEME STYLES & SCRIPTS
================================================== */
function waff_child_enqueue_styles() {
    //wp_enqueue_style( 'waff-parent-style', get_template_directory_uri() . '/style.css' );
    //wp_enqueue_style( 'waff-parent-style', get_stylesheet_directory_uri() . '/parent-style.css' ); // Keep some styles from waff
    wp_enqueue_style( 'bootstrap', 		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', 				array(), '5.3.2'); 
    wp_enqueue_style( 'bootstrap-icons','https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', 	array(), '1.11.3'); 
    wp_enqueue_style( 'aos', 			'https://unpkg.com/aos@next/dist/aos.css', 												array(), '3.0.0'); // Version beta next
    wp_enqueue_style( 'slick', 			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', 					array(), '1.8.1'); 
    wp_enqueue_style( 'fancybox', 		'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', 	array(), '3.5.7'); 
    wp_enqueue_style( 'fontawesome',	'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css', 			array(), '5.13.0'); 
	//
    wp_enqueue_style( 'waff-style', get_stylesheet_directory_uri() . '/dist/css/style-'.WAFF_STYLES.'.css', 					array(), WAFF_THEME_VERSION); // Will import framework.css
    //wp_enqueue_style( 'waff-logo', get_stylesheet_directory_uri() . '/css/logo-invert.css', 									array(), '1.0.0'); 	    

	// Competitions only
	if ( is_singular( 'competitions' ) ) {
		// wp_enqueue_style( 'gridjs', 'https://unpkg.com/gridjs/dist/theme/mermaid.min.css', 										array(), '1.0.0' );
	}
}

function waff_child_enqueue_scripts() {
	// Jquery
    // wp_enqueue_script( 'modernizer', 			get_stylesheet_directory_uri() . '/dist/js/theme/vendor/modernizr-2.8.3-respond-1.4.2.min.js', 	array(), '2.8.3',true);
	// Distant 
	wp_enqueue_script( 'bootstrap', 			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',			array(), '5.3.2',true);
    wp_enqueue_script( 'slick', 				'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',					array(), '1.8.1',true);
    wp_enqueue_script( 'aos', 					'https://unpkg.com/aos@next/dist/aos.js',												array(), '3.0.0',true); // Version beta next
    wp_enqueue_script( 'fancybox', 				'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js',		array(), '3.5.7',true);
	wp_enqueue_script( 'jquery-lazy', 			'https://cdn.jsdelivr.net/npm/jquery-lazy@1.7.11/jquery.lazy.min.js', 					array(), '1.7.11',true);
	wp_enqueue_script( 'jquery-lazy-plugins', 	'https://cdn.jsdelivr.net/npm/jquery-lazy@1.7.11/jquery.lazy.plugins.min.js',			array(), '1.7.11',true);
    // wp_enqueue_script( 'color-thief', 		'https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js',			array(), '2.3.0',true); // Done w/ php
	// Local
	wp_enqueue_script( 'waff-logo', 			get_stylesheet_directory_uri() . '/dist/js/theme/logo-invert.js', 						array(), WAFF_THEME_VERSION,true); // Passer dans le header
	wp_enqueue_script( 'waff-main', 			get_stylesheet_directory_uri() . '/dist/js/theme/main.js', 								array('jquery'), WAFF_THEME_VERSION,true);
	wp_enqueue_script( 'waff-custom', 			get_stylesheet_directory_uri() . '/dist/js/theme/custom.js', 							array('jquery'), '1.0.0',true);
	//wp_enqueue_script( 'countdown', 			get_stylesheet_directory_uri() . '/dist/js/theme/countdown.js', 						array(),'1.0.0',true); // Passer dans le block

	if( true === WAFF_ISFILM_VERSION ) {
		wp_enqueue_script( 'fitty', 				get_stylesheet_directory_uri() . '/dist/js/theme/vendor/fitty.min.js', 				array(), '1.0.0',true);
		wp_enqueue_script( 'waff-programmation-ajax', get_stylesheet_directory_uri() . '/dist/js/theme/programmation-ajax.js', 			array('jquery'), WAFF_THEME_VERSION,true); //#43 New ajax version 
	}

	// Competitions only
	if ( is_singular( 'competitions' ) ) {
		wp_enqueue_script( 'gridjs', 'https://unpkg.com/gridjs/dist/gridjs.umd.js', 													array(), '1.0.0', true );
	}
}

// Add attributes / integrity check
function waff_add_style_attributes( $html, $handle ) {
    if ( 'bootstrap' === $handle ) return str_replace( "media='all'", "media='all' integrity='sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN' crossorigin='anonymous'", $html );
    return $html;
}
function waff_add_script_attributes( $html, $handle ) {
	if ( 'bootstrap' === $handle ) return str_replace( "id='bootstrap-js'", "id='bootstrap-js' integrity='sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL' crossorigin='anonymous'", $html );
    return $html;
}


/* Enqueue PARENT ADMIN SCRIPTS
================================================== */
function waff_child_admin_scripts($hook) {
	$screen       = get_current_screen();
	$screen_id    = $screen ? $screen->id : '';
	
	// For Edit collection / brand pages
	//if ( in_array( $screen_id, array( 'edit-collection', 'edit-brand', 'edit-theme' ) ) ) 

	// For All 
    wp_enqueue_style( 'child-custom-styles', 	get_stylesheet_directory_uri() . '/dist/css/admin/child-custom-styles.css', array(), '1.0.0'); 
    wp_enqueue_script( 'child-custom-admin', 	get_stylesheet_directory_uri() . '/dist/js/admin/child-custom-admin.js','','',true);
}	

/**
 * Enqueue inline script for the accessibility settings module.
 */
function waff_localstorage_scripts() {

	$night = get_theme_mod( 'night_mode', waff_defaults( 'night_mode' ) );

	// If the option is not available, or we're not in the Customizer, return.
	if ( $night || is_customize_preview() ) {
		echo '
	<!-- BEGIN:: Theme night_mode-->
	<script type="text/javascript">
		! function(e, t, n) {
			"use strict";
			function o(e) { var n = localStorage.getItem(e); n && ( "true" === n && t.documentElement.classList.add(e) ) }
			"querySelector" in t && "addEventListener" in e, "localStorage" in e && (o("night-mode") )
		}(window, document)
	</script>
	<!-- END:: Theme night_mode-->
	';
	}
}

/**
 * Enqueue inline script for mailchimp popup.
 */
function waff_mailchimp_scripts() {

	$mailchimp_popup = get_theme_mod( 'mailchimp_popup', waff_defaults( 'mailchimp_popup' ) );

	// If the option is not available, or we're not in the Customizer, return.
	if ( $mailchimp_popup ) { // || is_customize_preview() Do not show on preview 
		echo '
	<!-- BEGIN:: Mailchimp popup integration -->
	<script id="mcjs">
		!function(c,h,i,m,p){
			m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)
		}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/b6c9c94df00c02d584564bcf2/f7d077a1f79e1dd7510312a37.js");
	</script>
	<!-- END:: Mailchimp popup integration -->
	';
	}
}

/* SETUP WAFF THEME 
================================================== */
/*function waff_setup() {
	register_nav_menus(
		array(
			'primary' 			=> esc_html__( 'Primary Menu', 'waff' ),
			'footer'  			=> esc_html__( 'Footer Menu', 'waff' ),
			'social'  			=> esc_html__( 'Social Menu', 'waff' ),
			'credits'  			=> esc_html__( 'Credits Menu', 'waff' ),
			'footer-secondary'  => esc_html__( 'Footer Secondary Menu', 'waff' ),
		)
	);
}*/
function waff_setup() {
	register_nav_menus(
		array(
			'primary' 			=> esc_html__( 'Primary Menu', 'waff' ),
			'secondary' 		=> esc_html__( 'Secondary Menu', 'waff' ),
			'social'  			=> esc_html__( 'Social Menu', 'waff' ),
			'credits'  			=> esc_html__( 'Credits Menu', 'waff' ),
			'footer-1'  		=> esc_html__( 'Footer Menu', 'waff' ),
			'footer-2'  		=> esc_html__( 'Footer Secondary Menu', 'waff' ),
			'footer-3' 	 		=> esc_html__( 'Footer Third Menu', 'waff' ),
			'preheader-1'  		=> esc_html__( 'Preheader Menu', 'waff' ),
		)
	);
}


/* REGISTER SIDEBARS
================================================== */
function waff_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog sidebar', 'waff' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Appears in the blog.', 'waff' ),
			'before_widget' => '<!-- #%1$s --><section id="%1$s" class="widget %2$s clearfix">',
			'after_widget'  => '</section>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer section (before)', 'waff' ),
			'id'            => 'sidebar-before',
			'description'   => esc_html__( 'Appears in the site footer before the footer menu.', 'waff' ),
			'before_widget' => '<!-- #%1$s --><section id="%1$s" class="widget %2$s clearfix">',
			'after_widget'  => '</section>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer section (after)', 'waff' ),
			'id'            => 'sidebar-after',
			'description'   => esc_html__( 'Appears in the site footer after the footer menu.', 'waff' ),
			'before_widget' => '<!-- #%1$s --><section id="%1$s" class="widget %2$s clearfix">',
			'after_widget'  => '</section>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>',
		)
	);
	if ( true === WAFF_REGISTER_PROGRAMMATION_SIDEBAR ) {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Programmation section (modal)', 'waff' ),
				'id'            => 'sidebar-programmation',
				//'class'			=> 'sidebar-programmation',
				'description'   => esc_html__( 'Appears in the programmation modal.', 'waff' ),
				'before_widget' => '<!-- #%1$s --><div id="%1$s" class="widget %2$s clearfix">',
				'after_widget'  => '</div>',
				'before_title'  => '<h6 class="widget-title">',
				'after_title'   => '</h6>',
			)
		);
	}
}

/* REMOVE UNWANTED PAGE TEMPLATES FROM PARENT THEME 
================================================== */
/*
*	Unset by using file name
*/
function waff_remove_page_templates( $templates ) {
    unset( $templates['template-$$$$.php'] );
    return $templates;
}

/* DO NO CACHE SHORTCODES 
================================================== */
// Créer le shortcode [stop_cache] à mettre dans les pages ne devant pas être mise en cache.
function waff_shortcode_no_cache($atts){
    define('DONOTCACHEPAGE',1);
}

/* REMOVE COMMENTS from post and pages 
================================================== */
function waff_remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}

/* HANDLE Template custom log in / log out from form post modal 
================================================== */
function waff_handle_custom_login() {
	
    if ( isset( $_POST['submit'] ) && isset( $_POST['action'] ) && $_POST['action'] == 'custom_login' ) {
        $user_login 	= sanitize_user( $_POST['user_login'] );
        $password 		= esc_attr( $_POST['user_password'] );
        $redirect_to 	= esc_url( $_POST['redirect_to'] );

        $creds = array(
            'user_login'    => $user_login,
            'user_password' => $password,
            'remember'      => true
        );

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
			echo '<div class="alert alert-action-1 alert-dismissible fade show position-fixed bottom-0 left-0 zi-max m-4" role="alert">
			' . $user->get_error_message() . '
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		  </div>';
        } else {
			// print_r($user);
            wp_redirect( home_url() );
            exit;
			// echo '<div class="alert alert-action-3 alert-dismissible fade show position-fixed bottom-0 left-0 zi-max m-4" role="alert">
			// 	You are now logged in !
			// 	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			// </div>';
        }
    }
}


/* HEX TO RGB COLOR
================================================== */
if ( ! function_exists( 'waff_hex2rgb' ) ) {
    function waff_hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
            $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
            return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );

        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
    }
}

    
/**
 * Return icon markup.
 * Based on the function from Twenty Seventeen.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 *     @type string $icon  Required SVG icon filename.
 *     @type string $title Optional SVG title.
 *     @type string $desc  Optional SVG description.
 * }
 * @return string SVG markup.
 */
function waff_get_icon( $args = array() ) {
	// Make sure $args are an array.
	if ( empty( $args ) ) {
		return __( 'Please define default parameters in the form of an array.', 'waff' );
	}

	// Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return __( 'Please define an icon name.', 'waff' );
	}

	// Set defaults.
	$defaults = array(
		'icon'     => '',
		'title'    => '',
		'desc'     => '',
		'fallback' => false,
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';

	// Set ARIA.
	$aria_labelledby = '';

	/*
	 * waff doesn't use the SVG title or description attributes; non-decorative icons are described with .screen-reader-text.
	 *
	 * However, child themes can use the title and description to add information to non-decorative SVG icons to improve accessibility.
	 *
	 * Example 1 with title: <?php echo waff_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ) ); ?>
	 *
	 * Example 2 with title and description: <?php echo waff_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ) ); ?>
	 *
	 * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
	 */
	if ( $args['title'] ) {
		$aria_hidden     = '';
		$unique_id       = uniqid();
		$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

		if ( $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
		}
	}

	// Begin SVG markup.
	$icon = '<i class="icon ' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

	// Display the title.
	if ( $args['title'] ) {
		$icon .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

		// Display the desc only if the title is already set.
		if ( $args['desc'] ) {
			$icon .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
		}
	}

	// Add some markup to use as a fallback for browsers that do not support SVGs.
	if ( $args['fallback'] ) {
		$icon .= '<span class="icon-fallback ' . esc_attr( $args['icon'] ) . '"></span>';
	}

	$icon .= '</i>';

	return $icon;
}

/**
 * Display SVG icons in social links menu.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string  $item_output The menu item output with social icon.
 */
function waff_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	$icons = waff_social_links_icons();

	// Change icon inside social links menu if there is supported class.
	if ( 'social' === $args->theme_location ) {
		// Look for icon classes
		foreach ( $icons as $attr => $value ) {
			if ( in_array($attr, $item->classes) ) {
				$item_output = str_replace( $args->link_after, '</span>' . waff_get_icon( array( 'icon' => esc_attr( $value ) ) ), $item_output );
			}
		}
	}
	// Else icon chain

	return $item_output;
}

/**
 * Returns an array of supported social links (URL and icon name).
 *
 * @return array $social_links_icons
 */
function waff_social_links_icons() {

	$social_links_icons = array(
		//FONTAWESOME
		'fa-facebook'				=> 'fab fa-facebook',
		'fa-facebook-f'     		=> 'fab fa-facebook-f',
		'fa-facebook-messenger'     => 'fab fa-facebook-messenger',
		'fa-facebook-square'      	=> 'fab fa-facebook-square',
		'fa-twitter'  				=> 'fab fa-twitter',
		'fa-instagram'     		 	=> 'fab fa-instagram',
		'fa-cc-apple-pay'   		=> 'fab fa-cc-apple-pay',
		'fa-cc-paypal'      		=> 'fab fa-cc-paypal',
		'fa-cc-visa'      			=> 'fab fa-cc-visa',
		'fa-cc-mastercard'     		=> 'fab fa-cc-mastercard',
		'fa-cc-stripe'      		=> 'fab fa-cc-stripe',
		'fa-creative-commons'      	=> 'fab fa-creative-commons',
		'fa-creative-commons-by'	=> 'fab fa-creative-commons-by',
		'fa-deezer'      			=> 'fab fa-deezer',
		'fa-figma'      			=> 'fab fa-figma',
		'fa-google'      			=> 'fab fa-google',
		'fa-google-drive'      		=> 'fab fa-google-drive',
		'fa-instagram'      		=> 'fab fa-instagram',
		'fa-instagram-square'      	=> 'fab fa-instagram-square',
		'fa-itunes'      			=> 'fab fa-itunes',
		'fa-itunes-note'      		=> 'fab fa-itunes-note',
		'fa-kickstarter'      		=> 'fab fa-kickstarter',
		'fa-kickstarter-k'      	=> 'fab fa-kickstarter-k',
		'fa-linkedin'      			=> 'fab fa-linkedin',
		'fa-linkedin-in'      		=> 'fab fa-linkedin-in',
		'fa-paypal'      			=> 'fab fa-paypal',
		'fa-pinterest'				=> 'fab fa-pinterest',
		'fa-pinterest-p'      		=> 'fab fa-pinterest-p',
		'fa-pinterest-square'      	=> 'fab fa-pinterest-square',
		'fa-slack'      			=> 'fab fa-slack',
		'fa-skype'      			=> 'fab fa-skype',
		'fa-snapchat'      			=> 'fab fa-snapchat',
		'fa-snapchat-ghost'      	=> 'fab fa-snapchat-ghost',
		'fa-snapchat-square'      	=> 'fab fa-snapchat-square',
		'fa-soundcloud'      		=> 'fab fa-soundcloud',
		'fa-spotify'      			=> 'fab fa-spotify',
		'fa-stripe'      			=> 'fab fa-stripe',
		'fa-telegram-plane'      	=> 'fab fa-telegram-plane',
		'fa-telegram'      			=> 'fab fa-telegram',
		'fa-trello'      			=> 'fab fa-trello',
		'fa-tumblr'      			=> 'fab fa-tumblr',
		'fa-tumblr-square'      	=> 'fab fa-tumblr-square',
		'fa-twitter'     	 		=> 'fab fa-twitter',
		'fa-twitter-square'      	=> 'fab fa-twitter-square',
		'fa-viadeo'      			=> 'fab fa-viadeo',
		'fa-viadeo-square'      	=> 'fab fa-viadeo-square',
		'fa-vimeo'      			=> 'fab fa-vimeo',
		'fa-vimeo-square'      		=> 'fab fa-vimeo-square',
		'fa-vimeo-v'      			=> 'fab fa-vimeo-v',
		'fa-whatsapp'      			=> 'fab fa-whatsapp',
		'fa-whatsapp-square'      	=> 'fab fa-whatsapp-square',
		'fa-youtube'      			=> 'fab fa-youtube',
		'fa-mailchimp'      		=> 'fab fa-mailchimp',
		'fa-newspaper'      		=> 'fab fa-newspaper',
		'fa-paper-plane'      		=> 'fas fa-paper-plane',
		'fa-flickr'      			=> 'fab fa-flickr',
		'fa-envelope'      			=> 'fa fa-envelope',
		//FIFAM
		'info'     		 			=> 'icon icon-info',
		'time'      				=> 'icon icon-time',
		'guest'      				=> 'icon icon-guest',
		'arrow'      				=> 'icon icon-arrow',
		'without'      				=> 'icon icon-without',
		'alert'      				=> 'icon icon-alert',
		'warning'      				=> 'icon icon-warning',
		'access'      				=> 'icon icon-access',
		'sun'      					=> 'icon icon-sun',
		'young'      				=> 'icon icon-young',
		'3d'      					=> 'icon icon-3d',
		'vonst'      				=> 'icon icon-vonst',
		'vosta'      				=> 'icon icon-vosta',
		'brochure'      			=> 'icon icon-brochure',
		'prev'      				=> 'icon icon-prev',
		'next'      				=> 'icon icon-next',
		'up'      					=> 'icon icon-up',
		'down'      				=> 'icon icon-down',
		'left'      				=> 'icon icon-left',
		'down-right'      			=> 'icon icon-down-right',
		'down-left'      			=> 'icon icon-down-left',
		'links'      				=> 'icon icon-links',
		'ok'      					=> 'icon icon-ok',
		'pic'      					=> 'icon icon-pic',
		'catalogue'      			=> 'icon icon-catalogue',
		'right-light'      			=> 'icon icon-right-light',
		'left-light'      			=> 'icon icon-left-light',
		'pin'      					=> 'icon icon-pin',
		'play'      				=> 'icon icon-play',
		'download'      			=> 'icon icon-download',
		'down-right-light'      	=> 'icon icon-down-right-light',
		'down-left-light'      		=> 'icon icon-down-left-light',
		'right'      				=> 'icon icon-right',
		'infinite'      			=> 'icon icon-infinite',
		'pin'      					=> 'icon icon-pin',
	);

	return apply_filters( 'waff_social_links_icons', $social_links_icons );
}
	
/**
  * Returns social menu 
*/
function waff_get_social_menu($colorclass = 'color-white color-light') {
	$menu = wp_nav_menu(
		array(
			'theme_location' => 'social',
			'items_wrap'      => '%3$s',
			'container'       => false,
			'echo'            => false,
			'depth'          => '0',
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>' . waff_get_icon( array( 'icon' => 'icon-links' ) ),
		)
	);

	if (is_null($menu)) {
		$menu = '';
	}

	return ( preg_replace( '/(<a )/', '<a class="link mx-2 '.$colorclass.'" ', strip_tags( $menu, '<a><span><i><title><desc>' ) ) );
	
	/*wp_nav_menu(
		array(
			'theme_location' => 'social',
			'menu_class'     => 'link color-white mx-2',
			'depth'          => 1,
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>' . coblocks_get_svg( array( 'icon' => 'icon-links' ) ),
		)
	);*/
}


/**
 * Returns Edition badge
 */
function waff_get_edition_badge($colorclass = 'color-black color-dark') {
	return sprintf('
		<!-- Edition badge -->
		<div class="text-center w-100-px d-block position-relative %s">
			<div><p class="edition-badge lh-1 m-0 display light" style="white-space: nowrap; display: inline-block; font-size: 87.5px;">%s</p></div>
			<div><p class="edition-badge lh-1 m-0 display bold" style="white-space: nowrap; display: inline-block; font-size: 24.096385542168676px;">%s <i class="icon icon-right" style="font-size: .65em;"></i> %s</p></div>
			<div><p class="edition-badge lh-1 m-0 display ls-1" style="white-space: nowrap; display: inline-block; font-size: 20.895522388059703px;">%s.%s</p></div>
		</div>
		',
		$colorclass,
		do_shortcode( '[getcurrenteditionsc]' ),
		do_shortcode( '[getcurrenteditionsc display="startdate"]' ),
		do_shortcode( '[getcurrenteditionsc display="enddate"]' ),
		do_shortcode( '[getcurrenteditionsc display="month"]' ),
		do_shortcode( '[getcurrenteditionsc display="year"]' )
	);
}

/**
 * Returns Languages select
 */
function waff_get_languages() {
	if (function_exists('qtranxf_getLanguage')) {

		$currentLang = qtranxf_getLanguage();
		$switchLang = 'en';

		if(is_404()) $url = get_option('home'); else $url = '';
		if($currentLang == 'fr') $switchLang = 'en'; else $switchLang = 'fr';

		$output .= '<button class="aux-languages py-0"><a class="position-relative d-inline-block" href="'.qtranxf_convertURL($url, $switchLang, false, true).'"><i class="icon icon-flag"></i><span class="currentlang">'.$currentLang.'</span></a>' . "\n";
		if ( function_exists( 'qtranxf_generateLanguageSelectCode' ) ) {	
			ob_start();
			$args = array('type'=> 'text', 'format' => '', 'id' => 'waff_get_languages');
			qtranxf_generateLanguageSelectCode($args);
			$output .= ob_get_clean();
		}
		$output .= '</button>';

		print($output);

	}
}