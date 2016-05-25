<?php
/**
 * Module Name: Social Share Buttons
 * Version: 1.1
 * Author: Xspire
 *
 * 1.0 - function  Xkit_Social_Share_Buttons()
 * 2.0 - function  xkit_get_share_buttons()
 */


if ( defined( 'XKIT_SOCIAL_SHARE_MODULE_ENABLE' ) && XKIT_SOCIAL_SHARE_MODULE_ENABLE ) {


	/*
	 * Load plugin files
	 */
	load_template( get_template_directory() . '/framework/modules/social-share/includes/share-providers.php', true );
	load_template( get_template_directory() . '/framework/modules/social-share/includes/share-templates.php', true );
	load_template( get_template_directory() . '/framework/modules/social-share/share-core.php', true );


	/*
	 * Returns the main instance of Xkit_Social_Share_Buttons to prevent the need to use globals.
	 *
	 * @return object Xkit_Social_Share_Buttons
	 */
	function Xkit_Social_Share_Buttons() {
		$instance = Xkit_Social_Share_Buttons::instance();

		return $instance;
	}


	/*
	 * Init Share Buttons
	 *
	 * @return string HTML
	 */
	Xkit_Social_Share_Buttons();


	/*
	 * Custom Get Share Buttons
	 *
	 * @param  array  $options
	 * @return string HTML
	 */
	function xkit_get_share_buttons( $options = array() ) {

		$default = array(
			'template'		=> 'default',
			'providers'		=> 'any'
		);

		$options = array_merge( $default, $options );
		$share_buttons = Xkit_Social_Share_Buttons();
		$share_buttons_html = $share_buttons->build_share_buttons( $options );

		return $share_buttons_html;
	}
}