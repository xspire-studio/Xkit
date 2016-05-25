<?php
	/*
	 * Sidebar
	 */
	function xkit_sidebar_controller( $sidebar_name = 'main' ) {
		
		if ( function_exists( 'xkit_dynamic_sidebar_name' ) ) {
			$sidebar_name = xkit_dynamic_sidebar_name( $sidebar_name );
		}
		
		if ( is_active_sidebar( $sidebar_name ) ) {
			dynamic_sidebar( $sidebar_name );
		}
	}
	xkit_add_controller( 'sidebar', 'xkit_sidebar_controller' );
	
	
	/*
	 * Sidebar Type
	 */
	function xkit_sidebar_type_controller( $custom_option_name = '' ) {

		// Singular
		if( is_singular() ) {
			
			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( is_front_page() ) {
				$option_name = 'home_sidebar';
			}
			elseif( is_page() ) {
				$option_name = 'page_sidebar';
			}
			elseif( xkit_is_shop() && is_product() ) {
				$option_name = 'single_product_sidebar';
			}
			elseif( is_singular('testimonials') ) {
				$option_name = 'single_testimonials_sidebar';
			}
			elseif( is_singular('portfolio') ) {
				$option_name = 'single_portfolio_sidebar';
			}
			else {
				$option_name = 'post_sidebar';
			}
			
			echo xkit_get_field_setting( 'post_settings', 'post_sidebar', $option_name, 'left' );
		}
		
		// Search
		elseif( is_search() ) {
			xkit_the_theme_option( 'search_sidebar', 'left' );
		}
		
		// Archive | Blog Page | Shop Archive
		elseif( is_archive() || xkit_is_blog_page() || ( xkit_is_shop() && !is_product() ) ) {

			if( $custom_option_name ) {
				$option_name = $custom_option_name;
			}
			elseif( xkit_is_shop() && !is_product() ) {
				$option_name = 'products_sidebar';
			}
			elseif( get_post_type() == 'portfolio' ) {
				$option_name = 'portfolio_sidebar';
			}
			elseif( get_post_type() == 'testimonials' ) {
				$option_name = 'testimonials_sidebar';
			}
			else {
				$option_name = 'archive_sidebar';
			}
			
			echo xkit_get_field_setting( 'archive_options', 'archive_sidebar', $option_name, 'left' );
		}
		
		// Home Archive
		elseif( is_home() ) {
			xkit_the_theme_option( 'home_sidebar', 'left' );
		}
	}
	xkit_add_controller( 'sidebar_type', 'xkit_sidebar_type_controller', 10, 2 );
	
	
	/*
	 * Sidebar Class
	 */
	function xkit_sidebar_class_controller() {
		$classes = array();
		
		// Sidebar Type
		$sidebar_type = xkit_get_controller( 'sidebar_type' );
		
		// Columns 
		if( $sidebar_type == 'two' ) {
			$classes[] = 'col-xs-12 col-md-3';
		}
		else {
			$classes[] = 'col-xs-12 col-md-3';
		}
		
		// Echo classes
		$classes = (array) apply_filters( 'xkit_sidebar_class', $classes );
		
		echo implode( $classes, ' ' );
	}
	xkit_add_controller( 'sidebar_class', 'xkit_sidebar_class_controller', 10, 2 );
	
	
	/*
	 * Aside
	 */
	function xkit_aside_controller( $aside_name = 'main' ) {
		
		// Sidebar Type
		$sidebar_type = xkit_get_controller( 'sidebar_type' );
		
		// Get sidebar
		if( $sidebar_type != 'without' ) {
			if( $aside_name == 'secondary' ) {
				if( $sidebar_type == 'two' ) {
					get_sidebar( $aside_name );
				}
			} else {
				get_sidebar( $aside_name );
			}
		}
	}
	xkit_add_controller( 'aside', 'xkit_aside_controller' );