"use strict";

(function($) {

	/* On document ready */
	jQuery(document).ready( function(){

		/* Refresh Add buttons to items with columns */
		function refreshColumnsButtons() {
			jQuery( '.add-column-btn' ).remove();
			jQuery( 'select[class*="edit-menu-item-menu_columns"]' ).closest('.menu-item').find('.item-controls .item-edit').before('<a class="add-column-btn" href="#">' + megaMenu.addColumn + '</a>');
		};
		refreshColumnsButtons();


		/* On sort stop */
		jQuery( '#menu-to-edit' ).live( 'sortstop', function( event, ui ) {
			var itemId 		= parseInt( ui.item.find( '.menu-item-data-db-id' ).attr( 'value' ) ),
				itemDepth 	= ui.item.menuItemDepth(),
				newDepth 	= ui.placeholder.menuItemDepth(),
				parentDepth = newDepth - 1,
				parent 		= ui.item.prevAll( '.menu-item-depth-' + parentDepth ).first(),
				menu_id 	= jQuery( '#nav-menu-meta-object-id' ).val();

			/* Refresh mega menu fields */
			if( itemId > 0 && itemDepth != newDepth ) {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: { 'action': 'refresh_menu_item_fields', item_id: itemId, depth: newDepth, menu: menu_id },
					beforeSend: function(){
						ui.item.css( 'opacity', '0.4' );
					},
					success: function( result ){
						if( result != 0 ){
							ui.item.find( '> .menu-item-settings .menu-location-box, .clear-after-box' ).remove();
							ui.item.find( '> .menu-item-settings p.field-move' ).before( result );
						}
						ui.item.css( 'opacity', '1' );

						// Refresh buttons
						refreshColumnsButtons();
					},
					error: function(e){
						ui.item.css( 'opacity', '1' );
					}
				});
			}

			/* Refresh columns labels */
			if( ui.item.hasClass( 'column-item' ) ) {
				var depth 		= parseInt( ui.placeholder.menuItemDepth(), 10 ),
					parentDepth = depth - 1,
					parentItem  = ui.item.prevAll( '.menu-item-depth-' + parentDepth ).first();

				// Add icon
				ui.item.find( '> .menu-item-bar .item-title .dashicons' ).remove();
				ui.item.find( '> .menu-item-bar .menu-item-title' ).before('<span class="dashicons dashicons-menu"></span> ');

				// Set control
				ui.item.find( '> .menu-item-bar .item-controls .item-delete' ).remove();
				ui.item.find( '> .menu-item-bar .item-controls .item-edit' ).before( ui.item.find( '> .menu-item-settings .item-delete' )[0].outerHTML );
				ui.item.find( '> .menu-item-bar .item-controls' ).find('.item-type').remove();

				// Set submenu title
				if( parentItem.length ) {
					ui.item.find( '> .menu-item-bar .is-submenu' ).html( megaMenu.forMenu + '"' + parentItem.find('> .menu-item-bar .menu-item-title').html() + '"' ).css( 'display', 'inline' );
				}
				else {
					ui.item.find( '> .menu-item-bar .is-submenu' ).html('').css( 'display', 'inline' );
				}
			}
		});


		/* Style column items */
		function styleColumnsItems(){
			var columnsItems = jQuery( '#menu-to-edit .column-item' );

			// Set labels
			columnsItems.each( function( index ) {
				var parentItemId = jQuery( this ).find( '.menu-item-data-parent-id' ).val(),
					parentItem 	 = jQuery( '#menu-item-' + parentItemId );

				// Add icon
				jQuery( this ).find( '> .menu-item-bar .item-title .dashicons' ).remove();
				jQuery( this ).find( '> .menu-item-bar .menu-item-title' ).before('<span class="dashicons dashicons-menu"></span> ');

				// Set control
				jQuery( this ).find( '> .menu-item-bar .item-controls .item-delete' ).remove();
				jQuery( this ).find( '> .menu-item-bar .item-controls .item-edit' ).before( jQuery( this ).find( '> .menu-item-settings .item-delete' )[0].outerHTML );
				jQuery( this ).find( '> .menu-item-bar .item-controls' ).find('.item-type').remove();


				// Set submenu title
				jQuery( this ).find( '> .menu-item-bar .menu-item-title' ).html( megaMenu.columnName );

				// Set submenu title
				if( parentItem.length ) {
					jQuery( this ).find( '> .menu-item-bar .is-submenu' ).html( megaMenu.forMenu + '"' + parentItem.find('> .menu-item-bar .menu-item-title').html() + '"' ).css( 'display', 'inline' );
				}
				else {
					jQuery( this ).find( '> .menu-item-bar .is-submenu' ).html('').css( 'display', 'inline' );
				}
			} );
		}
		styleColumnsItems();


		/* Create columns */
		jQuery( '.menu-item' ).on( 'click', '.add-column-btn', function() {
			var menuItem 	  = jQuery( this ).closest('.menu-item'),
				menuItemDepth = parseInt( menuItem.menuItemDepth(), 10 ),
				istertColumns = { "-1" : { 
					'menu-item-type' 	: 'custom',
					'menu-item-title' 	: megaMenu.columnName,
					'menu-item-url' 	: '#'
				}};

			// Check ajax
			if( menuItem.hasClass('ajaxing') ) {
				return false;
			}

			// Find children
			var childrenItems = menuItem.childMenuItems();
			if( childrenItems.length > 0 ) {
				var columnsCount = parseInt( childrenItems.find('.column').length );
			}

			// Ajax get columns items
			var menu_id 	= jQuery( '#nav-menu-meta-object-id' ).val();
			var nonce_field = jQuery( '#menu-settings-column-nonce' ).val();

			if( menu_id && nonce_field ) {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: { 'action': 'add-menu-item', 'menu': menu_id, 'menu-item': istertColumns, 'menu-settings-column-nonce': nonce_field, 'item_type': 'column' },
					beforeSend: function(){
						menuItem.css( 'opacity', '0.4' ).addClass('ajaxing');
					},
					success: function( result ) {
						menuItem.css( 'opacity', '1' ).removeClass('ajaxing');
						var newItems = jQuery( result );
						if( newItems[0].outerHTML ) {

							// Style items
							newItems.addClass('column-item');

							// Insert columns
							newItems.each( function( index ){

								// Append item params
								jQuery( this ).find('.menu-item-settings').append('<input type="hidden" class="menu-item-data-is-column" name="menu-item-is_column[' + jQuery( this ).find('.menu-item-data-db-id').val() + ']" value="1" />');

								// Add item to menu html
								menuItem.after( jQuery( this )[0].outerHTML );
								menuItem.next().shiftDepthClass( 1 ).updateParentMenuItemDBId();
							});

							styleColumnsItems();
						}
					},
					error: function(e){
						menuItem.css( 'opacity', '1' ).removeClass('ajaxing');
					}
				});
			}

			return false;
		});

		/* Screen options fix */
		jQuery('#screen-options-wrap .metabox-prefs input[type="checkbox"]').each( function( index ) {
			jQuery( this ).attr( 'id', jQuery( this ).attr( 'name' ) );
		} );

	});
})(jQuery);