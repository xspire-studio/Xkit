<?php
/**
 * Generate Page Title
 *
 * @package Xkit
 * @subpackage Helper page-title
 * @version: 1.1
 *
 * 1.0 - function xkit_get_top_level_term()
 * 2.0 - function xkit_get_term_name_for_post()
 * 3.0 - function xkit_get_page_title()
 */



/*
 * Top Lvl Term
 * 
 * @return object
 */
if( !function_exists('xkit_get_top_level_term') ) {
	function xkit_get_top_level_term( $term, $taxonomy ) {
		if( $term->parent == 0 ){
			return $term;
		}
		$parent = get_term( $term->parent, $taxonomy );
		return xkit_get_top_level_term( $parent, $taxonomy );
	}
}


/*
 * Get Term Name for Single Post
 * 
 * @param  int     $post_id
 * @return string
 */
function xkit_get_term_name_for_post( $post_id ) {
	if( intval( $post_id ) <= 0 ) {
		return false;
	}

	$taxonomies   = get_taxonomies( '', 'names' );
	$post_terms   = wp_get_post_terms( $post_id, $taxonomies );
	$post_type    = get_post_type( $post_id );
	$page_title = '';

	/* Get Top Level Term */
	if( !empty( $post_terms ) && is_array( $post_terms ) ) {
		foreach( $post_terms as $post_term ) {
			if( $post_term->taxonomy != 'post_tag' ) {
				$top_lvl_term = xkit_get_top_level_term( $post_term, $post_term->taxonomy );

				break;
			}
		}
		if( is_object( $top_lvl_term ) ) {
			if( !empty( $top_lvl_term->name ) ) {
				$page_title = $top_lvl_term->name;
			}
		}
	}

	/* Page Title */
	if( $post_type == 'page' ) {
		$page_title = get_the_title( $post_id );
	}

	/* Single for Post Type */
	if( empty( $page_title ) && !empty( $post_type ) ) {
		$post_type_object = get_post_type_object( $post_type );

		if( $post_type_object->labels->name ) {
			$page_title = $post_type_object->labels->name;
		}
	}

	return $page_title;
}


/*
 * Get Page Title
 * 
 * @param  array  $set_loc
 * @param  bool   $term_name_for_post
 * @return string
 */
function xkit_get_page_title( $set_loc = array(), $term_name_for_post = false ) {
	$page_title = '';
	$top_lvl_term = '';

	/* Localization */
	$default_loc = array(
		'home'       => esc_html__( 'Home', 'xkit' ),
		'category'   => esc_html__( 'Category Archive for: %s', 'xkit' ),
		'search'     => esc_html__( 'Search Results for: %s', 'xkit' ),
		'author'     => esc_html__( 'Author Archive for: %s', 'xkit' ),
		'tag'        => esc_html__( 'Tag Archives for: %s', 'xkit' ),
		'daily'      => esc_html__( 'Daily Archive for: %s', 'xkit' ),
		'monthly'    => esc_html__( 'Monthly Archive for: %s', 'xkit' ),
		'yearly'     => esc_html__( 'Yearly Archive for: %s', 'xkit' ),
		'attachment' => esc_html__( 'Attachment: %s', 'xkit' ),
		'error_404'  => esc_html__( 'Page Not Found', 'xkit' )
	);

	$loc  = (object) array_merge( $default_loc, $set_loc );

	/* Home */
	if( is_home() || is_front_page() ) {
		$page_title = $loc->home;
	}
	
	/* Error 404 */
	elseif( is_404() ) {
		$page_title = $loc->error_404;
	}

	/* Category Archive */
	elseif( is_category() ) {
		$page_title = sprintf( $loc->category , single_cat_title( '', false ) );
	}

	/* Search Archive */
	elseif( is_search() ) {
		$page_title = sprintf( $loc->search, get_search_query() );
	}

	/* Tag Archive */
	elseif( is_tag() ) {
		$page_title = sprintf( $loc->tag, single_tag_title( '', false ) );
	}

	/* Author Archive */
	elseif( is_author() && $author = get_queried_object() ) {
		$page_title = sprintf( $loc->author, $author->display_name );
	}

	/* Post Type Archive */
	elseif( is_post_type_archive() ) {
		$page_title = post_type_archive_title( '', false );
	}

	/* Tax / Term Archive */
	elseif( is_tax() ) {
		$page_title = single_term_title( '', false );
	}

	/* Date Archives */
	elseif ( is_day() ) {
		$time_format = apply_filters( 'xkit_daily_archive_format', 'd F Y' );
		$page_title = sprintf( $loc->daily, get_the_time( $time_format ) );
	}
	elseif ( is_month() ) {
		$time_format = apply_filters( 'xkit_monthly_archive_format', 'F, Y' );
		remove_filter( 'get_post_time', 'xkit_declension_russian_time' );
		remove_filter( 'get_the_time', 'xkit_declension_russian_time' );
		$page_title = sprintf( $loc->monthly, get_the_time( $time_format ) );
		add_filter( 'get_the_time', 'xkit_declension_russian_time' );
		add_filter( 'get_post_time', 'xkit_declension_russian_time' );
	}
	elseif ( is_year() ) {
		$time_format = apply_filters( 'xkit_yearly_archive_format', 'Y' );
		$page_title = sprintf( $loc->yearly, get_the_time( $time_format ) );
	}

	/* Attachment */
	elseif( is_attachment() ) {
		$attach_id = get_queried_object_id();
		$page_title = sprintf( $loc->attachment, get_the_title( $attach_id ) );
	}

	/* Single Post || Page */
	elseif( is_single() || is_page() ) {
		$post_id = get_queried_object_id();

		if( $term_name_for_post == true ) {
			$page_title = xkit_get_term_name_for_post( $post_id );
		}
		else{
			$page_title = get_the_title( $post_id );
		}
	}
	
	/* Site Title */
	else {
		$page_title = get_bloginfo( 'name', 'display' );
	}
	
	/* Return Page Title */
	$page_title = apply_filters( 'xkit_page_title', $page_title );
	$page_title = esc_html( $page_title );
	
	return $page_title;
}