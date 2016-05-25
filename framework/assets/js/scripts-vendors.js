"use strict";

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


(function($) {
	
	/** 
	 * Open in Tab
	*/
	$( '[data-js="tab-delegation"]' ).each( tabDelegation );

	function tabDelegation() {
		var $this = $( this ),
			data  = $this.data();
		if( data.tab ) $this.on( 'click', 'a', openInTab );
	}

	function openInTab( e ) {
		e.preventDefault(); 

		var $this = $( this ),
			url = $this.attr( 'href' );

		window.open( url, '_blank' );
	}

})(jQuery);