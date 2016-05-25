"use strict";

jQuery(document).ready( function(){
	/* Import Page Routes */
	/* ======================*/
	var demos_page = 'themes.php?page=' + ImportData.import_page_slug + '';
	if( typeof window.wp.themes !== 'undefined'  ){
		window.wp.themes.Router = Backbone.Router.extend({

			routes: {
				'themes.php?demo=:slug': 'demo',
				'themes.php?search=:query': 'search',
				'themes.php?s=:query': 'search',
				demos_page : 'demos',
				'': 'demos'
			},

			baseUrl: function( url ) {
				var r_url = 'themes.php?page=' + ImportData.import_page_slug + url;
				return r_url;
			},

			themePath: '&demo=',
			searchPath: '&search=',

			search: function( query ) {
				jQuery( '.wp-filter-search' ).val( query );
			},

			themes: function() {
				jQuery( '.wp-filter-search' ).val( '' );
			},

			navigate: function() {
				if ( Backbone.history._hasPushState ) {
					Backbone.Router.prototype.navigate.apply( this, arguments );
				}
			}
		});
	}


	/* Change Import Type */
	/* ======================*/
	jQuery('.import-options select#import_type').live('change', function(){
		if( jQuery(this).val() == 'custom_set' ){
			jQuery(this).siblings('.custom-import-set').slideDown(200);
		}
		else{
			jQuery(this).siblings('.custom-import-set').slideUp(200);
		}

	});


	/*
	*  Change file name
	*/
	jQuery('#import-page .styled-file input[type="file"]').live('change', function(e){
		if (jQuery(this).val().lastIndexOf('\\')){
			var i = jQuery(this).val().lastIndexOf('\\') + 1;
		}
		else{
			var i = jQuery(this).val().lastIndexOf('/') + 1;
		}

		jQuery(this).siblings('.file-label').html(jQuery(this).val().slice(i));
	});


	/* AJAX | Import Demo */
	/* ======================*/
	function xkit_htmlspecialchars(str) {
		if (typeof(str) == "string") {
			str = str.replace(/&/g, "&amp;");
			str = str.replace(/"/g, "&quot;");
			str = str.replace(/'/g, "&#039;");
			str = str.replace(/</g, "&lt;");
			str = str.replace(/>/g, "&gt;");
		}
		return str;
	}

	var installingDemo = false;
	jQuery('.theme-actions .button.ajax-import').live('click', function(){
		var button = jQuery(this);
		var optionsForm = jQuery(this).closest('.theme-wrap').find('.import-options form');
		var importTimer;
		var loadingImportStatus = false;

		if( button.hasClass('disabled') ){
			return false;
		}

		jQuery('#import-progress .progress-line').css( 'width', 0 + '%' );
		jQuery('#import-progress .progress-title').html( 0 + '%' );
		if(optionsForm){
			if(installingDemo == false){
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: optionsForm.serialize() + '&action=import_demo',
					beforeSend: function( result ){
						// Lock Body
						jQuery('body').addClass('importing-content');

						// Progress bar
						importTimer = setInterval(function() {
							if( loadingImportStatus == false ) {
								jQuery.ajax({
									type: 'POST',
									url: ImportData.process_uri,
									data: { site_path: ImportData.site_path },
									success: function( result ){
										if( parseInt( result ) != 0 ) {
											jQuery('#import-progress .progress-line').css( 'width', parseInt( result ) + '%' );
											jQuery('#import-progress .progress-title').html( parseInt( result ) + '%' );
										}
									},
									complete: function(e){
										loadingImportStatus = false;
									},
								});
							}
						}, 600 );
						jQuery('#import-progress').slideDown(200);

						// Buttons
						button.addClass('disabled');
						installingDemo = true;
					},
					success: function( result ){
						// Lock Body
						jQuery('body').removeClass('importing-content');

						// Progress bar
						jQuery('#import-progress .progress-line').css( 'width', 100 + '%' );
						jQuery('#import-progress .progress-title').html( 100 + '%' );
						clearInterval(importTimer);

						// Buttons
						button.removeClass('disabled');
						installingDemo = false;

						// Result
						jQuery('#import-notices').html( result );
						jQuery('#notices-form .notices-area').text( xkit_htmlspecialchars(result) ).parents('#notices-form').submit();
					},
					error: function(e){
						// Lock Body
						jQuery('body').removeClass('importing-content');

						// Progress bar
						jQuery('#import-progress .progress-line').css( 'width', 100 + '%' );
						jQuery('#import-progress .progress-title').html( 100 + '%' );
						clearInterval(importTimer);

						// Buttons
						button.removeClass('disabled');
						installingDemo = false;

						// Result
						var ajaxMessage = '<div class="error notice is-dismissible"><p>Ajax Error!</p></div>';
						jQuery('#notices-form .notices-area').text( xkit_htmlspecialchars(ajaxMessage) ).parents('#notices-form').submit();
					}
				});
			}
		}

		return false;
	});


	/* Before Window Close */
	/* ======================*/
	window.onbeforeunload = function() {
		if( installingDemo === true ){
			var message = 'If you navigate away from the page, the import will not end.';
			return message;
		}
	}
});