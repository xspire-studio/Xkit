<?php
/**
 * Module Name: Import Demo Data
 * Version: 1.0
 * Author: Xspire
 *
 * 1.0  - hook     init       | load_import_translations()
 * 2.0  - hook     admin_menu | import_page()
 * 3.0  - hook     admin_enqueue_scripts | load_admin_scripts()
 * 4.0  - method is_module_page()
 * 5.0  - method add_error()
 * 6.0  - method add_alert()
 * 7.0  - method add_message()
 * 8.0  - method clear_notices()
 * 9.0  - method show_notices()
 * 10.0 - method get_process()
 * 11.0 - method update_process()
 * 12.0 - hook     wp_ajax_import_demo | ajax_import()
 * 13.0 - filter   wp_import_terms | clear_match_menu_items()
 * 14.0 - method get_custom_set()
 * 15.0 - filter   wp_import_posts | filter_posts()
 * 16.0 - filter   import_post_meta_key | post_meta_fix()
 * 17.0 - filter   wp_import_post_data_raw | process_posts_import()
 * 18.0 - method import_content()
 * 19.0 - method update_options()
 * 20.0 - method set_menu_locations()
 * 21.0 - method set_settings_pages()
 * 22.0 - method get_file_content()
 * 23.0 - method import_options()
 * 24.0 - method import_revsliders()
 * 25.0 - method get_demo_screenshot()
 * 26.0 - method search_demos()
 * 27.0 - method import_page_html()
 * 28.0 - method customize_themes_print_templates()
 */

if ( defined( 'XKIT_IMPORTER_MODULE_ENABLE' ) && XKIT_IMPORTER_MODULE_ENABLE ) {

	/*
	 * Class For Import Demo Data
	 */
	class Xkit_Demo_Import {

		/*
		 * Instance
		 */
		private static $instance;


		/*
		 * Notices
		 *
		 * @var array
		 */
		public $notices = array();


		/*
		 * Demos
		 *
		 * @var array
		 */
		public $demos = array();


		/*
		 * Theme options uri
		 *
		 * @var string
		 */
		public $theme_options_uri = '';


		/*
		 * Demos page slug
		 *
		 * @var string
		 */
		public $import_page_slug = 'import-demo';


		/*
		 * Demos path
		 *
		 * @var string
		 */
		public $demos_path = '';


		/*
		 * Demos uri
		 *
		 * @var string
		 */
		public $demos_uri = '';


		/*
		 * Default demo version
		 *
		 * @var string
		 */
		public $default_demo_version = '1.0';


		/*
		 * Default author name
		 *
		 * @var string
		 */
		public $default_author_name = 'Xspire';


		/*
		 * Default author uri
		 *
		 * @var string
		 */
		public $default_author_uri = 'http://themeforest.net/user/xspire/';


		/*
		 * Count importing posts
		 *
		 * @var int
		 */
		private $all_import_posts = 0;


		/*
		 * Current importing post
		 *
		 * @var int
		 */
		private $current_import_post = 1;


		/*
		 * Constructor. Set up cacheable values and settings.
		 */
		public function __construct() {

			/* Clear notices */
			$this->clear_notices();


			/* Create dir */
			if( !is_dir( get_template_directory() . '/includes/demos' ) ){
				mkdir( get_template_directory() . '/includes/demos', 0755, true );
			}


			/* Set demos dir */
			$this->demos_path = get_template_directory() . '/includes/demos/';
			$this->demos_uri = get_template_directory_uri() . '/includes/demos/';


			/* Theme options uri */
			if( function_exists( 'get_theme_options_uri' ) ) {
				$this->theme_options_uri = get_theme_options_uri();
			}
			else{
				$this->theme_options_uri = admin_url( 'themes.php?page=theme-options' );
			}


			/* Import Page Init */
			add_action( 'admin_menu', array( &$this, 'import_page' ) );


			/* Ajax */
			add_action( 'wp_ajax_import_demo', array( &$this, 'ajax_import' ) );


			/* Enqueue Scripts */
			if( $this->is_module_page( $this->import_page_slug ) ) {
				add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_scripts' ) );
			}
		}


		/*
		 * Demo page Init
		 */
		public function import_page() {
			/* Register theme page */
			add_theme_page( 
				esc_html__( 'Demo Content', 'xkit' ),
				esc_html__( 'Demo Content', 'xkit' ),
				'edit_theme_options',
				$this->import_page_slug,
				array( &$this, 'import_page_html' )
			);
		}


		/*
		 * admin_enqueue_scripts | load_admin_scripts()
		 *
		 * Load scripts for Demos page
		 */
		public function load_admin_scripts() {

			// Deregister scripts
			wp_deregister_script('heartbeat');

			// Enqueue scripts
			wp_enqueue_script( 'theme' );
			wp_enqueue_script( 'customize-loader' );

			// Declare the URL to the AJAX requests
			wp_localize_script(
				'jquery',
				'ImportData',
				array(
					'process_uri' 	   => get_template_directory_uri() . '/framework/modules/importer/includes/process-ajax.php',
					'site_path'		   => get_home_path(),
					'import_page_slug' => $this->import_page_slug
				)
			);

			// Load files
			wp_enqueue_style( 'xkit-import', get_template_directory_uri() . '/framework/modules/importer/assets/css/import.css', false, XKIT_THEME_VERSION, 'all' );
			wp_enqueue_script( 'xkit-import', get_template_directory_uri() . '/framework/modules/importer/assets/js/import.js', false, XKIT_THEME_VERSION, false );
		}


		/*
		 * Check whether the current page is Demo
		 *
		 * @param  string  $page_slug
		 * @return bool
		 */
		public function is_module_page( $page_slug = '' ) {
			$current_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
			$page_opened = pathinfo( $current_path );

			if( $page_opened['basename'] == 'themes.php' ) {
				parse_str( $_SERVER['QUERY_STRING'], $page_query );

				if( isset( $page_query['page'] ) && $page_query['page'] == $page_slug ) {
					return true;
				}
			}

			return false;
		}


		/*
		 * Add notice error
		 *
		 * @var string $error
		 */
		public function add_error( $error ) {
			$this->notices['errors'][] = $error;
		}


		/*
		 * Add notice alert
		 *
		 * @var string $message
		 */
		public function add_alert( $message ) {
			$this->notices['alerts'][] = $message;
		}


		/*
		 * Add notice message
		 *
		 * @var string $message
		 */
		public function add_message( $message ) {
			$this->notices['messages'][] = $message;
		}


		/*
		 * Clear notices
		 *
		 * @var string $type
		 */
		public function clear_notices( $type = '' ) {
			if( !empty( $type ) ) {
				unset( $this->notices[$type] );
				return;
			}
			$this->notices = array();
		}


		/*
		 * Clear notices
		 *
		 * @var string $type
		 */
		public function show_notices( $type = '' ) {
			$out = '';

			if( !empty( $type ) ) {
				$list_notices[ $type ] = $this->notices[ $type ];
			}
			else{
				$list_notices = $this->notices;
			}

			foreach( ( array ) $list_notices as $notice_type => $notices ) {
				if( $notice_type == 'errors' ) {
					$out .= '<div class="error notice is-dismissible">';
					foreach( ( array ) $notices as $notice ) {
						$out .= '<p>' . $notice. '</p>';
					}
					$out .= '</div>';
				}

				if( $notice_type == 'alerts' ) {
					$out .= '<div class="update-nag notice is-dismissible">';
					foreach( ( array ) $notices as $notice ) {
						$out .= '<p>' . $notice. '</p>';
					}
					$out .= '</div>';
				}

				if( $notice_type == 'messages' ) {
					$out .= '<div class="updated notice is-dismissible">';
					foreach( ( array ) $notices as $notice ) {
						$out .= '<p>' . $notice. '</p>';
					}
					$out .= '</div>';
				}
			}

			echo wp_kses_post( $out );

			$this->clear_notices();
		}


		/*
		 * Get import process
		 */
		public function get_process() {
			$import_percent = intval( get_option( 'import_percent' ) );
			return $import_percent;
		}


		/*
		 * Update import process
		 *
		 * @param int  $new_percent
		 * @param int  $status 					Global Process status: 1-100
		 * @param bool $reset_counter_posts	RE  Clear global process
		 */
		public function update_process( $new_percent, $status = 0, $reset_counter_posts = false ) {
			if( is_numeric( $new_percent ) ) {
				update_option( 'import_percent', intval( $new_percent ) );
				update_option( 'importing_status', $status );
			}

			if( $reset_counter_posts == true ) {
				$this->current_import_post = 1;
			}
		}


		/*
		 * wp_ajax_import_demo | ajax_import()
		 *
		 * Ajax Import
		 */
		public function ajax_import() {
			global $wpdb;

			/* Load import module */
			load_template( get_template_directory() . '/framework/modules/importer/includes/xkit-wordpress-importer.php', true );

			/* Import start. Reset old process */
			$this->update_process( 0, 1, true );

			/* Importer classes */
			if( class_exists( 'Xkit_Import' ) ) {
				global $xkit_importing_demo_id;
				$xkit_importing_demo_id = sanitize_text_field( $_POST['demo_id'] );

				if( ! empty( $xkit_importing_demo_id ) ) {

					// Check content path
					$content_path = $this->demos_path . $xkit_importing_demo_id . '/content.xml.gz';
					if( !file_exists( $content_path ) ) {
						$this->add_error( esc_html__( 'Options file is not found!', 'xkit' ) );
						$this->show_notices();

						// Reset process
						$this->update_process( 0, 0, true );
						wp_die();
					}

					$this->update_process( 3, 1 );

					// Import wp content
					add_filter( 'wp_import_terms', array( &$this, 'clear_match_menu_items' ), 10, 1 );  // Clear match menu items
					add_filter( 'wp_import_posts', array( &$this, 'filter_posts' ), 10, 1 ); 			// Filter posts
					add_filter( 'import_post_meta_key', array( &$this, 'post_meta_fix' ), 10, 2 ); 		// Post meta fix
					add_filter( 'wp_import_post_data_raw', array( &$this, 'replace_home_page' ), 10, 1 ); // Process posts
					add_filter( 'wp_import_post_data_raw', array( &$this, 'process_posts_import' ), 11, 1 ); // Process posts

					// Import content
					$content_result = $this->import_content( $content_path );
					if( !$content_result ) {
						$this->add_error( esc_html__( 'Import content failed!', 'xkit' ) );
						$this->show_notices();

						// Reset process
						$this->update_process( 0, 0, true );
						wp_die();
					}
					$this->update_process( 90, 1 );

					// Import options
					$this->import_options( $xkit_importing_demo_id );

					// Import revsliders
					$this->import_revsliders();

					// Import finish
					update_option( 'active_demo', $xkit_importing_demo_id ); // Set active demo

					// Import end
					$this->update_process( 100, 0, true );
					$this->add_message( esc_html__( 'Import finished!', 'xkit' ) );
				}

				$xkit_importing_demo_id = false;
			}
			else{
				$this->add_error( esc_html__( 'Import module not found. Consult the developers!', 'xkit' ) );
			}

			$this->show_notices();

			// Reset process
			$this->update_process( 0, 0, true );
			wp_die();
		}


		/*
		 * wp_import_terms | clear_match_menu_items()
		 *
		 * Clear match menu items
		 *
		 * @return array
		 */
		public function clear_match_menu_items( $import_terms ) {
			if( empty( $import_terms ) ) {
				return false;
			}

			// Check Custom Set
			$clear_menus = true;
			if( !empty( $_POST['import_type'] ) && $_POST['import_type'] == 'custom_set' ) {
				$import_set = ( array ) $this->get_custom_set();

				if( is_array( $_POST['custom_set_array'] ) ) {
					$import_set = array_merge( $import_set, (array) $_POST['custom_set_array'] ) ;
				}

				if( isset( $import_set['nav_menu_item'] ) && $import_set['nav_menu_item'] == 0 ) {
					$clear_menus = false;
				}
			}

			// Clear menu matches
			$nav_menus = wp_get_nav_menus();
			if( !empty( $nav_menus ) && $clear_menus == true ) {
				$this->update_process( 5, 1 );

				foreach ( $nav_menus as $nav_menu ) {

					// Search menu matches
					foreach ( $import_terms as $import_term ) {
						if( $import_term['slug'] == $nav_menu->slug ) {
							$nav_menu_items = wp_get_nav_menu_items( $nav_menu->slug );

							// Delete items
							foreach( $nav_menu_items as $item ) {
								wp_delete_post( $item->ID, true );
							}
						}
					}
				}
			}

			return $import_terms;
		}


		/*
		 * Get custom post types for Import
		 *
		 * @return array
		 */
		public function get_custom_set() {
			$args = array( 
				'public'   => true,
				'_builtin' => false
			 );
			$post_types = ( array ) get_post_types( $args, 'names', 'and' );
			array_unshift( $post_types, 'post', 'page', 'nav_menu_item' );

			$import_set = array();
			foreach( $post_types as $post_type ) {
				$import_set[$post_type] = 0;
			}

			$import_set = apply_filters( 'xkit_custom_import_set', $import_set );
			return $import_set;
		}


		/*
		 * wp_import_posts | filter_posts()
		 *
		 * Filter Posts
		 *	
		 * @param  array $posts
		 * @return array
		 */
		public function filter_posts( $posts ) {
			if( is_array( $posts ) ) {
				$this->all_import_posts = count( $posts );
			}

			// Custom Set
			if( !empty( $_POST['import_type'] ) && $_POST['import_type'] == 'custom_set' ) {

				$import_set = ( array ) $this->get_custom_set();
				$exclude_post_types = array();

				if( is_array( $_POST['custom_set_array'] ) ) {
					$import_set = array_merge( $import_set, (array) $_POST['custom_set_array'] ) ;
				}

				// Exclude Post Types
				foreach( $import_set as $p_type => $type_val ) {
					if( $type_val == 0 ) {
						$exclude_post_types[] = $p_type;

						// Exclude Woocommerce
						if( $p_type == 'product' ) {
							array_push( $exclude_post_types, 'product_variation', 'shop_order', 'shop_order_refund', 'shop_coupon', 'shop_webhook' );
						}
					}
				}

				// Filter Posts
				$filtered_posts = array();
				foreach( $posts as $single_post ) {
					if( in_array( $single_post['post_type'], $exclude_post_types ) ) {
						continue;
					}
					$filtered_posts[] = $single_post;
				}
				$posts = $filtered_posts;
			}

			return $posts;
		}


		/*
		 * import_post_meta_key | post_meta_fix()
		 *
		 * Post Meta Fix
		 *
		 * @param  string  $key
		 * @param  int     $post_id
		 * @return string
		 */
		public function post_meta_fix( $key, $post_id ) {
			global $wpdb;
			$post_meta = $wpdb->get_row( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key='{$key}' AND post_id={$post_id}", ARRAY_A );

			if( !empty( $post_meta['meta_key'] ) ) {
				return false;
			}

			return $key;
		}


		/*
		 * wp_import_post_data_raw | replace_home_page()
		 *
		 * Replace Home Page
		 *
		 * @param  array $post
		 * @return array
		 */
		public function replace_home_page( $post ) {
			if( $post['post_name'] == 'home' ) {
				$current_home = get_page_by_title( 'Home' );

				if( isset( $current_home->ID ) ) {
					wp_delete_post( $current_home->ID, true );
				}
			}

			return $post;
		}


		/*
		 * wp_import_post_data_raw | process_posts_import()
		 *
		 * Process Posts
		 *
		 * @param  array $post
		 * @return array
		 */
		public function process_posts_import( $post ) {

			/* Import process */
			$all_import_posts = intval( $this->all_import_posts );
			$current_post     = intval( $this->current_import_post );

			if( $all_import_posts > 0 ) {
				$process = round( ( $current_post * 85 ) / $all_import_posts ) + 5;

				if( intval( $process ) >= 1 ) {
					$this->update_process( intval( $process ), 1 );
					$this->current_import_post = $current_post + 1;
				}
			}

			return $post;
		}


		/*
		 * Imported terms ids
		 *
		 * @param array $processed_terms
		 */
		private function xkit_imported_terms_ids( $processed_terms = array() ) {
			do_action( 'xkit_imported_terms_ids', $processed_terms );
		}


		/*
		 * Imported posts ids
		 *
		 * @param array $processed_posts
		 */
		private function xkit_imported_posts_ids( $processed_posts = array() ) {
			do_action( 'xkit_imported_posts_ids', $processed_posts );
		}


		/*
		 * Import Content
		 *
		 * @param string $file_path
		 * @return bool
		 */
		public function import_content( $file_path = '' ) {
			if( ! file_exists( $file_path ) ) {
				return false;
			}

			// Import Init
			$import = new Xkit_Import();

			// Import attachments
			$import->fetch_attachments = ( $_POST && key_exists( 'attachments', $_POST ) && $_POST['attachments'] ) ? true : false;

			ob_start();

			// Import process
			$import->import( $file_path );

			// Processed posts / terms
			$this->xkit_imported_terms_ids( $import->processed_terms );
			$this->xkit_imported_posts_ids( $import->processed_posts );

			ob_end_clean();

			return true;
		}


		/*
		 * Update options
		 *
		 * @param  array $options
		 * @return bool
		 */
		public function update_options( $options = array() ) {
			if( empty( $options ) ) {
				return false;
			}

			foreach( $options as $option_name => $option_value ) {
				$option_value = maybe_unserialize( $option_value );
				update_option( $option_name, $option_value );
			}

			return true;
		}


		/*
		 * Set Menu Locations
		 *
		 * @param  array  $menus
		 * @return bool
		 */
		public function set_menu_locations( $menus = '' ) {
			if( empty( $menus ) ) {
				return false;
			}

			global $wpdb;
			$menu_array = array();
			$terms_table = $wpdb->prefix . "terms";

			foreach ( $menus as $registered_menu => $menu_slug ) {
				$term_rows = $wpdb->get_results( "SELECT * FROM $terms_table WHERE slug='{$menu_slug}'", ARRAY_A );

				if( isset( $term_rows[0]['term_id'] ) ) {
					$term_id_by_slug = $term_rows[0]['term_id'];
				} else {
					$term_id_by_slug = null;
				}
				$menu_array[ $registered_menu ] = $term_id_by_slug;
			}

			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $menu_array ) );

			return true;
		}


		/*
		 * Set settings pages
		 *
		 * @param  array $options
		 * @return bool
		 */
		public function set_settings_pages( $options ) {
			if( empty( $options ) ) {
				return false;
			}

			if( !empty( $options['show_on_front'] ) ) {
				update_option( 'show_on_front', $options['show_on_front'] );
			}

			if( !empty( $options['page_on_front'] ) ) {
				$home = get_page_by_title( $options['page_on_front'] );

				if( isset( $home->ID ) ) {
					update_option( 'page_on_front', $home->ID ); // Front Page
				}
			}

			if( !empty( $options['page_for_posts'] ) ) {
				$blog = get_page_by_title( $options['page_for_posts'] );

				if( isset( $blog->ID ) ) {
					update_option( 'page_for_posts', $blog->ID ); // Front Page
				}
			}

			return true;
		}


		/*
		 * Get file content
		 *
		 * @param  string $path
		 * @return string
		 */
		public function get_file_content( $path = '' ) {
			if( !empty( $path ) ) {
				if( file_exists( $path ) && is_file( $path ) ) {
					$content = xkit_file_load_content( $path );

					if( !empty( $content ) ) {
						$decoded_content = json_decode( $content, true );

						if( is_array( $decoded_content ) ) {
							return $decoded_content;
						}
					}
				}
			}

			return false;
		}


		/*
		 * Import All options
		 *
		 * @param  string  $xkit_importing_demo_id
		 * @return bool
		 */
		public function import_options( $xkit_importing_demo_id = '' ) {
			if( empty( $xkit_importing_demo_id ) ) {
				return false;
			}

			// Get options content
			$options_path = $this->demos_path . $xkit_importing_demo_id . '/options.json';
			$options_content = $this->get_file_content( $options_path );

			if( empty( $options_content ) ) {
				$this->add_error( esc_html__( 'Options file is not found!', 'xkit' ) );
				return false;
			}

			// Import sidebars
			if( !empty( $options_content['sidebars'] ) && is_array( $options_content['sidebars'] ) ) {
				$this->update_options( $options_content['sidebars'] );
			}

			// Import widgets
			if( !empty( $options_content['widgets'] ) && is_array( $options_content['widgets'] ) ) {
				$this->update_options( $options_content['widgets'] );
			}
			$this->update_process( 93, 1 );

			// Import acf options
			if( !empty( $options_content['acf_options'] ) && is_array( $options_content['acf_options'] ) ) {
				$this->update_options( $options_content['acf_options'] );
			}

			// Import visual composer
			if( !empty( $options_content['vc_options'] ) && is_array( $options_content['vc_options'] ) ) {
				$this->update_options( $options_content['vc_options'] );
			}

			// Setting pages
			$posts_per_page = apply_filters( 'xkit_import_post_per_page', 10 );
			update_option( 'posts_per_page', intval( $posts_per_page ) );

			// Import menus
			$this->set_menu_locations( $options_content['menu_locations'] );

			// Setting pages
			$this->set_settings_pages( $options_content['settings_pages'] );

			// Actions
			do_action( 'xkit_import_updated_options' );

			$this->update_process( 97, 1 );

			return true;
		}


		/*
		 * Import Revslider
		 */
		public function import_revsliders() {
			global $xkit_importing_demo_id, $wpdb;

			if( class_exists( 'UniteFunctionsRev' ) ) { // if revslider is activated
				$rev_directory = $this->demos_path . $xkit_importing_demo_id . '/revsliders/';

				if( !file_exists( $rev_directory ) ) {
					return;
				}

				$rev_files = array();

				foreach( glob( $rev_directory . '*.zip' ) as $filename ) { // get all files from revsliders data dir
					$filename = basename( $filename );
					$rev_files[] = $rev_directory . $filename;
				}

				foreach( $rev_files as $rev_file ) { // finally import rev slider data files

					$filepath = $rev_file;

					// Check for duplicate
					$rev_table = $wpdb->prefix . 'revslider_sliders';
					$rev_slider_slug = pathinfo( $filepath, PATHINFO_FILENAME );
					$slider_exist = $wpdb->get_row( "SELECT * FROM $rev_table WHERE alias='{$rev_slider_slug}'", ARRAY_A );

					if( !empty( $slider_exist['id'] ) ) {
						$this->add_alert( $rev_slider_slug . ' - ' . esc_html__( 'Slider already exist!', 'xkit' ) );
						continue;
					}

					//check if zip file or fallback to old, if zip, check if all files exist
					$zip = new ZipArchive;
					$importZip = $zip->open( $filepath, ZIPARCHIVE::CREATE );

					if( $importZip === true ) { //true or integer. If integer, its not a correct zip file

						//check if files all exist in zip
						$slider_export     = $zip->getStream( 'slider_export.txt' );
						$custom_animations = $zip->getStream( 'custom_animations.txt' );
						$dynamic_captions  = $zip->getStream( 'dynamic-captions.css' );
						$static_captions   = $zip->getStream( 'static-captions.css' );

						if( !$slider_export ) {
							$this->add_error( $rev_slider_slug . ' - ' . esc_html__( 'Slider_export.txt does not exist!', 'xkit' ) );
							continue;
						}

						$content    = '';
						$animations = '';
						$dynamic 	= '';
						$static 	= '';

						while ( !feof( $slider_export ) ) $content .= xkit_fsread( $slider_export, 1024 );
						if( $custom_animations ) { while ( !feof( $custom_animations ) ) $animations .= xkit_fsread( $custom_animations, 1024 ); }
						if( $dynamic_captions ) { while ( !feof( $dynamic_captions ) ) $dynamic .= xkit_fsread( $dynamic_captions, 1024 ); }
						if( $static_captions ) { while ( !feof( $static_captions ) ) $static .= xkit_fsread( $static_captions, 1024 ); }

						xkit_fsclose( $slider_export );
						if( $custom_animations ) { xkit_fsclose( $custom_animations ); }
						if( $dynamic_captions ) { xkit_fsclose( $dynamic_captions ); }
						if( $static_captions ) { xkit_fsclose( $static_captions ); }

					}else{ //check if fallback
						//get content array
						$content = xkit_file_load_content( $filepath );
					}

					if( $importZip === true ) { //we have a zip
						$db = new UniteDBRev();

						$RevSliderClass = new RevSlider();

						//update/insert custom animations
						$animations = @unserialize( $animations );
						if( !empty( $animations ) ) {
							foreach( $animations as $key => $animation ) { //$animation['id'], $animation['handle'], $animation['params']
								$exist = $db->fetch( GlobalsRevSlider::$table_layer_anims, "handle = '".$animation['handle']."'" );
								if( !empty( $exist ) ) { //update the animation, get the ID
									if( $updateAnim == "true" ) { //overwrite animation if exists
										$arrUpdate = array();
										$arrUpdate['params'] = stripslashes( json_encode( str_replace( "'", '"', $animation['params'] ) ) );
										$db->update( GlobalsRevSlider::$table_layer_anims, $arrUpdate, array( 'handle' => $animation['handle'] ) );

										$id = $exist['0']['id'];
									}else{ //insert with new handle
										$arrInsert = array();
										$arrInsert["handle"] = 'copy_'.$animation['handle'];
										$arrInsert["params"] = stripslashes( json_encode( str_replace( "'", '"', $animation['params'] ) ) );

										$id = $db->insert( GlobalsRevSlider::$table_layer_anims, $arrInsert );
									}
								}else{ //insert the animation, get the ID
									$arrInsert = array();
									$arrInsert["handle"] = $animation['handle'];
									$arrInsert["params"] = stripslashes( json_encode( str_replace( "'", '"', $animation['params'] ) ) );

									$id = $db->insert( GlobalsRevSlider::$table_layer_anims, $arrInsert );
								}

								//and set the current customin-oldID and customout-oldID in slider params to new ID from $id
								$content = str_replace( array( 'customin-'.$animation['id'], 'customout-'.$animation['id'] ), array( 'customin-'.$id, 'customout-'.$id ), $content );	
							}
						}

						//overwrite/append static-captions.css
						if( !empty( $static ) ) {
							if( $updateStatic == "true" ) { //overwrite file
								RevOperations::updateStaticCss( $static );
							}else{ //append
								$static_cur = RevOperations::getStaticCss();
								$static = $static_cur."\n".$static;
								RevOperations::updateStaticCss( $static );
							}
						}
						//overwrite/create dynamic-captions.css
						//parse css to classes
						$dynamicCss = UniteCssParserRev::parseCssToArray( $dynamic );

						if( is_array( $dynamicCss ) && $dynamicCss !== false && count( $dynamicCss ) > 0 ) {
							foreach( $dynamicCss as $class => $styles ) {
								//check if static style or dynamic style
								$class = trim( $class );

								if( ( strpos( $class, ':hover' ) === false && strpos( $class, ':' ) !== false ) || //before, after
									strpos( $class," " ) !== false || // .tp-caption.imageclass img or .tp-caption .imageclass or .tp-caption.imageclass .img
									strpos( $class,".tp-caption" ) === false || // everything that is not tp-caption
									( strpos( $class,"." ) === false || strpos( $class,"#" ) !== false ) || // no class -> #ID or img
									strpos( $class,">" ) !== false ) { //.tp-caption>.imageclass or .tp-caption.imageclass>img or .tp-caption.imageclass .img
									continue;
								}

								//is a dynamic style
								if( strpos( $class, ':hover' ) !== false ) {
									$class = trim( str_replace( ':hover', '', $class ) );
									$arrInsert = array();
									$arrInsert["hover"] = json_encode( $styles );
									$arrInsert["settings"] = json_encode( array( 'hover' => 'true' ) );
								}else{
									$arrInsert = array();
									$arrInsert["params"] = json_encode( $styles );
								}
								//check if class exists
								$result = $db->fetch( GlobalsRevSlider::$table_css, "handle = '".$class."'" );

								if( !empty( $result ) ) { //update
									$db->update( GlobalsRevSlider::$table_css, $arrInsert, array( 'handle' => $class ) );
								}else{ //insert
									$arrInsert["handle"] = $class;
									$db->insert( GlobalsRevSlider::$table_css, $arrInsert );
								}
							}
						}
					}

					$content = preg_replace( '!s:( \d+ ):"( .*? )";!', "'s:'.strlen( '$2' ).':\"$2\";'", $content ); //clear errors in string

					$arrSlider = @unserialize( $content );
					if( empty( $arrSlider ) ) {
						$this->add_error( $rev_slider_slug . ' - ' . esc_html__( 'Wrong export slider file format! This could be caused because the ZipArchive extension is not enabled.', 'xkit' ) );
						continue;
					}

					//update slider params
					$sliderParams = $arrSlider["params"];
					$sliderExists = false;

					if( $sliderExists ) {
						$sliderParams["title"] = $RevSliderClass->arrParams["title"];
						$sliderParams["alias"] = $RevSliderClass->arrParams["alias"];
						$sliderParams["shortcode"] = $RevSliderClass->arrParams["shortcode"];
					}

					if( isset( $sliderParams["background_image"] ) ){
						$sliderParams["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath( $sliderParams["background_image"] );
					}

					$json_params = json_encode( $sliderParams );

					//update slider or create new
					if( $sliderExists ) {
						$arrUpdate = array( "params"=>$json_params );
						$db->update( GlobalsRevSlider::$table_sliders,$arrUpdate,array( "id"=>$sliderID ) );
					}else{	//new slider
						$arrInsert = array();
						$arrInsert["params"] = $json_params;
						$arrInsert["title"] = UniteFunctionsRev::getVal( $sliderParams, "title","Slider1" );
						$arrInsert["alias"] = UniteFunctionsRev::getVal( $sliderParams, "alias","slider1" );
						$sliderID = $db->insert( GlobalsRevSlider::$table_sliders,$arrInsert );
					}

					/* Slides Handle */

					//delete current slides
					if( $sliderExists ){
						$RevSliderClass->deleteAllSlides();
					}

					//create all slides
					$arrSlides = $arrSlider["slides"];

					$alreadyImported = array();

					//wpml compatibility
					$slider_map = array();

					foreach( $arrSlides as $slide ) {
						$params = $slide["params"];
						$layers = $slide["layers"];

						//convert params images:
						if( isset( $params["image"] ) ) {
							//import if exists in zip folder
							if( strpos( $params["image"], 'http' ) !== false ) {
							}else{
								if( trim( $params["image"] ) !== '' ) {
									if( $importZip === true ) { //we have a zip, check if exists
										$image = $zip->getStream( 'images/'.$params["image"] );
										if( !$image ) {
											echo wp_kses_post( $params["image"] ) . wp_kses_post( __( ' not found!<br>', 'xkit' ) );
										}else{
											if( !isset( $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]] ) ) {
												$importImage = UniteFunctionsWPRev::import_media( 'zip://'.$filepath."#".'images/'.$params["image"], $sliderParams["alias"].'/' );

												if( $importImage !== false ) {
													$alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]] = $importImage['path'];

													$params["image"] = $importImage['path'];
												}
											}else{
												$params["image"] = $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]];
											}


										}
									}
								}
								$params["image"] = UniteFunctionsWPRev::getImageUrlFromPath( $params["image"] );
							}
						}

						//convert layers images:
						foreach( $layers as $key=>$layer ) {
							if( isset( $layer["image_url"] ) ) {
								//import if exists in zip folder
								if( trim( $layer["image_url"] ) !== '' ) {
									if( strpos( $layer["image_url"], 'http' ) !== false ) {
									}else{
										if( $importZip === true ) { //we have a zip, check if exists
											$image_url = $zip->getStream( 'images/'.$layer["image_url"] );
											if( !$image_url ) {
												echo esc_url( $layer["image_url"] ) . wp_kses_post( __( ' not found!<br>', 'xkit' ) );
											}else{
												if( !isset( $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]] ) ) {
													$importImage = UniteFunctionsWPRev::import_media( 'zip://'.$filepath."#".'images/'.$layer["image_url"], $sliderParams["alias"].'/' );

													if( $importImage !== false ) {
														$alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]] = $importImage['path'];

														$layer["image_url"] = $importImage['path'];
													}
												}else{
													$layer["image_url"] = $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]];
												}
											}
										}
									}
								}
								$layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath( $layer["image_url"] );
								$layers[$key] = $layer;
							}
						}

						//create new slide
						$arrCreate = array();
						$arrCreate["slider_id"] = $sliderID;
						$arrCreate["slide_order"] = $slide["slide_order"];

						$my_layers = json_encode( $layers );
						if( empty( $my_layers ) ){
							$my_layers = stripslashes( json_encode( $layers ) );
						}
						$my_params = json_encode( $params );
						if( empty( $my_params ) ){
							$my_params = stripslashes( json_encode( $params ) );
						}


						$arrCreate["layers"] = $my_layers;
						$arrCreate["params"] = $my_params;

						$last_id = $db->insert( GlobalsRevSlider::$table_slides,$arrCreate );

						if( isset( $slide['id'] ) ) {
							$slider_map[$slide['id']] = $last_id;
						}
					}

					//change for WPML the parent IDs if necessary
					if( !empty( $slider_map ) ) {
						foreach( $arrSlides as $slide ) {
							if( isset( $slide['params']['parentid'] ) && isset( $slider_map[$slide['params']['parentid']] ) ) {
								$update_id = $slider_map[$slide['id']];
								$parent_id = $slider_map[$slide['params']['parentid']];

								$arrCreate = array();

								$arrCreate["params"] = $slide['params'];
								$arrCreate["params"]['parentid'] = $parent_id;
								$my_params = json_encode( $arrCreate["params"] );
								if( empty( $my_params ) ){
									$my_params = stripslashes( json_encode( $arrCreate["params"] ) );
								}

								$arrCreate["params"] = $my_params;

								$db->update( GlobalsRevSlider::$table_slides,$arrCreate,array( "id"=>$update_id ) );
							}
						}
					}

					//check if static slide exists and import
					if( isset( $arrSlider['static_slides'] ) && !empty( $arrSlider['static_slides'] ) ) {
						$static_slide = $arrSlider['static_slides'];
						foreach( $static_slide as $slide ) {

							$params = $slide["params"];
							$layers = $slide["layers"];

							//convert params images:
							if( isset( $params["image"] ) ) {
								//import if exists in zip folder
								if( strpos( $params["image"], 'http' ) !== false ) {
								}else{
									if( trim( $params["image"] ) !== '' ) {
										if( $importZip === true ) { //we have a zip, check if exists
											$image = $zip->getStream( 'images/'.$params["image"] );
											if( !$image ) {
												echo wp_kses_post( $params["image"] ) . wp_kses_post( __( ' not found!<br>', 'xkit' ) );

											}else{
												if( !isset( $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]] ) ) {
													$importImage = UniteFunctionsWPRev::import_media( 'zip://'.$filepath."#".'images/'.$params["image"], $sliderParams["alias"].'/' );

													if( $importImage !== false ) {
														$alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]] = $importImage['path'];

														$params["image"] = $importImage['path'];
													}
												}else{
													$params["image"] = $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]];
												}


											}
										}
									}
									$params["image"] = UniteFunctionsWPRev::getImageUrlFromPath( $params["image"] );
								}
							}

							//convert layers images:
							foreach( $layers as $key=>$layer ) {
								if( isset( $layer["image_url"] ) ) {
									//import if exists in zip folder
									if( trim( $layer["image_url"] ) !== '' ) {
										if( strpos( $layer["image_url"], 'http' ) !== false ) {
										}else{
											if( $importZip === true ) { //we have a zip, check if exists
												$image_url = $zip->getStream( 'images/'.$layer["image_url"] );
												if( !$image_url ) {
													echo wp_kses_post( $layer["image_url"] ) . wp_kses_post( __( ' not found!<br>', 'xkit' ) );
												}else{
													if( !isset( $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]] ) ) {
														$importImage = UniteFunctionsWPRev::import_media( 'zip://'.$filepath."#".'images/'.$layer["image_url"], $sliderParams["alias"].'/' );

														if( $importImage !== false ) {
															$alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]] = $importImage['path'];

															$layer["image_url"] = $importImage['path'];
														}
													}else{
														$layer["image_url"] = $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]];
													}
												}
											}
										}
									}
									$layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath( $layer["image_url"] );
									$layers[$key] = $layer;
								}
							}

							//create new slide
							$arrCreate = array();
							$arrCreate["slider_id"] = $sliderID;

							$my_layers = json_encode( $layers );
							if( empty( $my_layers ) ){
								$my_layers = stripslashes( json_encode( $layers ) );
							}
							$my_params = json_encode( $params );
							if( empty( $my_params ) ){
								$my_params = stripslashes( json_encode( $params ) );
							}


							$arrCreate["layers"] = $my_layers;
							$arrCreate["params"] = $my_params;

							if( $sliderExists ) {
								unset( $arrCreate["slider_id"] );
								$db->update( GlobalsRevSlider::$table_static_slides,$arrCreate,array( "slider_id"=>$sliderID ) );
							}else{
								$db->insert( GlobalsRevSlider::$table_static_slides,$arrCreate );
							}
						}
					}
				} // each revSlider
			}
		}


		/*
		 * Get Demo Screenshot
		 *
		 * @param  string $demo_id
		 * @return string Screenshot path
		 */
		public function get_demo_screenshot( $demo_id ) {
			$demo_path = $this->demos_path . $demo_id . '/';
			$demo_uri = $this->demos_uri . $demo_id . '/';
			$screenshot_path = '';

			foreach ( glob( $demo_path . 'screenshot.*' ) as $filepath ) {
				if( getimagesize( $filepath ) ) {
					return $demo_uri . basename( $filepath );
				}
			}

			return $screenshot_path;
		}


		/*
		 * Search Demos
		 *
		 * @return array
		 */
		public function search_demos() {
			$demos_array = array();

			/* Create demos array */
			$found_demos = ( array ) glob( $this->demos_path . '*', GLOB_ONLYDIR );
			foreach( $found_demos as $demo ) {
				$demo_id = basename( $demo );
				$demo_info_array = array();

				// Demo Info
				$demo_info_path = $this->demos_path . $demo_id . '/demo-info.json';

				if( file_exists( $demo_info_path ) ) {
					$demo_info_json = xkit_file_load_content( $demo_info_path );
					$demo_info_array = ( array ) json_decode( $demo_info_json, true );
				}

				// Get active demo Info
				$active_demo = get_option( 'active_demo' );
				$current_demo_active = false;
				if( $active_demo == $demo_id ) {
					$current_demo_active = true;
				}

				// Demo array
				$demos_array[] = array_merge( array( 
					'name'		   => $demo_id,
					'id'		   => sanitize_title( $demo_id ),
					'screenshot'   => array( $this->get_demo_screenshot( $demo_id ) ),
					'version'	   => $this->default_demo_version,
					'description'  => '',
					'active'	   => $current_demo_active,
					'author'	   => $this->default_author_name,
					'authorAndUri' => '<a href="' . $this->default_author_uri . '" target="_blank">' . $this->default_author_name . '</a>',
					'hasUpdate'	   => false,
					'live_preview_uri' => '',
					'actions'	   => array( 
						'customize'			=> $this->theme_options_uri,
						'live_preview_uri'	=> '#',
						'activate'			=> '#',
						'delete'			=> '#'
					 )
				), $demo_info_array );
			}

			return $demos_array;
		}


		/*
		 * Import page HTML
		 *
		 * @return HTML
		 */
		public function import_page_html() {

			/* Get all demos */
			$this->demos = $this->search_demos();

			/* Check capability */
			if ( !current_user_can( 'edit_theme_options' ) ){
				wp_die( esc_html__( 'Access is denied', 'xkit' ), 403 );
			}

			/* Scripts settings */
			add_thickbox();
			wp_localize_script( 'theme', '_wpThemeSettings', array( 
				'themes'   => $this->demos,
				'settings' => array( 
					'isInstall'		=> false,
					'canInstall'    => false,
					'installURI'    => null,
					'confirmDelete' => '',
					'adminUrl'      => parse_url( admin_url(), PHP_URL_PATH ),
				 ),
				'l10n' => array( 
					'addNew'            => esc_html__( 'Add New Theme', 'xkit' ),
					'search'            => esc_html__( 'Search Installed Themes', 'xkit' ),
					'searchPlaceholder' => esc_html__( 'Search installed themes...', 'xkit' ), // placeholder ( no ellipsis )
					'themesFound'       => esc_html__( 'Number of Themes found: %d', 'xkit' ),
					'noThemesFound'     => esc_html__( 'No themes found. Try a different search.', 'xkit' ),
				 ),
			 ) );
			?>
			<div id="import-page" class="wrap">
				<div id="import-notices">
					<?php
						if( !empty( $_POST['notices'] ) ) {
							echo wp_kses_post( html_entity_decode( $_POST['notices'] ) );
						}
						else{
							?>
							<div class="update-nag notice is-dismissible">
								<p><?php esc_html_e( 'Installing demo content will not alter any of your pages or posts, but it will overwrite your Customizer settings. This is not reversible unless you have previously made a backup of your settings.', 'xkit' ); ?></p>
								<p><?php esc_html_e( 'If you plan to use shop, please install WooCommerce before you run import.', 'xkit' ); ?></p>
								<p><?php esc_html_e( 'If you plan to use forums, please install bbPress before you run import.', 'xkit' ); ?></p>
							</div>
							<?php
						}

						// Empty demos
						if( empty( $this->demos ) ) {
							?>
							<div class="error notice">
								<p><?php esc_html_e( 'Demos not found.', 'xkit' ); ?></p>
							</div>
							<?php
						}
					?>
				</div>

				<form id="notices-form" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
					<textarea class="notices-area" name="notices"><?php echo esc_textarea( $_POST['notices'] );  ?></textarea>
				</form>

				<div class="import-page-content">
					<div class="header-box">
						<div class="head-title"><?php esc_html_e( 'Install Demos', 'xkit' ); ?></div>
						<span class="title-count theme-count"><?php echo count( $this->demos ); ?></span>
						<div class="logo"><img src="<?php echo get_template_directory_uri() . '/framework/modules/importer/assets/images/header-logo.png'; ?>" /></div>
					</div>

					<div class="theme-browser">
						<div class="themes">
							<?php
							foreach ( $this->demos as $demo ) :
								$aria_action = esc_attr( $demo['id'] . '-action' );
								$aria_name   = esc_attr( $demo['id'] . '-name' );
								?>
								<div class="theme<?php if ( $demo['active'] ) echo ' active'; ?>" tabindex="0">
									<?php if ( ! empty( $demo['screenshot'][0] ) ) { ?>
										<div class="theme-screenshot">
											<img src="<?php echo esc_url( $demo['screenshot'][0] ); ?>" alt="" />
										</div>
									<?php } else { ?>
										<div class="theme-screenshot blank"></div>
									<?php } ?>

									<span class="more-details" id="<?php echo esc_attr( $aria_action ); ?>"><?php esc_html_e( 'Open Demo', 'xkit' ); ?></span>
									<div class="theme-author"><?php printf( esc_html__( 'By %s', 'xkit' ), $demo['author'] ); ?></div>

									<?php if ( $demo['active'] ) { ?>
										<h3 class="theme-name" id="<?php echo esc_attr( $aria_name ); ?>">
											<?php printf( wp_kses_post( __( '<span>Installed demo:</span> %s', 'xkit' ) ), $demo['name'] ); ?>
										</h3>
									<?php } else { ?>
										<h3 class="theme-name" id="<?php echo esc_attr( $aria_name ); ?>"><?php echo wp_kses_post( $demo['name'] ); ?></h3>
									<?php } ?>

									<div class="theme-actions">
										<?php if ( $demo['active'] ) { ?>
											<?php if ( $demo['actions']['customize'] && current_user_can( 'edit_theme_options' ) ) { ?>
												<a class="button button-primary" href="<?php echo esc_url( $demo['actions']['customize'] ); ?>"><?php esc_html_e( 'Theme Options', 'xkit' ); ?></a>
											<?php } ?>
										<?php } else { ?>
											<?php if ( !empty( $demo['live_preview_uri'] ) ) { ?>
												<a class="button button-primary" href="<?php echo esc_url( $demo['live_preview_uri'] ); ?>" target="_blank"><?php esc_html_e( 'Live Preview', 'xkit' ); ?></a>
											<?php } ?>
										<?php } ?>
									</div>
								</div>
								<?php
							endforeach;
							?>

							<br class="clear" />
						</div>
					</div>

					<div class="theme-overlay"></div>
				</div>
			</div><!-- .wrap -->
			<?php
			$this->customize_themes_print_templates();
		}


		/*
		 * Print JS templates for the demo-browsing UI in the Customizer. The template is synchronized with PHP above!
		 */
		public function customize_themes_print_templates() {
			?>
			<script id="tmpl-theme" type="text/template">
				<# if ( data.screenshot[0] ) { #>
					<div class="theme-screenshot">
						<img src="{{ data.screenshot[0] }}" alt="" />
					</div>
				<# } else { #>
					<div class="theme-screenshot blank"></div>
				<# } #>
				<span class="more-details" id="{{ data.id }}-action"><?php esc_html_e( 'Open Demo', 'xkit' ); ?></span>
				<div class="theme-author"><?php printf( esc_html__( 'By %s', 'xkit' ), '{{{ data.author }}}' ); ?></div>

				<# if ( data.active ) { #>
					<h3 class="theme-name" id="{{ data.id }}-name">
						<?php
						/* translators: %s: theme name */
						printf( wp_kses_post( __( '<span>Installed:</span> %s', 'xkit' ) ), '{{{ data.name }}}' );
						?>
					</h3>
				<# } else { #>
					<h3 class="theme-name" id="{{ data.id }}-name">{{{ data.name }}}</h3>
				<# } #>

				<div class="theme-actions">

				<# if ( data.active ) { #>
					<# if ( data.actions.customize ) { #>
						<a class="button button-primary" href="{{ data.actions.customize }}"><?php esc_html_e( 'Theme Options', 'xkit' ); ?></a>
					<# } #>
				<# } else { #>
					<# if ( data.live_preview_uri != '' ) { #>
						<a class="button button-secondary" href="{{{ data.live_preview_uri }}}" target="_blank"><?php esc_html_e( 'Live Preview', 'xkit' ); ?></a>
					<# } #>
				<# } #>

				</div>
			</script>

			<script id="tmpl-theme-single" type="text/template">
				<div class="theme-backdrop"></div>
				<div class="theme-wrap">
					<div class="theme-header">
						<button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show previous demo', 'xkit' ); ?></span></button>
						<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show next demo', 'xkit' ); ?></span></button>
						<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Close details dialog', 'xkit' ); ?></span></button>
					</div>
					<div class="theme-about">
						<div class="theme-screenshots">
						<# if ( data.screenshot[0] ) { #>
							<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
						<# } else { #>
							<div class="screenshot blank"></div>
						<# } #>
						</div>

						<div class="theme-info">
							<# if ( data.active ) { #>
								<span class="current-label"><?php esc_html_e( 'Current Demo', 'xkit' ); ?></span>
							<# } #>
							<h3 class="theme-name">{{{ data.name }}}<span class="theme-version"><?php printf( esc_html__( 'Version: %s', 'xkit' ), '{{ data.version }}' ); ?></span></h3>
							<h4 class="theme-author"><?php printf( esc_html__( 'By %s', 'xkit' ), '{{{ data.authorAndUri }}}' ); ?></h4>

							<p class="theme-description">{{{ data.description }}}</p>

							<div class="import-options">
								<h3 class="title"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Import options', 'xkit' ); ?></h3>

								<form action="<?php $_SERVER['REQUEST']; ?>" method="post">
									<input type="hidden" value="{{{ data.id }}}" name="demo_id">

									<table class="form-table">
										<tr>
											<th scope="row">
												<?php esc_html_e( 'Import Type', 'xkit' ); ?>
												<span><?php esc_html_e( 'Choose if you would like to import all or specific content', 'xkit' ); ?></span>
											</th>
											<td>
												<select id="import_type" name="import_type">
													<option value="full_content"><?php esc_html_e( 'Full Content', 'xkit' ); ?></option>
													<option value="custom_set"><?php esc_html_e( 'Custom Set', 'xkit' ); ?></option>
												</select>

												<div class="custom-import-set">
													<?php
														$import_set = $this->get_custom_set();
														if( ! empty( $import_set ) && is_array( $import_set ) ) {
															foreach( $import_set as $import_item => $val ) {
																?>
																	<label for="<?php echo esc_attr( $import_item ); ?>">
																		<input type="checkbox" id="<?php echo esc_attr( $import_item ); ?>" value="1" name="custom_set_array[<?php echo esc_attr( $import_item ); ?>]" checked>
																		<?php
																			if( $import_item == 'product' ) {
																				$import_item = 'Woocommerce';
																			}
																			if( $import_item == 'nav_menu_item' ) {
																				$import_item = 'Menu';
																			}

																			echo ucfirst( $import_item ); 
																		?>
																	</label>
																<?php
															}
														}
													?>
												</div>
											</td>
										</tr>
										<tr>
											<th scope="row">
												<?php esc_html_e( 'Import attachments?', 'xkit' ); ?>
												<span><?php esc_html_e( 'Do you want to import media files?', 'xkit' ); ?></span>
											</th>
											<td>
												<input type="checkbox" value="1" id="import-atachments" name="attachments" checked>
											</td>
										</tr>
									</table>
								</form>
							</div>

							<# if ( data.tags ) { #>
								<p class="theme-tags"><span><?php esc_html_e( 'Tags:', 'xkit' ); ?></span> {{{ data.tags }}}</p>
							<# } #>
						</div>
					</div>

					<div class="theme-actions">
						<div id="import-progress">
							<h4><?php esc_html_e( 'Importing...', 'xkit' ); ?></h4>
							<div class="progress-line"><span class="progress-title">0%</span></div>
						</div>

						<div class="active-theme">
							<a href="{{{ data.actions.customize }}}" class="button button-primary"><?php esc_html_e( 'Theme Options', 'xkit' ); ?></a>
							<a class="button button-secondary ajax-import" href="{{{ data.actions.activate }}}"><?php esc_html_e( 'ReInstall Demo', 'xkit' ); ?></a>
						</div>
						<div class="inactive-theme">
							<# if ( data.actions.activate ) { #>
								<a href="{{{ data.actions.activate }}}" class="button button-primary ajax-import"><?php esc_html_e( 'Install Demo', 'xkit' ); ?></a>
							<# } #>

							<# if ( data.live_preview_uri != '' ) { #>
								<a class="button button-secondary" href="{{{ data.live_preview_uri }}}" target="_blank"><?php esc_html_e( 'Live Preview', 'xkit' ); ?></a>
							<# } #>
						</div>
					</div>
				</div>
			</script>
			<?php
		}


		/*
		 * Instance get
		 */
		public static function get() {

			if ( !is_null( self::$instance ) ) {
				return self::$instance;
			}

			self::$instance = new self;

			return self::$instance;
		}
	}


	/*
	 * Helper function for the XS Demo Import Class.
	 */
	$Xkit_Demo_Import = Xkit_Demo_Import::get();


	/*
	 * Include Export
	 */
	if ( defined( 'XKIT_THEME_DEBUG' ) && class_exists('Xkit_Demo_Import') ) {
		if( XKIT_THEME_DEBUG == true ) {
			load_template( get_template_directory() . '/framework/modules/importer/exporter.php', true );
		}
	}
}