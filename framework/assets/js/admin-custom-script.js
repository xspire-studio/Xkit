"use strict";

/* ---------------------------------------------------------------------------
 * Simple Functions
 * --------------------------------------------------------------------------- */
 
function UpdateQueryString(key, value, url) {
	if (!url) url = window.location.href;
	var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
		hash;

	if (re.test(url)) {
		if (typeof value !== 'undefined' && value !== null){
			return url.replace(re, '$1' + key + "=" + value + '$2$3');
		} else {
			hash = url.split('#');
			url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
			if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
				url += '#' + hash[1];
			return url;
		}
	}
	else {
		if (typeof value !== 'undefined' && value !== null) {
			var separator = url.indexOf('?') !== -1 ? '&' : '?';
			hash = url.split('#');
			url = hash[0] + separator + key + '=' + value;
			if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
				url += '#' + hash[1];
			return url;
		}
		else {
			return url;
		}
	}
}

/* ---------------------------------------------------------------------------
 * jQuery Functions
 * --------------------------------------------------------------------------- */

(function($) {
	/** 
	 * Forward port jQuery.live()
	 * Wrapper for newer jQuery.on()
	 * Uses optimized selector context 
	 * Only add if live() not already existing.
	*/
	if (typeof $.fn.live == 'undefined' || !($.isFunction($.fn.live))) {
	  $.fn.extend({
		  live: function (event, callback) {
			 if (this.selector) {
				  $(document).on(event, this.selector, callback);
			  }
		  }
	  });
	}
})(jQuery);