<?php
/**
 * Migrate from Fifam 1 to WaffTwo
 *
 * @package WaffTwo\Migrate
 */

namespace WaffTwo\Migrate;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	
	// Handle old shortcodes
	add_shortcode('spb_text_block', $n( 'waff_spb_text_block') );
	add_shortcode('spb_column', $n( 'waff_spb_text_block') );
	add_shortcode('spb_row', $n( 'waff_spb_text_block') );
	add_shortcode('spb_divider', $n( 'waff_spb_text_block') );
	add_shortcode('spb_blank_spacer', $n( 'waff_spb_text_block') );
	add_shortcode('spb_gmaps', $n( 'waff_spb_text_block') );
	add_shortcode('spb_map_pin', $n( 'waff_spb_text_block') );
	add_shortcode('spb_raw_html', $n( 'waff_spb_text_block') );

	add_shortcode('spb_button', $n( 'waff_spb_button') );

}


/* Handle old spb_text_block SC 
================================================== */
function waff_spb_text_block($atts, $content){
    return do_shortcode($content);    
}

/* Handle old spb_button SC 
================================================== */
function waff_spb_button($atts, $content){
	$atts = shortcode_atts( array(
		'button_text' => '',
	), $atts );

    return '<button href="'.do_shortcode($content).'">'.esc_html($atts['button_text']).'</button>';    
}