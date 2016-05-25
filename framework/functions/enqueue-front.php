<?php
/* 
 * Removes Contactform 7 styles
 */
remove_action( 'wp_enqueue_scripts', 'wpcf7_enqueue_styles' );


/*
 * wp_enqueue_scripts | xkit_enqueue_script_and_style_fix()
 *
 * Enqueue fix
 */
function xkit_enqueue_script_and_style_fix(){
	wp_enqueue_script( 'xkit-html5shiv', get_template_directory_uri() . '/framework/assets/js/html5.js', array(), XKIT_THEME_VERSION, true );
	wp_script_add_data( 'xkit-html5shiv', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'xkit-css3-mediaqueries', get_template_directory_uri() . '/framework/assets/js/css3-mediaqueries.js', array(), XKIT_THEME_VERSION, true );
	wp_script_add_data( 'xkit-css3-mediaqueries', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'xkit-selectivizr', get_template_directory_uri() . '/framework/assets/js/selectivizr-min.js', array(), XKIT_THEME_VERSION, true );
	wp_script_add_data( 'xkit-selectivizr', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'xkit_enqueue_script_and_style_fix' );


/*
 * wp_enqueue_scripts | xkit_enqueue_scripts_vendors()
 *
 * Enqueue scripts vendors
 */
function xkit_enqueue_scripts_vendors(){
	wp_enqueue_script( 'xkit-scripts-vendors', get_template_directory_uri() . '/framework/assets/js/scripts-vendors.js', array( 'jquery' ), XKIT_THEME_VERSION, true );

	wp_localize_script( 'xkit-scripts-vendors', 'init_localize_object', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' )
	));
}
add_action( 'wp_enqueue_scripts', 'xkit_enqueue_scripts_vendors' );
?>