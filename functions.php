<?php
/**
 * Child theme : WaffThree
 * Our Child theme overrides.
 * "Keep it light, keep it simple." - Mahatma Gandhi 🙃
 *
 */

define( 'WAFF_VERSION', '3.0' );
define( 'WAFF_DEBUG', false );

// Use classic Widget Editor
// add_filter( 'use_widgets_block_editor', '__return_false' );


/** Die if no setup */
if ( !file_exists(get_theme_file_path( '../'.preg_replace('/^www\./i', '', $_SERVER['SERVER_NAME']).'.setup.php' ))  ) {
	wp_die('Error : please define setup.');
}

/**
 * i18n : Load theme langage.
 */
/*
*	You can uncomment the line below to include your own translations
*	into your child theme, simply create a "language" folder and add your po/mo files
*/
load_child_theme_textdomain('waff', get_theme_file_path('language/'));

/**
 * i18n : Qtranslate XT.
 */
// Lang
$edit_lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : get_bloginfo("language");
if ( function_exists('qtranxf_getLanguage') ) {
	$locale = ( qtranxf_getLanguage() != '' )?qtranxf_getLanguage():$edit_lang;
}

// Blog name 
$blogname = get_bloginfo('name');
$blogdescription = get_option('blogdescription');
if ( function_exists('qtranxf_split') && function_exists('qtranxf_getLanguage') ) {
	$blogname = qtranxf_split(get_bloginfo('name'))[$locale];
	$blogdescription = qtranxf_split(get_option('blogdescription'))[$locale];
}

// Define
define( 'WAFF_CUSTOM_BRAND', $blogname ); // Localized brand
define( 'WAFF_CUSTOM_BRAND_DESCRIPTION', $blogdescription ); // Localized brand

// Domain 
$sub = substr($_SERVER['SERVER_NAME'], 0, 3);
if ( $sub == ( 'www' || 'dev') ) {
	/* We have www.example.com OR dev.example.com  */
	$domain = substr($_SERVER['SERVER_NAME'], 4);
} else {
	/* We have example.com */
	$domain = $_SERVER['SERVER_NAME'];
}
define( 'WAFF_DOMAIN', $domain );

/**
 * Allow uploading of all types of formats to the media.
 */
add_filter('upload_mimes', 'wpm_myme_types', 1, 1);
function wpm_myme_types($mime_types){
    $mime_types['zip'] = 'application/zip';
    return $mime_types;
}


/**
 * Waff setup.
 */
require_once get_theme_file_path( '../'.preg_replace('/^www\./i', '', $_SERVER['SERVER_NAME']).'.setup.php' );

/**
 * Common functions ( outside namespace )
 */
require_once get_theme_file_path( 'includes/commons.php' );

/**
 * Core setup, hooks, and filters.
 */
require_once get_theme_file_path( 'includes/core.php' );

/**
 * Core setup, hooks, and filters.
 */
require_once get_theme_file_path( 'includes/theme.php' );

 /**
 * Customizer additions & child block editor assets.
 */
require_once get_theme_file_path( 'includes/defaults.php' );
require_once get_theme_file_path( 'includes/customizer.php' );

/**
 * Custom template tags for this child theme.
 */
require_once get_theme_file_path( 'includes/template-tags.php' );

/**
 * Page Wide Meta functions.
 */
require_once get_theme_file_path( 'includes/wide-meta.php' );

/**
 * Page Dark Meta functions.
 */
require_once get_theme_file_path( 'includes/dark-meta.php' );

/**
 * Migrate from Fifam 1 to WaffTwo theme.
 */
require_once get_theme_file_path( 'includes/migrate.php' );

/**
 * Blocks setup and functions.
 */
require_once get_theme_file_path( 'includes/blocks.php' );

/**
 * Run setup functions.
 */
WaffTwo\Core\setup();
WaffTwo\Theme\setup();
WaffTwo\Customizer\setup();
WaffTwo\Wide_Meta\setup();
WaffTwo\Dark_Meta\setup();
WaffTwo\Migrate\setup();
WaffTwo\Blocks\setup();