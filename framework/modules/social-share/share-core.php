<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class For Social Share Buttons
 * 
 * 1.0 - hook    wp_enqueue_scripts          | enqueue_scripts()
 * 2.0 - hook    init                        | load_localisation()
 * 3.0 - method  get_providers()
 * 4.0 - method  wp_ajax_get_social_counters | get_social_counters()
 * 5.0 - method  get_template()
 * 6.0 - method  build_share_buttons()
 * 7.0 - method  share_post_shortcode()
 * 8.0 - method  share_post_shortcode()
 */
class Xkit_Social_Share_Buttons {

	/*
	 * The single instance of Xkit_Social_Share_Buttons.
	 *
	 * @var static
	 */
	private static $_instance = null;


	/*
	 * Settings class object
	 *
	 * @var array
	 */
	public $options = array();


	/*
	 * The module version
	 *
	 * @var string
	 */
	public $_version;


	/*
	 * Current Url
	 *
	 * @var string
	 */	
	private $current_url = '';


	/*
	 * Constructor function.
	 */
	public function __construct () {
		$this->_version = '1.0';

		$this->options = array(
			'template'		=> 'default',
			'providers'		=> 'any'
		);
		
		$this->current_url = preg_replace( '/\?.*/', '', home_url( add_query_arg( NULL, NULL ) ) );

		/* Load frontend scripts & styles */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		/* Create shortcode */
		xkit_create_shortcode( 'social_share', array( $this, 'share_post_shortcode' ) );

		/* Ajax */
		add_action( 'wp_ajax_get_social_counters', array( $this, 'get_social_counters' ) );
		add_action( 'wp_ajax_nopriv_get_social_counters', array( $this, 'get_social_counters' ) );
	}


	/*
	 * Load frontend scripts.
	 */
	public function enqueue_scripts() {

		/* Scripts */
		wp_enqueue_script( 'xkit-prefix-social-share', get_template_directory_uri() . '/framework/modules/social-share/assets/js/social-share.js', array('jquery'), $this->_version, true );

		/* Styles */
		wp_enqueue_style( 'xkit-prefix-social-share', get_template_directory_uri() . '/framework/modules/social-share/assets/css/social-share.css', array(), $this->_version );
	}


	/*
	 * Main Xkit_Social_Share_Buttons Instance
	 *
	 * Ensures only one instance of Xkit_Social_Share_Buttons is loaded or can be loaded.
	 *
	 * @return Main Xkit_Social_Share_Buttons instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/*
	 * Return the providers of shares
	 * 
	 * @return  array
	 */
	public function get_providers() {
		$providers   = array();
		$providers = apply_filters( 'xkit_social_providers', $providers, $this->current_url );

		/* Filter providers */
		$included_providers = $this->options['providers'];
		if( !empty( $included_providers ) ) {
			if( is_string( $included_providers ) ) {
				if( $included_providers !== 'any' ) {
					$new_providers 		= explode( ',', $included_providers );
					$new_providers 		= array_map( 'trim', $new_providers );
					$new_providers		= array_flip( $new_providers );
					$filtered_providers = array_intersect_key( $providers, $new_providers );
					$providers 			= array_merge( $new_providers, $filtered_providers );
				}
			}
			elseif( is_array( $included_providers ) ) {
				$new_providers		= array_flip( $included_providers );
				$filtered_providers = array_intersect_key( $providers, $included_providers );
				$providers 			= array_merge( $new_providers, $filtered_providers );
			}
		}

		/* Return providers */
		return $providers;
	}


	/*
	 * Return providers counters
	 * 
	 * @param  string  $current_url
	 * @return json
	 */
	public function get_social_counters() {
		$cache = wp_cache_get( 'xkit_social_counters' );
		if ( $cache ){
			return print( $cache );
		}
		
		if( isset( $_POST['current_url'] ) ) {
			$current_url = preg_replace( '/\?.*/', '', esc_url( $_POST['current_url'] ) );
		}
		else {
			$current_url = $this->current_url;
		}
		
		$counters = apply_filters( 'xkit_social_counters', array(), $current_url );
		echo json_encode( $counters );

		wp_cache_add( 'xkit_social_counters', json_encode( $counters ) );
		die();
	}


	/*
	 * Get template HTML
	 * 
	 * @param  string  $template_name
	 * @param  array   $providers
	 * @return string  template HTML
	 */
	public function get_template( $template_name = '', $providers ) {

		$templates = array();
		$templates = apply_filters( 'xkit_share_buttons_templates', $templates, $providers );

		if( array_key_exists( $template_name, $templates ) ) {
			$template = $templates[$template_name];
		}
		else{
			$template = $templates['default'];
		}

		return $template;
	}


	/*
	 * Build the html the share button component
	 *
	 * @param  array  $options
	 * @return string HTML
	 */
	public function build_share_buttons( $options = array() ) {

		/* Set options */
		$this->options = array_merge( $this->options, $options );

		/* Get providers */
		$providers = $this->get_providers();

		/* Share buttons template */
		$template  = $this->get_template( $this->options['template'], $providers );

		/* Return buttons html */
		ob_start();

		echo wp_unslash( $template );
		do_action( 'xkit_share_buttons_html', $providers, $template );
		$buttons_html = ob_get_contents();

		ob_end_clean();

		return $buttons_html;
	}


	/*
	 * Shortcode for adding the sharing buttons to content or templates
	 * [social_share], [social_share template="default" providers="any"], <?php echo do_shortcode('[social_share template="default" providers="any"]'); ?>
	 *
	 * @param  array  $atts
	 * @param  string $content
	 * @return string shortcode HTML
	 */
	public function share_post_shortcode( $atts, $content = null ) {
		$options = shortcode_atts( $this->options, $atts );

		return $this->build_share_buttons( $options );
	}
}