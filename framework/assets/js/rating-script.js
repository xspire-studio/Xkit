"use strict";

(function($){
	/**
	 * Event mouseleave stars
	 */
	$('.box-rating.actived .stars > .fa').live( 'mouseenter', function(){
		$(this).parent().addClass('move');	
	});
	
	/**
	 * Event mouseleave stars
	 */
	$('.box-rating.actived .stars > .fa').live( 'mouseleave', function(){
		$(this).parent().removeClass('move');	
	});
	 
	/**
	 * Handler Rating
	 */
	$('.box-rating.actived .stars > .fa').live( 'click', function(){
		var rElement = $(this).parents('.box-rating');

		var hasCookie = getCookie( 'rating_' + rElement.data('id') );
		
		// Action rating
		if( !rElement.hasClass('voted') && !rElement.hasClass('processing') && !hasCookie ){
			$.ajax({
				type: 'POST',
				url: init_localize_object.ajaxurl,
				data: { 'action': 'post_rating', 
						'val':     $(this).index() + 1,
						'post_id': rElement.data('id'), 
						'nonce':   rElement.data('nonce')
					  },
				beforeSend: function(){
					$(rElement).addClass('processing');
					$(rElement).find('.msg').remove();
					$(rElement).find('.info, .stars').hide();
					$(rElement).find('.loading').show();
				},
				success: function(data){
					try {
						var objData = JSON.parse(data);

						if(typeof(objData.response) != 'undefined') {
							var info  = objData.response.info;
							var stars = objData.response.stars;

							// element for change
							var output = $('.box-rating[data-id="' + rElement.data('id') + '"]');
							
							output.find('.info').html(info);
							output.find('.stars').html(stars);
							
							// 2628000 seconds = 1 month
							setCookie( 'rating_' + rElement.data('id'), 1, { expires: 2628000 } );
						}
						
						if(typeof(objData.msg) != 'undefined') {
							$(rElement).addClass('voted');
							$(rElement).append('<span class="msg">'+objData.msg+'</span>');
						} else {
							$(rElement).append('<span class="msg">Error!</span>');
						}
	
					} catch (err) {
						$(rElement).append('<span class="msg">Error!</span>');
					}
				},
				error: function(){
					$(rElement).append('<span class="msg">Error server!</span>');
				},
				complete: function(){
					$(rElement).removeClass('processing');
					$(rElement).find('.info, .stars').show();
					$(rElement).find('.loading').hide();
					
					setTimeout(function() { $(rElement).find('.msg').fadeOut(); }, 2000 );
				}	
			});
		}
		
		// Already voted
		if( rElement.hasClass('voted') || hasCookie ){
			$(rElement).find('.msg').remove();
			$(rElement).append('<span class="msg">'+objRating.already_voted+'</span>');
			
			setTimeout(function() {
				$(rElement).find('.msg').fadeOut(); 
			}, 2000 );
		}
	});
	
})(jQuery);