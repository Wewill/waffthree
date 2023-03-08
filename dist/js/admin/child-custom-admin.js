/**
 * Admin javascript functions file.
 */

( function( $ ) {
	"use strict";

	/* Document Ready */
	$( document ).ready( function () {

		// Hide custom page options fields ( from meta box io )
		if ( !document.body.classList.contains('role-administrator') ) {
			$('#page-options .rwmb-meta-box #waff_page_advanced_class').prop( "disabled", true );
			$('#page-options .rwmb-meta-box #waff_page_advanced_class').closest('.rwmb-field').css('opacity', '0.7');
		}

	});

} )( jQuery );
