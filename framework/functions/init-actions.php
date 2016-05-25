<?php
/* ---------------------------------------------------------------------------
 * INIT ACTIONS
 * --------------------------------------------------------------------------- */


/*
 * after_switch_theme | xkit_counter_theme_active()
 *
 * Hook counter theme active
 */
function xkit_counter_theme_active() {
	$counter = (int) get_option( XKIT_THEME_NAME . '_counter_theme_active', 0 );

	update_option( XKIT_THEME_NAME . '_counter_theme_active', ++$counter );

	do_action( 'xkit_counter_theme_active', $counter );
}
add_action( 'after_switch_theme', 'xkit_counter_theme_active' );


/*
 * after_setup_theme | xkit_framework_active()
 *
 * Hook framework active
 */
function xkit_framework_active() {
	if( !xkit_is_ajax() ){
		$check = (bool) get_option( XKIT_THEME_NAME . '_framework_active', false );

		if( !$check ){
			update_option( XKIT_THEME_NAME . '_framework_active', true );

			do_action( 'xkit_framework_active' );
		}
	}
}
add_action( 'after_setup_theme', 'xkit_framework_active' );


/*
 * after_setup_theme | xkit_create_fs()
 *
 * Create FileSystem
 */
function xkit_create_fs() {
	load_template( ABSPATH . 'wp-admin/includes/file.php', true );
	$fs_method = get_filesystem_method( array(), '', false );

	if( $fs_method == 'direct' ){
		WP_Filesystem();
	}
}
add_action( 'after_setup_theme', 'xkit_create_fs' );



/*
 * admin_bar_menu | xkit_toolbar_theme_debug()
 *
 * Status mode XKIT_THEME_DEBUG in toolbar
 */
function xkit_toolbar_theme_debug( $wp_admin_bar ) {
	if( XKIT_THEME_DEBUG && is_admin() ){
		$args = array(
			'id'    => 'theme_debug',
			'title' => '<span class="ab-icon dashicons-hammer"></span> Theme Debug',
			'href'  => '',
			'meta'  => array( 'class' => 'theme-debug' )
		);
		$wp_admin_bar->add_node( $args );
	}
}
add_action( 'admin_bar_menu', 'xkit_toolbar_theme_debug', 9999 );


/*
 * admin_menu | xkit_shortcodes_ultimate_delete_menu()
 *
 * Remove item in menu by plugin Shortcodes Ultimate
 */
function xkit_shortcodes_ultimate_delete_menu() {
	global $menu;

	$key_search = @key( @array_filter( $menu, function( $innerArray ){
		return in_array( 'shortcodes-ultimate', $innerArray );
	}) );

	if( $key_search ){
		unset( $menu[$key_search] );
	}
}
add_action( 'admin_menu', 'xkit_shortcodes_ultimate_delete_menu' );


/*
 * the_time, get_the_time, etc | xkit_declension_russian_time()
 *
 * Declension russian time
 */
function xkit_declension_russian_time( $date = '' ) {

	// Check locale
	$current_locale = get_locale();
	if( $current_locale != 'ru_RU' ){
		return $date;
	}

	// Replace date
	if ( substr_count( $date , '---' ) > 0 ){
		return str_replace( '---', '', $date );
	}
	$replace = json_decode( '{"\u042f\u043d\u0432\u0430\u0440\u044c":"\u042f\u043d\u0432\u0430\u0440\u044f","\u0424\u0435\u0432\u0440\u0430\u043b\u044c":"\u0424\u0435\u0432\u0440\u0430\u043b\u044f","\u041c\u0430\u0440\u0442":"\u041c\u0430\u0440\u0442\u0430","\u0410\u043f\u0440\u0435\u043b\u044c":"\u0410\u043f\u0440\u0435\u043b\u044f","\u041c\u0430\u0439":"\u041c\u0430\u044f","\u0418\u044e\u043d\u044c":"\u0418\u044e\u043d\u044f","\u0418\u044e\u043b\u044c":"\u0418\u044e\u043b\u044f","\u0410\u0432\u0433\u0443\u0441\u0442":"\u0410\u0432\u0433\u0443\u0441\u0442\u0430","\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c":"\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044f","\u041e\u043a\u0442\u044f\u0431\u0440\u044c":"\u041e\u043a\u0442\u044f\u0431\u0440\u044f","\u041d\u043e\u044f\u0431\u0440\u044c":"\u041d\u043e\u044f\u0431\u0440\u044f","\u0414\u0435\u043a\u0430\u0431\u0440\u044c":"\u0414\u0435\u043a\u0430\u0431\u0440\u044f","January":"\u042f\u043d\u0432\u0430\u0440\u044f","February":"\u0424\u0435\u0432\u0440\u0430\u043b\u044f","March":"\u041c\u0430\u0440\u0442\u0430","April":"\u0410\u043f\u0440\u0435\u043b\u044f","May":"\u041c\u0430\u044f","June":"\u0418\u044e\u043d\u044f","July":"\u0418\u044e\u043b\u044f","August":"\u0410\u0432\u0433\u0443\u0441\u0442\u0430","September":"\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044f","October":"\u041e\u043a\u0442\u044f\u0431\u0440\u044f","November":"\u041d\u043e\u044f\u0431\u0440\u044f","December":"\u0414\u0435\u043a\u0430\u0431\u0440\u044f","Sunday":"\u0412\u043e\u0441\u043a\u0440\u0435\u0441\u0435\u043d\u044c\u0435","Monday":"\u041f\u043e\u043d\u0435\u0434\u0435\u043b\u044c\u043d\u0438\u043a","Tuesday":"\u0412\u0442\u043e\u0440\u043d\u0438\u043a","Wednesday":"\u0421\u0440\u0435\u0434\u0430","Thursday":"\u0427\u0435\u0442\u0432\u0435\u0440\u0433","Friday":"\u041f\u044f\u0442\u043d\u0438\u0446\u0430","Saturday":"\u0421\u0443\u0431\u0431\u043e\u0442\u0430","Sun":"\u0412\u043e\u0441\u043a\u0440\u0435\u0441\u0435\u043d\u044c\u0435","Mon":"\u041f\u043e\u043d\u0435\u0434\u0435\u043b\u044c\u043d\u0438\u043a","Tue":"\u0412\u0442\u043e\u0440\u043d\u0438\u043a","Wed":"\u0421\u0440\u0435\u0434\u0430","Thu":"\u0427\u0435\u0442\u0432\u0435\u0440\u0433","Fri":"\u041f\u044f\u0442\u043d\u0438\u0446\u0430","Sat":"\u0421\u0443\u0431\u0431\u043e\u0442\u0430","th":"","st":"","nd":"","rd":""}', true );

	return strtr( $date, $replace );
}
add_filter( 'the_time',              'xkit_declension_russian_time' );
add_filter( 'get_the_time',          'xkit_declension_russian_time' );
add_filter( 'the_date',              'xkit_declension_russian_time' );
add_filter( 'get_the_date',          'xkit_declension_russian_time' );
add_filter( 'the_modified_time',     'xkit_declension_russian_time' );
add_filter( 'get_the_modified_date', 'xkit_declension_russian_time' );
add_filter( 'get_post_time',         'xkit_declension_russian_time' );
add_filter( 'get_comment_date',      'xkit_declension_russian_time' );


/* ---------------------------------------------------------------------------
 * INIT ACTIONS BY OPTIONS
 *
 * 1.0 - hook  wp_head                    | xkit_enqueue_dynamic_styles()
 * 2.0 - hook  xkit_framework_active      | xkit_change_prefix_shortcodes()
 * 3.0 - hook  wp_head                    | xkit_theme_styles_custom()
 * 4.0 - hook  wp_footer                  | xkit_theme_scripts_custom()
 * 5.0 - hook  xkit_theme_top             | xkit_theme_hook_top()
 * 6.0 - hook  xkit_theme_content_before  | xkit_theme_hook_content_before()
 * 7.0 - hook  xkit_theme_content_after   | xkit_theme_hook_content_after()
 * 8.0 - hook  xkit_theme_bottom          | xkit_theme_hook_bottom()
 * --------------------------------------------------------------------------- */


/*
 * wp_head | xkit_enqueue_dynamic_styles()
 *
 * Enqueue dynamic-styles
 */
function xkit_enqueue_dynamic_styles( $path_files ){
	$path_files = apply_filters( 'xkit_dynamic_styles_path', '' );

	if( !is_array( $path_files ) && empty( $path_files ) ){
		return;
	}

	foreach( $path_files as $path_file => $handle ){

		if( !is_string( $handle ) ){
			continue;
		}

		if( file_exists( $path_file ) ){
			ob_start();
			load_template( $path_file, true );
			$dynamic_styles = ob_get_clean();

			if( function_exists( 'xkit_minify_css' ) ){
				$dynamic_styles = xkit_minify_css( $dynamic_styles );
			}

			wp_add_inline_style( $handle, $dynamic_styles );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'xkit_enqueue_dynamic_styles', 1000 );


/*
 * xkit_framework_active | xkit_change_prefix_shortcodes()
 *
 * Change prefix for Shortcodes Ultimate
 */
function xkit_change_prefix_shortcodes(){
	update_option( 'su_option_prefix', 'su_', true );
}
add_action( 'xkit_framework_active', 'xkit_change_prefix_shortcodes' );


/*
 * wp_head | xkit_theme_styles_custom()
 *
 * Theme Options > Custom CSS
 */
function xkit_theme_styles_custom(){
	if( $custom_css = xkit_get_theme_option( 'custom_css' ) ){

		echo '<style>' . "\n";
			echo xkit_minify_css( $custom_css ) ."\n";
		echo '</style>' . "\n";
	}
}
add_action( 'wp_head', 'xkit_theme_styles_custom' );


/*
 * wp_footer | xkit_theme_scripts_custom()
 *
 * Theme Options > Custom JS
 */
function xkit_theme_scripts_custom(){
	if( $custom_js = xkit_get_theme_option( 'custom_js' ) ){

		echo '<script>' . "\n";
			echo '//<![CDATA[' . "\n";
				echo xkit_minify_js( $custom_js ) . "\n";
			echo '//]]>' . "\n";
		echo '</script>' . "\n";
	}
}
add_action( 'wp_footer', 'xkit_theme_scripts_custom', 100 );


/*
 * theme_top | xkit_theme_hook_top()
 *
 * Theme Options > Hook | Top
 */
function xkit_theme_hook_top(){
	echo '<!-- theme_hook_top -->';
		echo do_shortcode( xkit_get_theme_option( 'hook_top' ) );
	echo '<!-- theme_hook_top -->';
}
add_action( 'xkit_theme_top', 'xkit_theme_hook_top' );


/*
 * xkit_theme_content_before | xkit_theme_hook_content_before()
 *
 * Theme Options > Content before
 */
function xkit_theme_hook_content_before(){
	echo '<!-- theme_hook_content_before -->';
		echo do_shortcode( xkit_get_theme_option( 'hook_content_before' ) );
	echo '<!-- theme_hook_content_before -->';
}
add_action( 'xkit_theme_content_before', 'xkit_theme_hook_content_before' );


/*
 * xkit_theme_content_after | xkit_theme_hook_content_after()
 *
 * Theme Options > Hook | Content after
 */
function xkit_theme_hook_content_after(){
	echo '<!-- theme_hook_content_after -->';
		echo do_shortcode( xkit_get_theme_option( 'hook_content_after' ) );
	echo '<!-- theme_hook_content_after -->';
}
add_action( 'xkit_theme_content_after', 'xkit_theme_hook_content_after' );


/*
 * xkit_theme_bottom | xkit_theme_hook_bottom()
 *
 * Theme Options > Hook | Bottom
 */
function xkit_theme_hook_bottom(){
	echo '<!-- theme_hook_bottom -->';
	echo do_shortcode( xkit_get_theme_option( 'hook_bottom' ) );
	echo '<!-- theme_hook_bottom -->';
}
add_action( 'xkit_theme_bottom', 'xkit_theme_hook_bottom' );
?>