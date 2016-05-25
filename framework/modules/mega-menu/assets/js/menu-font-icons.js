"use strict";

/*
*  Icon Packs : Render Field - icon
*/
jQuery( '.icon-box .icons-container .item' ).live( 'click', function( ) {
	jQuery( this ).parents( '.icon-box' ).find( 'input' ).val( jQuery( this ).data( 'icon' ) );

	jQuery( this ).parents( '.icon-box' ).find( '.icon-preview' ).html( '<i class="item ' + jQuery( this ).data( 'icon' ) + '"></i>' );

	jQuery( this ).siblings( ).removeClass( 'active' );

	jQuery( this ).addClass( 'active' );

	/* Open/Hide icon changed field */
	if( jQuery( this ).data( 'icon' ) == '' ) {
		jQuery( this ).parents( '.icon-box' ).find( 'input' ).slideUp( 150 ).addClass( 'icon-change-hidden' );
	}
	else {
		jQuery( this ).parents( '.icon-box' ).find( 'input' ).slideDown( 150 ).removeClass( 'icon-change-hidden' );
	}
} );