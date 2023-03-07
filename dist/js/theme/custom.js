/**
 * Theme javascript functions file.
 */

( function( $ ) {
	"use strict";

	var
	html			= $( 'html' ),
	body			= $( 'body' ),
	nightToggle 		= $( '#night-mode-toggle' ),
	nightActive 		= ( 'night-mode' );

	/* Document Ready */
	$( document ).ready( function () {

		/* Night Mode */
		nightToggle.on( 'click', function( e ) {

			if ( html.hasClass( nightActive ) ) {
				html.removeClass( nightActive );
				localStorage.setItem( 'night-mode', 'false' );
			} else {
				html.addClass( nightActive );
				localStorage.setItem( 'night-mode', 'true' );
			}
		});
	
	});

} )( jQuery );
