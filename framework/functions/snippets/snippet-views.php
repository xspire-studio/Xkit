<?php
/**
 * Post Views
 *
 * @package Xkit
 * @subpackage Snippet Views
 *
 * @version: 1.0
 *
 * 1.0 - function xkit_get_post_views();
 * 2.0 - function xkit_set_post_views();
 * 3.0 - filter   template_redirect          | xkit_process_post_views()
 * 4.0 - filter   manage_posts_columns       | xkit_posts_column_views()
 * 5.0 - hook     manage_posts_custom_column | xkit_posts_custom_column_views()
 */



/*
 * Get views count
 *
 * @param  int  $post_id
 * @return int  Count views
 */
function xkit_get_post_views( $post_id = false ){

	// Check post ID
	global $post;
	if ( !$post_id && is_object( $post ) ) {
		$post_id = $post->ID;
	}

	if( ! $post_id ) {
		return 0;
	}

	// Get count
	$count_key = 'xkit_views_count';
	$count     = intval( get_post_meta( $post_id, $count_key, true ) );

	if( $count == 0 ) {
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, $count );
	}

	return $count;
}


/*
 * Set views count
 *
 * @param  int  $post_id
 * @param  int  $custom_count
 * @return bool Result of operation
 */
function xkit_set_post_views( $post_id, $custom_count = false ) {

	// Check post ID
	global $post;
	if ( !$post_id && is_object( $post ) ) {
		$post_id = $post->ID;
	}

	if( !$post_id ) {
		return;
	}

	// Get current count
	$count_key = 'xkit_views_count';
	$count = intval( get_post_meta( $post_id, $count_key, true ) );

	// Create new count
	if( $custom_count ) {
		$new_count = intval( $custom_count );
	}
	else {
		$new_count = $count + 1;
	}

	// Update Views
	$result = update_post_meta( $post_id, $count_key, $new_count );

	return $result;
}


/*
 * template_redirect | xkit_process_post_views()
 *
 * Process post views
 */
function xkit_process_post_views() {

	$options = array(
		'admin_views'	=> true,
		'cookie' 		=> true
	);

	$options = apply_filters( 'xkit_views_options', $options );

	global $post;
	$current_user = wp_get_current_user();

	if( is_object( $post ) ) {
		$post_id = $post->ID;
	}

	// Check if is post
	if( empty( $post_id ) ) {
		return;
	}

	if( !wp_is_post_revision( $post_id ) ) {
		if( is_single() || is_page() ) {

			// Admin views
			if( $options['admin_views'] === true ) {
				if ( $current_user->has_cap('administrator') ) {
					return;
				}
			}

			// COOKIE Defense
			if( $options['cookie'] == true ){
				if ( !empty( $_COOKIE[ USER_COOKIE . '_views' ] ) ) {
					$views_cookie = $_COOKIE[ USER_COOKIE . '_views' ];
				}

				if ( !empty( $views_cookie ) ) {
					$viewed = array_map( 'intval', explode( ',', $views_cookie ) );
				}
				else {
					$viewed = array();
				}

				if ( !empty( $views_cookie ) && in_array( $post->ID, $viewed ) ) {
					return;
				}

				$viewed[] = $post_id;
				setcookie(
					USER_COOKIE . '_views',
					implode(
						',', $viewed
					),
					time() + 31536000,
					COOKIEPATH,
					COOKIE_DOMAIN,
					false,
					true
				);
			}

			// Set views
			xkit_set_post_views( $post_id );
		}
	}
}
add_action( 'template_redirect', 'xkit_process_post_views', 200 );
remove_action( 'init', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // To keep the count accurate, lets get rid of prefetching


/*
 * manage_posts_columns | xkit_posts_column_views()
 * 
 * @param  array  $posts_columns   An array of column names.
 * @param  string $post_type	   The post type slug.
 * @return array  Posts columns
 */
function xkit_posts_column_views( $posts_columns, $post_type ) {
	$posts_columns['xkit_views_count'] = esc_html__( 'Views', 'xkit' );

	return $posts_columns;
}
add_filter( 'manage_pages_columns', 'xkit_posts_column_views', 5, 2 );
add_filter( 'manage_posts_columns', 'xkit_posts_column_views', 5, 2 );


/*
 * manage_posts_custom_column | xkit_posts_custom_column_views()
 *
 * Posts custom column views
 *
 * @param  string $column_name   The name of the column to display. 
 * @param  string $post_id	     The ID of the current post. Can also be taken from the global $post->ID. 
 * @return string Columns HTML
 */
function xkit_posts_custom_column_views( $column_name, $id ) {
	if( $column_name === 'xkit_views_count' ) {
		echo xkit_get_post_views( $id );
	}
}
add_action( 'manage_posts_custom_column', 'xkit_posts_custom_column_views', 5, 2 );


/*
 * Column for Hierarcical post types
 */
function xkit_posts_hierarchical_column() {
	$all_post_types = get_post_types( '', 'names' );
	foreach ( $all_post_types as $post_type ) {
		if( is_post_type_hierarchical( $post_type ) ) {
			add_action( 'manage_' . $post_type . '_posts_custom_column', 'xkit_posts_custom_column_views', 5, 2 );
		}
	}
}
add_action( 'init', 'xkit_posts_hierarchical_column' );