<?php

/**
 * Theme Functions
 *
 * @author Xspire group
 * @link http://xspirestudio.com/
 */

define( 'XKIT_BLOG_NAME', get_bloginfo( 'name' ) );
define( 'XKIT_BLOG_DESCRIPTION', get_bloginfo( 'description' ) );
define( 'XKIT_THEME_NAME', 'Xkit' );
define( 'XKIT_THEME_VERSION', '1.0' );
define( 'XKIT_THEME_DEBUG', true );


echo 'Updated upstream';

echo 'Update sasha';

echo 'Update plx';


/*
 * LOAD FRAMEWORK
 */
require_once( get_template_directory() . '/framework/framework.php' );


/*
 * THEME START
 */
class Xkit_Theme extends Xkit {
echo '3333333333333';

echo '44444444';
	public function init() {

		define( 'XKIT_OPTIONS_MODULE_ENABLE', true );
		define( 'XKIT_TGMPA_MODULE_ENABLE', true );
		define( 'XKIT_IMPORTER_MODULE_ENABLE', true );
		define( 'XKIT_BFI_THUMB_MODULE_ENABLE', true );
		define( 'XKIT_RUSTOLAT_MODULE_ENABLE', true );
		define( 'XKIT_MEGA_MENU_MODULE_ENABLE', true );
		define( 'XKIT_MAINTENANCE_MODULE_ENABLE', true );
		define( 'XKIT_PRELOADER_MODULE_ENABLE', true );
		define( 'XKIT_PAGINATION_MODULE_ENABLE', true );
		define( 'XKIT_SOCIAL_SHARE_MODULE_ENABLE', true );
		define( 'XKIT_SIDEBARGEN_MODULE_ENABLE', true );


		/*
		 * Load Theme Textdomain
		 */
		function xkit_load_theme_textdomain() {
			load_theme_textdomain( 'xkit', get_template_directory() . '/languages' );
		}
		add_action( 'after_setup_theme', 'xkit_load_theme_textdomain' );

		
		/*
		 * Load Xkitl10n
		 */
		function xkit_load_textdomain() {
			return 'xkit';
		}
		add_filter( 'acf/settings/l10n_textdomain', 'xkit_load_textdomain' );


		/* BBP Notice Fix */
		if( function_exists( 'bbp_setup_current_user' ) ) {
			remove_action( 'set_current_user', 'bbp_setup_current_user' );
			add_action( 'init', 'bbp_setup_current_user', 10 );
		}
		
		
		echo 111111111111111111111111;


		/* CORE */
		parent::init();


		/*
		 * Includes
		 */
		get_template_part( 'includes/auth-user' );
		get_template_part( 'includes/customizer' );
		xkit_autoload_files( get_template_directory() . '/includes/controllers' );
		xkit_autoload_files( get_template_directory() . '/includes/settings' );
		xkit_autoload_files( get_template_directory() . '/includes/widgets' );


		/*
		 * Set the content width based on the theme's design and stylesheet.
		 */
		if ( ! isset( $content_width ) ) {
			$content_width = 1080;
		}


		/*
		 * Include Sripts & Styles
		 */
		function xkit_frontend_enqueue_scripts() {

			/* ======== Scripts ======== */
			// De Register Scripts
			if ( class_exists( 'woocommerce' ) ) {
				wp_dequeue_script( 'select2' );
				wp_deregister_script( 'select2' );
			}

			// Enqueue Scripts Libraries
			wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/framework/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'placeholders', get_template_directory_uri() . '/framework/assets/js/placeholders.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'select2', get_template_directory_uri() . '/framework/assets/js/jquery.select2.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'owl-carousel',  get_template_directory_uri() . '/framework/assets/js/jquery.owl.carousel.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'jquery-easing', get_template_directory_uri() . '/framework/assets/js/jquery.easing-1.3.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'validate', get_template_directory_uri() . '/framework/assets/js/jquery.validate.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			
			// Enqueue Custom Scripts
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script('comment-reply');
			}
			if ( get_locale() == 'ru_RU' ) {
				wp_enqueue_script( 'xkit-validate-messages_ru', get_template_directory_uri() . '/framework/assets/js/jquery.validate.messages_ru.min.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			}
			wp_enqueue_script( 'xkit-cookie', get_template_directory_uri() . '/framework/assets/js/cookie.js', array( 'jquery' ) );
			wp_enqueue_script( 'xkit-loadmore', get_template_directory_uri() . '/js/xkit-loadmore.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			wp_enqueue_script( 'xkit-init', get_template_directory_uri() . '/js/xkit-init.js', array( 'jquery' ), XKIT_THEME_VERSION, true );
			
			
			/* ======== Styles ======== */
			// Deregister Styles
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
			wp_deregister_style( 'contact-form-7' );
			wp_deregister_style( 'font-awesome' );

			// Enqueue Styles Libraries
			wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/framework/assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'icomoon', get_template_directory_uri() . '/framework/assets/css/icomoon.css' );
			wp_enqueue_style( 'select2', get_template_directory_uri() . '/framework/assets/css/select2.css' );
			wp_enqueue_style( 'animate', get_template_directory_uri() . '/framework/assets/css/animate.min.css' );
			wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/framework/assets/css/owl.carousel.css' );
			wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/framework/assets/css/magnific-popup.css' );
			
			// Enqueue Custom Styles
			wp_enqueue_style( 'xkit-grid', get_template_directory_uri() . '/css/grid.css' );
			wp_enqueue_style( 'xkit-style', get_template_directory_uri() . '/style.css' );

			// Enqueue Custom Fonts
			xkit_wp_enqueue_google_font( 'Open+Sans:400,600,700' );
		}
		add_action( 'wp_enqueue_scripts', 'xkit_frontend_enqueue_scripts', 200 );


		/*
		 * Editor Styles
		 */
		function xkit_add_custom_editor_styles() {
			add_editor_style( 'css/editor-styles.css' );
		}
		add_action( 'current_screen', 'xkit_add_custom_editor_styles' );

		
		/*
		 * Dynamic Styles Path
		 */
		add_filter( 'xkit_dynamic_styles_path', function( $path_files ) {
			return array(
				get_template_directory() . '/includes/dynamic-styles.php' => 'xkit-style'
			);
		} );


		/*
		 * Site Preloader
		 *
		 * @param mixed $content
		 * @return mixed $content
		 */
		function xkit_preloader_url() {
			return get_template_directory_uri() . '/images/preloader.gif';
		}
	
		$site_preloader = xkit_get_theme_option( 'site_preloader', false );
		if( !$site_preloader ) {
			add_filter( 'xkit_preloader_include', '__return_false' );
		} else {
			add_filter( 'xkit_default_loader_url', 'xkit_preloader_url' );
		}


		/*
		 * Register Nav Menu
		 */
		register_nav_menu( 'main_menu', 'Main menu' );


		/*
		 * Main Mega Menu
		 */
		if( function_exists( 'xkit_register_mega_menu' ) ) {

			/*
			 * Menu Thumbnail Item
			 *
			 * @param bool $show
			 * @param string $key
			 * @param object $item
			 * @return bool $show
			 */
			function xkit_menu_thumb_item( $show, $key, $item ){
				if( $key == 'display_post_thumbnail' ) {
					if( $item->object == 'post' || $item->object == 'page' || $item->object == 'portfolio' || $item->object == 'product' ) {
						return true;
					} else {
						return false;
					}
				}

				return $show;
			}


			/*
			 * Menu Thumbnail Sizes
			 *
			 * @param array $thumb_args
			 * @return array $thumb_args
			 */
			function xkit_menu_thumb_sizes( $thumb_args ) {
				$thumb_args['image_size'] ='xkit-image-size-299x187';

				return $thumb_args;
			}
			add_filter( 'xkit_mega_menu_thumb_settings' , 'xkit_menu_thumb_sizes' );


			/*
			 * Header Mega Menu
			 */
			function xkit_header_mega_menu() {
				$location = 'main_menu';
				$mega_menu_fields = array(
					'menu_link'		=> array(
						'type'		=> 'checkbox',
						'label'		=> esc_html__( 'Display without link?', 'xkit' ),
						'depth' 	=> 'any'
					),
					'display_post_thumbnail' => array(
						'type'		=> 'checkbox',
						'label'		=> esc_html__( 'Display post thumbnail', 'xkit' ),
						'depth' 	=> '2,3',
					),
					'menu_columns' => array(
						'type'		=> 'select',
						'label'		=> esc_html__( 'Columns', 'xkit' ),
						'depth' 	=> 0,
						'options'	=> array(
							'1'	=> 1,
							'2'	=> 2,
							'3'	=> 3,
						),
						'default_value' => 1
					),
					'menu_icon' => array(
						'type'		=> 'icons',
						'label'		=> esc_html__( 'Icon', 'xkit' ),
					),
				);

				xkit_register_mega_menu( $mega_menu_fields, $location );

				add_filter( 'xkit_admin_mega_menu_checkbox_show', 'xkit_menu_thumb_item', 10, 3 );
			}
			add_action( 'init', 'xkit_header_mega_menu' );
		}
		
		
		/*
		 * Mega Menu - Add Sub Menu Arrows
		 *
		 * @param  string $item_output  The menu item's starting HTML output.
		 * @param  object $item 		Menu item data object.
		 * @param  int	  $depth		Depth of menu item. Used for padding.
		 * @param  array  $args			An array of wp_nav_menu() arguments.
		 * @return string $item_output 	The menu HTML
		 */
		function xkit_menu_arrows( $item_output, $item, $depth, $args ) {
			if( $args->theme_location == 'main_menu' ) {
				if( in_array( 'menu-item-has-children', (array) $item->classes ) ) {
					
					// Icon
					if( $depth == 0 ) {
						$replace_icon = '<i class="fa fa-chevron-down arrow"></i>';
					}
					else {
						$replace_icon = '<i class="fa fa-chevron-right arrow"></i>';
					}
					
					// Replace icon
					$item_output = preg_replace( '/(item-link.*?)(<\/(a|div)>)/Uis', '$1' . $replace_icon . '$2', trim( $item_output ) );
					return $item_output;
				}
			}
			
			return $item_output;
		}
		add_filter( 'walker_nav_menu_start_el', 'xkit_menu_arrows', 10, 4 );


		/*
		 * Add Theme Support
		 */
		if ( function_exists( 'add_theme_support' ) ) {

			// Add Theme Support
			add_theme_support( 'post-thumbnails' );

			// Post Formats
			add_theme_support( 'post-formats', array( 'audio', 'video', 'gallery', 'link', 'quote' ) );

			// Automatic Feed Links
			add_theme_support( 'automatic-feed-links' );

			// Check add_theme_support fix
			add_theme_support( 'custom-header' );
			add_theme_support( 'custom-background' );
			add_theme_support( 'title-tag' );

			remove_theme_support( 'custom-header' );
			remove_theme_support( 'custom-background' );
		}


		/*
		 * Thumbnails Sizes
		 */
		if ( function_exists( 'add_image_size' ) ) {

			// Single Post
			add_image_size( 'xkit-image-size-1347x758', 1347, 758, true );
		}


		/*
		 * Custom Site Icon Sizes
		 * 
		 * @param  array $sizes
		 * @return array $sizes
		 */
		function xkit_custom_site_icon_sizes( $sizes ) {
			array_push( $sizes, 57, 76, 120, 152 );

			return $sizes;
		}
		add_filter( 'site_icon_image_sizes', 'xkit_custom_site_icon_sizes' );


		/*
		 * Custom Site Icon Tags
		 */
		function xkit_custom_site_icon_tags( $meta_tags ) {
			array_push( $meta_tags,
				sprintf( '<link rel="apple-touch-icon" href="%s">', esc_url( get_site_icon_url( 57 ) ) ),
				sprintf( '<link rel="apple-touch-icon" href="%s" sizes="76x76">', esc_url( get_site_icon_url( 76 ) ) ),
				sprintf( '<link rel="apple-touch-icon" href="%s" sizes="120x120">', esc_url( get_site_icon_url( 120 ) ) ),
				sprintf( '<link rel="apple-touch-icon" href="%s" sizes="152x152">', esc_url( get_site_icon_url( 152 ) ) )
			);

			return $meta_tags;
		}
		add_filter( 'site_icon_meta_tags', 'xkit_custom_site_icon_tags' );


		/*
		 * Register Sidebars
		 */
		function xkit_theme_widgets_init() {

			// Main Sidebar
			register_sidebar( array(
				'name'  		=> esc_html__( 'Main Sidebar', 'xkit' ),
				'id'  			=> 'main',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' 	=> '</div>',
				'before_title' 	=> '<h3 class="widget-title">',
				'after_title' 	=> '</h3>'
			) );

			// Secondary Sidebar
			register_sidebar( array(
				'name'  		=> esc_html__( 'Secondary Sidebar', 'xkit' ),
				'id'  			=> 'secondary',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' 	=> '</div>',
				'before_title' 	=> '<h3 class="widget-title">',
				'after_title' 	=> '</h3>'
			) );

			// Footer Widget Area
			register_sidebar( array(
				'name'  		=> esc_html__( 'Footer Sidebar', 'xkit' ),
				'id'  			=> 'footer',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' 	=> '</div>',
				'before_title' 	=> '<h3 class="widget-title">',
				'after_title' 	=> '</h3>'
			) );
		}
		add_action( 'widgets_init', 'xkit_theme_widgets_init' );


		/*
		 * Sidebars Generator Widgets Wrap
		 *
		 * @param  array $cs_args
		 * @return array $cs_args
		 */
		function xkit_custom_sidebars_widgets_wrap( $cs_args ) {
			$cs_args = array(
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
			);

			return $cs_args;
		}
		add_filter( 'xkit_cs_before_title', 'xkit_custom_sidebars_widgets_wrap' );


		/*
		 * Is Live Sidebar
		 *
		 * @param  string $sidebar_name
		 * @return bool $live
		 */
		function xkit_is_live_sidebar( $sidebar_name ) {
			global $wp_registered_sidebars;
			$live = false;

			if ( is_array( $sidebar_name ) ) {
				foreach ( $sidebar_name as $sidebar ) {
					if ( array_key_exists( $sidebar, $wp_registered_sidebars ) && is_active_sidebar( $sidebar ) ) {
						$live = true;
					}
				}
			} else {
				if ( array_key_exists( $sidebar_name, $wp_registered_sidebars ) && is_active_sidebar( $sidebar_name ) ) {
					$live = true;
				}
			}

			return $live;
		}


		/*
		 * Seo Meta Tags for Post Types
		 */
		add_filter( 'xkit_seo_meta_tags_post_types', function( $screens ) {
			return array( 'post', 'page', 'portfolio', 'product' );
		} );


		/*
		 * Filter whether comments are open for a given post type.
		 *
		 * @param string $status       Default status for the given post type,
		 *                             either 'open' or 'closed'.
		 * @param string $post_type    Post type. Default is <code>post</code>.
		 * @param string $comment_type Type of comment. Default is <code>comment</code>.
		 * @return string (Maybe) filtered default status for the given post type.
		 */
		function xkit_allow_page_comments( $status, $post_type, $comment_type ) {
			if ( 'page' !== $post_type ) {
				return $status;
			}

			return 'open';
		}
		add_filter( 'get_default_comment_status', 'xkit_allow_page_comments', 10, 3 );
		

		/*
		 * Requests Decompress Fix
		 */
		function xkit_request_decompress_fix( $request ){
			$request['decompress'] = false;
			return $request;
		}
		add_filter( 'http_request_args', 'xkit_request_decompress_fix' );


		/*
		 * Add Font Size & Font Family to Editor
		 */
		function xkit_mce_buttons( $buttons ) {
			array_unshift( $buttons, 'fontselect' ); // Add Font Select
			array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select

			return $buttons;
		}
		function xkit_mce_text_sizes( $args ){
			$args['fontsize_formats'] = "9px 10px 12px 13px 14px 16px 18px 20px 22px 24px 28px 32px 36px 40px 48px 52px 56px 60px 64px";
			return $args;
		}
		add_filter( 'mce_buttons_2', 'xkit_mce_buttons' );
		add_filter( 'tiny_mce_before_init', 'xkit_mce_text_sizes' );


		/*
		 * Widget "Tag Cloud"
		 */
		function xkit_widget_tag_cloud_per_page( $args ) {
			$args['number'] = 25;

			return $args;
		}
		add_filter( 'widget_tag_cloud_args', 'xkit_widget_tag_cloud_per_page' );


		/*
		 * Custom Portfolio Column Create
		 */
		function xkit_portfolio_columns_create( $columns ) {
			$column_thumbnail = array( 
				'thumbnail' => esc_html__( 'Thumbnail', 'xkit' )
			);
			$columns = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, NULL, true );

			return $columns;
		}
		add_filter( 'manage_edit-portfolio_columns', 'xkit_portfolio_columns_create' );


		/*
		 * Custom Portfolio Column Content
		 */
		function xkit_portfolio_columns_content( $column_name, $post_id ) {
			if ( 'thumbnail' == $column_name ){
				if ( has_post_thumbnail( $post_id ) ) {
					$post = '<a href="' . get_edit_post_link( $post_id ) .'">' . get_the_post_thumbnail( $post_id, 'xkit-image-size-100x100' ) . '</a>';

					echo wp_kses_post( $post );
				}
			}
		}
		add_filter( 'manage_portfolio_posts_custom_column', 'xkit_portfolio_columns_content', 10, 3 );


		/*
		 * Custom Testimonials Columns Create
		 */
		function xkit_testimonials_columns_create( $columns ) {
			$column_thumbnail = array( 
				'avatar' => esc_html__( 'Avatar', 'xkit' )
			);
			$columns = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, NULL, true );

			return $columns;
		}
		add_filter( 'manage_edit-testimonials_columns', 'xkit_testimonials_columns_create' );


		/*
		 * Custom Testimonials Columns Content
		 */
		function xkit_testimonials_columns_content( $column_name, $post_id ) {
			if ( 'avatar' == $column_name ){
				if ( has_post_thumbnail( $post_id ) ) {
					$post = '<a href="' . get_edit_post_link( $post_id ) .'">' . get_the_post_thumbnail( $post_id, 'xkit-image-size-100x100' ) . '</a>';

					echo wp_kses_post( $post );
				}
			}
		}
		add_filter( 'manage_testimonials_posts_custom_column', 'xkit_testimonials_columns_content', 10, 3 );
		
		
		/*
		 * Add Content Formating to Post
		 */
		function xkit_posts_formatting_class( $classes ) {
			if( is_singular() ) {
				$classes[] = 'content-formatting';
			}
			
			return $classes;
		}
		add_filter( 'post_class', 'xkit_posts_formatting_class' );
		
		
		/*
		 * AJAX Load Posts
		 */
		function xkit_ajax_pagination_template_redirect(){
			if( isset( $_POST['xkit_ajax_pagination'] ) ) {
				global $wp_query;
				
				// Posts HTML
				ob_start();				
					while ( have_posts() ) : the_post();
						get_template_part( 'includes/templates/' . xkit_get_controller( 'post_content_template' ) );
					endwhile;
				$posts_html = ob_get_clean();
				
				// Next Page				
				$next_page = false;
				if( $next_posts_page = next_posts( $wp_query->max_num_pages, false ) ) {
					$next_page = $next_posts_page;
				}				
				
				// Return Result
				$response = array(
					'html'		=> $posts_html,
					'next_page' => $next_page
				);
				wp_send_json( $response );
			}
		}
		add_action( 'template_redirect', 'xkit_ajax_pagination_template_redirect' );
	}
}

$Xkit_Theme = new Xkit_Theme();


/*
 * REQUIRE PLUGINS
 */
if( defined( 'XKIT_TGMPA_MODULE_ENABLE' ) && XKIT_TGMPA_MODULE_ENABLE ) {
	function xkit_framework_require_plugins() {

		$plugins = array(
			array(
				'name'                => 'Xkit Theme CPT',
				'slug'                => 'custom-post-types',
				'source'              => get_template_directory() . '/plugins/custom-post-types.zip',
				'version'             => '1.0',
				'required'            => true,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),

			array(
				'name'                => 'ACF | Advanced Custom Fields',
				'slug'                => 'advanced-custom-fields-pro',
				'source'              => get_template_directory() . '/plugins/advanced-custom-fields-pro.zip',
				'version'             => '5.3.7',
				'required'            => true,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),

			array(
				'name'                => 'Page Builder by SiteOrigin',
				'slug'                => 'siteorigin-panels',
				'required'            => true,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),
			
			array(
				'name'                => 'Slider Revolution',
				'slug'                => 'revslider',
				'source'              => get_template_directory() . '/plugins/revslider.zip',
				'version'             => '5.2.5.1',
				'required'            => false,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),

			array(
				'name'                => 'Envato WordPress Toolkit',
				'slug'                => 'envato-wordpress-toolkit-master',
				'source'              => 'https://github.com/envato/envato-wordpress-toolkit/archive/master.zip',
				'version'             => '1.7.3',
				'required'            => false,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),

			array(
				'name'                => 'Contact form 7',
				'slug'                => 'contact-form-7',
				'required'            => false,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),

			array(
				'name'                => 'WooCommerce',
				'slug'                => 'woocommerce',
				'required'            => false,
				'force_activation'    => false,
				'force_deactivation'  => false,
			),
		);

		$config = array( 
			'id'           => 'theme-tgmpa',
			'default_path' => '',
			'menu'         => 'theme-install-plugins',
			'parent_slug'  => 'themes.php',
			'capability'   => 'edit_theme_options',
			'has_notices'  => true,
			'dismissable'  => false,
			'dismiss_msg'  => '',
			'is_automatic' => true,
			'message'      => '',
			'strings'      => array(
				'page_title'  => esc_html__( 'Install Required Plugins', 'xkit' ),
				'menu_title'  => esc_html__( 'Install Plugins', 'xkit' ),
				'nag_type'    => 'error',
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'xkit' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'xkit' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'xkit' ),
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'xkit' ),
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'xkit' ),
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'xkit' ),
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'xkit' ),
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'xkit' ),
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'xkit' ),
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'xkit' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'xkit' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'xkit' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'xkit' ),
			)
		);

		tgmpa( $plugins, $config );
	}
	add_action( 'tgmpa_register', 'xkit_framework_require_plugins' );
}