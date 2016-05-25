<?php
/*
 * Module Name: Custom Sidebars
 * Version: 1.0
 * Author: Xspire
*/



if ( defined( 'XKIT_SIDEBARGEN_MODULE_ENABLE' ) && XKIT_SIDEBARGEN_MODULE_ENABLE ) {


	/*
	 * after_setup_theme || xkit_inc_sidebars_free_init
	 * 
	 * Load Custom Sidebars
	 */
	function xkit_inc_sidebars_free_init() {
		// Check if the PRO plugin is present and activated.
		if ( class_exists( 'Xkit_CustomSidebars' ) ) {
			return false;
		}

		// Load the actual core.
		load_template( get_template_directory() . '/framework/modules/sidebar-generator/inc/class-custom-sidebars.php', true );

		// Include function library
		if ( file_exists( get_template_directory() . '/framework/modules/sidebar-generator/inc/external/wpmu-lib/core.php' ) ) {
			load_template( get_template_directory() . '/framework/modules/sidebar-generator/inc/external/wpmu-lib/core.php', true );
		}

		// Initialize the plugin
		Xkit_CustomSidebars::instance();
	}
	add_action( 'after_setup_theme', 'xkit_inc_sidebars_free_init' );


	/*
	 * Custom Sidebars Empty Plugin
	 */
	if ( ! class_exists( 'Xkit_CustomSidebarsEmptyPlugin' ) ) {
		class Xkit_CustomSidebarsEmptyPlugin extends WP_Widget {
			public function Xkit_CustomSidebarsEmptyPlugin() {
				parent::__construct( false, $name = 'Xkit_CustomSidebarsEmptyPlugin' );
			}
			public function form( $instance ) {
				// Nothing, just a dummy plugin to display nothing
			}
			public function update( $new_instance, $old_instance ) {
				// Nothing, just a dummy plugin to display nothing
			}
			public function widget( $args, $instance ) {
				echo '';
			}
		}
	}
}



/* 
 * Dynamic sidebar name
 *
 * @param  string $sidebar
 * @return string Active sidebar name
 */
function xkit_dynamic_sidebar_name( $sidebar = '' ){

	if( class_exists( 'Xkit_CustomSidebarsReplacer' ) ){
		$sidebars_replacer = Xkit_CustomSidebarsReplacer::instance();
		$defaults = $sidebars_replacer::get_options();
		$replacements = $sidebars_replacer->determine_replacements( $defaults );

		if( !empty( $replacements[ $sidebar ] ) ){
			$sidebar = (string) $replacements[ $sidebar ][0];
		}
	}

	return $sidebar;
}