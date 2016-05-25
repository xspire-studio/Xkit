<?php
/*
 * Module Name: ACF fields styled
 * Version: 1.0.0
 * Author: Xspire
 */



if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/* styled-radio */
	load_template( get_template_directory() . '/framework/modules/acf-fields-styled/inc/styled-radio.php', true );

	/* styled-checkbox */
	load_template( get_template_directory() . '/framework/modules/acf-fields-styled/inc/styled-checkbox.php', true );

	/* styled-true_false */
	load_template( get_template_directory() . '/framework/modules/acf-fields-styled/inc/styled-true_false.php', true );


	/*
	 *  Add script and style for styled elements
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-css-styled', get_template_directory_uri() . '/framework/modules/acf-fields-styled/css/styled.css', array( 'acf-input', 'acf-field-group' ) );
	});
}
?>