"use strict";

jQuery(document).ready( function(){
	
	/* Social Total Counter */
	if( jQuery( '.social-share.total-counter' ).length ) {
		/*
		var allSocialProviders = [];
		jQuery( '.social-share .share-item' ).each( function( index ) {
			var classList = jQuery( this ).attr('class').split(/\s+/);
			
			if( jQuery.isArray( classList ) ) {
				allSocialProviders.push( classList[ classList.length-1 ] );
			}
		} );
		*/
		//console.log( allSocialProviders );
		
		jQuery.ajax({
			type: 'POST',
			url:  init_localize_object.ajaxurl,
			data: { 'action': 'get_social_counters', 'current_url': window.location.href },
			success: function( result ){
				var counters = jQuery.parseJSON( result );
				var totalCount = 0;

				jQuery.each( counters, function( key, count ) {
					if( jQuery( '.social-share.total-counter' ).find( '.share-item.' + key ).length ){
						totalCount += parseInt( count );
					}
				});

				if( parseInt( totalCount ) > 0 ){
					jQuery('.social-share.total-counter' ).append('<span class="total-counter">' + totalCount + '</span>');
				}
			}
		});
	}

	/* Social Flat Counters */
	if( jQuery('.social-share.with-counters').length ){
		jQuery.ajax({
			type: 'POST',
			url:  init_localize_object.ajaxurl,
			data: { 'action': 'get_social_counters', 'current_url': window.location.href },
			success: function( result ){
				var counters = jQuery.parseJSON( result );

				jQuery.each( counters, function( key, count ) {
					jQuery('.social-share.with-counters' ).find( '.share-item.' + key + ' a' ).append('<span class="social-counter">' + count + '</span>');
				})
			}
		});
	}
});