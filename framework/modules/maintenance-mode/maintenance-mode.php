<?php
/**
 * Maintenance Mode
 *
 * @package Xkit
 * @subpackage Maintenance Mode
 *
 * 1.0 - method __construct()
 * 2.0 - hook     admin_menu | admin_init()
 * 3.0 - hook     init       | maintenance_active()
 * 4.0 - method build_options_page()
 * 5.0 - method _maintenance_save()
 * 6.0 - method check_user_capability()
 * 7.0 - method is_url_excluded()
 */



if ( defined( 'XKIT_MAINTENANCE_MODULE_ENABLE' ) && XKIT_MAINTENANCE_MODULE_ENABLE  ) {

	class Xkit_Maintenance_Mode {

		protected $_exception_urls;
		protected $_options_slug;
		protected $_shortname;
		protected $_options_value;


		public function __construct(){

			$this->_exception_urls = array( 'wp-login.php', '/plugins/', 'wp-admin/', 'upgrade.php', 'trackback/', 'feed/' );
			$this->_options_slug   = 'maintenance-options-page';
			$this->_shortname      = 'maintenance_mode';
			add_action( 'admin_menu', array( &$this, 'admin_init' ) );
			add_action( 'init', array( &$this, 'maintenance_active' ) );

			$this->_options_value = array (

				array( 'type'    => 'title',
					'name'    => esc_html__( 'Maintenance Mode', 'xkit' ) ),

				array( 'type'    => 'open' ),

				array( 'id'      => $this->_shortname . '_enabled',
					'type'    => 'select',
					'options' => array( 'no' => esc_html__( 'no', 'xkit' ), 'yes' => esc_html__( 'yes', 'xkit' ) ),
					'name'    => esc_html__( 'Enable mode ?', 'xkit' ),
					'desc'    => esc_html__( 'Enabled Maintenance Mode the site will be closed to the public. While the site Administrator will be able to continue to browse the site.', 'xkit' ),
					'std'     => 'no' ),

				array( 'id'      => $this->_shortname . '_title_text',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xkit' ),
					'desc'    => esc_html__( 'Enter the title text.', 'xkit' ),
					'std'     => esc_html__( 'The site is in maintenance', 'xkit' ) ),

				array( 'id'      => $this->_shortname . '_general_text',
					'type'    => 'textarea',
					'name'    => esc_html__( 'Description', 'xkit' ),
					'desc'    => wp_kses_post( __( '<code>Allowed html-tags: &lt;a href=&quot;&quot; title=&quot;&quot;&gt;, &lt;strong&gt;, &lt;em&gt;</code>', 'xkit' ) ),
					'std'     => wp_kses_post( __( 'We apologize for any inconvenience. This is a routine technical check. We\'ll be back soon!'  , 'xkit' ) ) ),

				array( 'id'      => $this->_shortname . '_footer_text',
					'type'    => 'textarea',
					'name'    => esc_html__( 'Footer', 'xkit' ),
					'desc'    => wp_kses_post( __( '<code>Allowed html-tags: &lt;a href=&quot;&quot; title=&quot;&quot;&gt;, &lt;strong&gt;, &lt;em&gt;</code>', 'xkit' ) ),
					'std'     => esc_html__( 'All rights reserved 2016 &copy;', 'xkit' ) ),

				array( 'type'    => 'close' )

			);
		}


		/* Init admin page */
		public function admin_init(){
			add_theme_page( 'Maintenance mode',  esc_html__( 'Maintenance mode', 'xkit' ), 'manage_options', $this->_options_slug, array( &$this, 'build_options_page' ) );
			wp_enqueue_style( 'xkit-maintenance-settings', get_template_directory_uri() . '/framework/modules/maintenance-mode/css/maintenance-settings.css', false, '1.0', 'all' );
		}


		/* Maintenance active mode */
		public function maintenance_active() {
			if ( get_option( 'maintenance_mode_enabled' ) == 'yes' ) {
				if ( !$this->check_user_capability() && !$this->is_url_excluded() ){
					nocache_headers();
					header( 'HTTP/1.0 503 Service Unavailable' );
					load_template( get_template_directory() . '/framework/modules/maintenance-mode/maintenance-page.php' );
					exit();
				}
			}
		}

		/* Build admin page */
		public function build_options_page(){

			if ( !current_user_can('manage_options') ){
				wp_die( esc_html__('You do not have sufficient rights to view this page.', 'xkit' ) );
			}

			if ( isset( $_REQUEST['saved'] ) ) { 
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'The settings are saved.', 'xkit' ) . '</strong></p></div>'; 
			}

			if ( isset( $_POST['save_settings'] ) ){
				$this->_maintenance_save( $_POST );
			}

			?>
				<div class="wrap mm_wrap">
					<h2><?php esc_html_e( 'Maintenance mode site', 'xkit' ); ?></h2>

					<div class="mm_opts">
						<form method="post">
							<?php
								foreach ( $this->_options_value as $value ) {

									switch ( $value['type'] ) {
										case 'title': break;

										case 'open' : print( '<div>' ); break;

										case 'close': print( '</div>' ); break;

										case 'section': print( '<h3>' . $value['name'] . '</h3>' ); break;

										case 'text': 
										?>
											<div class="mm_input mm_text">
												<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo wp_kses_post( $value['name'] ); ?></label>
												<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" value="<?php echo stripslashes( htmlspecialchars( get_option( $value['id'], $value['std'] ),ENT_QUOTES ) ); ?>" />
												<small><?php echo wp_kses_post( $value['desc'] ); ?></small><div class="clearfix"></div>
											</div>
										<?php
										break;

										case 'textarea':
										?>
											<div class="mm_input mm_textarea">
												<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo wp_kses_post( $value['name'] ); ?></label>
												<textarea name="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" cols="" rows=""><?php echo stripslashes( htmlspecialchars( get_option( $value['id'], $value['std'] ),ENT_QUOTES ) ); ?></textarea>
												<small><?php echo wp_kses_post( $value['desc'] ); ?></small><div class="clearfix"></div>
											</div>
										<?php
										break;

										case 'select':
										?>
											<div class="mm_input mm_select">
												<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo wp_kses_post( $value['name'] ); ?></label>

												<select name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>">
													<?php foreach ( $value['options'] as $key => $option ): ?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( $value['id'] ), esc_attr( $key ) ); ?>><?php echo esc_attr( $option ); ?></option>
													<?php endforeach; ?>
												</select>

												<small><?php echo wp_kses_post( $value['desc'] ); ?></small><div class="clearfix"></div>
											</div>
										<?php
										break;

										case 'checkbox':
										?>
											<div class="mm_input mm_checkbox">
												<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo wp_kses_post( $value['name'] ); ?></label>
												<input type="checkbox" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" value="1" <?php checked( get_option( $value['id'] ), 1 ); ?> />
												<small><?php echo wp_kses_post( $value['desc'] ); ?></small><div class="clearfix"></div>
											</div>
										<?php
										break;

									}
								}
							?>

							<div style="clear:both;"></div>

							<input class="maintenance-button button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Save changes', 'xkit' ); ?>" />
						</form>
					</div>
				</div>
			<?php
		}


		/* Maintenance settings save */
		protected function _maintenance_save( $form_data ) {
			foreach ( $form_data as $k => $v ) {
				if ( !( $k == 'save_settings' ) ) {
					update_option( $k, stripslashes( $v ) );
				}
			}
		}


		/* Maintenance capability */
		public function check_user_capability(){
			if ( is_super_admin() || current_user_can( 'manage_options' ) ){
				return true;
			}

			return false;
		}


		/* IS url excluded */
		public function is_url_excluded() {
			foreach ( $this->_exception_urls as $url ){
				if ( strstr( $_SERVER['PHP_SELF'], $url) ){
					return true;
				}
			}
			if ( strstr( $_SERVER['QUERY_STRING'], 'feed=') ){
				return true;
			}
			return false;
		}
	}


	/*
	 * Init Maintenance Mode
	 */
	add_action( 'init', function (){
		global $xkit_theme_maintenance;

		$xkit_theme_maintenance = new Xkit_Maintenance_Mode();
	}, 5 );

}
?>