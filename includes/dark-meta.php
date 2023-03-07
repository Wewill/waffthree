<?php
/**
 * Page Title Meta setup
 *
 * @package WaffTwo\Dark_Meta
 */

namespace WaffTwo\Dark_Meta;

/**
 * Set up Dark Meta hooks
 *
 * @return void
 */
function setup() {

	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'waff_is_page_dark', $n( 'page_dark_toggle' ));

	register_meta(
		'post',
		'page_dark_toggle',
		array(
			'sanitize_callback' => $n( 'page_dark_toggle_callback' ),
			'type'              => 'string',
			'description'       => __( 'Toggle page in dark mode.', 'go' ),
			'show_in_rest'      => true,
			'single'            => true,
		)
	);

}

/**
 * Hide page dark meta callback
 *
 * @param  string $status Hide page title status (eg: enable, disabled).
 *
 * @return string Whether or not the page title should be enabled or not.
 */
function page_dark_toggle_callback( $status ) {

	$status = strtolower( trim( $status ) );

	if ( ! in_array( $status, array( 'enabled', 'disabled' ), true ) ) {

		$status = '';

	}

	return $status;

}

/**
 * Return page dark meta
 * *
 * @return array Filtered title data.
 */
function page_dark_toggle( $return ) {

	$return = false;

	if ( is_singular( 'page' ) && 'enabled' === get_post_meta( get_the_ID(), 'page_dark_toggle', true ) ) {

		$return = true;

	}

	return (bool) $return;

}