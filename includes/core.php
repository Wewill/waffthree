<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package Go\Core
 */

namespace WaffTwo\Core;

use function Go\hex_to_hsl;
use function Go\load_inline_svg;
//use function Go\AMP\is_amp;
//use function Go\get_palette_color;

use function Go\Core\fonts_url as fonts_url;


/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	// GO
	// add_action( 'after_setup_theme', $n( 'development_environment' ) );
	// add_action( 'after_setup_theme', $n( 'i18n' ) );
	// add_action( 'after_setup_theme', $n( 'theme_setup' ) );
	// add_action( 'admin_init', $n( 'editor_styles' ) );
	// add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	// add_action( 'enqueue_block_editor_assets', $n( 'block_editor_assets' ) );
	// add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	// add_action( 'wp_print_footer_scripts', $n( 'skip_link_focus_fix' ) );
	// add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );
	// add_filter( 'body_class', $n( 'body_classes' ) );
	// add_filter( 'nav_menu_item_title', $n( 'add_dropdown_icons' ), 10, 4 );
	// add_filter( 'go_page_title_args', $n( 'filter_page_titles' ) );
	// add_filter( 'comment_form_defaults', $n( 'comment_form_reply_title' ) );
	// add_filter( 'the_content_more_link', $n( 'read_more_tag' ) );


	// Child
	add_action( 'after_setup_theme', $n( 'waff_i18n' ) );
	add_action( 'after_setup_theme', $n( 'waff_theme_setup' ), 20 );
	
	add_action( 'admin_init', $n( 'waff_editor_styles' ) );

	add_action( 'wp_enqueue_scripts', $n( 'waff_styles') );
	add_action( 'enqueue_block_editor_assets', $n( 'waff_block_editor_assets' ) );

	add_filter( 'go_default_design_style', $n( 'waff_default_design_style'), 20); // > if not Bug colors 
	add_filter( 'go_default_color_scheme', $n( 'waff_default_color_scheme'), 20); // > if not Bug colors 
	
	// // add_action( 'after_setup_theme', $n( 'waff_theme_setup' ), 20 ); // Moved up 
	add_filter( 'go_design_styles', $n( 'waff_design_styles'), 2 ); // Do not need to load again > if not Bug colors  

	add_filter( 'go_header_variations', $n( 'waff_header_variations') );
	add_filter( 'go_default_header_variation', $n( 'waff_default_header_variation') );
	
	add_filter( 'go_footer_variations', $n( 'waff_footer_variations') );
	add_filter( 'go_default_footer_variation', $n( 'waff_default_footer_variation') );
	
	add_filter( 'body_class', $n( 'body_classes' ) );
	add_filter( 'go_page_title_args', $n( 'waff_filter_page_titles' ), 20, 1);
	
	remove_filter( 'nav_menu_item_title', 'Go\Core\\add_dropdown_icons');
	add_filter( 'nav_menu_item_title', $n( 'waff_add_dropdown_icons' ), 10, 4 );

	if ( !is_login() && !is_admin() && !function_exists('rwmb_meta') ) {
		wp_die('Error : please install Meta Box plugin.');
	}
	
	if ( !is_login() && !is_admin() && !function_exists('waff_load_textdomain') ) {
		wp_die('Error : please install WAFF Functions plugin.');
	}

	// if ( !is_admin() && !function_exists('wacwk_load_textdomain') ) {
	// 	wp_die('Error : please install WAFF Custom Walker plugin.');
	// }

}

/**
 * Makes Theme available for translation.
 *
 * Translations can be added to the /languages directory.
 * If you're building a theme based on Go, change the
 * filename of '/languages/go.pot' to the name of your project.
 *
 * @return void
 */
function waff_i18n() {
	load_child_theme_textdomain('waff', get_theme_file_path('language/'));
}

/*
	Used for custom meta blocks functions to retrieve colors 
*/ 
function get_palette() {
	//$color_schemes = WaffTwo\Core\get_design_style()['color_schemes']['one'];
	$color_schemes = get_design_style()['color_schemes']['one'];
	$disallowedKeys = array('label');
	$disallowedValues = array('#fff','#FFF','#ffffff','#FFFFFF','#000','#000000');
	$palette = array();
	foreach ($color_schemes as $key => $value) {
	    if (!in_array($key, $disallowedKeys))
	    	if (!in_array($value, $disallowedValues))
				$palette[] = $value;
	}
	return $palette;
}

/**
 * Enqueue block editor assets.
 *
 * @return void
 */
function waff_block_editor_assets() {

	echo ((true === WAFF_DEBUG)?'<code> #waff_block_editor_assets</code>':'');

	// This file provides our Customizer setup, not template partials.
	// phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	require_once get_theme_file_path( 'includes/customizer.php' );

	wp_enqueue_script(
		'child-block-filters',
		wp_unslash( get_theme_file_uri( 'dist/js/admin/child-block-filters.js' ) ),
		array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-components' ),
		GO_VERSION,
		true
	);

	ob_start();

	\WaffTwo\Customizer\waff_inline_css();

	$waff_styles = ob_get_clean();

	wp_localize_script(
		'child-block-filters',
		'ChildBlockFilters',
		array(
			'inlineStyles' => str_replace( ':root', '.editor-styles-wrapper', $waff_styles ),
		)
	);

}

/**
 * Set our Go child theme's default footer variant.
 *
 * @link https://github.com/godaddy-wordpress/go/blob/master/includes/core.php#L977
 * @return void
 */
function waff_footer_variations($default_footer_variations) {
	$child_footer_variations = array(
		'footer-waff' => array(
			'label'         => esc_html_x( 'Footer waff', 'name of the waff footer variation option', 'go' ),
			'preview_image' => get_theme_file_uri( 'dist/images/admin/footer-'.WAFF_PARTIALS.'.svg' ),
			'partial'       => function() {
				return get_template_part( 'partials/footers/footer', WAFF_PARTIALS);
			},
		),
	);
	
	// Merge footer variations, if you want to keep default ones
	// $child_footer_variations = array_merge($default_footer_variations, $child_footer_variations);

	return $child_footer_variations;
}

function waff_default_footer_variation() {
	return 'footer-'.WAFF_PARTIALS;
}

/**
 * Set our Go child theme's default header variant.
 *
 * @link https://github.com/godaddy-wordpress/go/blob/master/includes/core.php#L977
 * @return void
 */
function waff_header_variations($default_header_variations) {
	$child_header_variations = array(
		'header-waff' => array(
			'label'         => esc_html_x( 'Header waff', 'name of the waff header variation option', 'go' ),
			'preview_image' => get_theme_file_uri( 'dist/images/admin/header-'.WAFF_PARTIALS.'.svg' ),
		),
	);
	
	// Merge header variations, if you want to keep default ones
	// $child_header_variations = array_merge($default_header_variations, $child_header_variations);

	return $child_header_variations;
}

function waff_default_header_variation() {
	return 'header-'.WAFF_PARTIALS;
}

/**
 * Add our new design style to Go's design styles.
 *
 * @link https://github.com/godaddy-wordpress/go/blob/master/includes/core.php#L557
 * @return void
 */
function waff_design_styles( $default_design_styles = '' ) {

	echo ((true === WAFF_DEBUG)?'<code> #waff_design_styles</code>':'');
	//echo ((true === WAFF_DEBUG)?'<pre>'.print_r($default_design_styles, 1).'</pre>':'');

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$rtl    = ! is_rtl() ? '' : '-rtl';

//	$default_design_styles['....'] = array(
//	Force style
	$waff_design_styles = array(
		'waff'   => array(
			'slug'          => 'waff',
			'label'         => _x( 'WAFF', 'design style name', 'go' ).' '.WAFF_THEME_NAME,
			'url'           => get_theme_file_uri( "dist/css/design-styles/style-waff{$suffix}.css" ),
			'editor_style'  => get_theme_file_uri( "dist/css/design-styles/style-waff-editor{$suffix}.css" ), //-editor
//			'url'            => get_theme_file_uri( "dist/css/design-styles/style-modern{$rtl}{$suffix}.css" ),
//			'editor_style'   => "dist/css/design-styles/style-modern-editor{$rtl}{$suffix}.css",
			'color_schemes' => array(
				'one'   =>  array_merge( array(
					'label'             => _x( 'Original', 'color palette name', 'go' ), // possibly for alternative (in the future)
				), (array)WAFF_COLORS ),
				// Double because of an error of sibling the right 'one' color schemes instead on waff-one ( new IN WP ? .. )
				'waff-one'   => array_merge( array(
					'label'             => _x( 'Original', 'color palette name', 'go' ), // possibly for alternative (in the future)
				), (array)WAFF_COLORS ),
			),
			/* > No google fonts
			'fonts' => array(
				'Bureau Grot'        => array(
					'400',
					'600',
					'800',
				),
			),*/
			/* > Google font */
			'fonts'          => (array)WAFF_GOOGLEFONTS,
			'font_size'      => '1.05rem',
			'type_ratio'     => '1.275',
			'viewport_basis' => '900',
		),
	);

	// Apply filter to retrieve the right color schemes in GO core functions
	apply_filters( 'go_color_schemes', $waff_design_styles['waff']['color_schemes'], $waff_design_styles['waff'] );

	// Merge header variations, if you want to keep default ones
	// $waff_design_styles = array_merge($default_design_styles, $waff_design_styles);

	return $waff_design_styles;
}

/**
 * Enqueues the editor styles.
 *
 * @return void
 */
function waff_editor_styles() {

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$rtl    = ! is_rtl() ? '' : '-rtl';

	echo ((true === WAFF_DEBUG)?'<code> #waff_editor_styles</code>':'');

	// Enqueue shared editor styles.
	// Do not need to load again > Bug colors ? 
	/*add_editor_style(
		//"dist/css/style-editor{$rtl}{$suffix}.css"
		get_theme_file_uri( "dist/css/style-editor{$rtl}{$suffix}.css" )
	);*/
	
	// Enqueue design style editor styles.
	// Already load in GO 
	// $design_style = get_design_style();

	// if ( $design_style && isset( $design_style['editor_style'] ) ) {
	// 	add_editor_style(
	// 		$design_style['editor_style']
	// 	);
	// }

	// Enqueue google fonts into the editor.
	add_editor_style( fonts_url() );

	// Enqueue fonts stylesheets into the editor.
	add_editor_style( get_theme_file_uri( "dist/fonts/admin/stylesheet-".WAFF_STYLES.".css" ) );

	// Enqueue front-end styles & BS 
	//add_editor_style( get_theme_file_uri( "dist/css/style.css" ) );
	//add_editor_style( get_theme_file_uri( "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" ) );

}


/**
 * Set our Go child theme's default style.
 *
 * @link https://github.com/godaddy-wordpress/go/blob/master/includes/core.php#L1112
 * @return void
 */
function waff_default_design_style() {
	return 'waff';
}

/**
 * Set our Go child theme's default color scheme.
 *
 * @link https://github.com/godaddy-wordpress/go/blob/master/includes/core.php#L1112
 * @return void
 */
function waff_default_color_scheme() {
	return 'one';
}

/**
 * Enqueue our child theme assets and dependencies.
 *
 * @return void
 */
function waff_styles() {

	echo ((true === WAFF_DEBUG)?'<code> #waff_styles</code>':'');

	// Enqueue the Go parent shared styles.
	$suffix                = SCRIPT_DEBUG ? '' : '.min';
	$rtl                   = ! is_rtl() ? '' : '-rtl';
	$go_style_dependencies = array();

	wp_enqueue_style(
		'go-style',
		get_theme_file_uri( "dist/css/style-shared{$rtl}{$suffix}.css" ),
		$go_style_dependencies,
		GO_VERSION
	);

	$design_style = get_design_style();

	if ( $design_style ) {
		wp_enqueue_style(
			'go-design-style-' . $design_style['slug'],
			$design_style['url'],
			array( 'go-style' ),
			GO_VERSION
		);
	}

}

/**
 * Returns the current design style.
 *
 * @return array
 */
function get_design_style() {

	echo ((true === WAFF_DEBUG)?'<code> #get_design_style</code>':'');

	$design_style = get_theme_mod( 'design_style', waff_default_design_style() ); // Child
	echo ((true === WAFF_DEBUG)?'<pre>'.print_r($design_style, 1).'</pre>':'');

	$supported_design_styles = waff_design_styles(); // Child
	echo ((true === WAFF_DEBUG)?'<pre>'.print_r($supported_design_styles, 1).'</pre>':'');

	if ( in_array( $design_style, array_keys( $supported_design_styles ), true ) ) {

		return $supported_design_styles[ $design_style ];

	}

	return false;

}

function waff_theme_setup() {

	echo ((true === WAFF_DEBUG)?'<code> #waff_theme_setup</code>':'');

	// Add custom editor font sizes.
	//remove_theme_support( 'editor-font-sizes');
	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name'      => esc_html_x( 'Small', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'XS', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 10,
				'slug'      => 'small',
			),
			array(
				'name'      => esc_html_x( 'Subline', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'S', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 11,
				'slug'      => 'subline',
			),
			array(
				'name'      => esc_html_x( 'Impact', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'I', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 14,
				'slug'      => 'impact',
			),
			array(
				'name'      => esc_html_x( 'Muted', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'M', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 13,
				'slug'      => 'muted',
			),
			array(
				'name'      => esc_html_x( 'Lead', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'L', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 24,
				'slug'      => 'lead',
			),
			array(
				'name'      => esc_html_x( 'Huge', 'font size option label', 'waff' ),
				'shortName' => esc_html_x( 'XL', 'abbreviation of the font size option label', 'waff' ),
				'size'      => 40,
				'slug'      => 'huge',
			),
		)
	);
	
	$design_style = get_design_style();

	if ( $design_style ) {

		$editorColorPalette = get_theme_support( 'editor-color-palette' );
		//remove_theme_support( 'editor-color-palette');

		if ( $editorColorPalette ) {
			
			$color_palette = array_merge( $editorColorPalette[0], array(
				array(
					'name'  => esc_html_x( 'Quinary', 'name of the fifth color palette selection', 'go' ),
					'slug'  => 'quinary',
					'color' => '#000000',
				),
			));

			add_theme_support( 'editor-color-palette', $color_palette );

		}
	}

}

/**
 * Add classes to body element.
 *
 * @param string|array $classes Classes to be added to body.
 * @return array
 */
function body_classes( $classes ) {

	if ( defined('WAFF_THEME') ) {
		$classes[] = 'waff-theme-'.strtolower(WAFF_THEME);
	}

	// From block filters  
	if ( is_singular( 'page' ) && true === (bool) apply_filters('waff_is_page_dark', false) ) {
		$classes[] = 'is-dark';
	}

	if ( is_singular( 'page' ) && true === (bool) apply_filters('waff_is_page_wide', false) ) {
		$classes[] = 'is-wide';
	}

	if ( is_singular( 'page' ) && 'enabled' === get_post_meta( get_the_ID(), 'hide_page_title', true ) ) {
		$classes[] = 'hide-title';
	}

	// From metaboxes 
	$prefix = 'waff_page_';
	$page_atts = array();
	$page_atts['anchors'] 				= rwmb_meta( $prefix . 'anchors' );
	$page_atts['header_image'] 			= rwmb_meta( $prefix . 'header_image' );
	$page_atts['header_style'] 			= rwmb_meta( $prefix . 'header_style' );
	
	if ( is_singular( 'page' ) && !empty($page_atts['anchors']) ) {
		$classes[] = 'has-anchors';
	}
	
	if ( is_singular( 'page' ) && !empty($page_atts['header_image']) ) {
		$classes[] = 'has-header-image';
	}
	
	if ( is_singular( 'page' ) && !empty($page_atts['header_style']) ) {
		$classes[] = 'is-'.implode('-',(array)$page_atts['header_style']);
	}

	return $classes;
}

/**
 * Filter the page titles
 *
 * @param array $args Page title arguments.
 *
 * @return $args Filtered page title arguments.
 */
function waff_filter_page_titles( $args ) {

	echo ((true === WAFF_DEBUG)?'<code> ##waff_filter_page_titles</code>':'');

	/* Animate Titles */
	$args['atts'] = array(
		'data-aos' => 'fade-down',
	);

	if ( is_home() ) {

		$args['title'] = get_the_title( get_option( 'page_for_posts', true ) );

	}

	if ( !is_front_page() && is_home() ) {

		$args['title'] = '';

	}

	if ( is_404() ) {

		// $args['title'] = esc_html__( "That page can't be found", 'go' );
		$args['custom'] = true;
		$args['title']  = sprintf(
			'<hgroup class="mt-10"><h6 class="headline d-inline-block text-light error__title">%s</h6><h1 class="subline-2 mt-0 text-light error__title">%s</h1></hgroup>',
			esc_html__( "That page can't be found", 'go' ),
			esc_html__( "Even the things we love break sometimes.", 'go' ),
		);
		$args['class'] = 'mt-10';

	}

	if ( is_archive() ) {

		/*$args['custom'] = true;
		$args['title']  = sprintf(
			'<h1 class="post__title m-0 text-center">%s</h1>',
			get_the_archive_title()
		);*/
		$args['custom'] = true;
		$args['title']  = sprintf(
			'<h6 class="headline d-inline-block mb-4 search__title">%s <span class="muted">%s</span></h6>',
			esc_html__( 'Looking for: ', 'waff' ),
			get_the_archive_title()
		);
	}

	if ( is_search() ) {

		$args['custom'] = true;
		$args['title']  = sprintf(
			'<h6 class="headline d-inline-block mb-4 search__title">%s <span class="muted">%s</span></h6>',
			esc_html__( 'Search for: ', 'waff' ),
			esc_html( get_search_query() )
		);

		global $wp_query;

		if ( 0 === $wp_query->found_posts ) {

			$args['custom'] = true;
			$args['title']  = sprintf(
				'<h6 class="headline d-inline-block mb-4 search__title">%s</h6>',
				esc_html__( 'Nothing Found', 'go' )
			);
			
		}
	}

	return $args;

}

/**
 * Add a dropdown icon to top-level menu items
 *
 * @param string $title The menu item's title.
 * @param object $item  The current menu item.
 * @param object $args  An object of wp_nav_menu() arguments.
 * @param int    $depth Depth of menu item (used for padding).
 *
 * Add a dropdown icon to top-level menu items.
 */
function waff_add_dropdown_icons( $title, $item, $args, $depth ) {

	ob_start();

	load_inline_svg( 'arrow-left.svg' );

	$icon = ob_get_clean();

	// Only add class to 'top level' items on the 'primary' menu.
	if ( 'primary' === $args->theme_location && 0 === $depth ) {
		foreach ( $item->classes as $value ) {
			if ( 'menu-item-has-children' === $value || 'page_item_has_children' === $value ) {
				$title = $title . $icon;
			}
		}
	}

	return $title;

}

/**
 * Get attachement ID by URL 
 */

function waff_get_image_id_by_url($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        return $attachment[0]; 
}

/**
 * Convert colors from hexa
 */

function waff_HTMLToRGB($htmlCode, $output='code') {
	if($htmlCode[0] == '#')
		$htmlCode = substr($htmlCode, 1);

	if (strlen($htmlCode) == 3)
	{
		$htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
	}

	$r = hexdec($htmlCode[0] . $htmlCode[1]);
	$g = hexdec($htmlCode[2] . $htmlCode[3]);
	$b = hexdec($htmlCode[4] . $htmlCode[5]);

	if ( $output == 'code') 
		return $b + ($g << 0x8) + ($r << 0x10);
	
	if ( $output == 'array') 
		return [$r, $g, $b];
	
}

/**
 * Convert colors from RGB
 */

function waff_RGBToHSL($RGB) {
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = ((float)$r) / 255.0;
    $g = ((float)$g) / 255.0;
    $b = ((float)$b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if($maxC == $minC)
    {
      $s = 0;
      $h = 0;
    }
    else
    {
      if($l < .5)
      {
        $s = ($maxC - $minC) / ($maxC + $minC);
      }
      else
      {
        $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
      }
      if($r == $maxC)
        $h = ($g - $b) / ($maxC - $minC);
      if($g == $maxC)
        $h = 2.0 + ($b - $r) / ($maxC - $minC);
      if($b == $maxC)
        $h = 4.0 + ($r - $g) / ($maxC - $minC);

      $h = $h / 6.0; 
    }

    $h = (int)round(255.0 * $h);
    $s = (int)round(255.0 * $s);
    $l = (int)round(255.0 * $l);

    return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
}

/**
 * Input: hex color
 * Output: hsl(in ranges from 0-1)
 * 
 * Takes the hex, converts it to RGB, and sends
 * it to RGBToHsl.  Returns the output.
 * 
*/
function hexToHsl($hex) {
	$r = "";
	$g = "";
	$b = "";

	$hex = str_replace('#', '', $hex);
	
	if (strlen($hex) == 3) {
		$r = substr($hex, 0, 1);
		$r = $r . $r;
		$g = substr($hex, 1, 1);
		$g = $g . $g;
		$b = substr($hex, 2, 1);
		$b = $b . $b;
	} elseif (strlen($hex) == 6) {
		$r = substr($hex, 0, 2);
		$g = substr($hex, 2, 2);
		$b = substr($hex, 4, 2);
	} else {
		return false;
	}

	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);

	$hsl =  rgbToHsl($r,$g,$b);
	return $hsl;
}

/**
 * 
 *Credits:
 * http://stackoverflow.com/questions/4793729/rgb-to-hsl-and-back-calculation-problems
 * http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/
 *
 * Called by hexToHsl by default.
 *
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/.
 * Assumes r, g, and b are contained in the range [0 - 255] and
 * returns h, s, and l in the format Degrees, Percent, Percent.
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
*/
function rgbToHsl($r, $g, $b){  
    //For the calculation, rgb needs to be in the range from 0 to 1. To convert, divide by 255 (ff). 
    $r /= 255;
    $g /= 255;
    $b /= 255;

    $myMax = max($r, $g, $b);
    $myMin = min($r, $g, $b);

    $maxAdd = ($myMax + $myMin);
    $maxSub = ($myMax - $myMin);

    //luminence is (max + min)/2
    $h = 0;
    $s = 0;
    $l = ($maxAdd / 2.0);

    //if all the numbers are equal, there is no saturation (greyscale).
    if($myMin != $myMax){
        if ($l < 0.5) {
            $s = ($maxSub / $maxAdd);
        } else {
            $s = (2.0 - $myMax - $myMin); //note order of opperations - can't use $maxSub here
            $s = ($maxSub / $s);
        }

        //find hue
        switch($myMax){
            case $r: 
                $h = ($g - $b);
                $h = ($h / $maxSub);
                break;
            case $g: 
                $h = ($b - $r); 
                $h = ($h / $maxSub);
                $h = ($h + 2.0);
                break;
            case $b: 
                $h = ($r - $g);
                $h = ($h / $maxSub); 
                $h = ($h + 4.0);
                break;
        } 
    }

    $hsl = hslToDegPercPerc($h, $s, $l);
    return $hsl;
}

/**
 * Input: HSL in ranges 0-1.
 * Output: HSL in format Deg, Perc, Perc.
 * 
 * Note: rgbToHsl calls this function by default.
 * 
 * Multiplies $h by 60, and $s and $l by 100.
 */
function hslToDegPercPerc($h, $s, $l) {
	//convert h to degrees
	$h *= 60;
	
	if ($h < 0) {
		$h += 360;
	}
	
	//convert s and l to percentage
	$s *= 100;
	$l *= 100;
	
	$hsl['h'] = $h;
	$hsl['s'] = $s;
	$hsl['l'] = $l;
	return $hsl;
}

/**
 * Input: HSL in format Deg, Perc, Perc
 * Output: An array containing HSL in ranges 0-1
 * 
 * Divides $h by 60, and $s and $l by 100.
 * 
 * hslToRgb calls this by default.
*/
function degPercPercToHsl($h, $s, $l) { 
	//convert h, s, and l back to the 0-1 range
	
	//convert the hue's 360 degrees in a circle to 1
	$h /= 360;
	
	//convert the saturation and lightness to the 0-1 
	//range by multiplying by 100
	$s /= 100;
	$l /= 100;
	
	$hsl['h'] =  $h;
	$hsl['s'] = $s;
	$hsl['l'] = $l;
	
	return $hsl;
}

/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/.
 * Assumes h, s, and l are in the format Degrees,
 * Percent, Percent, and returns r, g, and b in 
 * the range [0 - 255].
 *
 * Called by hslToHex by default.
 *
 * Calls: 
 *   degPercPercToHsl
 *   hueToRgb
 *
 * @param   Number  h       The hue value
 * @param   Number  s       The saturation level
 * @param   Number  l       The luminence
 * @return  Array           The RGB representation
 */
function hslToRgb($h, $s, $l){
	$hsl = degPercPercToHsl($h, $s, $l);
	$h = $hsl['h'];
	$s = $hsl['s'];
	$l = $hsl['l'];

	//If there's no saturation, the color is a greyscale,
	//so all three RGB values can be set to the lightness.
	//(Hue doesn't matter, because it's grey, not color)
	if ($s == 0) {
   		$r = $l * 255;
   		$g = $l * 255;
   		$b = $l * 255;
	}
	else {
		//calculate some temperary variables to make the 
		//calculation eaisier.
   		if ($l < 0.5) {
   			$temp2 = $l * (1 + $s);
   		} else {
   			$temp2 = ($l + $s) - ($s * $l);
   		}
   		$temp1 = 2 * $l - $temp2;
		
		//run the calculated vars through hueToRgb to
		//calculate the RGB value.  Note that for the Red
		//value, we add a third (120 degrees), to adjust 
		//the hue to the correct section of the circle for
		//red.  Simalarly, for blue, we subtract 1/3.
   		$r = 255 * hueToRgb($temp1, $temp2, $h + (1 / 3));
   		$g = 255 * hueToRgb($temp1, $temp2, $h);
   		$b = 255 * hueToRgb($temp1, $temp2, $h - (1 / 3));
	}
		
	$rgb['r'] = $r;
	$rgb['g'] = $g;
	$rgb['b'] = $b;

	return $rgb;
}

/**
 * Converts an HSL hue to it's RGB value.  
 *
 * Input: $temp1 and $temp2 - temperary vars based on 
 * whether the lumanence is less than 0.5, and 
 * calculated using the saturation and luminence
 * values.
 *  $hue - the hue (to be converted to an RGB 
 * value)  For red, add 1/3 to the hue, green 
 * leave it alone, and blue you subtract 1/3 
 * from the hue.
 *
 * Output: One RGB value.
 *
 * Thanks to Easy RGB for this function (Hue_2_RGB).
 * http://www.easyrgb.com/index.php?X=MATH&$h=19#text19
 *
*/
function hueToRgb($temp1, $temp2, $hue) {
   	if ($hue < 0) { 
   		$hue += 1;
   	}
   	if ($hue > 1) {
   		$hue -= 1;
   	}
   	
   	if ((6 * $hue) < 1 ) {
   		return ($temp1 + ($temp2 - $temp1) * 6 * $hue);
   	} elseif ((2 * $hue) < 1 ) {
   		return $temp2;
   	} elseif ((3 * $hue) < 2 ) {
   		return ($temp1 + ($temp2 - $temp1) * ((2 / 3) - $hue) * 6);
   	}
   	return $temp1;
}

/**
 * Converts HSL to Hex by converting it to 
 * RGB, then converting that to hex.
 * 
 * string hslToHex($h, $s, $l[, $prependPound = true]
 * 
 * $h is the Degrees value of the Hue
 * $s is the Percentage value of the Saturation
 * $l is the Percentage value of the Lightness
 * $prependPound is a bool, whether you want a pound 
 *  sign prepended. (optional - default=true)
 *
 * Calls: 
 *   hslToRgb
 *
 * Output: Hex in the format: #00ff88 (with 
 * pound sign).  Rounded to the nearest whole
 * number.
*/
function hslToHex($h, $s, $l, $prependPound = true) {
	//convert hsl to rgb
	$rgb = hslToRgb($h,$s,$l);

	//convert rgb to hex
	$hexR = $rgb['r'];
	$hexG = $rgb['g'];
	$hexB = $rgb['b'];
	
	//round to the nearest whole number
	$hexR = round($hexR);
	$hexG = round($hexG);
	$hexB = round($hexB);
	
	//convert to hex
	$hexR = dechex($hexR);
	$hexG = dechex($hexG);
	$hexB = dechex($hexB);
	
	//check for a non-two string length
	//if it's 1, we can just prepend a
	//0, but if it is anything else non-2,
	//it must return false, as we don't 
	//know what format it is in.
	if (strlen($hexR) != 2) {
		if (strlen($hexR) == 1) {
			//probably in format #0f4, etc.
			$hexR = "0" . $hexR;
		} else {
			//unknown format
			return false;
		}
	}
	if (strlen($hexG) != 2) {
		if (strlen($hexG) == 1) {
			$hexG = "0" . $hexG;
		} else {
			return false;
		}
	}
	if (strlen($hexB) != 2) {
		if (strlen($hexB) == 1) {
			$hexB = "0" . $hexB;
		} else {
			return false;
		}
	}
	
	//if prependPound is set, will prepend a
	//# sign to the beginning of the hex code.
	//(default = true)
	$hex = "";
	if ($prependPound) {
		$hex = "#";
	}
	
	$hex = $hex . $hexR . $hexG . $hexB;
	
	return $hex;
}

/**
 * Do markdown helper
 */

// PHP function of Shortcode [do_markdown]
function waff_do_markdown($content='') {
	//  ['\\\*\\\*(\\\w.+?)\\\*\\\*', {'bold': true}], // **value**
	//  ['\\\*(\\\w.+?)\\\*', {'italics': true}], // *value*
	$content = do_shortcode($content);
	$content = str_replace('###SPACE###', '', $content); // Je ne sais pas d'ou cela provient mais probablement types
	$content = str_replace('&#8217;', '\'', $content); // Gerer les carateres spéciaux de word 
	$content = str_replace('&#8220;', '&ldquo;', $content);
	$content = str_replace('&#8221;', '&rdquo;', $content);
	$content = str_replace('&#8211;', '&ndash;', $content);
	$content = str_replace('&#8230;', '&hellip;', $content); // Gérer les ...
	$content = str_replace(': #', ': &num;', $content); // Gérer les attrributs <span style="color: #...."

	$content = htmlentities($content);
    $patterns = array('/\*\*\*(\w.+?)\*\*\*/', '/\*\*(\w.+?)\*\*/', '/\*(\w.+?)\*/', '/\#\#([^#]+?)\#\#/', '/\#([^#]+?)\#/'); // '/\#\#([^#(SPACE)]+?)\#\#/', '/\#([^#(SPACE)]+?)\#/' // '/\#\#(\w.+?)\#\#/', '/\#(\w.+?)\#/'
	$replacements = array('<span class="label">$1</span>', '<strong>$1</strong>','<em>$1</em>', '<span class="paragraph-huge">$1</span>', '<span class="paragraph-small">$1</span>');
	  ksort($patterns);
		ksort($replacements);
    $content = preg_replace($patterns, $replacements, $content);
  	return html_entity_decode($content);
}

function waff_clean_tags($content='') {
	$content = strip_tags(do_shortcode($content), array('p'));
	return $content;
}

function waff_trim($content = '', $length = 100) {
	$content = wpv_do_shortcode($content);
	$length = (int)$length;
	if (strlen($content) > $length) {
		$content = substr($content, 0, $length) . '&hellip;';
	}
	return $content;
}