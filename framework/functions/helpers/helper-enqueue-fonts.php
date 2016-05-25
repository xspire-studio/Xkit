<?php
/**
 * Functions for working with fonts
 *
 * @package Xkit
 * @subpackage Helper enqueue-fonts
 *
 * 1.0 - function  xkit_get_list_safe_fonts()
 * 2.0 - function  xkit_build_safe_font_class()
 * 3.0 - function  xkit_get_array_google_fonts()
 * 4.0 - function  xkit_wp_enqueue_google_font()
 */


/**
 * Get list safe fonts
 *
 * @return array
 */
function xkit_get_list_safe_fonts(){
	return array(
		'Arial',
		'Arial Black',
		'Comic Sans MS',
		'Courier New',
		'Georgia',
		'Impact',
		'Times New Roman',
		'Trebuchet MS',
		'Verdana',
		'Symbol'
	);
}


/*
 * Convert string to a valid font class name.
 *
 * @param  string $class
 * @return string
 */
function xkit_build_safe_font_class( $class ){
	return preg_replace( '/\W+/', '', strtolower( str_replace( " ", "_", strip_tags( $class ) ) ) );
}


/*
 * Get array google fonts
 * array[0] Open Sans:regular,italic,700,700italic&subset=latin-ext,latin
 * array[1] Open Sans:regular,italic,700,700
 * array[2] Open Sans
 * array[3] regular,italic,700,700italic
 * array[4] latin-ext,latin
 *
 * @param int $format
 * return array
 */
function xkit_get_array_google_fonts( $format = -1 ){

	if( ! $wp_google_fonts = wp_cache_get( 'wp_google_fonts' ) ){
		$google_fonts_list = xkit_file_load_content( get_template_directory() . '/framework/assets/fonts-list/google-fonts.min.list' );

		preg_match_all( '/^((.*?):(.*?))&subset=(.*?)$/sim', $google_fonts_list, $wp_google_fonts );

		wp_cache_set( 'wp_google_fonts', $wp_google_fonts );
	}

	if( $format >= 0 ){
		return $wp_google_fonts[$format];
	} else {
		return $wp_google_fonts;
	}
}



/*
 * Add extra google font to a registered stylesheet.
 *
 * @param string      $handle
 * @param array       $deps
 * @param string|bool $ver
 * @param string      $media
 */
function xkit_wp_enqueue_google_font( $handle, $deps = array(), $ver = false, $media = 'all' ){
	global $xkit_queue_google_fonts;

	$font_family        = '';
	$font_variants      = '';
	$font_subsets       = '';

	$handle = preg_replace( '/\+/im', ' ', $handle );
	$handle = preg_replace( '/&subset=/im', ':', $handle );
	$handle = preg_replace( '/^.*?family=/im', '', $handle );

	if( preg_match( '/^((.*?):(.*?)):(.*?)$/sim', $handle, $font_params ) ){
		$font_family        = $font_params[2];
		$font_variants      = $font_params[3];
		$font_subsets       = $font_params[4];
	} elseif( preg_match( '/^((.*?):(.*?))$/sim', $handle, $font_params ) ) {
		$font_family        = $font_params[2];
		$font_variants      = $font_params[3];
	} else {
		$wp_google_fonts = xkit_get_array_google_fonts();

		if( isset( $wp_google_fonts[2] ) ){
			$font_key = array_search( $handle, $wp_google_fonts[2] );
		}

		if( isset( $font_key ) && $font_key ){
			$font_family        = $wp_google_fonts[2][$font_key];
			$font_variants      = $wp_google_fonts[3][$font_key];
			$font_subsets       = $wp_google_fonts[4][$font_key];
		}
	}

	if( isset( $font_family ) && $font_family ){

		// Build google fonts list
		if( $font_variants ){
			$array_variants = (array) explode( ',', $font_variants );

			if( isset( $xkit_queue_google_fonts[$font_family] ) && is_array( $xkit_queue_google_fonts[$font_family] ) ){
				$merge_array_variants = array_unique( array_merge( $array_variants, $xkit_queue_google_fonts[$font_family] ) );
				$merge_array_variants = array_diff( (array) $merge_array_variants, array( '' ) );

				$xkit_queue_google_fonts[$font_family] = $merge_array_variants;
			} else {
				$xkit_queue_google_fonts[$font_family] = $array_variants;
			}
		} else {
			if( !isset( $xkit_queue_google_fonts[$font_family] ) ){
				$xkit_queue_google_fonts[$font_family] = '';
			}
		}

		// Output google fonts
		if( is_array( $xkit_queue_google_fonts ) && $xkit_queue_google_fonts ){

			foreach( $xkit_queue_google_fonts as $family => $variants ){
				$array_family_full[] = $family . ':' . implode( ',', $variants );
			}

			$subset = apply_filters( 'xkit_google_fonts_subset', 'latin,cyrillic' );

			// build url
			$fonts_url = add_query_arg( array(
				'family' => urlencode( implode( '|', $array_family_full ) ),
				'subset' => urlencode( $subset ),
			), '//fonts.googleapis.com/css' );

			if( wp_style_is( 'xkit-theme-google-fonts' ) ){
				wp_dequeue_style( 'xkit-theme-google-fonts' );
				wp_deregister_style( 'xkit-theme-google-fonts' );
			}

			wp_enqueue_style( 'xkit-theme-google-fonts', $fonts_url, $deps, $ver, $media );
		}
	} else {
		wp_enqueue_style( xkit_build_safe_font_class( $handle ), $handle, $deps, $ver, $media );
	}
}