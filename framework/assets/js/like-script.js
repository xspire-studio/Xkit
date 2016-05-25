"use strict";

(function($){
	/**
	 * Handler Post Like
	 */
	$('.box-like.actived').find('.plus, .up, .down').live( 'click', function(){
		var lElement = $(this).parents('.box-like');
		
		var hasCookie = getCookie( 'like_' + lElement.data('id') );
		
		// Action post like
		if( !lElement.hasClass('liked') && !lElement.hasClass('processing') && !hasCookie ){
			$.ajax({
				type: 'POST',
				url: init_localize_object.ajaxurl,
				data: { 'action': 'post_like', 
						'event':   $(this).data('event'),
						'post_id': lElement.data('id'), 
						'nonce':   lElement.data('nonce')
					  },
				beforeSend: function(){
					$(lElement).addClass('processing');
					$(lElement).find('.msg').remove();
					$(lElement).find('.counter').hide();
					$(lElement).find('.loading').show();
				},
				success: function(data){
					try {
						var objData = JSON.parse(data);
						
						if(typeof(objData.like_count) != 'undefined') {
							var counter = parseInt( objData.like_count );
							
							if( counter > 0 ){
								var status = 'positive';
							} else if( counter < 0 ){
								var status = 'negative';
							} else{
								var status = 'null';
							}
							
							// element for change
							var output = $('.box-like[data-id="' + lElement.data('id') + '"]');
							
							output.find('.counter').html(counter);
							output.find('.counter').removeClass('positive negative null');
							output.find('.counter').addClass(status);
							
							// 2628000 seconds = 1 month
							setCookie( 'like_' + lElement.data('id'), 1, { expires: 2628000 } );
						}
						
						if(typeof(objData.msg) != 'undefined') {
							$(lElement).addClass('liked');
							$(lElement).append('<span class="msg">'+objData.msg+'</span>');
						} else {
							$(lElement).append('<span class="msg">Error!</span>');
						}
	
					} catch (err) {
						$(lElement).append('<span class="msg">Error!</span>');
					}
				},
				error: function(){
					$(lElement).append('<span class="msg">Error server!</span>');
				},
				complete: function(){
					$(lElement).removeClass('processing');
					$(lElement).find('.counter').show();
					$(lElement).find('.loading').hide();
					
					setTimeout(function() { $(lElement).find('.msg').fadeOut(); }, 2000 );
				}	
			});
		}
		
		// Already voted
		if( lElement.hasClass('liked') || hasCookie ){
			$(lElement).find('.msg').remove();
			$(lElement).append('<span class="msg">'+objLike.already_voted+'</span>');
			
			setTimeout(function() {
				$(lElement).find('.msg').fadeOut(); 
			}, 2000 );
		}
	});
	
})(jQuery);