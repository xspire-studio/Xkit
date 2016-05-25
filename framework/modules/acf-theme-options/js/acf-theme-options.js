"use strict";

/*
*  Event theme options ---------------------------------
*/
	var thMsg = new Array();

	/* Default toastr */
	toastr.options.escapeHtml = true;
	toastr.options.timeOut = 5000;
	toastr.options.newestOnTop = false;
	toastr.options.onShown = function() { 
		jQuery('body').removeClass('ajaxloader');
	}

	/*
	*  Export options
	*/
	jQuery('form.acf-form .btn-export-options').live('click', function(e){   
		window.open( ajaxurl + '?action=export_file_theme_options', acfMsgTO.download_file  );

		return false;
	});

	/*
	*  Import options
	*/
	jQuery('form.acf-form .btn-import-options').live('click', function(e){

		var data = new FormData(jQuery('form.acf-form')[0]);

		jQuery.ajax({
			url: ajaxurl + '?action=import_theme_options', 
			type: 'post',
			data: data,
			processData: false,
			contentType: false,
			beforeSend: function(XHR){
				jQuery('body').addClass('ajaxloader');
			},
		}).success(function(data){
			try {
				var msg = JSON.parse(data);
				var msgType = '';
				var msgText = '';

				if( msg['msg'] != undefined ){
					 msgText = msg['msg'];
				}

				if( msg['type'] != undefined ){
					 msgType = msg['type'];

					if( msg['type'] == 'success'  ){

						jQuery('form.acf-form')[0].reset();

						thMsg.push([[msgType], [msgText]]); 

						setCookie('thMsg', JSON.stringify(thMsg), {expires:2000});

						location.reload();
					}
					else{
						 toastr[msgType](msgText);
					}
				}
			} catch(e) {
				toastr['warning'](data);
				toastr['error']('Error ' + e.name + ":" + e.message + "\n" + e.stack);
			}

		}).fail(function(){
			toastr['error'](acfMsgTO.error_occurred);
		});

		return false;
	});

	/*
	*  Restore defaults
	*/
	jQuery('form.acf-form .btn-restore-defaults').live('click', function(e){

		var isConfirm = confirm(acfMsgTO.are_you_sure);

		if( isConfirm ){
			jQuery.ajax({
				url: ajaxurl + '?action=restore_defaults_options', 
				type: 'post',
				processData: false,
				contentType: false,
				beforeSend: function(XHR){
					jQuery('body').addClass('ajaxloader');
				},
			}).success(function(data){
				try {
					var msg = JSON.parse(data);
					var msgType = '';
					var msgText = '';

					if( msg['msg'] != undefined ){
						 msgText = msg['msg'];
					}

					if( msg['type'] != undefined ){
						 msgType = msg['type'];

						if( msg['type'] == 'success'  ){

							jQuery('form.acf-form')[0].reset();

							thMsg.push([[msgType], [msgText]]); 

							setCookie('thMsg', JSON.stringify(thMsg), {expires:2000});

							location.reload();
						}
						else{
							 toastr[msgType](msgText);
						}
					}
				} catch(e) {
					toastr['warning'](data);
					toastr['error']('Error ' + e.name + ":" + e.message + "\n" + e.stack);
				}

			}).fail(function(){
				toastr['error'](acfMsgTO.error_occurred);
			});
		}

		return false;
	});

	/*
	*  Change file name
	*/
	jQuery('form.acf-form .options-settings input[type="file"]').live('change', function(e){
		if (jQuery(this).val().lastIndexOf('\\')){
			var i = jQuery(this).val().lastIndexOf('\\') + 1;
		}
		else{
			var i = jQuery(this).val().lastIndexOf('/') + 1;
		}

		jQuery(this).siblings('.file-label').html(jQuery(this).val().slice(i));
	});

/*
*  Show Cookies Msg
*/
setTimeout(function() {
	if( getCookie('thMsg') != undefined ){
		var arrMsg = JSON.parse( getCookie('thMsg') );

		if( arrMsg != undefined && arrMsg.length > 0  ){
			arrMsg.forEach( function( item, i, arrMsg ) {
				toastr[ item[0] ]( item[1] );
			});
		}

		deleteCookie('thMsg');
	}
}, 50);



/*
*  Options load ---------------------------------
*/
jQuery(document).ready(function(e) {
	jQuery('.options-content').removeClass('load');
});


/*
*  Save options ---------------------------------
*/
jQuery(document).ready(function(e) {

	jQuery('form.acf-form').live('submit', function(e){
		jQuery('body').addClass('ajaxloader');

		acf.validation.fetch( jQuery('form.acf-form') );

		return false;
	});

	acf.add_action( 'submit', function($form){
		jQuery.ajax({
			url: acf.get('ajaxurl') + '?action=save_theme_options',
			data: jQuery($form).serialize(),
			type: 'post'
		}).success(function(data){
			try {

				var msg = JSON.parse(data);
				var msgType = '';
				var msgText = '';

				if( msg['msg'] != undefined ){
					 msgText = msg['msg'];
				}

				if( msg['type'] != undefined ){
					msgType = msg['type'];

					toastr[msgType](msgText);
				}
			} catch(e) {
				toastr['warning'](data);
				toastr['error']('Error ' + e.name + ":" + e.message + "\n" + e.stack);
			}

		}).fail(function(){
			toastr['error'](acfMsgTO.error_occurred);
		});

		throw 'exit';
	} );

});



/*
*  Event tabs ---------------------------------
*/
jQuery(document).ready(function(e) {

	// set jQuery BIG tabs
	if( jQuery( '.theme_tabs' ).length > 0 ){

		jQuery('.theme_tabs').find('> ul > li > a').live('click', function(){
			var $this = jQuery(this).parents('.theme_tabs');

			var oldItem = jQuery($this).find('> ul > li .ui-tabs-active');
			var oldPanel = jQuery($this).find('> ul > li .ui-tabs-active').attr('rel');

			var newItem = jQuery(this);
			var newPanel = jQuery(this).attr('rel');

			// Reset
			jQuery($this).find('> ul > li').removeClass('ui-tabs-active');
			jQuery($this).find('> div > div').hide();

				//beforeActivate
				var tab = jQuery(newItem).parent('li').attr('data-slug');

				if(typeof tab != 'undefined' && tab){
					var newUrl = UpdateQueryString('tab', tab, UpdateQueryString('subtab'));

					if(window.location.href != newUrl){
						window.history.replaceState(null, null, newUrl);
					}
				}

				if( jQuery( newPanel ).find( '.theme_subtabs' ) != 'undefined' ){
					jQuery( newPanel ).find( '.theme_subtabs' ).find('> ul > li').eq(0).find('a').click();
				}

			// Activate
			jQuery(newItem).parent('li').addClass('ui-tabs-active');
			jQuery(newPanel).show();

			return false;
		});
	}

	// set jQuery SMALL tabs
	if( jQuery( '.theme_subtabs' ).length > 0 ){

		jQuery('.theme_subtabs').find('> ul > li > a').live('click', function(){
			var $this = jQuery(this).parents('.theme_subtabs');

			var oldItem = jQuery($this).find('> ul > li .ui-tabs-active');
			var oldPanel = jQuery($this).find('> ul > li .ui-tabs-active').attr('rel');

			var newItem = jQuery(this);
			var newPanel = jQuery(this).attr('rel');

			// Reset
			jQuery($this).find('> ul > li').removeClass('ui-tabs-active');
			jQuery($this).find('> div > div').hide();

				//beforeActivate
				var subtab = jQuery(newItem).parent('li').attr('data-slug');

				if(typeof subtab != 'undefined' && subtab){
					var newUrl = UpdateQueryString('subtab', subtab);

					if(window.location.href != newUrl){
						window.history.replaceState(null, null, newUrl);
					}
				}

			// Activate
			jQuery(newItem).parent('li').addClass('ui-tabs-active');
			jQuery(newPanel).show();

			return false;
		});
	}


	// validation ACF FORM
	var tab_validation = acf.model.extend({

		active: 1,

		actions: {
			'add_field_error': 'add_field_error'
		},

		add_field_error: function( $field ){

			jQuery('body').removeClass('ajaxloader');

			// bail early if already focused
			if( !this.active ) {
				return;
			}

			if( jQuery( $field ).parents( '.tab' ).length > 0 ){
				jQuery( '.theme_tabs' ).find('> ul > li').eq(jQuery( $field ).parents( '.tab' ).index()).find('a').click();
			}

			if( jQuery( $field ).parents('.subtab').length > 0 ){
				jQuery( '.theme_subtabs' ).find('> ul > li').eq(jQuery( $field ).parents( '.subtab' ).index()).find('a').click();
			}

			// reference
			var self = this;

			// disable functionality for 1sec (allow next validation to work)
			this.active = 0;

			setTimeout(function(){

				self.active = 1;

			}, 1000);
		}
	});	
});	