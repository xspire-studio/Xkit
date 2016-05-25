<?php
/*
 * Module Name: Page Preloader
 * Version: 1.0
 * Author: Xspire
 *
 * 1.0 - filter    wp_head   | xkit_site_template_include()
 * 2.0 - filter    wp_footer | xkit_add_preloader_html()
 */



if ( defined( 'XKIT_PRELOADER_MODULE_ENABLE' ) && XKIT_PRELOADER_MODULE_ENABLE ) {
	
	
	/**
	 * Create Buffer of Site HTML
	 * 
	 * @param  string $template
	 * @return string Site HTML
	 */
	function xkit_site_template_include() {
		/* Preloader include */
		$preloader_include = apply_filters( 'xkit_preloader_include', true );

		if( !$preloader_include ){
			return false;
		}

		ob_start();
	}
	add_action( 'wp_head', 'xkit_site_template_include', 1 );


	/**
	 * Add preloader html to Page
	 */
	function xkit_add_preloader_html() {
		/* Preloader include */
		$preloader_include = apply_filters( 'xkit_preloader_include', true );

		if( !$preloader_include ){
			return false;
		}
		
		// Get Buffer of Site HTML ( started in wp_head )
		//$content = ob_get_clean();
		
		// Generate Preloader HTML
		$img_array 		    = xkit_get_theme_option( 'site_preloader_img' );
		$preloader_bg_color = xkit_get_theme_option( 'site_preloader_bg', '#FFFFFF' );
		$default_loader_url = apply_filters( 'xkit_default_loader_url', get_template_directory_uri() . '/framework/modules/page-preloader/images/loader.gif' );
		$image_url          = isset( $img_array['url'] ) ? $img_array['url'] : $default_loader_url;
		
		ob_start();
		?>
			<div id="page-preloader">
				<span class="spinner"></span>
			</div>
			
			<script>
				(function($){
					"use strict";
					jQuery( window ).on('load', function () {
						var xkit_preloader = jQuery('#page-preloader'),
							xkit_spinner   = xkit_preloader.find('.spinner');
						xkit_spinner.fadeOut();
						xkit_preloader.delay( 350 ).fadeOut('slow');
					});
				})(jQuery);
			</script>

			<style>
				#page-preloader {
					position: fixed;
					left: 0;
					top: 0;
					right: 0;
					bottom: 0;
					width: 100%;
					height: 100%;
					background: url('<?php echo esc_url( $image_url ); ?>') <?php echo esc_html( $preloader_bg_color ); ?> no-repeat 50% 50%;
					z-index: 999999;
				}
			</style>
		<?php
		$html = ob_get_clean();
		$html = apply_filters( 'xkit_preloader_html', $html );
		
		// Return HTML
		print( $html );
	}
	add_action( 'wp_footer', 'xkit_add_preloader_html', 1 );
}