<?php
/**
 * Functions for working with text
 *
 * @package Xkit
 * @subpackage Helper text
 *
 * 1.0 - function  xkit_esc_attr_meta()
 * 2.0 - function  xkit_request_as_words()
 * 3.0 - function  xkit_str_truncate()
 * 4.0 - function  xkit_get_excerpt()
 * 5.0 - filter    the_content_more_link | xkit_content_more_link() 
 */



/*
 * Escaping for HTML attributes for meta tags
 *
 * @param  string $text
 * @return string
 */
function xkit_esc_attr_meta( $text ){
	return trim( esc_attr( strip_tags( stripslashes( $text ) ) ) );
}


/*
 * User-readable nice words for a given request
 *
 * @param  string $request
 * @return string
 */
function xkit_request_as_words( $request ) {
	$request = htmlspecialchars( $request );
	$request = str_replace( '.html', ' ', $request );
	$request = str_replace( '.htm', ' ', $request );
	$request = str_replace( '.', ' ', $request );
	$request = str_replace( '/', ' ', $request );
	$request = str_replace( '-', ' ', $request );
	$request_a = explode( ' ', $request );
	$request_new = array();
	foreach ( $request_a as $token ) {
		$request_new[] = trim( $token );
	}
	$request = implode( ' ', $request_new );
	return $request;
}


/*
 * Truncates string with specified length
 * 
 * @param  string  $string
 * @param  int     $length
 * @param  string  $etc
 * @param  bool    $break_words
 * @param  bool    $middle
 * @return string
 */
function xkit_str_truncate( $string, $length = 80, $etc = '&#133;', $break_words = false, $middle = false ) {
	if ( $length == 0 ){
		return '';
	}

	if ( mb_strlen( $string ) > $length ) {
		$length -= min( $length, mb_strlen( $etc ) );
		if ( !$break_words && !$middle ) {
			$string = preg_replace( '/\s+?(\S+)?$/', '', mb_substr( $string, 0, $length + 1 ) );
		}
		if( !$middle ) {
			return mb_substr( $string, 0, $length ) . $etc;
		} else {
			return mb_substr( $string, 0, $length/2 ) . $etc . mb_substr( $string, -$length/2 );
		}
	} else {
		return $string;
	}
}


/*
 * Get post excerpt
 * 
 * @param  array   $args
 * @return string  HTML
 */
function xkit_get_excerpt( $args = array() ) {

	global $post;

	$default = array( 
		'post_object'	=> $post,
		'maxchar' 		=> 350,
		'text_wrap'		=> '%s',
		'save_format' 	=> false,
		'more_link' 	=> true,
		'display_dots'	=> true,
		'more_text' 	=> esc_html__( 'Read more', 'xkit' ),
		'echo' 			=> true,
	);

	$args = array_merge( $default, $args );
	extract( $args );

	if( empty( $post_object ) || !is_object( $post_object ) ){
		return false;
	}
	
	/* Get Text */
	$text = $post_object->post_excerpt ? $post_object->post_excerpt : $post_object->post_content;

	/* Tag <!--more--> */
	if( ! $post_object->post_excerpt && strpos( $post_object->post_content, '<!--more-->') ) {
		preg_match ('~(.*?)<!--more-->~s', $text, $match );
		$text = trim( $match[1] );
	}

	/* Clear shortcodes */
	$text = preg_replace ( '~\[[^\]]+\]~', '', $text );

	/* Clear HTML tags */
	$text = strip_tags( $text, $save_format );

	/* Substr text */
	if ( mb_strlen( $text ) > $maxchar ) {
		$text = mb_substr( $text, 0, $maxchar );
		$text = mb_ereg_replace( '/(.*)[\s\D+]/si', '$1', $text ); // remove the last word, it is 99% incomplete
		
		$text .= $display_dots ? ' ...' : ''; // Add Dots
	}

	/* Text Formatting */
	if( $save_format ) {
		$text = apply_filters( 'the_content', $text );
	}

	/* Clear smiles */
	$text = preg_replace( '@\*[a-z0-9-_]{0,15}\*@', '', $text );

	/* Wrap text */
	if( $text_wrap ) {
		$text = sprintf( $text_wrap, $text);
	}

	/* Read More Link */
	if( $more_link ) {
		$text .= ' <a class="read-more" href="' . get_permalink( $post_object->ID ) . '">' . $more_text . '</a>';
	}

	/* Return text */
	if( $echo === true ) {
		return print( $text );
	}

	return $text;
}


/*
 * the_content_more_link | xkit_content_more_link()
 *
 * Replace content more link
 * 
 * @param  array   $args
 * @return string  HTML
 */
function xkit_content_more_link( $more ) {
	return '<a href="' . get_permalink() . '" class="more-link">' . esc_html__( 'Read More', 'xkit' ) . '</a>';
}
add_filter( 'the_content_more_link', 'xkit_content_more_link' );
?>