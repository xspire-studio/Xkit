<?php
/**
 * Include plugin Advanced Custom Fields and load theme options by json
 *
 * @package Xkit
 * @subpackage Options
 *
 * 1.0  - function  xkit_deactivate_acf_lite()
 * 4.0  - filter    acf/settings/save_json       | xkit_theme_acf_json_save_point()
 * 5.0  - filter    acf/settings/load_json       | xkit_theme_acf_json_load_point()
 * 6.0  - hook      template_redirect            | xkit_acf_alternatives()
 * 7.0  - function  xkit_the_field_theme()
 * 8.0  - function  xkit_get_field_theme()
 * 9.0  - function  xkit_the_sub_field_theme()
 * 10.0 - function  xkit_get_sub_field_theme()
 * 11.0 - function  xkit_get_theme_option()
 * 12.0 - function  xkit_get_theme_option_font()
 */



if( defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ){

	/*
	 *  Deactivate ACF lite
	 */
	function xkit_deactivate_acf_lite(){
		if( class_exists('acf') ){
			if( !class_exists('acf_pro') ){
				if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
					load_template( ABSPATH . '/wp-admin/includes/plugin.php', true );
				}
				deactivate_plugins(  'advanced-custom-fields/acf.php' );
			}
		}
	}
	xkit_deactivate_acf_lite();

	/*
	 *  Directory for synchronize
	 */
	if( !is_dir( get_template_directory() . '/includes/synchronize' ) ){
		mkdir( get_template_directory() . '/includes/synchronize', 0755, true );
	}
	
	/*
	 * Replace double slash to single
	 *
	 * @param  string $str
	 * @return string
	 */
	if ( !function_exists( 'xkit_acf_normalize_slash' ) )
	{
		function xkit_acf_normalize_slash( $str ){
			return preg_replace('/([^:])(\/{2,})/', '$1/', $str);
		}
	}

	
	if( class_exists('acf_pro') ){

		/*
		 * acf/settings/save_json | xkit_theme_acf_json_save_point()
		 *
		 * Each time you save a field group a JSON file will be created / 
		 * updated with the field group and field settings.
		 */
		function xkit_theme_acf_json_save_point( $path ) {
			return get_template_directory() . '/includes/synchronize';
		}
		add_filter( 'acf/settings/save_json', 'xkit_theme_acf_json_save_point' );


		/*
		 * acf/settings/load_json | xkit_theme_acf_json_load_point()
		 *
		 * During ACF’s initialization procedure, all .json files within the acf-json folder will be loaded
		 */
		function xkit_theme_acf_json_load_point( $paths ) {
			unset( $paths[0] );

			$paths[] = get_template_directory() . '/includes/synchronize';

			return $paths;
		}
		add_filter( 'acf/settings/load_json', 'xkit_theme_acf_json_load_point' );


		/* Hide ACF field group menu item */
		if( !XKIT_THEME_DEBUG ){
			add_filter( 'acf/settings/show_admin', '__return_false' );
		}


		/*  Add-ons for ACF | Advanced Custom Fields */
		if( class_exists( 'acf_pro' ) ){
			foreach ( glob( get_template_directory() . '/framework/modules/acf-*' ) as $filename ) {
				$path_file = $filename . '/' . basename( $filename ). '.php';

				if( file_exists( $path_file ) ){
					load_template( $path_file, true );
				}
			}
		}

	// if can not acf
	} else {
		function xkit_register_theme_options_page() {
			add_theme_page( esc_html__( 'Theme Options', 'xkit' ), esc_html__( 'Theme Options', 'xkit' ), 'edit_posts', 'theme-options', function() {
				?>
					<div class="notice_disable_acf error">
						<p>
							<h4>
								<?php esc_html_e( 'IMPORTANT! To activate the ability to settings themes, install plug-in please:', 'xkit' ); ?>
								<a href="<?php echo admin_url('/themes.php?page=theme-install-plugins&plugin_status=install'); ?>">ACF | Advanced Custom Fields</a>
							</h4>
						</p>
					</div>
				<?php
			} );
		}
		add_action( 'admin_menu', 'xkit_register_theme_options_page' );
	}
}


/* ---------------------------------------------------------------------------
 * If ACF disabled or can not be found
 * --------------------------------------------------------------------------- */
function xkit_acf_alternatives(){
	if( !class_exists( 'acf_pro' ) ){
		if( !function_exists( 'the_field' ) ){
			function the_field(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'the_sub_field' ) ){
			function the_sub_field(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_fields' ) ){
			function get_fields(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_field' ) ){
			function get_field(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_sub_field' ) ){
			function get_sub_field(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_sub_field_object' ) ){
			function get_sub_field_object(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_field_object' ) ){
			function get_field_object(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_field_objects' ) ){
			function get_field_objects(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'the_flexible_field' ) ){
			function the_flexible_field(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'get_row_layout' ) ){
			function get_row_layout(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'have_rows' ) ){
			function have_rows(){ // alternative for acf
				return false;
			}
		}
		if( !function_exists( 'has_sub_field' ) ){
			function has_sub_field(){ // alternative for acf
				return false;
			}
		}
	}
}
add_action( 'template_redirect', 'xkit_acf_alternatives' );


/* ---------------------------------------------------------------------------
 * Helper functions for ACF
 * --------------------------------------------------------------------------- */

/*
 * This function is the same as echo xkit_get_field_theme().
 *
 * @param  string $selector The field name or key
 * @param  string $default  The default value
 * @param  mixed  $post_id  The post_id of which the value is saved against
 * @return n/a
 */
function xkit_the_field_theme( $selector, $default = '', $post_id = false, $format_value = true ){
	$value = xkit_get_field_theme( $selector, $default = '', $post_id, $format_value );

	if( is_array( $value ) ) {
		$value = @implode( ', ', $value );
	}

	if( xkit_value_exists( $value ) ){
		print( $value );
	} else{
		print( $default );
	}
}


/*
 * This function will return a custom field value for a specific field name/key + post_id.
 * There is a 3rd parameter to turn on/off formating. This means that an image field will not use
 * its 'return option' to format the value but return only what was saved in the database.
 *
 * @param  string  $field_name   The field name or key
 * @param  string  $default      The default value
 * @param  mixed   $post_id      The post_id of which the value is saved against
 * @param  boolean $format_value Whether or not to format the value as described above
 * @return mixed
 */
function xkit_get_field_theme( $field_name, $default = '', $post_id = false, $format_value = true ) {
	if ( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {
		$value = get_field( $field_name, $post_id, $format_value );

		$value = apply_filters( 'xkit_get_field_theme', $value, $field_name, $post_id );

		if( xkit_value_exists( $value ) ){
			return $value;
		} else{
			return $default;
		}
	} else {
		return $default;
	}
}


/*
 * This function is the same as echo xkit_get_sub_field_theme
 *
 * @param  string $field_name The field name
 * @param  string $default    The default value
 * @return n/a
 */
function xkit_the_sub_field_theme( $field_name, $default = '', $format_value = true ) {
	$value = xkit_get_sub_field_theme( $field_name, $default = '', $format_value );

	if( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	if( xkit_value_exists( $value ) ){
		print( $value );
	} else{
		print( $default );
	}
}


/*
 * This function is used inside a 'has_sub_field' while loop to return a sub field value
 *
 * @param  string $field_name The field name
 * @param  string $default    The default value
 * @return mixed
 */
function xkit_get_sub_field_theme( $field_name, $default = '', $format_value = true ) {
	if ( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {
		$value = get_sub_field( $field_name, $format_value );

		$value = apply_filters( 'xkit_get_sub_field_theme', $value, $field_name );

		if( xkit_value_exists( $value ) ){
			return $value;
		} else{
			return $default;
		}
	} else {
		return $default; 
	}
}


/*
 * This function will return custom option by theme.
 *
 * @param  string $selector The field name or key
 * @param  string $default  The default value
 * @return mixed
 */
function xkit_get_theme_option( $field_name, $default = '' ){
	if ( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {
		$value = get_field( $field_name, 'options' );

		wp_cache_delete( 'load_value/post_id=options/name=' . $field_name, 'acf' );

		$value = apply_filters( 'xkit_get_theme_option', $value, $field_name );

		if( xkit_value_exists( $value ) ){
			return $value;
		} else {
			return $default;
		}
	} else {
		return $default;
	}
}


/*
 * This function will print custom option by theme.
 *
 * @param  string $selector The field name or key
 * @param  string $default  The default value
 * @return mixed
 */
function xkit_the_theme_option( $field_name, $default = '' ){
	print xkit_get_theme_option( $field_name, $default );
}


/*
 * This function will return custom option setting font by theme.
 *
 * The parameter $type can return the following font settings:
 *    backupfont, font-family, font-weight, font-style, font-size, 
 *    line-height, letter-spacing, text-align, direction, text-color.
 *
 * @param  string $field_name The field name or key
 * @param  string $type       The setting by font
 * @param  string $default    The default value
 * @return mixed
 */
function xkit_get_theme_option_font( $field_name, $type = '', $default = '' ){
	if ( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {
		$value = get_field( $field_name, 'options' );

		$value = apply_filters( 'xkit_get_theme_option_font', $value, $field_name, $type );

		if( xkit_value_exists( $value ) ){
			if( $type && isset( $value[$type] ) ){
				return $value[$type];
			} else {
				return $value;
			}
		} else {
			return $default;
		}
	} else {
		return $default;
	}
}
?>