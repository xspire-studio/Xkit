<?php
	/*
	 * Logotype
	 */
	function xkit_logotype_controller( $logo_color = 'var-color-main', $desc_color = 'var-color-main', $logo_font = 'var-font-title', $desc_font = 'var-font-title' ) {
		$logotype = xkit_get_theme_option( 'logo_type', 'icon' );
		
		if ( $logotype ) {
			?>
			<div class="logo-wrap">
				<a href="<?php echo esc_url( get_home_url() ); ?>" class="logo-link <?php esc_attr_e( $logo_color . ' ' . $logo_font ); ?>">
					<?php
						// Logo Image|Icon
						if ( $logotype == 'icon' ) {
							$logo_icon = xkit_get_theme_option( 'logo_icon', false );
							
							if( $logo_icon ) {
								$logo_icon_size = xkit_get_theme_option( 'logo_icon_size', 52 );
								?>
									<i style="font-size: <?php echo esc_attr( $logo_icon_size ); ?>px;" class="<?php echo esc_attr( $logo_icon ); ?>"></i>
								<?php
							}
						} elseif ( $logotype == 'image' ) {
							$logo_image = xkit_get_theme_option( 'logo_image' );

							if ( $logo_image ) {
								
								// Standart Image
								$logo_image_width = xkit_get_theme_option( 'logo_image_width', 52 );
								$logo_image_height = xkit_get_theme_option( 'logo_image_height', 52 );
								?>
									<div class="logo-image"></div>

									<style type="text/css">
										.logo-image{
											background: url('<?php echo esc_url( $logo_image ); ?>') center center no-repeat;
											background-size: <?php echo esc_attr( $logo_image_width ); ?>px, <?php echo esc_attr( $logo_image_height ); ?>px;
											width: <?php echo esc_attr( $logo_image_width ); ?>px;
											height: <?php echo esc_attr( $logo_image_height ); ?>px;
										}
									</style>
								<?php
								
								// Retina Image
								$logo_retina_image = xkit_get_theme_option( 'logo_retina_image' );
								if ( $logo_retina_image ) {
									?>
										<style type="text/css">
											@media only screen and (min--moz-device-pixel-ratio: 1.5){
												.logo-image{
													background: url('<?php echo esc_url( $logo_retina_image ); ?>') center center no-repeat !important;
												}
											}
										</style>
									<?php
								}
							}
						}

						// Logo Title
						if ( xkit_get_theme_option( 'display_logo_title', true ) ) {
							echo wp_kses_post( xkit_get_theme_option( 'logo_title', 'Xkit' ) );
						}

						// Logo Sub Title
						if ( xkit_get_theme_option( 'display_logo_subtitle', true ) ) {
							?>
								<div class="logo-description <?php esc_attr_e( $desc_color . ' ' . $desc_font ); ?>"><?php echo wp_kses_post( xkit_get_theme_option( 'logo_subtitle', 'wordpress theme' ) ); ?></div>
							<?php
						}
					?>
				</a>
			</div>
			<?php 
		}
	}
	xkit_add_controller( 'logotype', 'xkit_logotype_controller' );
	
	
	/*
	 * Title
	 */
	function xkit_title_controller( $custom_option_name = '' ) {
		
		// Vars
		$display_title = true;

		// Singular
		if( is_singular() ) {	
		
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( is_front_page() ) {
				$option_name = 'home_title';
			}
			elseif( is_page() ) {
				$option_name = 'page_title';
			}
			elseif( xkit_is_shop() && is_product() ) {
				$option_name = 'single_product_title';
			}
			elseif( is_singular('testimonials') ) {
				$option_name = 'single_testimonials_title';
			}
			elseif( is_singular('portfolio') ) {
				$option_name = 'single_portfolio_title';
			}
			else {
				$option_name = 'post_title';
			}
			
			$display_title = xkit_get_field_setting( 'post_settings', 'post_title', $option_name, $display_title );
		}
		
		// Archives | Blog Page | Shop Archive
		elseif( is_archive() || xkit_is_blog_page() || ( xkit_is_shop() && !is_product() ) ) {
			
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( xkit_is_shop() && !is_product() ) {
				$option_name = 'products_title';
			}
			elseif( get_post_type() == 'portfolio' ) {
				$option_name = 'portfolio_title';
			}
			elseif( get_post_type() == 'testimonials' ) {
				$option_name = 'testimonials_title';
			}
			else {
				$option_name = 'archive_title';
			}
			
			$display_title = xkit_get_field_setting( 'archive_options', 'archive_title', $option_name, $display_title );
		}
		
		// Home Archive
		elseif( is_home() ) {
			$display_title = xkit_get_theme_option( 'home_title', $display_title );
		}
		
		// Output Title
		if( $display_title ) {
			echo xkit_get_page_title(); 
		}		
	}
	xkit_add_controller( 'title', 'xkit_title_controller' );
	
	
	/*
	 * BreadCrumbs
	 */
	function xkit_breadcrumbs_controller( $custom_option_name = '', $separator = '<span class="separator">/</span>', $args = array() ) {
		
		// Vars
		$display_breadcrumbs = true;
		$default = array( 'sep' => $separator );
		$args    = array_merge( $default, $args );

		// Singular
		if( is_singular() ) {
			
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( is_front_page() ) {
				$option_name = 'home_breadcrumbs';
			}
			elseif( is_page() ) {
				$option_name = 'page_breadcrumbs';
			}
			elseif( xkit_is_shop() && is_product() ) {
				$option_name = 'single_product_breadcrumbs';
			}
			elseif( is_singular('testimonials') ) {
				$option_name = 'single_testimonials_breadcrumbs';
			}
			elseif( is_singular('portfolio') ) {
				$option_name = 'single_portfolio_breadcrumbs';
			}
			else {
				$option_name = 'post_breadcrumbs';
			}
			
			$display_breadcrumbs = xkit_get_field_setting( 'post_settings', 'post_breadcrumbs', $option_name, $display_breadcrumbs );
		}
		
		// Search
		elseif( is_search() ) {
			$display_breadcrumbs = xkit_get_theme_option( 'search_breadcrumbs', $display_breadcrumbs );
		}
		
		// Archive | Blog Page | Shop Archive
		elseif( is_archive() || xkit_is_blog_page() || ( xkit_is_shop() && !is_product() ) ) {
			
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( xkit_is_shop() && !is_product() ) {
				$option_name = 'products_breadcrumbs';
			}
			elseif( get_post_type() == 'portfolio' ) {
				$option_name = 'portfolio_breadcrumbs';
			}
			elseif( get_post_type() == 'testimonials' ) {
				$option_name = 'testimonials_breadcrumbs';
			}
			else {
				$option_name = 'archive_breadcrumbs';
			}
			
			$display_breadcrumbs = xkit_get_field_setting( 'archive_options', 'archive_breadcrumbs', $option_name, $display_breadcrumbs );
		}
		
		// Home Archive
		elseif( is_home() ) {
			$display_breadcrumbs = xkit_get_theme_option( 'home_breadcrumbs', $display_breadcrumbs );
		}
		
		// "Blog" Root Page
		if( $display_breadcrumbs && ( xkit_is_blog_page() || is_singular('post') || is_category() ) ) {
			if( get_option( 'show_on_front' ) == 'page' ) {
				$posts_page_id = intval( get_option( 'page_for_posts' ) );
				
				if( xkit_is_blog_page() ) {
					$posts_page_url = '';
				}
				else {
					$posts_page_url = get_permalink( $posts_page_id );
				}
				
				if( $posts_page_id !== 0 ) {
					$args['root_link'] = array(
						'url' 	=> $posts_page_url,
						'title' => get_the_title( $posts_page_id )
					);
				}
			}
		}
		
		// "Woocommerce" Root Page
		elseif( $display_breadcrumbs && xkit_is_shop() ) {
			$args['root_link'] = array(
				'url'	=> get_post_type_archive_link( 'product' ),
				'title' => get_post_type_object( 'product' )
			);
		}
		
		// Output Breadcrumbs
		if( $display_breadcrumbs ) {
			xkit_get_breadcrumbs( $args );
		}
	}
	xkit_add_controller( 'breadcrumbs', 'xkit_breadcrumbs_controller' );
	
	
	/*
	 * Thumbnail
	 */
	function xkit_thumbnail_controller( $image_size = 'full', $post_formats = true ) {
		global $post;
		
		// Check Post
		if( !isset( $post->ID ) || post_password_required() || is_attachment() ) {
			return false;
		}
		
		// Post Format Thumbnail
		if( ! has_post_thumbnail( $post->ID ) && $post_formats == true ) {
			$post_format = get_post_format( $post->ID );
			
			switch( $post_format ) {				
				case 'quote':
					if( $post_quote = xkit_get_field_theme( 'post_quote', '', $post->ID ) ) {
						$author_name = xkit_get_field_theme( 'post_quote_author', '', $post->ID );
						?>
							<div class="post-thumbnail quote-format">
								<h3 <?php xkit_get_schema_markup( 'title', true); ?>><?php echo esc_html( $post_quote ); ?></h3>
								<div class="quote-ref"><?php echo esc_html( $author_name ); ?></div>
							</div>
						<?php
					}
				break;
				
				case 'link':
					if( $post_link = xkit_get_field_theme( 'post_link', '', $post->ID ) ) {
						$post_link_desc = xkit_get_field_theme( 'post_link_desc', '', $post->ID );
						?>
							<div class="post-thumbnail link-format">
								<h3 <?php xkit_get_schema_markup( 'title', true); ?>><?php echo esc_html( $post_link_desc ); ?></h3>
								<a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_link ); ?></a>
							</div>
						<?php
					}
				break;
				
				case 'audio':
					if( $post_audio = xkit_get_field_theme( 'post_audio', '', $post->ID ) ) {
						?>
							<div class="post-thumbnail audio-format">
								<?php print( $post_audio ); ?>
							</div>
						<?php
					}
				break;
				
				case 'video':
					if( $post_video = xkit_get_field_theme( 'post_video', '', $post->ID ) ) {
						?>
							<div class="post-thumbnail video-format">
								<?php print( $post_video ); ?>
							</div>
						<?php
					}
				break;
			}
		}
		
		// Post Default Thumbnail
		else {
			
			// Singular
			if ( is_page() ) {
				xkit_generate_post_thumbnail( array(
					'image_size'   => $image_size,
					'display_link' => 'image'
				), true );
			}
			elseif ( is_singular( 'post' ) ) {
				xkit_generate_post_thumbnail( array(
					'image_size'   => $image_size,
					'display_link' => 'image',
					'gallery_images_count' => 1
				), true );
			}
			elseif ( is_singular( 'testimonials' ) ) {
				xkit_generate_post_thumbnail( array(
					'image_size'   => $image_size
				), true );
			}
			elseif ( is_single() ) {
				xkit_generate_post_thumbnail( array(
					'image_size'   => $image_size
				), true );
			}
			
			
			// Archives | Blog Page | Home | Search
			elseif ( is_archive() || xkit_is_blog_page() || is_home() || is_search() ) {
				
				// Portfolio
				if( get_post_type() == 'portfolio' ) {
					xkit_generate_post_thumbnail( array(
						'image_size'   => $image_size,
						'no_photo'     => array( 'background' => '247, 247, 247')
					), true );
				}
				
				// Testimonials
				elseif( get_post_type() == 'testimonials' ) {					
					xkit_generate_post_thumbnail( array(
						'image_size'   => $image_size
					), true );
				}
				
				// All other
				else {
					xkit_generate_post_thumbnail( array(
						'image_size'   		=> $image_size,
						'parse_first_image' => true,
						'parse_embed_thumb' => true,
						'no_photo'          => array( 'background' => '247, 247, 247' ),
						'display_link'      => 'post'
					), true );
				}
			}
			
			// Shop
			elseif( xkit_is_shop() && !is_product() ) {
				xkit_generate_post_thumbnail( array(
					'image_size'   => $image_size,
					'no_photo'     => array( 'background' => '247, 247, 247')
				), true );
			}
		}
	}
	xkit_add_controller( 'thumbnail', 'xkit_thumbnail_controller' );
	
	
	/*
	 * Archive Description
	 */
	function xkit_archive_description_controller() {
		if( is_tax() || is_category() || is_tag() ) {
			$term_description = term_description();
			
			if( $term_description ) {
				?>
					<div class="archive-description"><?php echo wp_kses_post( $term_description ); ?></div>
				<?php
			}
		}
	}
	xkit_add_controller( 'archive_description', 'xkit_archive_description_controller' );

	
	/*
	 * Post Date
	 */
	function xkit_post_date_controller() {
		$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		printf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date(),
			esc_attr( get_the_modified_date( 'c' ) ),
			get_the_modified_date()
		);
	}
	xkit_add_controller( 'post_date', 'xkit_post_date_controller' );

	
	/*
	 * Post Count Comments
	 */
	function xkit_post_count_comments_controller() {
		$comments_count = wp_count_comments();
		printf( _n( '1 Comment', '%1$s Comments', $comments_count->approved, 'xkit' ), number_format_i18n( $comments_count->approved ) );
	}
	xkit_add_controller( 'post_count_comments', 'xkit_post_count_comments_controller' );
	
	
	/*
	 * Pagination
	 */
	function xkit_pagination_controller( $custom_option_name = '' ) {
		// Vars
		$pagination_type = 'load_more';
		
		// Search
		if( is_search() ) {
			$pagination_type = xkit_get_theme_option( 'search_pagination', $pagination_type );
		}
		
		// Archives | Blog Page | Shop Archive
		elseif( is_archive() || xkit_is_blog_page() || ( xkit_is_shop() && !is_product() ) ) {
			
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( xkit_is_shop() && !is_product() ) {
				$option_name = 'products_pagination';
			}
			elseif( get_post_type() == 'portfolio' ) {
				$option_name = 'portfolio_pagination';
			}
			elseif( get_post_type() == 'testimonials' ) {
				$option_name = 'testimonials_pagination';
			}
			else {
				$option_name = 'archive_pagination';
			}
			
			$pagination_type = xkit_get_field_setting( 'archive_options', 'archive_pagination', $option_name, $pagination_type );
		}
		
		// Home Archive
		elseif( is_home() ) {
			$pagination_type = xkit_get_theme_option( 'home_pagination', $pagination_type );
		}
		
		// Get Pagination		
		switch ( $pagination_type ) {
			case 'load_more':
				global $wp_query;
				$next_posts_page = next_posts( $wp_query->max_num_pages, false );
				
				if( $next_posts_page ) {
					?>
						<div class="ajax-pagination" data-next-page="<?php echo $next_posts_page; ?>">
							<button class="button load-more-btn">Load More</button>
						</div>
					<?php
				}
			break;
			
			case 'infinite':
				global $wp_query;
				$next_posts_page = next_posts( $wp_query->max_num_pages, false );
				
				if( $next_posts_page ) {
					?>
						<div class="ajax-pagination" data-next-page="<?php echo $next_posts_page; ?>">
							<img src="<?php echo get_template_directory() . '/images/pagination-loader.gif'; ?>" />
						</div>
					<?php
				}
			break;
			
			case 'standart':
			
			break;
			
			case 'default':
			
			break;
		}
		
		?>
		
		<?php
	}
	xkit_add_controller( 'pagination', 'xkit_pagination_controller' );
	
	
	/*
	 * Post Above Banner
	 */
	function xkit_above_banner_controller() {
		if ( xkit_get_field_setting( 'post_settings', 'post_above_banner', 'post_above_banner', false ) ) { 
			?>
			<section class="post-banner above-banner"><?php echo xkit_get_field_setting( 'post_settings', 'post_above_banner_code', 'post_above_banner_code', '' ); ?></section>
			<?php 
		}
	}
	xkit_add_controller( 'post_above_banner', 'xkit_above_banner_controller' );
	
	
	/*
	 * Post Below Banner
	 */
	function xkit_below_banner_controller() {
		if ( xkit_get_field_setting( 'post_settings', 'post_below_banner', 'post_below_banner', false ) ) { 
			?>
			<section class="post-banner below-banner"><?php echo xkit_get_field_setting( 'post_settings', 'post_below_banner_code', 'post_below_banner_code', '' ); ?></section>
			<?php
		}
	}
	xkit_add_controller( 'post_below_banner', 'xkit_below_banner_controller' );
	
	
	/*
	 * Post Meta
	 */
	function xkit_post_meta_controller() {
		if ( xkit_get_field_setting( 'post_settings', 'post_meta_section', 'post_meta_section', true ) ) { 
			?>
				<!-- Post Info -->
				<div class="entry-info">
					<span class="info-item entry-author">
						<?php esc_html_e( 'Posted by', 'xkit' ); ?>
						<span <?php xkit_get_schema_markup( 'author', true ); ?>><?php the_author_posts_link(); ?></span>
					</span>
					
					<span class="info-item entry-date">
						<?php esc_html_e( 'Posted on', 'xkit' ); ?>
						<?php xkit_do_controller( 'post_date' ); ?>
					</span>

					<?php 
						if ( 'post' === get_post_type() ) { 
							?>
							<span class="info-item entry-cats">
								<?php esc_html_e( 'Categories', 'xkit' ); ?>
								<?php echo get_the_category_list( ', ' ); ?>
							</span>
							
							<span class="info-item entry-tags">
								<?php esc_html_e( 'Tags', 'xkit' ); ?>
								<?php echo get_the_tag_list( '', ', ' ); ?>
							</span>
							<?php
						}
					?>
					
					<span class="info-item entry-comments">
						<?php xkit_do_controller( 'post_count_comments' ); ?>
					</span>
				</div>
			<?php
		}
	}
	xkit_add_controller( 'post_meta', 'xkit_post_meta_controller' );
	
	
	/*
	 * Post Rating
	 */
	function xkit_post_rating_controller() {
		if ( xkit_get_field_setting( 'post_settings', 'post_like', 'post_like', false ) ) {
			$post_like_style = xkit_get_field_setting( 'post_settings', 'post_like_style', 'post_like_style', 'heart' );
			?>
				<div class="post-rating">
					<?php
						if ( $post_like_style == 'ratting' ) {
							xkit_the_rating( 'auto', false, true );
						} else {
							xkit_the_like( 'auto', $post_like_style, true );
						}
					?>
				</div>
			<?php
		}
	}
	xkit_add_controller( 'post_rating', 'xkit_post_rating_controller' );
	
	
	/*
	 * Link Pages
	 */
	function xkit_post_link_pages_controller( $args = array() ) {
		$default_args = array(
			'before'           => '<div class="pagination"><span class="title">' . esc_html__( 'Pages:', 'xkit' ) . '</span>',
			'after'            => '</div>',
			'pagelink'         => '<span class="page">%</span>',
			'echo'             => 1,
		);
		
		if( is_array( $args ) ) {
			$links_args = array_merge( $default_args, $args );
		}
		
		wp_link_pages( $links_args );
	}
	xkit_add_controller( 'post_link_pages', 'xkit_post_link_pages_controller' );
	
	
	/*
	 * Post Share
	 */
	function xkit_post_share_controller( $display_title = true ) {
		$social_list = xkit_get_field_setting( 'post_settings', 'post_social', 'post_social', array() );
		
		if( is_array( $social_list ) && !empty( $social_list ) ) {
			?>
				<div class="post-share">
					<?php 
						if( $display_title ) {
							?>
								<div class="box-title"><?php echo esc_html__( 'Please share this', 'xkit' ); ?></div>
							<?php
						}
					?>
					<?php echo do_shortcode( '[social_share template="default" providers="' . implode( ',', $social_list ) . '"]' ); ?>
				</div>
			<?php
		}
	}
	xkit_add_controller( 'post_share', 'xkit_post_share_controller' );
	
	
	/*
	 * Post Tags
	 */
	function xkit_post_tags_controller( $display_title = true ) {
		
		if( xkit_get_field_setting( 'post_settings', 'post_tagcloud', 'post_tagcloud', false ) ) {
			?>
				<div class="post-tags">
					<?php 
						if( $display_title ) {
							?>
								<div class="box-title"><?php esc_html_e( 'Tags', 'xkit' ); ?></div>
							<?php
						}
						echo get_the_tag_list( '', ', ' );
					?>
				</div>
			<?php
		}
	}
	xkit_add_controller( 'post_tags', 'xkit_post_tags_controller' );
	
	
	/*
	 * Post Author Box
	 */
	function xkit_post_autor_box_controller() {
		if ( xkit_get_field_setting( 'post_settings', 'post_author', 'post_author', false ) ) {
			get_template_part('includes/templates/post-author-box');
		}
	}
	xkit_add_controller( 'post_autor_box', 'xkit_post_autor_box_controller' );
	
	
	/*
	 * Post Author Box
	 */
	function xkit_post_comments_controller() {
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
	xkit_add_controller( 'post_comments', 'xkit_post_comments_controller' );