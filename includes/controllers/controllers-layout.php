<?php	
	/*
	 * Site Layout
	 */
	function xkit_site_layout_controller() {
		echo xkit_get_field_setting( 'layout_options', 'layout_type', 'site_layout', 'wide' );
	}
	xkit_add_controller( 'site_layout_class', 'xkit_site_layout_controller' );
	
	
	/*
	 * Posts Layout Class
	 */
	function xkit_posts_layout_class_controller() {
		
		// Archive | Blog Page | Shop Archive | Search
		if( is_archive() || is_home() || ( xkit_is_shop() && !is_product() ) || is_search() ) {
			
			if( is_home() && !xkit_is_blog_page() ) {
				$option_name = 'home_posts_layout';
			}
			elseif( is_search() ) {
				$option_name = 'archive_posts_layout';
			}			
			elseif( get_post_type() == 'portfolio' ) {
				$option_name = 'portfolio_posts_layout';
			}
			elseif( get_post_type() == 'testimonials' ) {
				$option_name = 'testimonials_posts_layout';
			}
			elseif( xkit_is_shop() && !is_product() ) {
				$option_name = 'products_posts_layout';
			}
			else {
				$option_name = 'archive_posts_layout';
			}
			
			echo xkit_get_field_setting( 'archive_options', 'archive_posts_layout', $option_name, 'grid_3' );
		}
		
		// Home Archive
		elseif( is_home() ) {
			xkit_the_theme_option( 'home_posts_layout', 'grid_3' );
		}
	}
	xkit_add_controller( 'posts_layout_class', 'xkit_posts_layout_class_controller' );
	
	
	/*
	 * Posts Sub Layout Class
	 */
	function xkit_posts_sub_layout_class_controller() {
		
		// Archive | Blog Page | Home
		if( is_archive() || is_home() || is_search() ) {			
			if( in_array( xkit_get_controller( 'posts_layout_class' ), array( 'grid_2', 'grid_3', 'grid_4' ) ) ) {				
			
				if( is_home() && !xkit_is_blog_page() ) {
					$option_name = 'home_posts_sub_layout';
				}
				elseif( is_search() ) {
					$option_name = 'archive_posts_sub_layout';
				}
				elseif( get_post_type() == 'testimonials' ) {
					$option_name = 'testimonials_posts_sub_layout';
				}
				else {
					$option_name = 'archive_posts_sub_layout';
				}
				
				echo xkit_get_field_setting( 'archive_options', 'archive_posts_sub_layout', $option_name, 'standart' );
			}
		}
	}
	xkit_add_controller( 'posts_sub_layout_class', 'xkit_posts_sub_layout_class_controller' );
	
	
	/*
	 * Content Class
	 */
	function xkit_content_class_controller() {
		
		// Vars
		$classes = array();
		
		// Sidebar Type Class
		$sidebar_type = xkit_get_controller( 'sidebar_type' );
		
		if( $sidebar_type ) {
			$classes[] = $sidebar_type . '-sidebar';
		}
		
		// Posts Layout Class
		$posts_layout = xkit_get_controller( 'posts_layout_class' );
		if( $posts_layout ) {
			$classes[] = $posts_layout;
		}
		
		// Posts Layout Class
		$posts_sub_layout = xkit_get_controller( 'posts_sub_layout_class' );
		if( $posts_sub_layout ) {
			$classes[] = $posts_sub_layout;
		}
		
		// Columns Class
		if( $sidebar_type == 'two' ) {
			$classes[] = 'col-xs-12 col-md-6';
		}
		elseif( $sidebar_type == 'without' ) {
			$classes[] = 'col-xs-12';
		}
		else {
			$classes[] = 'col-xs-12 col-md-8 col-lg-9';
		}		
		
		// Echo classes
		$classes = apply_filters( 'xkit_content_class', $classes );
		
		echo implode( $classes, ' ' );
	}
	xkit_add_controller( 'content_class', 'xkit_content_class_controller' );


	/*
	 * Post Content Template
	 */
	function xkit_post_content_template_controller() {
		
		// Testimonials Content Template
		if( get_post_type() == 'testimonials' ) {
			$posts_layout = xkit_get_controller( 'posts_layout_class' );
			
			switch ( $posts_layout ) {
				case 'list': echo 'testimonials-content-list'; break;
				default: // Grid
					$posts_sub_layout = xkit_get_controller( 'posts_sub_layout_class' );				
					$template_name = $posts_sub_layout ? 'testimonials-content-grid-' . $posts_sub_layout : 'testimonials-content-grid-standart';
					echo esc_attr( $template_name );
					break;
			}
		}
		
		// Portfolio Content Template
		elseif( get_post_type() == 'portfolio' ) {
			$posts_layout = xkit_get_controller( 'posts_layout_class' );
			
			/*
			
			!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!			
			
			*/
			
		}
		
		// Posts Content Template
		else {
			$posts_layout = xkit_get_controller( 'posts_layout_class' );
			
			switch ( $posts_layout ) {
				case 'list':   echo 'post-content-list'; break;
				case 'full':   echo 'post-content-full'; break;
				default: // Grid
					$posts_sub_layout = xkit_get_controller( 'posts_sub_layout_class' );
					$template_name = $posts_sub_layout ? 'post-content-grid-' . $posts_sub_layout : 'post-content-grid-standart';
					echo esc_attr( $template_name );
					break;
			}
		}
	}
	xkit_add_controller( 'post_content_template', 'xkit_post_content_template_controller' );


	/*
	 * Post Content Class
	 */
	function xkit_post_content_class_controller() {
		$posts_layout = xkit_get_controller( 'posts_layout_class' );
		
		switch ( $posts_layout ) {
			case 'list':   echo 'col-xs-12'; break;
			case 'full':   echo 'col-xs-12'; break;
			case 'grid_2': echo 'col-xs-12 col-sm-6'; break;
			case 'grid_3': echo 'col-xs-12 col-sm-6 col-md-4'; break;
			case 'grid_4': echo 'col-xs-12 col-sm-6 col-md-3'; break;
			default:       echo 'col-xs-12 col-sm-6';
		}
	}
	xkit_add_controller( 'post_content_class', 'xkit_post_content_class_controller' );