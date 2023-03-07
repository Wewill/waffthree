<?php
/**
 * Page Title Meta setup
 *
 * @package WaffTwo\Wide_Meta
 */

namespace WaffTwo\Wide_Meta;

/**
 * Set up Wide Meta hooks
 *
 * @return void
 */
function setup() {

	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'waff_is_page_wide', $n( 'page_wide_toggle' ) );

	register_meta(
		'post',
		'page_wide_toggle',
		array(
			'sanitize_callback' => $n( 'page_wide_toggle_callback' ),
			'type'              => 'string',
			'description'       => __( 'Toggle page in wide mode.', 'go' ),
			'show_in_rest'      => true,
			'single'            => true,
		)
	);

}

/**
 * Wide meta callback
 *
 * @param  string $status Hide page title status (eg: enable, disabled).
 *
 * @return string Whether or not the page title should be enabled or not.
 */
function page_wide_toggle_callback( $status ) {

	$status = strtolower( trim( $status ) );

	if ( ! in_array( $status, array( 'enabled', 'disabled' ), true ) ) {

		$status = '';

	}

	return $status;

}

/**
 * Wide page 
 * *
 * @return array Filtered title data.
 */
function page_wide_toggle( $return ) {

	$return = false;

	if ( is_singular( 'page' ) && 'enabled' === get_post_meta( get_the_ID(), 'page_wide_toggle', true ) ) {

		$return = true;

	}

	return $return;

}
