<?php
/**
 * Theme framework, global router
 *
 * @date	21/03/2016
 * @since	1.0
 */

defined( 'XKIT_THEME_DEBUG' ) or define( 'XKIT_THEME_DEBUG', false );


/**
 * Main class theme
 *
 * @subpackage Xkit
 */
class Xkit {
	/* 
	 * Construct queue
	 */
	public function __construct() {
		$this->init();
	}

	/* 
	 * INIT (global theme queue)
	 */
	public function init() {

		// Options
		load_template( get_template_directory() . '/framework/functions/options.php', true );

		// Init
		load_template( get_template_directory() . '/framework/functions/debug.php', true );
		load_template( get_template_directory() . '/framework/functions/init.php', true );
		load_template( get_template_directory() . '/framework/functions/init-actions.php', true );
		load_template( get_template_directory() . '/framework/functions/enqueue-front.php', true );

		// Autoload
		xkit_autoload_files( get_template_directory() . '/framework/functions/helpers' );
		xkit_autoload_files( get_template_directory() . '/framework/modules' );
		xkit_autoload_files( get_template_directory() . '/framework/functions/snippets' );
	}
}


/*
 * Enqueue assets to admin
 */
function xkit_enqueue_assets_admin() {
	wp_enqueue_style( 'acf-custom-style', get_template_directory_uri() . '/framework/assets/css/acf-custom-style.css', array( 'acf-input', 'acf-field-group' ) );
	wp_enqueue_style( 'xkit-admin-custom-style', get_template_directory_uri() . '/framework/assets/css/admin-custom-style.css' );

	wp_enqueue_script( 'xkit-admin-custom-script', get_template_directory_uri() . '/framework/assets/js/admin-custom-script.js', array( 'jquery' ) );
	wp_enqueue_script( 'xkit-cookie', get_template_directory_uri() . '/framework/assets/js/cookie.js', array( 'jquery' ) );
}
add_action( 'admin_init', 'xkit_enqueue_assets_admin' );
?>