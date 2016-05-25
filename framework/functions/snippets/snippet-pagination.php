<?php
/**
 * Posts Pagination
 *
 * @package Xkit
 * @subpackage Snippet Related Posts
 *
 * @version: 1.0
 *
 * 1.0 - function xkit_pagination_generate_html();
 * 2.0 - function xkit_get_pagination_args();
 * 3.0 - function xkit_pagination_get_single();
 * 4.0 - function xkit_the_pagination();
 */


/**
 * Generate html tags
 *
 * @param  string $tag
 * @return string HTML
 */
function xkit_pagination_generate_html( $tag ) {
	static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

	$args = func_get_args();

	$tag = array_shift( $args );

	if ( is_array( $args[0] ) ) {
		$closing = $tag;
		$attributes = array_shift( $args );
		foreach ( $attributes as $key => $value ) {
			if ( false === $value ){
				continue;
			}

			if ( true === $value ){
				$value = $key;
			}

			$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
	} else {
		list( $closing ) = explode( ' ', $tag, 2 );
	}

	if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
		return "<{$tag} />";
	}

	$content = implode( '', $args );

	return "<{$tag}>{$content}</{$closing}>";
}


/**
 * Get pagination vars for query
 *
 * @param  array  $query
 * @param  string $type
 * @return array
 */
function xkit_get_pagination_args( $query, $type ) {
	switch( $type ) {
		case 'users':
			// WP_User_Query
			$posts_per_page = $query->query_vars['number'];
			$paged          = max( 1, floor( $query->query_vars['offset'] / $posts_per_page ) + 1 );
			$total_pages    = max( 1, ceil( $query->total_users / $posts_per_page ) );
			break;
		default:
			// WP_Query
			$posts_per_page = intval( $query->get( 'posts_per_page' ) );
			$paged          = max( 1, absint( $query->get( 'paged' ) ) );
			$total_pages    = max( 1, absint( $query->max_num_pages ) );
			break;
	}

	return array( $posts_per_page, $paged, $total_pages );
}


/**
 * Get pagination link
 *
 * @param  int    $page
 * @param  string $raw_text
 * @param  array  $attr
 * @param  string $format
 * @return string HTML
 */
function xkit_pagination_get_single( $page, $raw_text, $attr, $format = '%PAGE_NUMBER%' ) {
	if ( empty( $raw_text ) ){
		return '';
	}

	$text = str_replace( $format, number_format_i18n( $page ), $raw_text );

	$attr['href'] = get_pagenum_link( $page );

	return xkit_pagination_generate_html( 'a', $attr, $text );
}


/**
 * Get page pagination
 *
 * @param  array  $args
 * @return string HTML
 */
function xkit_the_pagination( $args = array() ) {

	/* Default Pagination */
	global $post;

	$default_args = wp_parse_args( $args, array(
		'before'        => '<div class="pagination-wrap">',
		'after'         => '</div>',                                                     // Show navigation even if there's only one page. To enable set 1
		'pages_text'    => esc_html__( 'Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'xkit' ), // Available variables - %CURRENT_PAGE%, %TOTAL_PAGES%
		'first_text'    => esc_html__( '&#171; First', 'xkit' ),                          // Available variables - %TOTAL_PAGES%
		'prev_text'     => esc_html__( 'Previous', 'xkit' ),  
		'current_text'  => esc_html__( '%PAGE_NUMBER%', 'xkit' ),                        // Available variables - %PAGE_NUMBER%
		'page_text'     => esc_html__( '%PAGE_NUMBER%', 'xkit' ),                        // Available variables - %PAGE_NUMBER%
		'next_text'     => esc_html__( 'Next', 'xkit' ),
		'dotleft_text'  => esc_html__( '...', 'xkit' ),
		'dotright_text' => esc_html__( '...', 'xkit' ),
		'last_text'     => esc_html__( 'Last &#187;', 'xkit' ),                          // Available variables - %TOTAL_PAGES%
		'num_pages'                    => 7,
		'num_larger_page_numbers'      => 3,                                             // Larger page numbers are in addition to the normal page numbers. Example: Pages 1, 2, 3, 10, 20, 30, 40, 50. Enter 0 to disable.
		'larger_page_numbers_multiple' => 10,                                            // For example, if multiple is 5, it will show: 5, 10, 15, 20, 25
		'always_show'   => 0,
		'query'         => $GLOBALS['wp_query'],
		'post_type'     => '',
		'echo'          => true
	) );

	$args = wp_parse_args( $args, $default_args );

	extract( $args, EXTR_SKIP );

	/* WP Pagenavi compatibility */
	if( function_exists('wp_pagenavi') ) {
		wp_pagenavi( array(
			'before' 		=> $before,
			'after'			=> $after,
			'wrapper_tag' 	=> 'div',
			'wrapper_class' => 'pagination',
			'options' 		=> array(),
			'query' 		=> $query,
			'type' 			=> 'posts',
			'echo' 			=> true
		) );

		return;
	}

	/* Set post_type */
	if( !$post_type ) {
		if( !empty( $post ) ){
			$post_type = get_post_type( $post );
		} else {
			$post_type = 'post';
		}
	}

	list( $posts_per_page, $paged, $total_pages ) = xkit_get_pagination_args( $query, $post_type );

	if ( 1 == $total_pages && !$always_show ) {
		return;
	}

	/* Set navigation data */
	$pages_to_show 			= absint( $num_pages );
	$larger_page_to_show 	= absint( $num_larger_page_numbers );
	$larger_page_multiple 	= absint( $larger_page_numbers_multiple );
	$pages_to_show_minus_1 	= $pages_to_show - 1;
	$half_page_start 		= floor( $pages_to_show_minus_1 / 2 );
	$half_page_end 			= ceil( $pages_to_show_minus_1 / 2 );
	$start_page 			= $paged - $half_page_start;

	if ( $start_page <= 0 ) {
		$start_page = 1;
	}

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
		$end_page   = $start_page + $pages_to_show_minus_1;
	}

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page   = $total_pages;
	}

	if ( $start_page < 1 ) {
		$start_page = 1;
	}

	$out = '';

	/* Text */
	if ( !empty( $pages_text ) ) {
		$pages_text = str_replace(
			array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
			array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ), 
			$pages_text
		);
		$out .= '<span class="pages">' . $pages_text . '</span>';
	}

	if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
		// First
		$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $first_text );
		$out .= xkit_pagination_get_single( 1, $first_text, array(
			'class' => 'first'
		), '%TOTAL_PAGES%' );
	}

	// Previous
	if ( $paged > 1 && !empty( $prev_text ) ) {
		$out .= xkit_pagination_get_single( $paged - 1, $prev_text, array(
			'class' => 'prev-link',
			'rel'   => 'prev'
		) );
	}

	if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
		if ( !empty( $dotleft_text ) ){
			$out .= '<span class="extend">' . $dotleft_text . '</span>';
		}
	}

	// Smaller pages
	$larger_pages_array = array();
	if ( $larger_page_multiple ){
		for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple ) {
			$larger_pages_array[] = $i;
		}
	}

	$larger_page_start = 0;
	foreach ( $larger_pages_array as $larger_page ) {
		if ( $larger_page < ( $start_page - $half_page_start ) && $larger_page_start < $larger_page_to_show ) {
			$out .= xkit_pagination_get_single( $larger_page, $page_text, array(
				'class' => 'smaller page',
			) );
			$larger_page_start++;
		}
	}

	if ( $larger_page_start ) {
		$out .= '<span class="extend">' . $dotleft_text .'</span>';
	}

	// Page numbers
	$timeline = 'smaller';
	foreach ( range( $start_page, $end_page ) as $i ) {
		if ( $i == $paged && !empty( $current_text ) ) {
			$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $current_text );
			$out .= '<span class="current-page">' . $current_page_text . '</span>';
			$timeline = 'larger';
		} else {
			$out .= xkit_pagination_get_single( $i, $page_text, array(
				'class' => 'page ' . $timeline,
			) );
		}
	}

	// Large pages
	$larger_page_end = 0;
	$larger_page_out = '';
	foreach ( $larger_pages_array as $larger_page ) {
		if ( $larger_page > ( $end_page + $half_page_end ) && $larger_page_end < $larger_page_to_show ) {
			$larger_page_out .= xkit_pagination_get_single( $larger_page, $page_text, array(
				'class' => 'larger page',
			) );
			$larger_page_end++;
		}
	}

	if ( $larger_page_out ) {
		$out .= '<span class="extend">' . $dotright_text . '</span>';
	}

	$out .= $larger_page_out;

	if ( $end_page < $total_pages ) {
		if ( !empty( $dotright_text ) ) {
			$out .= '<span class="extend">' . $dotright_text . '</span>';
		}
	}

	// Next
	if ( $paged < $total_pages && !empty( $next_text ) ) {
		$out .= xkit_pagination_get_single( $paged + 1, $next_text, array(
			'class' => 'next-link',
			'rel'	=> 'next'
		) );
	}

	if ( $end_page < $total_pages ) {
		// Last
		$out .= xkit_pagination_get_single( $total_pages, $last_text, array(
			'class' => 'last',
		), '%TOTAL_PAGES%' );
	}


	// Out
	$out = $before . "<div class='pagination'>\n$out\n</div>" . $after;

	$out = apply_filters( 'xkit_post_navigation', $out );

	if ( $echo ) {
		return print( $out );
	}

	return $out;
}