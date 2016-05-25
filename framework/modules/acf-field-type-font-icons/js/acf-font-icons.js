"use strict";

jQuery(document).ready(function(e) {
/*
 * Icon Packs: Field - icon
 */
	function renderItemFieldIcon(){

		var classIconSel = '.acf-field-list .acf-field-object-font-icons .acf-field[data-name="icon"]';

		// Loop field icon-select
		jQuery( classIconSel ).find('select').hide().each(function( indexObj, selectObj ) {

			if( !jQuery( selectObj ).hasClass('completed') ){
				
				jQuery( selectObj ).after('<div class="icons-container"></div>');

				// optgroups
				jQuery( selectObj ).find('optgroup').each(function( igroup, optgroup ) {

					jQuery(optgroup).parents('select').siblings('.icons-container').append('<h3>' + jQuery(optgroup).attr('label') + '</h3>');

					// options
					jQuery( optgroup ).find('option').each(function( index, element ) {
						var aAlass = '';
						
						if( jQuery(element).parents('select').val() == jQuery(element).val() )
							var aAlass = ' active';

						jQuery(element).parents('select').siblings('.icons-container')
							.append('<i class="item ' + jQuery(element).val() + aAlass + '" data-icon="' + jQuery(element).val() + '"></i>');
					});
				});
				
				jQuery( selectObj ).addClass('completed');
			}
		});

		// Event click icon-item
		jQuery('.acf-field-object-font-icons .icons-container').on('click', '.item', function(){
			jQuery(this).parents('.acf-field-object-font-icons .acf-field[data-name="icon"]').find('select option[value="' + jQuery(this).data('icon') + '"]').prop('selected', true).change();

			jQuery(this).siblings().removeClass('active');

			jQuery(this).addClass('active');
		});
	}
	renderItemFieldIcon();


	// Action change_field_type for icon
	try {
		acf.add_action('change_field_type', function( $el ){

			jQuery($el).find('.acf-field-object-font-icons .acf-field[data-name="icon"] .icons-container i').removeClass('active');
			
			jQuery($el).find('.acf-field-object-font-icons .acf-field[data-name="icon"] select').each(function( indexObj, selectObj ) {
					jQuery( selectObj ).find('option').each(function( index, element ) {
						if( jQuery(element).parent().val() == jQuery(element).val() )
							jQuery($el).find('.acf-field-object-font-icons .acf-field[data-name="icon"] .icons-container i').eq(index).addClass('active');
					});
			});

			renderItemFieldIcon();
		});

	} catch (err) {

	}
});


/*
 * Icon Packs : Render Field - icon
 */
jQuery('.acf-render-field[data-name="icon-packs"] .icons-container .item').live('click', function(){
	jQuery(this).parents('.acf-render-field[data-name="icon-packs"]').find('input').val(jQuery(this).data('icon'));

	jQuery(this).parents('.acf-render-field[data-name="icon-packs"]').find('.icon-preview').html('<i class="item ' + jQuery(this).data('icon') + '"></i>');

	jQuery(this).siblings().removeClass('active');

	jQuery(this).addClass('active');
});