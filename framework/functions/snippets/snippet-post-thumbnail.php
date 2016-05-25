<?php
/**
 * Post thumbnail
 *
 * @package Xkit
 * @subpackage Snippet Post Thumbnail
 *
 * @version: 1.0.4
 *
 * 1.0 - method get_post_thumbnail()
 * 3.0 - method get_gallery_thumbnail()
 * 4.0 - method get_audio_thumbnail()
 * 5.0 - filter wp_audio_shortcode   | audio_visibility_fix()
 * 6.0 - method get_embed_thumbnail()
 * 7.0 - filter xkit_thumbnail_video_html | add_video_thumbnail_controls()
 * 8.0 - method parse_first_image()
 * 9.0 - method get_no_photo_image()
 * 10.0 - method add_image_link()
 * 11.0 - function xkit_generate_post_thumbnail()
 */



/*
 * Class to control breadcrumbs display.
 *
 */
if( class_exists( 'Xkit_Post_Thumbnail_Class' ) ) {
	return false;
}

class Xkit_Post_Thumbnail_Class {

	/*
	 * Settings array
	 *
	 * @var array
	 */
	public $params = array();


	/*
	 * Attachment ID for current thumb
	 *
	 * @var array
	 */
	public $attachment_id = false;


	/*
	 * Image size data
	 *
	 * @var array
	 */
	public $image_size_data = array();


	/*
	 * Constructor. Set up cacheable values and settings.
	 */
	public function __construct() {
	}


	/*
	 * Get post thumbnail
	 *
	 * @param  array  $args  Post thumbnail settings
	 * @return string Thumbnail HTML
	 */
	public function get_post_thumbnail( $args = array() ) {
		global $post;

		/* Create params */
		$this->params = array(
			'post_id' 	 	  	=> '',
			'image_size' 	  	=> 'full',
			'parse_first_image' => false,
			'parse_embed_thumb'	=> false,
			'no_photo' 	  		=> false,
			'display_link' 	  	=> false,
			'display_audio' 	=> false,
			'gallery_images_count' => 5
		);

		
		/* Attacment id */
		$this->attachment_id = false;

		
		/* Set params */
		if ( !empty( $post->ID ) ) {
			$this->params['post_id'] = $post->ID;
		}
		$this->params = array_merge( $this->params, $args );
		
		
		/* Check Post ID */
		if( empty( $this->params['post_id'] ) ) {
			return false;
		}

		
		/* Set image size data */
		if( is_array( $this->params['image_size'] ) ) {
			if( intval( $this->params['image_size'][0] ) > 0 && intval( $this->params['image_size'][1] ) > 0 ) {
				$size_data = array(
					'width'		=> $this->params['image_size'][0],
					'height'	=> $this->params['image_size'][1]
				);
			}
		}
		elseif( is_string( $this->params['image_size'] ) ) {
			$size_data = xkit_get_image_sizes( $this->params['image_size'] );
		}

		if( !empty( $size_data ) ) {
			$this->image_size_data = $size_data;
		}

		
		/* Thumbnail image */
			/* Get post thumbnail */
			if( has_post_thumbnail( $this->params['post_id'] ) ) {
				$thumbnail = get_the_post_thumbnail( $this->params['post_id'], $this->params['image_size'] );
				$this->attachment_id = get_post_thumbnail_id( $this->params['post_id'] );
			}

			/* Get post formats image */
			if( empty( $thumbnail ) ) {
				$post_format = get_post_format();
				switch( $post_format ) {
					case 'gallery':
						$thumbnail = $this->get_gallery_thumbnail();
						break;
					case 'audio':
						$thumbnail = $this->get_audio_thumbnail();
						break;
					case 'video':
						$thumbnail = $this->get_embed_thumbnail();
						break;
				}
			}

			/* Parse first image from content */
			if( empty( $thumbnail ) && $this->params['parse_first_image'] == true ) {
				$thumbnail = $this->parse_first_image();
			}

			/* Parse embed from content */
			if( empty( $thumbnail ) ) {
				$thumbnail = $this->get_embed_thumbnail();
			}

			/* No photo image */
			if( empty( $thumbnail ) ) {
				$thumbnail = $this->get_no_photo_image();
			}

		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Thumbnail wrap link */
		$thumbnail = $this->add_image_link( $thumbnail );

		/* Return thumbnail */
		$wrap = apply_filters( 'xkit_generated_thumbnail_wrap', array(
			'before_thumbnail'	=> '<div class="post-thumbnail">',
			'after_thumbnail'	=> '</div>'
		));
		$thumbnail = $wrap['before_thumbnail'] . apply_filters( 'xkit_generated_thumbnail_html', $thumbnail, $this->params['post_id'], $this->params['image_size'] ) . $wrap['after_thumbnail'];

		return $thumbnail;
	}


	/*
	 * Get gallery thumbnail
	 *
	 * @param  int    $count
	 * @return string Gallery image HTML
	 */
	public function get_gallery_thumbnail( $count = 5 ) {
		$gallery = get_post_gallery( $this->params['post_id'], false );

		/* Check gallery */
		if( empty( $gallery['ids'] ) ) {
			return false;
		}

		/* Create gallery */
		$thumbnail = '';
		$image_thumb = '';
		$images_ids = explode( ',', $gallery['ids'] );
		$images_ids = array_slice( $images_ids, 0, intval( $this->params['gallery_images_count'] ) );
		
		foreach( $images_ids as $image_id ) {
			$this->attachment_id = $image_id;
			$image_thumb = wp_get_attachment_image( $image_id, $this->params['image_size'], false );
			$image_thumb = apply_filters( 'xkit_thumbnail_gallery_image', $image_thumb, $image_id, $this->params['image_size'] );

			/* Gallery image wrap link */
			if( $count == 1 ) {
				$thumbnail .= $image_thumb;
			}
			else {
				$thumbnail .= $this->add_image_link( $image_thumb );
			}
		}

		/* Add thumb attachment */
		if( !empty( $images_ids[0] ) ) {
			$this->attachment_id = $images_ids[0];
		}

		/* Disable link on carousel */
		if( $count > 1 ) {
			$this->params['display_link'] = false;
		}

		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Return thumbnail */
		$thumbnail = '<span class="post-format-gallery">' . $thumbnail . '</span>';
		
		return apply_filters( 'xkit_thumbnail_gallery_html', $thumbnail, $this->params['post_id'], $this->params['image_size'] );
	}


	/*
	 * Get audio thumbnail
	 *
	 * @return string Audio frame HTML
	 */
	public function get_audio_thumbnail() {
		$audio = get_attached_media( 'audio', $this->params['post_id'] );
		$audio = array_shift( $audio );

		/* Filter audio (fix) */
		add_filter( 'wp_audio_shortcode', array( &$this, 'audio_visibility_fix' ), 10, 5 );

		if( empty( $audio->guid ) ) {
			return false;
		}

		/* Disable media fallback */
		add_filter( 'wp_mediaelement_fallback', '__return_false' );

		/* Create audio thumbnail */
		$thumbnail = do_shortcode('[audio url="' . $audio->guid . '"]');

		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Return thumbnail */
		$this->params['display_link'] = false;
		$thumbnail = '<span class="post-format-audio">' . $thumbnail . '</span>';
		
		return apply_filters( 'xkit_thumbnail_audio_html', $thumbnail, $this->params['post_id'] );
	}

	/*
	 * wp_audio_shortcode | audio_visibility_fix()
	 *
	 * Fix visibility audio
	 *
	 * @param  string  $html    Audio shortcode HTML output.
	 * @param  array   $atts    Array of audio shortcode attributes.
	 * @param  string  $audio   Audio file.
	 * @param  int     $post_id Post ID.
	 * @param  string  $library Media library used for the audio shortcode.
	 * @return string  HTML
	 */
	public function audio_visibility_fix( $html, $atts, $audio, $post_id, $library ){
		$html = str_replace ( 'visibility: hidden;', '', $html );
		return $html;
	}


	/*
	 * Get embed thumbnail
	 *
	 * @return string Embed frame HTML
	 */
	public function get_embed_thumbnail() {
		$post_content = get_the_content( $this->params['post_id'] );

		if( empty( $post_content ) ) {
			return false;
		}

		/* Generate thumbnail */
		$thumbnail_type = $this->params['parse_embed_thumb'];

		if( $thumbnail_type === 'embed' || $thumbnail_type === true ) {
			$thumbnail = xkit_media_parse_embed_from_content( $post_content, false, $this->image_size_data );

			if( !empty( $thumbnail ) ) {
				$this->params['display_link'] = false;
			}
		}
		elseif( $thumbnail_type === 'thumb' ) {
			$embed_url = xkit_media_parse_embed_from_content( $post_content, true, $this->image_size_data );

			if( !empty( $embed_url ) ) {
				$thumbnail = xkit_media_embed_thumbnail( $embed_url, false );
			}
		}

		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Return thumbnail */
		if( $thumbnail_type === 'thumb' ) {
			add_filter( 'xkit_thumbnail_video_html', array( &$this, 'add_video_thumbnail_controls' ), 10, 4 );
		}
		$thumbnail = '<span class="post-format-video">' . $thumbnail . '</span>';

		return apply_filters( 'xkit_thumbnail_video_html', $thumbnail, $this->params['post_id'], $thumbnail_type );
	}


	/*
	 * xkit_thumbnail_video_html | add_video_thumbnail_controls()
	 *
	 * Video Thumbnail - add control icon
	 *
	 * @param  string $thumbnail
	 * @param  int    $post_id
	 * @param  array  $wrap
	 * @param  string $thumbnail_type
	 * @rerurn string Edited video thumbnail HTML
	 */
	public function add_video_thumbnail_controls( $thumbnail, $post_id, $thumbnail_type ) {
		$thumbnail = '<span class="wrap-video-thumb">' . $thumbnail . '<span class="dashicons dashicons-controls-play"></span></span>';

		return $thumbnail;
	}


	/*
	 * Parse first image
	 *
	 * @return string First image HTML
	 */
	public function parse_first_image() {
		$content = get_the_content( $this->params['post_id'] );
		$thumbnail = '';

		/* Get first wp-image from content */
		$attachid_from_content = intval( xkit_media_parse_attachid_from_content( $content ) );
		if( $attachid_from_content > 0 ) {
			$image_data = wp_get_attachment_image_src( $attachid_from_content, $this->params['image_size'] );

			if( $image_data ) {
				$thumbnail = '<img src="' . $image_data[0] . '" alt="'. get_the_title( $this->params['post_id'] ) .'" width="'. $image_data[1] .'" height="'. $image_data[2] .'" />';

				// Add thumb attachment
				$this->attachment_id = $attachid_from_content;
			}
		}

		/* Parse custom first image from content */
		if( !$thumbnail ) {
			$thumbnail = xkit_media_parse_first_image( $content );
		}

		/* Parse first gallery image */
		if( !$thumbnail ) {
			$thumbnail = $this->get_gallery_thumbnail( $this->params['post_id'], $this->params['image_size'], 1 );
		}

		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Return thumbnail */
		$thumbnail = apply_filters( 'xkit_thumbnail_first_image_html', $thumbnail, $this->params['post_id'], $this->params['image_size'] );

		return $thumbnail;
	}


	/*
	 * Get no photo image
	 *
	 * @return string No image HTML
	 */
	public function get_no_photo_image() {
		$no_photo_image = false;
		$thumbnail = '';

		/* Custom no thumbnail */
		if( empty( $this->params['no_photo'] ) ) {
			return false;
		}
		
		/* HTML Thumbnail */
		if ( filter_var( $this->params['no_photo'], FILTER_VALIDATE_URL ) ) { 
			$thumbnail = '<span class="no-photo"><img src="' . $this->params['no_photo'] . '" alt="no-photo" /></span>';
		}
		elseif( is_string( $this->params['no_photo'] ) ) {
			$thumbnail = '<span class="no-photo">' . $this->params['no_photo'] . '</span>';
		}
		elseif( is_array( $this->params['no_photo'] ) && isset( $this->params['no_photo']['background'] ) ) {

			/* Background Color */
			$bg_color = explode( ',', $this->params['no_photo']['background'] );
			if( count( $bg_color ) < 3 ) {
				$bg_color = array( 0, 0, 0 );
			}

			/* Background Size */
			if( is_string( $this->params['image_size'] ) ) {
				$image_width = intval( xkit_get_image_size_width( $this->params['image_size'] ) );
				$image_height = intval( xkit_get_image_size_height( $this->params['image_size'] ) );
			}
			elseif( is_array( $this->params['image_size'] ) ) {
				$image_width = isset( $this->params['image_size'][0] ) ? intval( $this->params['image_size'][0] ) : 0 ;
				$image_height = isset( $this->params['image_size'][1] ) ? intval( $this->params['image_size'][1] ) : 0 ;
			}
			else{
				$image_width = 0;
				$image_height = 0;
			}

			/* Generate Image */
			ob_start();
			$image = imagecreatetruecolor( $image_width, $image_height );
			$color = imagecolorallocate( $image, trim( $bg_color[0] ), trim( $bg_color[1] ), trim( $bg_color[2] ) );
			imagefill( $image, 0, 0, $color ); 
			imagepng( $image, null, 9 );
			imagedestroy( $image );
			$image_content = ob_get_clean();

			/* Create Src */
			$image_data = xkit_encode_data64( $image_content );
			$src = 'data:image/png;base64,' . $image_data;

			/* Create Thumbnail */
			$thumbnail = '<span class="no-photo"><img src="' . $src . '" alt="no-photo" /></span>';
		}
		
		/* Check thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}
		
		/* Return Thumbnail */
		$thumbnail = apply_filters( 'xkit_thumbnail_no_photo_html', $thumbnail, $this->params['post_id'], $this->params['image_size'] );

		return $thumbnail;
	}


	/*
	 * Wrap image to link
	 *
	 * @param  string $thumbnail HTML
	 * @return string Post thumbnail with link
	 */
	public function add_image_link( $thumbnail = false ) {
		/* Check Thumbnail */
		if( empty( $thumbnail ) ) {
			return false;
		}

		/* Wrap image to link */
		if( $this->params['display_link'] == 'post' || $this->params['display_link'] === true ) {
			$thumbnail = sprintf( '<a href="%1$s" class="post-link">%2$s</a>', get_permalink( $this->params['post_id'] ), $thumbnail );
		}
		elseif( $this->params['display_link'] == 'image' ) {
			$full_thumb_url = wp_get_attachment_image_src( $this->attachment_id, 'full' );

			if( !empty( $full_thumb_url[0] ) ) {
				$thumbnail = sprintf( '<a href="%1$s" class="image-link">%2$s</a>', $full_thumb_url[0], $thumbnail );
			}
		}

		/* Return thumbnail */
		return $thumbnail; 
	}
}


/*
 * Post thumbnail function
 *
 * @param  string $args   Replace default params
 * @param  bool   $out   Return type
 * @return string Post Thumbnail HTML
 */
function xkit_generate_post_thumbnail( $args = array(), $out = false ) {
	$Xkit_Post_Thumbnail_Class = new Xkit_Post_Thumbnail_Class;
	$thumbnail = $Xkit_Post_Thumbnail_Class->get_post_thumbnail( $args );

	/* Out */
	if( $out == true ) {
		return print( $thumbnail );
	}

	return $thumbnail;
}