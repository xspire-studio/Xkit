<?php
/* ---------------------------------------------------------------------------
 * INIT PHP
 * --------------------------------------------------------------------------- */


/*
 * SSL | Compatibility
 *
 * @param  boolean $value
 * @return string
 */
if( ! function_exists( 'xkit_theme_ssl' ) )
{
	function xkit_theme_ssl( $echo = false ){
		$ssl = '';
		if( is_ssl() ) $ssl = 's';
		if( $echo ){
			echo esc_attr( $ssl );
		}
		return $ssl;
	}
}


/*
 * Is value exists
 *
 * @param  mixed $value
 * @return boolean
 */
if ( !function_exists( 'xkit_value_exists' ) )
{
	function xkit_value_exists( $value = '' ){

		if( !isset( $value ) ){
			return false;
		}

		if( ( is_array( $value ) ) && empty( $value ) ){
			return false;
		}

		if( ( is_string( $value ) && !is_numeric( $value ) ) && empty( $value ) ){
			return false; 
		}

		return true;
	}
}


/*
 * Replace double slash to single
 *
 * @param  string $str
 * @return string
 */
if ( !function_exists( 'xkit_normalize_slash' ) )
{
	function xkit_normalize_slash( $str ){
		return preg_replace('/([^:])(\/{2,})/', '$1/', $str);
	}
}


/*
 * Is url exist
 *
 * @param  string $url
 * @return bool
 */
if ( !function_exists( 'xkit_is_url_exist' ) )
{
	function xkit_is_url_exist( $url ){
		$code = false;
		$response = wp_remote_head( $url, array('timeout' => 2) );
		if( !is_wp_error( $response ) ){
			$code = intval( $response['response']['code'] );
		}

		if( $code == 200 ){
			return true;
		} else {
			return false;
		}
	}
}


/*
 * Encrypt and decrypt data
 *
 * @param  string $key
 * @param  string $text
 * @return string
 */
if ( !function_exists( 'xkit_encrypt_data' ) )
{
	function xkit_encrypt_data( $key = 'xkit', $text ){
		$encDataBin = mcrypt_encrypt( MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_ECB );
		$encDataStr = bin2hex($encDataBin);
		return $encDataStr;
	}
}
if ( !function_exists( 'xkit_decrypt_data' ) )
{
	function xkit_decrypt_data( $key = 'xkit', $text ){
		$encDataBin = pack( "H*" , $text );
		$secretData = mcrypt_decrypt( MCRYPT_BLOWFISH, $key, $encDataBin, MCRYPT_MODE_ECB );
		return $secretData;
	}
}


/*
 * Get the IP of the user
 */
if ( !function_exists( 'xkit_get_user_ip' ) )
{
	function xkit_is_valid_user_ip( $ip = null ) {
		if( preg_match( "#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip ) ){
			return true;
		}

		return false;
	}

	function xkit_get_user_ip() {
		$ip = false;
		if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
			$ipa[] = trim( strtok( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) );
		}

		if( isset($_SERVER['HTTP_CLIENT_IP'] ) ){
			$ipa[] = $_SERVER['HTTP_CLIENT_IP']; 
		}

		if( isset( $_SERVER['REMOTE_ADDR'] ) ){
			$ipa[] = $_SERVER['REMOTE_ADDR'];
		}

		if( isset( $_SERVER['HTTP_X_REAL_IP'] ) ){
			$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
		}

		// check the ip addresses for validity since priority
		foreach( $ipa as $ips ){
			if( xkit_is_valid_user_ip( $ips ) ){
				$ip = $ips;
				break;
			}
		}
		return $ip;
	}
}


/*
 * Autoload files in the directory
 *
 * @param string $path
 */
if ( !function_exists( 'xkit_autoload_files' ) )
{
	function xkit_autoload_files( $path ){
		if( is_dir( $path ) ){
			$files = scandir( $path );
		} else {
			return false;
		}

		// loop folders
		foreach( $files as $file ) {
			$path_file = $path . "/" . basename( $file );

			if ( file_exists( $path_file ) && $file != "index.php" ){

				if( is_dir( $path_file ) && file_exists( $path_file . "/$file.php" ) ){
					load_template( $path_file . "/$file.php", true );
				}
				elseif( is_file( $path_file ) && preg_match( "/\.php$/i", $path_file )  ){
					load_template( $path_file, true );
				}
			}
		}
	}
}


/*
 * Plural Form
 *
 * @param  int    $n
 * @param  string $forms
 * @return string
 */
if ( !function_exists( 'xkit_plural_form' ) )
{
	function xkit_plural_form( $n, $forms ){
		return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
	}
}


/*
 * Time ago
 *
 * @param  string $time
 * @return string
 */
if ( !function_exists( 'xkit_timing_ago' ) )
{
	function xkit_timing_ago( $time ) {
		$periods = array( esc_html__( 'second', 'xkit' ), esc_html__( 'minute', 'xkit' ), esc_html__( 'hour', 'xkit' ), esc_html__( 'day', 'xkit' ), esc_html__( 'week', 'xkit' ), esc_html__( 'month', 'xkit' ), esc_html__( 'year', 'xkit' ), esc_html__( 'decade', 'xkit' ) );
		$lengths = array( '60', '60', '24', '7', '4.35', '12', '10' );

		$now = time();

		$difference = $now - $time;
		$tense = esc_html__(  'ago', 'xkit' );

		for ( $j = 0; $difference >= $lengths[$j] && $j < count( $lengths ) - 1; $j++ ) {
			$difference/= $lengths[$j];
		}

		$difference = round($difference);

		if ( $difference != 1 ) {
			$periods[$j] .= 's';
		}

		return "$difference $periods[$j] {$tense} ";
	}
}


/*
 * Converts the whole Object into an Array
 *
 * @param  object $object
 * @return string
 */
if ( !function_exists( 'xkit_object_to_array' ) )
{
	function xkit_object_to_array( $object ) {
		if( !is_object( $object ) && !is_array( $object ) ){
			return $object;
		}

		return array_map( 'objectToArray', (array) $object );
	}
}


/*
 * The merger of the two rows and values unique array
 *
 * @param  string $parent_line
 * @param  string $child_line
 * @return array
 */
if ( !function_exists( 'xkit_unique_line_merge' ) )
{
	function xkit_unique_line_merge( $parent_line = '', $child_line = '' ){
		if( $parent_line ){
			$parents_array = (array) explode( ',', $parent_line );
		} else {
			$parents_array = array();
		}

		if( $child_line ){
			$child_array = (array) explode( ',', $child_line );
		} else {
			$child_array = array();
		}

		$array_merge = array_unique( array_merge( $parents_array, $child_array ) );

		if( $array_merge ){
			return implode( ',', $array_merge );
		}
	}
}


/*
 * File contents functions
 */
function xkit_file_load_content() {
	$func = 'file' . '_get' . '_contents';
	return call_user_func_array( $func, func_get_args() );
}
function xkit_file_save_content() {
	$func = 'file' . '_put' . '_contents';
	return call_user_func_array( $func, func_get_args() );
}


/*
 * File stream functions
 */
function xkit_fsopen() {
	$func = 'f' . 'open';
	return call_user_func_array( $func, func_get_args() );
}
function xkit_fswrite() {
	$func = 'f' . 'write';
	return call_user_func_array( $func, func_get_args() );
}
function  xkit_fsread() {
	$func = 'f' . 'read';
	return call_user_func_array( $func, func_get_args() );
}
function xkit_fsclose() {
	$func = 'f' . 'close';
	return call_user_func_array( $func, func_get_args() );
}


/*
 * Base 64 encode functions
 */
function xkit_encode_data64() {
	$func = 'base' . '64' . '_encode';
	return call_user_func_array( $func, func_get_args() );
}
function xkit_decode_data64() {
	$func = 'base' . '64' . '_decode';
	return call_user_func_array( $func, func_get_args() );
}


/*
 * Shortcodes
 */
function xkit_create_shortcode(){
	$func = 'add' . '_shortcode';
	return call_user_func_array( $func, func_get_args() );
}


/*
 * HTTP Requests
 */
function xkit_wp_load_http(){
	$func = 'wp_get' . '_http';
	return call_user_func_array( $func, func_get_args() );
}


/*
 * Converts first character to upper case
 *
 * @param  string $str
 * @param  string $encoding
 * @return string
 */
if ( !function_exists( 'xkit_mb_ucfirst' ) && extension_loaded( 'mbstring' ) )
{
	function xkit_mb_ucfirst( $str, $encoding='UTF-8' ){
		$str = mb_ereg_replace( '^[\ ]+', '', $str );
		$str = mb_strtoupper( mb_substr( $str, 0, 1, $encoding ), $encoding ) . mb_substr( $str, 1, mb_strlen( $str ), $encoding );

		return $str;
	}
}


/*
 * Get the code for youtube videos
 *
 * @param  string $source
 * @return string
 */
if ( !function_exists( 'xkit_get_youtube_code' ) )
{
	function xkit_get_youtube_code( $source ){
		preg_match( '#(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?#x', $source, $video_code );

		return $video_code[1];
	}
}


/*
 * Get the url of the current page
 *
 * @return string
 */
if ( !function_exists( 'xkit_get_current_page_url' ) )
{
	function xkit_get_current_page_url(){
		$pageURL = 'http';
		if ( isset($_SERVER['HTTPS'] ) ) {
			if ( $_SERVER['HTTPS'] == 'on' ) {
				$pageURL.= 's';
			}
		}
		$pageURL.= '://';
		if ( $_SERVER['SERVER_PORT'] != '80' ) {
			$pageURL.= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		}
		else {
			$pageURL.= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}
}


/*
 * Convert hexdec color string to rgb(a) string
 *
 * @param  string  $color
 * @param  boolean $opacity
 * @return string
 */
if ( !function_exists( 'xkit_hex2rgba' ) )
{
	function xkit_hex2rgba( $color, $opacity = false ) {

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if ( empty( $color ) ) return $default;

		//Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr($color, 1);
		}

		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		}
		elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		}
		else {
			return $default;
		}

		$rgb = array_map( 'hexdec', $hex );

		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) $opacity = 1.0;
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		}
		else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

		return $output;
	}
}


/*
 * Generate hex darker color with PHP
 *
 * @param  string  $hex
 * @param  int     $factor
 * @return string
 */
if ( !function_exists( 'xkit_hex_darker' ) ) {
	function xkit_hex_darker( $hex, $factor = 30 ) {
		$new_hex = '';
		if ($hex == '' || $factor == '') {
			return false;
		}

		$hex = str_replace( '#', '', $hex );

		$base['R'] = hexdec( $hex{0} . $hex{1} );
		$base['G'] = hexdec( $hex{2} . $hex{3} );
		$base['B'] = hexdec( $hex{4} . $hex{5} );

		foreach ( $base as $k => $v) {
			$amount = $v / 100;
			$amount = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$new_hex.= $new_hex_component;
		}

		return '#' . $new_hex;
	}
}


/*
 * Find if the current browser is on mobile device
 *
 * @return boolean
 */
if ( !function_exists( 'xkit_is_mobile' ) )
{
	function xkit_is_mobile() {
		if( preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		} else {
			return false;
		}
	}
}


/*
 * HTML Minifier
 *
 * @param  string $input
 * @return string
 */
if ( !function_exists( 'xkit_minify_html' ) )
{
	function xkit_minify_html( $input ) {
		if( trim( $input ) === "" ) { return $input; }
		$input = preg_replace_callback( '#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function( $matches ) {
			return '<' . $matches[1] . preg_replace( '#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2] ) . $matches[3] . '>';
		}, str_replace( "\r", "", $input ) );
		if( strpos( $input, ' style=' ) !== false) {
			$input = preg_replace_callback( '#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function( $matches ) {
				return '<' . $matches[1] . ' style=' . $matches[2] . xkit_minify_css( $matches[3] ) . $matches[2];
			}, $input);
		}
		return preg_replace(
			array(
				// t = text
				// o = tag open
				// c = tag close
				// Keep important white-space(s) after self-closing HTML tag(s)
				'#<(img|input)(>| .*?>)#s',
				// Remove a line break and two or more white-space(s) between tag(s)
				'#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
				'#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
				'#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
				'#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
				'#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
				'#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
				'#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
				'#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
				// Remove HTML comment(s) except IE comment(s)
				'#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
			),
			array(
				'<$1$2</$1>',
				'$1$2$3',
				'$1$2$3',
				'$1$2$3$4$5',
				'$1$2$3$4$5$6$7',
				'$1$2$3',
				'<$1$2',
				'$1 ',
				'$1',
				""
			),
		$input);
	}
}


/*
 * CSS Minifier
 *
 * @param  string $input
 * @return string
 */
if ( !function_exists( 'xkit_minify_css' ) )
{
	function xkit_minify_css( $input ) {
		if( trim( $input ) === "" ) { return $input; }
		return preg_replace(
			array(
				// Remove comment(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
				// Remove unused white-space(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
				// Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
				'#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
				// Replace `:0 0 0 0` with `:0`
				'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
				// Replace `background-position:0` with `background-position:0 0`
				'#(background-position):0(?=[;\}])#si',
				// Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
				'#(?<=[\s:,\-])0+\.(\d+)#s',
				// Minify string value
				'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
				'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
				// Minify HEX color code
				'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
				// Replace `(border|outline):none` with `(border|outline):0`
				'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
				// Remove empty selector(s)
				'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
			),
			array(
				'$1',
				'$1$2$3$4$5$6$7',
				'$1',
				':0',
				'$1:0 0',
				'.$1',
				'$1$3',
				'$1$2$4$5',
				'$1$2$3',
				'$1:0',
				'$1$2'
			),
		$input );
	}
}


/*
 * JavaScript Minifier
 *
 * @param  string $input
 * @return string
 */
if ( !function_exists( 'xkit_minify_js' ) )
{
	function xkit_minify_js( $input ) {
		if( trim( $input ) === "" ) { return $input; }
		return preg_replace(
			array(
				// Remove comment(s)
				'#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
				// Remove white-space(s) outside the string and regex
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
				// Remove the last semicolon
				'#;+\}#',
				// Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
				'#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
				// --ibid. From `foo['bar']` to `foo.bar`
				'#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
			),
			array(
				'$1',
				'$1$2',
				'}',
				'$1$3',
				'$1.$3'
			),
		$input);
	}
}




/* ---------------------------------------------------------------------------
 * INIT WORDPRESS
 * --------------------------------------------------------------------------- */


/*
 * AJAX check
 */
if ( ! function_exists('xkit_is_ajax') ) {
	function xkit_is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}


/*
 * Get theme options
 *
 * @return array
 */
function xkit_get_theme_options(){
	global $wpdb;

	return $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options 
								WHERE option_name LIKE 'options_%' OR option_name LIKE '_options_%'", "ARRAY_N" );
}


/*
 * Set theme options
 *
 * @param array $options
 */
function xkit_set_theme_options( $options = array() ){
	if( $options ){
		foreach( $options as $option ){
			if( isset( $option[0] ) && isset( $option[1] ) ){
				update_option( $option[0], maybe_unserialize( $option[1] ) );
			}
		}
	}
}


/*
 * Has the post pagebuilder ?
 *
 * @param int $post_id
 * @return bool
 */
function xkit_has_pagebuilder( $post_id ){
	if( get_post_meta( $post_id, 'panels_data', true ) ){
		return true;
	} else {
		return false;
	}
}


/*
 * Get taxonomy link
 *
 * @return string
 */
function xkit_get_taxonomy_link(){
	global $wp_query;

	$term = $wp_query->get_queried_object();

	return get_term_link( $term, $term->taxonomy );
}


/*
 * Get the child categories by parent
 *
 * @param  string $parent_cat
 * @return string
 */
function xkit_get_child_categories( $parent_id ) {
	$child_categories = get_categories( 'child_of=' . $parent_id ); 

	if( $child_categories ){
		foreach ( $child_categories as $category ) {
			$array_categories[] = $category->cat_ID;
		}

		return implode( ',', $array_categories );
	}
}


/*
 * Connect to FileSystem
 *
 * @param  string $url
 * @param  array  $fields
 * @param  string $method
 * @return bool
 */
function xkit_connect_fs( $url, $fields = null, $method = ''  ) {
	global $wp_filesystem, $need_login_fs;

	$need_login_fs = false;

	// Create credentials
	if( false === ( $credentials = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
		return false;
	}

	// Check if credentials are correct or not
	if( !WP_Filesystem( $credentials ) ) {
		request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

		$need_login_fs = true;
		return false;
	}

	return true;
}


/*
 * Convert Dashed
 *
 * @param  string $url
 * @param  array  $fields
 * @param  string $method
 * @return bool
 */
function xkit_convert_dashes( $string, $direction = 'up' ) {
	if( $direction == 'up' ) {
		return str_replace( '_', '-', (string) $string );
	}
	else {
		return str_replace( '-', '_', (string) $string );
	}
}


/*
 * Match arrays
 *
 * @return bool
 */
function xkit_array_match() {
	$args = func_get_args();
	
	foreach ( $args as $ak => $av ) {
		$args[ $ak ] = array_values( $av );
	}
	return call_user_func_array( 'array_merge', $args );
}


/*
 * Is Blog Page
 *
 * @return bool
 */
function xkit_is_blog_page() {
	
	// Vars
	global $wp_query;
	$is_blog_page = false;
	
	// Check Page
	if ( isset( $wp_query ) && (bool) $wp_query->is_posts_page ) {
		$is_blog_page = true;
	}
	
	return $is_blog_page;
}


/*
 * Is Shop
 *
 * @return bool
 */
function xkit_is_shop() {
	
	// Vars
	$is_shop = false;
	
	// Check Shop
	if( class_exists( 'woocommerce' ) && is_woocommerce() ) {
		$is_shop = true;
	}
	
	return $is_shop;
}


/*
 * Add Controller
 *
 * @param  string 	$tag              The name of the filter to hook the $function_to_add callback to.
 * @param  callable $function_to_add  The callback to be run when the filter is applied.
 * @param  int		$priority         (Optional) Used to specify the order in which the functions associated with a particular action are executed.
 * @param  int 		$accepted_args    (Optional) The number of arguments the function accepts. 
 * @return bool
 */
function xkit_add_controller( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	global $xkit_controllers;

	$xkit_controllers[$tag][$priority] = array( 'function' => $function_to_add, 'accepted_args' => $accepted_args );
	
	return true;
}


/*
 * Do Controller
 *
 * @param  string $tag     The name of the filter hook.
 * @param  mixed  $value   The value on which the filters hooked to $tag are applied on.
 * @param  mixed  $var,... (Optional) Additional variables passed to the functions hooked to $tag.
 * @return mixed  HTML
 */
function xkit_do_controller( $tag ) {
	global $xkit_controllers;

	// Check Controller Exists
	if ( !isset( $xkit_controllers[$tag] ) ) {
		return false;
	}
	
	// Sort Priority
	ksort( $xkit_controllers[$tag] );
	
	// Get Funtion Args
	$args = func_get_args();
	
	// Do Controller
	foreach ( (array) $xkit_controllers[$tag] as $xkit_current_controller ) {
		if ( isset( $xkit_current_controller['function'] ) ) {
			call_user_func_array( $xkit_current_controller['function'], array_slice( $args, 1, (int) $xkit_current_controller['accepted_args'] ) );
		}
	}
}


/*
 * Get Controller
 *
 * @param  string $tag     The name of the filter hook.
 * @param  mixed  $value   The value on which the filters hooked to $tag are applied on.
 * @param  mixed  $var,... (Optional) Additional variables passed to the functions hooked to $tag.
 * @return mixed  $value
 */
function xkit_get_controller( $tag ) {
	
	// Get Funtion Args
	$args = func_get_args();
	
	// Do Controller
	ob_start();
		call_user_func_array( 'xkit_do_controller', $args );
	$value = ob_get_clean();
	
	return $value;
}


/*
 * Remove Controller
 *
 * @param  string $tag    	          The controller hook to which the function to be removed is hooked.
 * @param  array  $function_to_remove The name of the function which should be removed.
 * @param  string $priority           The priority of the function
 * @return bool						  Whether the function existed before it was removed
 */
function xkit_remove_controller( $tag, $function_to_remove, $priority = 10 ) {
	global $xkit_controllers;
	
    $r = isset( $xkit_controllers[ $tag ][ $priority ]['function'] );
 
    if ( true === $r && $xkit_controllers[ $tag ][ $priority ]['function'] === $function_to_remove ) {
		unset( $xkit_controllers[ $tag ][ $priority ] );
		
		if ( empty( $xkit_controllers[ $tag ] ) ) {
			unset( $xkit_controllers[ $tag ] );
		}
		
		return true;
    }
 
    return false;
}


/*
 * Get Field Setting
 *
 * @param  string $checker_field_name
 * @param  string $single_field_name
 * @param  string $options_field_name
 * @param  string $default_value
 * @return mixed  $value
 */
function xkit_get_field_setting( $checker_field_name, $single_field_name, $options_field_name, $default_value ) {
	$queried_object  = get_queried_object();
	
	if ( $queried_object && xkit_get_field_theme( $checker_field_name, false, $queried_object ) ) {
		$field_setting = xkit_get_field_theme( $single_field_name, $default_value, $queried_object );
	} else {
		$field_setting = xkit_get_theme_option( $options_field_name, $default_value );
	}
	
	return $field_setting;
}
?>