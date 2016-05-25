<?php
/**
 * SEO Pack Optimizes your WordPress blog for SEO (Search Engine Optimization)
 *
 * @package Xkit
 * @subpackage SEO Pack
 *
 * 1.0 - function xkit_build_seo_title_formatting()
 * 2.0 - function xkit_theme_seo_title()
 * 3.0 - function xkit_theme_seo_meta_tags()
 * 4.0 - hook     wp_footer                         | xkit_theme_google_analytics()
 * 5.0 - hook     add_meta_boxes                    | xkit_seo_meta_tags_meta_box()
 * 6.0 - hook     save_post                         | xkit_seo_meta_tags_meta_box_save()
 * 7.0 - hook     category_add_form_fields          | xkit_category_seo_field_keywords_add()
 * 8.0 - hook     category_edit_form_fields         | xkit_category_seo_field_keywords_edit()
 * 9.0 - hook     created_category, edited_category | xkit_category_seo_field_keywords_save()
 */


if( !defined( 'AIOSEOP_PLUGIN_NAME' ) && !defined( 'WPSEO_FILE' ) ){

	if( xkit_get_theme_option( 'seo_use_built', true ) ){

		/*
		 * Remove wordpress generate title
		 */
		remove_theme_support( 'title-tag' );


		/*
		 * Removing extra characters before output
		 */
		function xkit_aioseop_output( $str ){
			$str = str_replace( array( "\r\n" ), ' ', $str );
			$str = trim( $str );

			return $str;
		}


		/*
		 * This function needed for the formation of the title of the specified pattern
		 *
		 * @param  string $format
		 * @param  string $title
		 * @return string
		 */
		function xkit_build_seo_title_formatting( $format, $title ){
			$build_title = str_replace( 
				array(
					'%page_title%',
					'%post_title%',
					'%category_title%',
					'%archive_title%',
					'%date%',
					'%author%',
					'%tag%',
					'%search%',
				),
				$title,
				$format
			);

			$blog_name = XKIT_BLOG_NAME;
			$blog_description  = XKIT_BLOG_DESCRIPTION;
			$paged = (get_query_var('paged')) ? get_query_var( 'paged' ) : 1;

			$build_title = str_replace( '%blog_title%', $blog_name, $build_title );
			$build_title = str_replace( '%blog_description%', $blog_description, $build_title );
			$build_title = str_replace( '%request_words%', xkit_request_as_words( $_SERVER['REQUEST_URI'] ), $build_title );
			$build_title = str_replace( '%page%', $paged, $build_title );

			return $build_title;
		}


		/*
		 * Build and output seo title
		 */
		function xkit_theme_seo_title( $title ){
			$title_loc = array(
				'home'       => XKIT_BLOG_NAME,
				'category'   => '%s',
				'search'     => '%s',
				'author'     => '%s',
				'tag'        => '%s',
				'daily'      => '%s',
				'monthly'    => '%s',
				'yearly'     => '%s',
				'attachment' => '%s',
			);

			/* title formats */
			if ( is_singular() ) {
				$title_format = esc_attr( xkit_get_theme_option( 'seo_page_title_format' ) );

				if( is_single() ){
					$title_format = esc_attr( xkit_get_theme_option( 'seo_post_title_format' ) );
				}

				if ( is_front_page() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_home_page_title_format' ) );
				}
			} else {
				if( is_front_page() ){
					$title_format = esc_attr( xkit_get_theme_option( 'seo_home_page_title_format' ) );

				} elseif ( is_category() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_category_title_format' ) );

				} elseif ( is_tag() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_tag_title_format' ) );

				} elseif ( is_tax() ) {	
					$title_format = esc_attr( xkit_get_theme_option( 'seo_category_title_format' ) );

				} elseif ( is_search() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_search_title_format' ) );

				} elseif ( is_author() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_author_archive_title_format' ) );

				} elseif ( is_archive() ) {

					if ( is_post_type_archive() ) {
						$title_format = esc_attr( xkit_get_theme_option( 'seo_author_archive_title_format' ) );

					} elseif ( is_day() ) {
						$title_format = esc_attr( xkit_get_theme_option( 'seo_date_archive_title_format' ) );

					} elseif ( is_month() ) {
						$title_format = esc_attr( xkit_get_theme_option( 'seo_date_archive_title_format' ) );

					} elseif ( is_year() ) {
						$title_format = esc_attr( xkit_get_theme_option( 'seo_date_archive_title_format' ) );
					}
				} elseif ( is_404() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_404_title_format' ) );
				} elseif ( is_paged() ) {
					$title_format = esc_attr( xkit_get_theme_option( 'seo_paged_title_format' ) );
				}
			}

			if( !isset( $title_format ) || !$title_format ){
				$title_format = '%page_title%';
			}

			/* title_new */
			$title_new = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );


			if ( is_singular() ) {
				global $post;

				if ( is_front_page() ) {
					$seo_home_title = esc_attr( xkit_get_theme_option( 'seo_home_title' ) );
					if( $seo_home_title ){
						$title_new = $seo_home_title;
					}
				}
				// ---

				if( $post_meta_title = esc_attr( get_post_meta( $post->ID, 'seo_title', true ) ) ){
					$title_new = $post_meta_title;
				}
			} else {
				if( is_front_page() ){
					$seo_home_title = esc_attr( xkit_get_theme_option( 'seo_home_title' ) );
					if( $seo_home_title ){
						$title_new = $seo_home_title;
					}
				}
			}

			if( ( isset( $title_format ) && $title_format ) || ( isset( $title_new ) && $title_new ) ){
				return xkit_build_seo_title_formatting( $title_format, $title_new );
			} else {
				return $title;
			}
		}
		add_filter( 'pre_get_document_title', 'xkit_theme_seo_title', 100 );


		/*
		 * Build and output seo meta tags
		 */
		function xkit_theme_seo_meta_tags(){
			$title_loc = array(
				'home'       => XKIT_BLOG_NAME,
				'category'   => '%s',
				'search'     => '%s',
				'author'     => '%s',
				'tag'        => '%s',
				'daily'      => '%s',
				'monthly'    => '%s',
				'yearly'     => '%s',
				'attachment' => '%s',
			);

			/* meta and og tags */
			$og_locale = get_locale();
			$og_type   = 'article';
			$og_image  = '';

			if ( is_singular() ) {
				global $post;

				$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
				$og_url   = get_permalink();
				if( !xkit_has_pagebuilder( $post->ID ) ){
					$og_desc  = xkit_get_excerpt( array( 
						'maxchar'   => 250, 
						'more_text' => '...', 
						'more_link' => false,
						'echo'      => false ) 
					);
				}
				$og_image = xkit_media_get_first_image_url();

				if ( is_front_page() ) {
					$seo_home_description = esc_attr( xkit_get_theme_option( 'seo_home_description' ) );
					if( $seo_home_description ){
						$meta_description = $seo_home_description;
					}
					$seo_home_keywords = esc_attr( xkit_get_theme_option( 'seo_home_keywords' ) );
					if( $seo_home_keywords ){
						$meta_keywords = $seo_home_keywords;
					}

					$og_home_title = xkit_get_theme_option( 'og_home_title' );
					if( $og_home_title ){
						$og_title = $og_home_title;
					}
					$og_home_description = xkit_get_theme_option( 'og_home_description' );
					if( $og_home_description ){
						$og_desc = $og_home_description;
					}
					$og_home_image = xkit_get_theme_option( 'og_home_image' );
					if( $og_home_image ){
						$og_image = $og_home_image;
					}
					$og_type = 'website';
				}

				$post_meta_description = esc_attr( get_post_meta( $post->ID, 'seo_description', true ) );
				if( $post_meta_description ){
					$meta_description = $post_meta_description;
				} elseif( !isset( $seo_home_description ) || !$seo_home_description ){
					if( !xkit_has_pagebuilder( $post->ID ) ){
						$meta_description = esc_attr( xkit_get_excerpt( array( 
							'maxchar'   => 250, 
							'more_text' => '...', 
							'more_link' => false,
							'echo'      => false ) 
						) );
					}
				}
				if( $post_meta_keywords = esc_attr( get_post_meta( $post->ID, 'seo_keywords', true ) ) ){
					$meta_keywords = $post_meta_keywords;
				}
			} else {
				$meta_description  = get_bloginfo( 'description' );

				$og_title = XKIT_BLOG_NAME;
				$og_desc  = xkit_str_truncate( XKIT_BLOG_DESCRIPTION, 250, '...' );
				$og_url   = esc_url( home_url( '/' ) );

				if( is_front_page() ){
					$seo_home_description = esc_attr( xkit_get_theme_option( 'seo_home_description' ) );
					if( $seo_home_description ){
						$meta_description = $seo_home_description;
					}
					$seo_home_keywords = esc_attr( xkit_get_theme_option( 'seo_home_keywords' ) );
						if( $seo_home_keywords ){
						$meta_keywords = $seo_home_keywords;
					}

					$og_home_title = xkit_get_theme_option( 'og_home_title' );
					if( $og_home_title ){
						$og_title = $og_home_title;
					}
					$og_home_description = xkit_get_theme_option( 'og_home_description' );
					if( $og_home_description ){
						$og_desc = $og_home_description;
					}
					$og_home_image = xkit_get_theme_option( 'og_home_image' );
					if( $og_home_image ){
						$og_image = $og_home_image;
					}
					$og_type = 'website';

				} elseif ( is_category() ) {
					$meta_description  = xkit_str_truncate( xkit_esc_attr_meta( category_description() ), 250, '...' );

					$category_seo_keywords = esc_attr( get_term_meta( get_queried_object()->term_id, 'category_seo_keywords', true ) );
					if( $category_seo_keywords ){
						$meta_keywords = $category_seo_keywords;
					}

					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
					$og_desc  = xkit_str_truncate( xkit_esc_attr_meta( category_description() ), 250, '...' );
					$og_url   = xkit_get_taxonomy_link();

				} elseif ( is_tag() ) {
					$meta_description  = xkit_str_truncate( xkit_esc_attr_meta( tag_description() ), 250, '...' );

					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
					$og_desc  = xkit_str_truncate( xkit_esc_attr_meta( tag_description() ), 250, '...' );
					$og_url   = xkit_get_taxonomy_link();

				} elseif ( is_tax() ) {
					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
					$og_url   = xkit_get_taxonomy_link();

				} elseif ( is_search() ) {
					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
					$og_url   = get_search_link();

				} elseif ( is_author() ) {
					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
					$og_url   = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );

				} elseif ( is_archive() ) {
					if ( is_post_type_archive() ) {
						$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
						$og_url   = get_post_type_archive_link( get_query_var( 'post_type' ) );

					} elseif ( is_day() ) {
						$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
						$og_url   = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );

					} elseif ( is_month() ) {
						$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
						$og_url   = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );

					} elseif ( is_year() ) {
						$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
						$og_url   = get_year_link( get_query_var( 'year' ) );
					}

				} else {
					$og_title = xkit_esc_attr_meta( xkit_get_page_title( $title_loc ) );
				}
			}

			// og image default
			if( !$og_image && $og_default_image = xkit_get_theme_option( 'og_default_image' ) ){
				$og_image = $og_default_image;
			}

			// fix bbpress
			if( function_exists( 'is_bbpress' ) && isset( $post ) && is_bbpress( $post->ID ) ) {
				$meta_description = null;
				$og_desc = null;
			}


			if( isset( $meta_description ) && $meta_description ){
				printf( '<meta name="description" content="%s" />%s', xkit_aioseop_output( $meta_description ), "\n" );
			}
			if( isset( $meta_keywords ) && $meta_keywords ){
				printf( '<meta name="keywords" content="%s" />%s', xkit_aioseop_output( $meta_keywords ), "\n" );
			}


			if( XKIT_BLOG_NAME ){
				printf( '<meta property="og:site_name" content="%s" />%s', xkit_aioseop_output( XKIT_BLOG_NAME ), "\n" );
			}
			if( isset( $og_image ) && $og_image ){
				printf( '<meta property="og:image" content="%s" />%s', xkit_aioseop_output( $og_image ), "\n" );
			}
			if( isset( $og_locale ) && $og_locale ){
				printf( '<meta property="og:locale" content="%s" />%s', xkit_aioseop_output( $og_locale ), "\n" );
			}
			if( isset( $og_title ) && $og_title ){
				printf( '<meta property="og:title" content="%s" />%s', xkit_aioseop_output( $og_title ), "\n" );
			}
			if( isset( $og_url ) && $og_url ){
				printf( '<meta property="og:url" content="%s" />%s', xkit_aioseop_output( $og_url ), "\n" );
			}
			if( isset( $og_type ) && $og_type ){
				printf( '<meta property="og:type" content="%s" />%s', xkit_aioseop_output( $og_type ), "\n" );
			}
			if( isset( $og_desc ) && $og_desc ){
				printf( '<meta property="og:description" content="%s" />%s', xkit_aioseop_output( $og_desc ), "\n" );
			}
		}
		add_action( 'wp_head', 'xkit_theme_seo_meta_tags' );

		/*
		 * wp_footer | xkit_theme_google_analytics()
		 *
		 * Google Analytics
		 */
		function xkit_theme_google_analytics(){
			if( $google_analytics = xkit_get_theme_option( 'seo_google_analytics' ) ){

				print( $google_analytics );
			}
		}
		add_action( 'wp_footer', 'xkit_theme_google_analytics', 100 );


		/* ---------------------------------------------------------------------------
		 * SEO Meta Tags Meta box
		 * --------------------------------------------------------------------------- */


		/*
		 * add_meta_boxes | xkit_seo_meta_tags_meta_box()
		 *
		 * Adding meta tags meta box
		 */
		function xkit_seo_meta_tags_meta_box() {
			$screens = (array) apply_filters( 'xkit_seo_meta_tags_post_types', 'post' );

			foreach ( $screens as $screen ){
				add_meta_box('xkit_seo_meta_tags_meta_box', esc_html__( 'SEO Meta Tags', 'xkit-textdomain' ),
					'xkit_seo_meta_tags_meta_box_callback',
					$screen
				);
			}
		}
		add_action( 'add_meta_boxes', 'xkit_seo_meta_tags_meta_box' );


		/* Callback meta tags by post */
		function xkit_seo_meta_tags_meta_box_callback( $post ) {

			wp_nonce_field( 'seo_meta_tags_meta_box', 'seo_meta_tags_meta_box_nonce' );

			$seo_title = get_post_meta( $post->ID, 'seo_title', true );
			$seo_description = get_post_meta( $post->ID, 'seo_description', true );
			$seo_keywords = get_post_meta( $post->ID, 'seo_keywords', true );
			?>
				<div class="seo-field clearfix">
					<div class="seo-label">
						<label for="seo_title"><?php esc_html_e( 'Title', 'xkit-textdomain' ); ?></label>
					</div>
					<div class="seo-input">
						<input type="text" id="seo_title" name="seo_title" value="<?php echo esc_attr( $seo_title ); ?>" data-counter="length_title" data-max="60" />
						<div class="counter"><input type="text" value="0" name="length_title" readonly> <?php esc_html_e( 'characters. Most search engines use a maximum of 60 chars for the title.', 'xkit-textdomain' ); ?></div>
					</div>
				</div>
				<div class="seo-field clearfix">
					<div class="seo-label">
						<label for="seo_description"><?php esc_html_e( 'Description', 'xkit-textdomain' ); ?></label>
					</div>
					<div class="seo-input">
						<textarea id="seo_description" name="seo_description" rows="6" data-counter="length_description" data-max="160"><?php echo esc_attr( $seo_description ); ?></textarea>
						<div class="counter"><input type="text" value="0" name="length_description" readonly> <?php esc_html_e( 'characters. Most search engines use a maximum of 160 chars for the description.', 'xkit-textdomain' ); ?></div>
					</div>
				</div> 
				<div class="seo-field clearfix">
					<div class="seo-label">
						<label for="seo_keywords"><?php esc_html_e( 'Keywords (comma separated)', 'xkit-textdomain' ); ?></label>
					</div>
					<div class="seo-input">
						<input type="text" id="seo_keywords" name="seo_keywords" value="<?php echo esc_attr( $seo_keywords ); ?>"  />
					</div>
				</div>
				<script>
					jQuery(function($){
						$('#seo_title, #seo_description').on('keyup', function(){
							var Counter = $(this).data('counter');
							var maxCounter = $(this).data('max');

							var lengthChars = $(this).val().length;

							$('input[name="'+Counter+'"]').val( lengthChars );

							if( lengthChars >= maxCounter ){
								$('input[name="'+Counter+'"]').addClass('limit');
							} else{
								$('input[name="'+Counter+'"]').removeClass('limit');
							}
						});
						$('#seo_title, #seo_description').keyup();
					});
				</script>
			<?php
		}


		/*
		 * save_post | xkit_seo_meta_tags_meta_box_save()
		 *
		 * Save meta tags by post
		 */
		function xkit_seo_meta_tags_meta_box_save( $post_id ) {
			if ( isset( $_POST['seo_title'] ) ){
				$signature = sanitize_text_field( $_POST['seo_title'] );

				update_post_meta( $post_id, 'seo_title', $signature );
			}

			if ( isset( $_POST['seo_description'] ) ){
				$signature = sanitize_text_field( $_POST['seo_description'] );

				update_post_meta( $post_id, 'seo_description', $signature );
			}

			if ( isset( $_POST['seo_keywords'] ) ){
				$signature = sanitize_text_field( $_POST['seo_keywords'] );

				update_post_meta( $post_id, 'seo_keywords', $signature );
			}
		}
		add_action( 'save_post', 'xkit_seo_meta_tags_meta_box_save' );


		/* ---------------------------------------------------------------------------
		 * SEO Keywords Field To Category
		 * --------------------------------------------------------------------------- */


		/*
		 * category_add_form_fields | xkit_category_seo_field_keywords_add()
		 *
		 * Add Seo Keywords Field To Category Form
		 */
		function xkit_category_seo_field_keywords_add( $taxonomy ) {
			?>
				<div class="form-field">
					<label for="category_seo_keywords"><?php esc_html_e( 'Keywords (comma separated)', 'xkit-textdomain' ); ?></label>
					<input name="category_seo_keywords" id="category_seo_keywords" type="text" value="" size="40" aria-required="true" />
					<p class="description"><?php esc_html_e( 'The recommended number of words in this field - no more than 10.', 'xkit-textdomain' ); ?></p>
				</div>
			<?php
		}
		add_action( 'category_add_form_fields', 'xkit_category_seo_field_keywords_add', 10 );


		/*
		 * category_edit_form_fields | xkit_category_seo_field_keywords_edit()
		 *
		 * Edit Seo Keywords Field To Category Form
		 */
		function xkit_category_seo_field_keywords_edit( $tag, $taxonomy ) {
			$category_seo_keywords = get_term_meta( $tag->term_id, 'category_seo_keywords', true );
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category_seo_keywords"><?php esc_html_e( 'Keywords (comma separated)', 'xkit-textdomain' ); ?></label></th>
				<td>
					<input type="text" name="category_seo_keywords" id="category_seo_keywords" value="<?php echo esc_attr( $category_seo_keywords ) ? esc_attr( $category_seo_keywords ) : ''; ?>" size="40" aria-required="true" />
					<p class="description"><?php esc_html_e( 'The recommended number of words in this field - no more than 10.', 'xkit-textdomain' ); ?></p>
				</td>
			</tr>
			<?php
		}
		add_action( 'category_edit_form_fields', 'xkit_category_seo_field_keywords_edit', 10, 2 );


		/*
		 * created_category, edited_category | xkit_category_seo_field_keywords_save()
		 *
		 * Save Seo Keywords Field Of Category Form
		 */
		function xkit_category_seo_field_keywords_save( $term_id, $tt_id ) {
			if ( isset( $_POST['category_seo_keywords'] ) ) {
				$category_seo_keywords = sanitize_text_field( $_POST['category_seo_keywords'] );
				
				update_term_meta( $term_id, 'category_seo_keywords', $category_seo_keywords );
			}
		}
		add_action( 'created_category', 'xkit_category_seo_field_keywords_save', 10, 2 ); 
		add_action( 'edited_category', 'xkit_category_seo_field_keywords_save', 10, 2 );

	}
}
?>