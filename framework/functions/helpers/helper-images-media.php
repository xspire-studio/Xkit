<?php
/**
 * Images functions helper
 *
 * @package Xkit
 * @subpackage Helper images-media
 * @version: 1.0
 *
 * 1.0  - function xkit_media_parse_first_image()
 * 2.0  - function xkit_media_parse_attachid_from_content()
 * 3.0  - function xkit_media_get_first_image_url()
 * 4.0  - function xkit_media_remove_first_image()
 * 5.0  - function xkit_media_get_first_gallery_image()
 * 6.0  - function xkit_media_get_all_images()
 * 7.0  - function xkit_media_wrap_all_images()
 * 8.0  - function xkit_media_remove_all_images()
 * 9.0  - function xkit_media_clear_all_images()
 * 10.0 - function xkit_media_get_attached_images()
 * 11.0 - function xkit_media_get_first_attached_image()
 * 12.0 - function xkit_media_get_attachment_id_by_url()
 * 13.0 - function xkit_media_create_wp_embed_player()
 * 14.0 - function xkit_media_get_embed_video()
 * 15.0 - function xkit_media_embed_thumbnail()
 * 16.0 - function xkit_media_parse_embed_from_content()
 * 17.0 - function xkit_get_image_sizes()
 * 18.0 - hook 	   img_caption_shortcode  |  xkit_media_fix_image_margins()
 */



/*
 * Get first post image. Used in filters.
 * 
 * @param  string  $content
 * @return string  Parsed image HTML
 */
function xkit_media_parse_first_image( $content = null ) {
	if ( !$content ) {
		$content = get_the_content();
	}
	preg_match( '~(<img[^>]+>)~sim', trim( $content ), $matches );

	return isset( $matches[1] ) ? $matches[1] : false;
}


/*
 * Parse first wp-image from content
 * 
 * @param  string  $content
 * @return string  Parsed image HTML
 */
function xkit_media_parse_attachid_from_content( $content = null ) {
	if ( !$content ) {
		$content = get_the_content();
	}

	preg_match( '/(<img.*?)wp-image-(.*?[^"|^s])?(".*?>)/si', trim( $content ), $matches );

	return isset( $matches[2] ) ? $matches[2] : false;
}


/*
 * Get post image meta
 * 
 * @param  int     $post_id
 * @return string  First image url from post
 */
function xkit_media_get_first_image_url( $post_id = null ) {
	global $post;
	$image_url = false;

	/* Set post id */
	if( !$post_id && isset( $post->ID ) ) {
		$post_id = $post->ID;
	}
	if( !$post_id ) {
		return false;
	}

	/* Get post content */
	$post_content = get_post( $post_id );
	$post_content = $post_content->post_content;
	$post_content = apply_filters( 'the_content', $post_content );
	$post_content = str_replace( ']]>', ']]&gt;', $post_content );

	/* Featured image */
	if ( function_exists( 'get_post_thumbnail_id' ) ) {
		$attachment_id = get_post_thumbnail_id( $post_id );

		if ( !empty( $attachment_id ) ) { 
			$image_url = wp_get_attachment_url( $attachment_id, false );
		}
	}

	/* Get first wp_image from content */
	if ( !$image_url ) {
		$attachid_from_content = intval( xkit_media_parse_attachid_from_content( $post_content ) );
		if( $attachid_from_content > 0 ) {
			$image_url = wp_get_attachment_url( $attachid_from_content, false );
		}
	}

	/* Get first attached image to post */
	if ( !$image_url ) {
		$first_attachment = xkit_media_get_first_attached_image( $post_id );

		if ( !empty( $attachment_id ) ) { 
			$image_url = wp_get_attachment_url( $first_attachment, false );
		}
	}


	/* Parse first gallery image */
	if ( !$image_url ) {
		$parsed_image = xkit_media_get_first_gallery_image( $post_id );
		if ( $parsed_image ) {
			preg_match('~src="([^"]+)"~si', $parsed_image, $matches);
			if ( isset( $matches[1] ) ) {
				$image_url = $matches[1];
			}
		}
	}

	/* Parse custom first image from content */
	if ( !$image_url ) {
		$parsed_image = xkit_media_parse_first_image( $post_content );
		if ( $parsed_image ) {
			preg_match('~src="([^"]+)"~si', $parsed_image, $matches);
			if ( isset( $matches[1] ) ) {
				$image_url = $matches[1];
			}
		}
	}

	return $image_url;
}


/*
 * Remove first image from post
 * 
 * @param  string  $content
 * @return string  Filtered content
 */
function xkit_media_remove_first_image( $content = null ) {
	if ( !$content ) {
		$content = get_the_content();
	}
	$content = trim( preg_replace( '~(<a[^>]+>)?\s*(<img[^>]+>)\s*(</a>)?~sim', '', $content, 1) );

	return $content;
}


/*
 * Get first image from post gallery.
 * 
 * @param  int     $post_id
 * @param  mixed   $image_size
 * @return string  Image HTML
 */
function xkit_media_get_first_gallery_image( $post_id = null, $image_size = 'full' ) {
	global $post;
	$image = false;

	/* Set post id */
	if( !$post_id && isset( $post->ID ) ) {
		$post_id = $post->ID;
	}

	/* Get gallery */
	$gallery = get_post_gallery( $post_id, false );

	/* Check gallery */
	if( empty( $gallery['ids'] ) ) {
		return false;
	}

	/* Get image */
	$thumbnail = '';
	$image_thumb = '';
	$images_ids = explode( ',', $gallery['ids'] );
	$image = wp_get_attachment_image( $images_ids[0], $image_size, false, array( 'title' => get_the_title( $images_ids[0] ), 'alt' => get_the_title( $images_ids[0] ) ) );

	return $image;
}


/*
 * Get all post images from content
 *
 * @param  string  $content
 * @return array   All finded images HTML in content
 */
function xkit_media_get_all_images( $content = null ) {
	if ( !$content ) {
		$content = get_the_content();
	}
	preg_match_all( '~(<img[^>]+>)~sim', $content, $matches );

	return $matches[1];
}


/*
 * Wrap all content images
 *
 * @param  string  $content
 * @param  string  $wrap_pattern
 * @return string  Content with wrapped images
 */
function xkit_media_wrap_all_images( $content = null, $wrap_pattern = '<div class="image">$1</div>' ) {
	if ( !$content ) {
		$content = get_the_content();
	}
	$content = preg_replace( '~(<img[^>]+>)~sim', $wrap_pattern, $content );

	return $content;
}


/*
 * Remove all post images.
 * 
 * @param  string  $content
 * @return string  Filtered content without images
 */
function xkit_media_remove_all_images( $content = null ) {
	if ( !$content ) {
		$content = get_the_content();
	}
	$content = trim( preg_replace( '~(<a[^>]+>)?\s*(<img[^>]+>)\s*(</a>)?~sim', '', $content ) );

	return $content;
}


/*
 * Remove links aroung images
 *
 * @param  string  $content
 * @return string  Filtered content
 */
function xkit_media_clear_all_images( $content ) {
	return preg_replace( '~<a[^>]*>(<img[^>]*>)<\/a>~iu', '$1', $content );
}


/*
 * Get all attached images.
 *
 * @param  int     $id
 * @return array   Images post objects
 */
function xkit_media_get_attached_images( $id = null ) {
	if ( !$id ) {
		return false;
	}

	$attrs = array(
		'post_parent' 		=> $id,
		'post_status' 		=> null,
		'post_type' 		=> 'attachment',
		'post_mime_type'	=> 'image',
		'order'				=> 'ASC',
		'numberposts' 		=> -1,
		'orderby' 			=> 'menu_order',
	);

	return get_children( $attrs );
}


/*
 * Get first post image attachment
 *
 * @param  int     $id
 * @return array   Image post object
 */
function xkit_media_get_first_attached_image( $id = null ) {
	if ( !$id ) {
		return false;
	}

	$attrs = array(
		'post_parent' 		=> $id,
		'post_status' 		=> null,
		'post_type' 		=> 'attachment',
		'post_mime_type' 	=> 'image',
		'order' 			=> 'ASC',
		'numberposts' 		=> 1,
		'orderby' 			=> 'menu_order',
	);

	$image = get_children( $attrs );

	/* Check images */
	if ( !count( $image ) ) {
		return false;
	}

	/* Return image */
	$image = array_values( $image );

	return $image[0];
}


/*
 * Get attachment ID by URL
 * 
 * @param  string  $url
 * @return int     Attachment ID
 */
function xkit_media_get_attachment_id_by_url( $url ) {

	$dir = wp_upload_dir();
	$dir = trailingslashit( $dir['baseurl'] );

	if(false === strpos( $url, $dir ) ) {
		return false;
	}

	$file = basename( $url );

	$query = array(
		'post_type' 	=> 'attachment',
		'fields' 		=> 'ids',
		'meta_query' 	=> array(
			array(
				'value' 	=> $file,
				'compare' 	=> 'LIKE',
			)
		)
	);

	$query['meta_query'][0]['key'] = '_wp_attached_file';
	$ids = get_posts( $query );

	foreach( $ids as $id ) {
		if( $url == array_shift( wp_get_attachment_image_src( $id, 'full' ) ) ) {
			return $id;
		}
	}
	wp_reset_postdata();

	$query['meta_query'][0]['key'] = '_wp_attachment_metadata';
	$ids = get_posts( $query );

	foreach( $ids as $id ) {
		$meta = wp_get_attachment_metadata( $id );
		foreach( $meta['sizes'] as $size => $values ) {
			if( $values['file'] == $file && $url == array_shift( wp_get_attachment_image_src( $id, $size ) ) ) {
				return $id;
			}
		}
	}
	wp_reset_postdata();

	return false;
}


/*
 * Create WP Embed
 *
 * Creates the necessary frame structure for available
 * sites using the default WP embed shortcode. If a video
 * address is one of the accepted sites that can use the
 * URL and oembed, aside from Vimeo and Youtube, this function
 * will be called.
 *
 * @return string HTML
 */
function xkit_media_create_wp_embed_player( $media_source = '', $width = 640, $height = 360, $allow_autoplay = 1 ) {
	$wp_embed = new WP_Embed();
	$output = $wp_embed->run_shortcode( '[embed width=' . $width . ' height=' . $height . ']' . $media_source . '[/embed]' );
	return $output;
}


/*
 * Get embed video from url
 * 
 * @param  string  $url
 * @return object
 */
function xkit_media_get_embed_video( $url ) {
	load_template( ABSPATH . WPINC . '/class-oembed.php', true );
	$WP_oEmbed = new WP_oEmbed();
	$provider = $WP_oEmbed->discover( $url );
	$data = $WP_oEmbed->fetch( $provider, $url );

	return $data;
}


/*
 * Get embed thumbnail for media entry
 * 
 * @param  string  $url
 * @param  bool    $echo
 * @return string  Image HTML
 */
function xkit_media_embed_thumbnail( $url = '', $echo = true ) {
	$data = xkit_media_get_embed_video( $url );

	if( !isset( $data->thumbnail_url ) ) {
		return false;
	}

	$embed_thumbnail = '<img src="' . $data->thumbnail_url . '" alt="' . $data->title . ' " />';
	if ( $echo ) {
		return print( $embed_thumbnail );
	}

	return $embed_thumbnail;
}


/*
 * Get embed thumbnail for media entry
 * 
 * @param  string $post_content
 * @param  bool   $return_url
 * @param  array  $shortcode_args
 * @param  array  $custom_tags
 * @return string Embed HTML
 */
function xkit_media_parse_embed_from_content( $post_content = null, $return_url = false, $shortcode_args = array(), $custom_tags = array() ) {
	if( empty( $post_content ) ) {
		return false;
	}

	global $shortcode_tags;

	/* Filter tags */
	if( !empty( $custom_tags ) ) {
		$filter_tags = $custom_tags;
	}
	else {
		$filter_tags = array( 'embed', 'audio', 'video', 'bandcamp', 'blip.tv', 'dailymotion', 'dailymotion-channel', 'digg', 'flickr', 'gist', 'instagram', 'medium', 'mixcloud', 'polldaddy', 'soundcloud', 'ted', 'twitch', 'twitchtv', 'twitter-timeline', 'videopress', 'wpvideo', 'vimeo', 'vine', 'wufoo', 'youtube', 'su_youtube', 'su_youtube_advanced', 'su_vimeo', 'su_screenr', 'su_dailymotion', 'su_audio', 'su_video' );
	}

	/* Create shortcode regex */
	$temp_shortcode_tags = $shortcode_tags;

	$shortcode_tags = array();
	foreach( $filter_tags as $tag ) {
		if( ! empty( $temp_shortcode_tags[ $tag ] ) ) {
			$shortcode_tags[ $tag ] = $temp_shortcode_tags[ $tag ];
		}
	}
	$video_regex = '#' . get_shortcode_regex() . '#i';
	$pattern_array = array( $video_regex );

	$shortcode_tags = $temp_shortcode_tags;

	/* Get the patterns from the embed object */
	if ( ! function_exists( '_wp_oembed_get_object' ) ) {
		include_file( ABSPATH . WPINC . '/class-oembed.php' );
	}
	$oembed = _wp_oembed_get_object();
	$pattern_array = array_merge( $pattern_array, array_keys( $oembed->providers ) );

	/* Create patterns array  */
	$cleared_pattern = array();
	foreach( $pattern_array as $pattern ) {
		preg_match( '/#(.*?)(#|$)/', $pattern, $matches );

		if( !empty( $matches[1] ) ) {
			$cleared_pattern[] = $matches[1];
		}
		else{
			$cleared_pattern[] = $pattern;
		}
	}

	/* Providers fix */
	$key = array_search('http://dai.ly/*', $cleared_pattern);
	if( $key !== false ) {
		$cleared_pattern[ $key ] = 'http://dai.ly/.*';
	}
	$key = array_search('http://blip.tv/*', $cleared_pattern);
	if( $key !== false ) {
		$cleared_pattern[ $key ] = 'http://blip.tv/.*';
	}

	/* Set all in one pattern */
	$pattern = '#(' . implode( ')|(', $cleared_pattern ) . ')#is';

	/* Simplistic parse of content line by line. */
	$lines = explode( "\n", $post_content );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( preg_match( $pattern, $line, $matches ) ) {

			/* Return url */
			if( $return_url == true ) {
				preg_match( '/[a-z]+:\/\/\S+/', $matches[0], $embed_url_matches );

				if( ! empty( $embed_url_matches[0] ) ) {
					return $embed_url_matches[0];
				}
			}

			/* Create embed */
			if ( strpos( $matches[0], '[' ) === 0 ) {
				$ret = do_shortcode( $matches[0] );
			}
			else {
				$ret = wp_oembed_get( $matches[0], $shortcode_args );
			}

			return $ret;
		}
	}
}


/*
 * Get Image sizes
 *
 * @param  string  $content
 * @return array   Size or sizes params
 */
function xkit_get_image_sizes( $size = '' ) {
	global $_wp_additional_image_sizes;

	$sizes = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	/* Create the full array with sizes and crop info */
	foreach( $get_intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
			$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
			$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
		}
		elseif ( isset( $_wp_additional_image_sizes[$_size] ) ) {
			$sizes[ $_size ] = array( 
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop']
			);
		}
	}

	/* Get only 1 size if found */
	if ( $size ) {
		if( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		} else {
			return false;
		}
	}

	return $sizes;
}


/**
 * Get size information for a specific image size.
 *
 * @uses   xkit_get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function xkit_get_image_size( $size ) {
	$sizes = xkit_get_image_sizes();

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}


/**
 * Get the width of a specific image size.
 *
 * @uses   xkit_get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|string $size Width of an image size or false if the size doesn't exist.
 */
function xkit_get_image_size_width( $size ) {
	if ( ! $size = xkit_get_image_size( $size ) ) {
		return false;
	}

	if ( isset( $size['width'] ) ) {
		return $size['width'];
	}

	return false;
}


/**
 * Get the height of a specific image size.
 *
 * @uses   xkit_get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|string $size Height of an image size or false if the size doesn't exist.
 */
function xkit_get_image_size_height( $size ) {
	if ( ! $size = xkit_get_image_size( $size ) ) {
		return false;
	}

	if ( isset( $size['height'] ) ) {
		return $size['height'];
	}

	return false;
}


/*
 * img_caption_shortcode | Fix image margins for captions
 *
 * @param  int     $x
 * @param  array   $attr
 * @param  string  $content
 * @return string  Image HTML
 */
function xkit_media_fix_image_margins( $x = null, $attr, $content ) {
	extract( shortcode_atts( array(
			'id'		=> '',
			'align'		=> 'alignnone',
			'width'		=> '',
			'caption'	=> ''
		), $attr ) );

	if ( 1 > (int) $width || empty( $caption ) ) {
		return $content;
	}

	if ( $id ) {
		$id = 'id="' . $id . '" ';
	}

	return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width:' . $width . 'px" >' . $content . '<p class="wp-caption-text">' . $caption . '</p></div>';
}
add_filter( 'img_caption_shortcode', 'xkit_media_fix_image_margins', 10, 3);