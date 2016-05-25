<?php
/**
Plugin Name: WPMU Dev code library
Plugin URI:  http://premium.wpmudev.org/
Description: Framework to support creating WordPress plugins and themes.
Version:     1.0.17
Author:      WPMU DEV
Author URI:  http://premium.wpmudev.org/
*/


$version = '1.0.17'; 

/**
 * Load Xkit_TheLib class definition if not some other plugin already loaded it.
 */
$class_name = 'Xkit_TheLib_' . str_replace( '.', '_', $version );
if ( ! class_exists( $class_name ) && file_exists( get_template_directory() . '/framework/modules/sidebar-generator/inc/external/wpmu-lib/functions-wpmulib.php' ) ) {
	load_template( get_template_directory() . '/framework/modules/sidebar-generator/inc/external/wpmu-lib/functions-wpmulib.php', true );
}

if ( ! class_exists( 'Xkit_TheLibWrap' ) ) {
	/**
	 * The wrapper class is used to handle situations when some plugins include
	 * different versions of Xkit_TheLib.
	 *
	 * Xkit_TheLibWrap will always keep the latest version of Xkit_TheLib for later usage.
	 */
	class Xkit_TheLibWrap {
		static public $version = '0.0.0';
		static public $object = null;

		static public function set_obj( $version, $obj ) {
			if ( version_compare( $version, self::$version, '>' ) ) {
				self::$version = $version;
				self::$object = $obj;
			}
		}
	};
}
$obj = new $class_name();
Xkit_TheLibWrap::set_obj( $version, $obj );

if ( ! function_exists( 'Xkit_WDev' ) ) {
	/**
	 * This is a shortcut function to access the latest Xkit_TheLib object.
	 *
	 * Usage:
	 *   Xkit_WDev()->message();
	 */
	function Xkit_WDev() {
		return Xkit_TheLibWrap::$object;
	}
}
