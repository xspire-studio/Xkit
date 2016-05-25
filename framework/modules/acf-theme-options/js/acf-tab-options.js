"use strict";

jQuery(document).ready(function(e) {
/*
*  TAB OPTIONS: Field - icon_tab
*/
	function renderItemFieldIcon(){

		var classIconSel = '.acf-field-list .acf-field[data-name="icon_tab"]';

		// Loop field icon-select
		jQuery( classIconSel ).find('select').hide().each(function( indexObj, selectObj ) {

			if( !jQuery( selectObj ).hasClass('completed') ){
				
				jQuery( selectObj ).after('<div class="icons-pack"></div>');
				
				jQuery( selectObj ).find('option').each(function( index, element ) {
					var aAlass = '';
					
					if( jQuery(element).parent().val() == jQuery(element).val() )
						var aAlass = ' active';

					jQuery(element).parent().siblings('.icons-pack')
						.append('<span class="dashicons ' + jQuery(element).val() + aAlass + '" data-icon="' + jQuery(element).val() + '"></span>');
				});

				jQuery( selectObj ).addClass('completed');
			}
		});

		// Event click icon-item
		jQuery('.icons-pack').on('click', '.dashicons', function(){
			jQuery(this).parents('.acf-field[data-name="icon_tab"]').find('select option[value=' + jQuery(this).data('icon') + ']').prop('selected', true).change();

			jQuery(this).siblings().removeClass('active');

			jQuery(this).addClass('active');
		});

	}
	renderItemFieldIcon();


	// Action change_field_type for icon_tab
	try {

		acf.add_action('change_field_type', function( $el ){

			jQuery($el).find('.acf-field[data-name="icon_tab"] .icons-pack span').removeClass('active');

			jQuery($el).find('.acf-field[data-name="icon_tab"] select').each(function( indexObj, selectObj ) {
					jQuery( selectObj ).find('option').each(function( index, element ) {
						if( jQuery(element).parent().val() == jQuery(element).val() )
							jQuery($el).find('.acf-field[data-name="icon_tab"] .icons-pack span').eq(index).addClass('active');
					});
			});

			renderItemFieldIcon();
		});

	} catch (err) {

	}
});