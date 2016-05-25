<?php
/*
 * Module Name: Mega Menu
 * Version: 1.0
 * Author: Xspire
 */



if ( defined( 'XKIT_MEGA_MENU_MODULE_ENABLE' ) && XKIT_MEGA_MENU_MODULE_ENABLE ) {


	/*
	 * Include Mega Menu Core
	 */
	load_template( get_template_directory() . '/framework/modules/mega-menu/includes/frontend.php', true );
	load_template( get_template_directory() . '/framework/modules/mega-menu/includes/backend.php', true );


	/*
	 * admin_enqueue_scripts | xkit_enqueue_admin_menu_scripts()
	 *
	 * Include scripts & styles in Admin
	 */
	function xkit_enqueue_admin_menu_scripts() {
		$current_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		$page_opened = pathinfo( $current_path );

		if( $page_opened['basename'] == 'nav-menus.php' ) {

			// Scripts
			wp_enqueue_script( 'xkit-mega-menu-admin', get_template_directory_uri() . '/framework/modules/mega-menu/assets/js/admin-mega-menu.js', array('jquery'), XKIT_THEME_VERSION, false );

			// Styles
			wp_enqueue_style( 'xkit-mega-menu-admin', get_template_directory_uri() . '/framework/modules/mega-menu/assets/css/admin-mega-menu.css', array(), XKIT_THEME_VERSION );

			// Mega menu script localization
			$translation_array = array( 
				'forMenu' 		=> esc_html__( 'For ', 'xkit' ), 
				'columnName' 	=> esc_html__( 'Column', 'xkit' ), 
				'addColumn' 	=> esc_html__( 'Add column', 'xkit' ), 
			);
			wp_localize_script( 'jquery', 'megaMenu', $translation_array );

			do_action( 'xkit_admin_mega_menu_sripts' );
		}
	}
	add_action( 'admin_enqueue_scripts', 'xkit_enqueue_admin_menu_scripts' );


	/*
	 * init | xkit_register_mega_menu()
	 *
	 * Register Mega menu fields
	 * 
	 * @param array  $mega_menu_fields  Fields list array
	 * @param string $location          Location slug
	 */
	function xkit_register_mega_menu( $mega_menu_fields, $location ) {
		if ( ! class_exists('Xkit_AddMenuFields') || empty( $mega_menu_fields ) || empty( $location ) ) {
			return false;
		}

		$menu_fields = new Xkit_AddMenuFields( $mega_menu_fields, $location );
	}


	/*
	 * wp_ajax_refresh_menu_item | wp_ajax_refresh_menu_item()
	 *
	 * Refresh menu item fields
	 */
	function xkit_refresh_menu_item_fields() {
		$item_id = intval( $_POST['item_id'] );
		$depth = intval( $_POST['depth'] );

		$item_data = wp_setup_nav_menu_item( get_post( $item_id ) );
		if( $item_data ){
			do_action( 'wp_nav_menu_item_custom_fields', $item_data, $depth, array(), 0 );
		}
		else{
			echo 0;
		}

		die();
	}
	add_action( 'wp_ajax_refresh_menu_item_fields', 'xkit_refresh_menu_item_fields' );
	add_action( 'wp_ajax_nopriv_refresh_menu_item_fields', 'xkit_refresh_menu_item_fields' );

}