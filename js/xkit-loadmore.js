"use strict";

( function ( $, window, document ) {
 
    jQuery.fn.xkit_ajax_load = function( options ) {
		
		/* Settings */
        var settings = jQuery.extend( {
            contentSelector	    : '#main-content > .row', 	 // Main content element selector e.g. #main
			itemSelector		: 'article.post', 			 // Item container element in loop e.g. article.post
            ajaxMoreSelector	: '.ajax-pagination', 		 // Navigation next element selector e.g. nav.navigation a.next
			paginationType		: 'more',				     // Pagination type Infinite scroll or Load more button. Default "more"
			loadingFinishedHtml : '<div class="loading-done">No More Posts Available</div>', // Text to show when loading is finished.Default "No More Posts Available"
        }, options );
		
		
		/* Vars */
		var xkit_loading = false,
			xkit_loaded  = false,
			xkit_url 	 = false;
		
		
		/* Init */
		var xkit_load_init = function() {			
			if( jQuery( settings.contentSelector ).length && jQuery( settings.ajaxMoreSelector ).length && jQuery( settings.itemSelector ).length ) {				
				xkit_url = jQuery( settings.ajaxMoreSelector ).attr( 'data-next-page' );
			}
			else {
				return false;
			}	
		}
		
		
		/* Load Ajax */
		var xkit_load_ajax = function() {
			
			// Check if url exixts
			if( !xkit_url ) {
				return false;
			}
			
			// Get Last Element
			var xkit_lastElem = jQuery( settings.contentSelector ).find( settings.itemSelector ).last();
			 
			// Start Loading
			xkit_loading = true;
			jQuery( document ).trigger( 'xkit_ajax_load_start' );			
			
			// Ajax call
            jQuery.ajax( {
                url         : xkit_url,
				method		: 'POST',
				data		: { 'xkit_ajax_pagination': true },
                dataType    : 'json',
                success     : function ( response ) {
					
					// Check Response
					if( !response ) {
						// Error Result
						xkit_loading = false;
						jQuery( document ).trigger( 'xkit_ajax_load_error' );
						
						return false;
					}

					// Insert HTML
					xkit_lastElem.after( response.html );
				 
					// End Loading
					xkit_loading = false;
					jQuery( document ).trigger( 'xkit_ajax_load_success' );
					
					// Next URL
					if( response.next_page ) {
						xkit_url = response.next_page;
					}
					else {
						// All Items Loaded
						xkit_loaded = true;
						jQuery( document ).trigger( 'xkit_ajax_load_complete' );
					}
				}
            } );
			
		};
		
		
		/* Window Scroll Trigger */
		jQuery( window ).on( 'scroll', function(){
			if( !xkit_loading && !xkit_loaded && settings.paginationType == 'infinite' && jQuery( window ).scrollTop() >= jQuery( settings.itemSelector ).last().offset().top + jQuery( settings.itemSelector ).last().outerHeight() - window.innerHeight ) {
			   xkit_load_ajax();
			}	
		});
		
		
		/* LoadMore Click Trigger */
		jQuery( document ).on( 'click', jQuery( settings.ajaxMoreSelector ).find( '.load-more-btn' ), function() {
			
			if( !xkit_loading && !xkit_loaded && settings.paginationType == 'more' ) {		
				xkit_load_ajax();								 
			}
		});
		
		
		/* Ajax Load Start Trigger */
		jQuery( document ).on( 'xkit_ajax_load_start', function() {
			jQuery( settings.contentSelector ).addClass( 'loading' );
			jQuery( settings.ajaxMoreSelector ).addClass( 'loading' );
				
			if( settings.paginationType == 'infinite' ) {
				jQuery( settings.ajaxMoreSelector ).show();
			}
		});
		
		
		/* Ajax Load Success|Error Trigger */
		jQuery( document ).on( 'xkit_ajax_load_success xkit_ajax_load_error', function() {
			jQuery( settings.contentSelector ).removeClass( 'loading' );
			jQuery( settings.ajaxMoreSelector ).removeClass( 'loading' );
			
			if( settings.paginationType == 'infinite' ) {
				jQuery( settings.ajaxMoreSelector ).hide();
			}
		});
		
		
		/* Ajax Load Complete Trigger */
		jQuery( document ).on( 'xkit_ajax_load_complete', function() {	
			jQuery( settings.ajaxMoreSelector ).html( settings.loadingFinishedHtml ).show();
		});
		
		
		/* Initialization Plugin */
		xkit_load_init();	
    };
 
} ( jQuery, window, document ) );