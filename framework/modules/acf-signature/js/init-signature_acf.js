"use strict";

jQuery(document).ready(function($) {
	jQuery('td.column-signature').each(function(index, obj) {
		var colorHash = new ColorHash();

		jQuery(obj).find('.circle').not('.no').css({
			background: colorHash.hex(jQuery(obj).find('.signature').html())
		});
	});
});