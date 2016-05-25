"use strict";

( function( $ ) {

	/*
	 * Document ready function
	 */
	jQuery( document ).ready( function() {
		$( '#main-content > .row' ).xkit_ajax_load();
	} );

} )( jQuery );