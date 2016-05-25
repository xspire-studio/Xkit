"use strict";

jQuery(function() {
	// change position slider
	function changeValSlider( element ){
		var val = parseFloat( jQuery(element).val() );
		var valMin = parseFloat( jQuery(element).data('min') );
		var valMax = parseFloat( jQuery(element).data('max') );
		
		if( valMin > val ){
			jQuery(element).val(valMin);
			jQuery(element).siblings('.slider').slider( 'value' , valMin );
		}
		else if( valMax < val ){
			jQuery(element).val(valMax);
			jQuery(element).siblings('.slider').slider( 'value' , valMax );
		}
		else{
			jQuery(element).siblings('.slider').slider( 'value' , val );
		}
	}


	/*
	*  EVENT
	*/

	// slider fix dynamically
	if( jQuery('.widgets-holder-wrap').length > 0 ){
		jQuery('.widgets-holder-wrap').find('.slider').each(function(index, element) {

			jQuery(element).slider({
				range: "min",
				min: jQuery(element).siblings('input[type="text"]').data('min'),
				max: jQuery(element).siblings('input[type="text"]').data('max'),
				step: jQuery(element).siblings('input[type="text"]').data('step'),
				value:  jQuery(element).siblings('input[type="text"]').val(),
				slide: function(event, ui) {
					jQuery(element).siblings('input[type="text"]').val(ui.value);
				},
				create: function( event, ui ) {
					if( jQuery(this).find('.ui-slider-range-min').length > 1 ){
						jQuery(this).find('.ui-slider-range-min').first().remove();
					}
				}
			});
		});
	}

	if( typeof(acf) != 'undefined' ){
		acf.add_action('append', function ($el) {
			jQuery($el).parents('.acf-flexible-content, .acf-repeater').find('.slider').each(function(index, element) {

				jQuery(element).slider({
					range: "min",
					min: jQuery(element).siblings('input[type="text"]').data('min'),
					max: jQuery(element).siblings('input[type="text"]').data('max'),
					step: jQuery(element).siblings('input[type="text"]').data('step'),
					value:  jQuery(element).siblings('input[type="text"]').val(),
					slide: function(event, ui) {
						jQuery(element).siblings('input[type="text"]').val(ui.value);
					},
					create: function( event, ui ) {
						if( jQuery(this).find('.ui-slider-range-min').length > 1 ){
							jQuery(this).find('.ui-slider-range-min').first().remove();
						}
					}
				});
			});
		});
	}

	// input keypress float
	jQuery('.acf-field[data-type="slider"] .acf-input input[type="text"]').live( 'keypress', function(e) {
		if( e.which == 8 || e.which == 13 ||e.keyCode == 37 
		|| e.keyCode == 39 || e.keyCode == 46 || e.keyCode == 116 ) {
			return true;
		}

		if( e.which < 46 || e.which > 59 ) {
			e.preventDefault();
		}
		
		if( e.which == 46 && jQuery(this).val().indexOf('.') != -1 ) {
			e.preventDefault();
		}
	});

	// change position slider [keypress - enter]
	jQuery('.acf-field[data-type="slider"] .acf-input input[type="text"]').live( 'keypress', function(e) {
		if(e.keyCode == 13){
			changeValSlider( this );

			return false;
		}
	});

	// change position slider [focusout]
	jQuery('.acf-field[data-type="slider"] .acf-input input[type="text"]').live( 'focusout', function(e) {
		changeValSlider( this );
	});
});