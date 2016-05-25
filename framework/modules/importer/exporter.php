<?php
/**
 * Name: Export Demo Data
 * Version: 1.1
 * Author: Xspire
 *
 * 1.0   - method export_widgets()
 * 2.0   - method export_sidebars()
 * 3.0   - method exclude_sidebar_keys()
 * 4.0   - method export_widgets_sidebars()
 * 5.0   - method export_acf_options()
 * 6.0   - method export_visual_composer()
 * 7.0   - method export_menus()
 * 8.0   - method export_settings_pages()
 * 9.0   - method export_all()
 * 10.0  - method register_export_page()
 * 11.0  - method export_page_html()
 */



/*
 * Class For Export Demo Data
 */
class Xkit_Demo_Export Extends Xkit_Demo_Import {

	/*
	 * Instance
	 */
	private static $instance;


	/*
	 * Demos page slug
	 *
	 * @var string
	 */
	public $export_page_slug = 'export-demo';


	/*
	 * Constructor. Set up values and settings.
	 */
	public function __construct() {

		/* Set demos dir */
		$this->demos_path = get_template_directory() . '/includes/demos/';
		$this->demos_uri = get_template_directory_uri() . '/includes/demos/';

		/* Load page */
		add_action('admin_menu', array($this, 'register_export_page'));

		/* Enqueue scripts */
		if( $this->is_module_page( $this->export_page_slug ) ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_scripts' ) );
		}
	}


	/*
	 * admin_enqueue_scripts | load_admin_scripts()
	 *
	 * Load scripts for Demos page
	 */
	public function load_admin_scripts() {

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
	 * Exports widgets
	 *
	 * @return array
	 */
	public function export_widgets() {
		global $wp_registered_widgets;

		$all_widgets = array();

		foreach ( $wp_registered_widgets as $widget_id => $widget_params ) {
			$all_widgets[] = $widget_params['callback'][0]->id_base;
		}

		foreach ( $all_widgets as $widget_id ) {
			$widget_data = get_option( 'widget_' . $widget_id );

			if ( !empty( $widget_data ) ) {
				$widget_datas[ 'widget_' . $widget_id ] = $widget_data;
			}
		}

		unset($all_widgets);

		return $widget_datas;
	}


	/*
	 * Exports sidebars
	 *
	 * @return array
	 */
	public function export_sidebars() {
		$sidebars_list = get_option('sidebars_widgets');
		$sidebars_list = $this->exclude_sidebar_keys( $sidebars_list );

		return $sidebars_list;
	}


	/*
	 * Exclude sidebars keys
	 *
	 * @param  array $keys
	 * @return array
	 */
	private function exclude_sidebar_keys( $keys = array() ) {
		if ( !is_array( $keys ) ) {
			return $keys;
		}

		unset( $keys['wp_inactive_widgets'] );
		unset( $keys['array_version'] );

		return $keys;
	}


	/*
	 * Exports sidebars & widgets
	 *
	 * @return array
	 */
	public function export_widgets_sidebars() {
		$output = array();
		$custom_sidebars = get_option( 'cs_sidebars' );
		$cs_modifiable = get_option( 'cs_modifiable' );

		// Sidebars options
		$output['sidebars'] = array(
			'sidebars_widgets'	=> $this->export_sidebars(),
			'cs_sidebars'		=> $custom_sidebars,
			'cs_modifiable' 	=> $cs_modifiable
		);

		// Widgets options
		$output['widgets'] = $this->export_widgets();

		return $output;
	}



	/*
	 * Export Advanced Custom Fields options
	 *
	 * @return array
	 */
	public function export_acf_options() {
		global $wpdb;
		$options_table = $wpdb->prefix . 'options';
		$acf_options = array();
		$b_acf_options = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table WHERE option_name LIKE 'options_%'", ARRAY_A );
		$t_acf_options = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table WHERE option_name LIKE '_options_%'", ARRAY_A );
		$d_acf_options = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table WHERE option_name='theme-option-defaults'", ARRAY_A );

		$all_options = array_merge( $b_acf_options, $t_acf_options );
		$all_options = array_merge( $all_options, $d_acf_options );

		// Add acf options
		if( !empty( $all_options ) && is_array( $all_options ) ) {
			foreach( $all_options as $acf_option ) {
				$acf_options['acf_options'][ $acf_option['option_name'] ] = $acf_option['option_value'];
			}
		}

		return $acf_options;
	}



	/*
	 * Export Visual Composer options
	 *
	 * @return array
	 */
	public function export_visual_composer() {
		global $wpdb;
		$vc_options = array();
		$options_table = $wpdb->prefix . 'options';
		$ultimate_options = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table WHERE option_name LIKE 'ultimate_%'", ARRAY_A );
		$wpb_js_options = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table WHERE option_name LIKE 'wpb_js_%'", ARRAY_A );

		$all_options = array_merge( $ultimate_options, $wpb_js_options );
		$exclude_array = array('ultimate_google_fonts', 'ultimate_updater', 'wpb_js_composer_license_activation_notified' );

		// Add vc options
		if( !empty( $all_options ) && is_array( $all_options ) ) {
			foreach( $all_options as $vc_option ) {
				if( !in_array( $vc_option['option_name'], $exclude_array ) ) {
					$vc_options['vc_options'][$vc_option['option_name']] = $vc_option['option_value'];
				}
			}
		}

		return $vc_options;
	}


	/*
	 * Exports navigation menus
	 *
	 * @return array
	 */
	public function export_menus() {
		global $wpdb;
		$output = array();

		// Menu locations
		$menu_locations = array();
		$locations = get_nav_menu_locations();

		$terms_table = $wpdb->prefix . "terms";
		foreach ( (array) $locations as $location => $menu_id ) {
			$menu_slug = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $terms_table where term_id=%d", $menu_id ), ARRAY_A );

			if (count($menu_slug) > 0) {
				$menu_locations[ $location ] = $menu_slug[0]['slug'];
			}
		}
		$output['menu_locations'] = $menu_locations;

		return $output;
	}


	/*
	 * Exports necessary options from Settings pages
	 *
	 * @return array
	 */
	public function export_settings_pages() {
		$output = array();

		$settings_pages = array(
			'show_on_front' => get_option( 'show_on_front' )
		);

		// Page on front
		$xkit_show_on_front = get_option( 'page_on_front' );
		$show_on_front = get_page( $xkit_show_on_front );
		if( !empty( $show_on_front->post_title ) ) {
			$settings_pages['page_on_front'] = $show_on_front->post_title;
		}
		else{
			$settings_pages['page_on_front'] = 0;
		}

		// Page for posts
		$xkit_post_page = get_option( 'page_for_posts' );
		$post_page = get_page( $xkit_post_page );
		if( !empty( $post_page->post_title ) ) {
			$settings_pages['page_for_posts'] = $post_page->post_title;
		}
		else{
			$settings_pages['page_for_posts'] = 0;
		}

		$output['settings_pages'] = $settings_pages;

		return $output;
	}


	/*
	 * Exports all options and sends a zip file as an output to browser or save to demo dir
	 */
	public function export_all() {
		global $wpdb;

		// File action
		if( !empty( $_POST['export_type'] ) ) {
			$file_action = sanitize_text_field( $_POST['export_type'] );
		}
		else{
			$file_action = 'download_demo';
		}

		/* Get data */
		// Get theme info
		$info_array = array();
		if( !empty( $_POST['demo_info'] ) ) {
			$demo_info = (array) $_POST['demo_info'];
			$info_array = array();

			foreach( $demo_info as $info_key => $info_value ) {
				if( !empty( $info_value ) ) {
					$info_value = strip_tags( $info_value );

					// demo id
					if( $info_key == 'id' ) {
						$info_value = sanitize_title( $info_value );
					}

					// author uri
					if( $info_key == 'authorAndUri' ) {
						$author_name = sanitize_text_field( $demo_info['author'] );

						if( empty( $author_name ) ) {
							$author_name = $info_value;
						}
						$info_value = '<a href="' . $info_value . '">' . $author_name . '</a>';
					}

					$info_array[$info_key] = $info_value;
				}
			}
		}
		if( ! empty( $info_array ) && is_array( $info_array ) ) {
			$json_info = json_encode( $info_array );
		}


		// Zip & folder names
		if( !empty( $info_array['id'] ) ) {
			$zip_name = $info_array['id'] . '.zip';
		}
		elseif( !empty( $info_array['name'] ) ) {
			$zip_name = sanitize_title( $info_array['name'] ) . '.zip';
		}

		// Check demo name
		if( empty( $zip_name ) ) {
			$this->add_error( esc_html__('ERROR. Enter demo name.', 'xkit' ) );
			return false;
		}


		$demo_path = pathinfo( $zip_name, PATHINFO_FILENAME );
		$new_demo_path = $this->demos_path . $demo_path . '/';


		// The demo is already exists?
		if( $file_action == 'save_demo' && file_exists( $new_demo_path ) ) {
			$this->add_error( $demo_path . ' - ' . esc_html__( 'This demo already exists.', 'xkit' ) );
			return false;
		}


		// Get options
		$options_array = array();
		$options_array = array_merge( $options_array, $this->export_settings_pages() );
		$options_array = array_merge( $options_array, $this->export_widgets_sidebars() );
		$options_array = array_merge( $options_array, $this->export_menus() );
		$options_array = array_merge( $options_array, $this->export_acf_options() );
		$options_array = array_merge( $options_array, $this->export_visual_composer() );

		if( ! empty( $options_array ) && is_array( $options_array ) ) {
			$json_options = json_encode( $options_array );
		}


		// Get demo content
		load_template( get_template_directory() . '/framework/modules/importer/includes/export.php', true );
		if( ! function_exists('xkit_export_wp_content') ) {
			$this->add_error( $demo_path . ' - ' . esc_html__( 'Export error.', 'xkit' ) );
			return false;
		}

		ob_start();
			xkit_export_wp_content();
			$demo_content = ob_get_contents();
		ob_end_clean();


		// Compress demo content
		$gzfile = 'content.xml.gz';
		$compressed_content = gzopen( $gzfile, 'w9' );
		gzwrite( $compressed_content, $demo_content );
		gzclose( $compressed_content );


		/* Create global archive */
		$zip = new ZipArchive();
		$zip->open( $zip_name, ZipArchive::CREATE );


		// Add theme info to archive
		if( !empty( $json_info ) ) {
			$zip->addFromString( 'demo-info.json', $json_info );
		}


		// Add options to archive
		if( !empty( $json_options ) ) {
			$zip->addFromString( 'options.json', $json_options );
		}


		// Add demo content to archive
		if( !empty( $demo_content ) ) {
			$zip->addFile( $gzfile );
		}


		// Add screenshot to archive
		if( !empty( $_FILES['screenshot']['tmp_name'] ) ) {
			$image_tmp = $_FILES['screenshot']['tmp_name'];
			$check = getimagesize( $image_tmp );

			if( $check !== false ) {
				// Image Type
				$image_type = explode( '/', image_type_to_mime_type( $check[2] ) );
				
				if( isset( $image_type[1] ) ){
					$zip->addFile( $_FILES['screenshot']['tmp_name'], 'screenshot.' . $image_type[1] );
				}
			}
		}


		// Close archive
		$zip->close();


		// Save as demo
		if( $file_action == 'save_demo' ) {
			$res = $zip->open( $zip_name );

			if ( $res === TRUE ) {
				if( empty( $this->demos_path ) ) {
					$this->add_error( esc_html__( 'Wrong demos path.', 'xkit' ) );
				}
				else{
					// extract it to the path we determined above
					$zip->extractTo( $new_demo_path );
					$this->add_message( $demo_path . ' - ' . esc_html__( 'Demo successfully created.', 'xkit' ) );
				}

			} else {
				$this->add_error( $demo_path . ' - ' . esc_html__( 'Error creating demo.', 'xkit' ) );
			}

			$zip->close();
		}


		// Download demo
		if( $file_action == 'download_demo' ) {

			$this->clear_notices();
			//send output to browser so user can download generated zip
			header( 'Content-Type: application/zip' );
			header( 'Content-disposition: attachment; filename=' . $zip_name );
			header( 'Content-Length: ' . filesize( $zip_name ) );
			echo xkit_file_load_content( $zip_name );
		}


		// Remove Temp Files
		if( file_exists( $zip_name ) ) {
			unlink( $zip_name );
		}
		if( file_exists( $gzfile ) ) {
			unlink( $gzfile );
		}

		if( $file_action == 'download_demo' ) {
			exit();
		}
	}


	/*
	 * Add export admin page
	 */
	public function register_export_page() {

		// Process export
		if( isset( $_REQUEST['demo_export'] ) ) {
			$this->export_all();
		}

		/* Register export page */
		add_theme_page(
			wp_kses_post( _x( 'Export Demo', 'menu title', 'xkit' ) ),
			wp_kses_post( _x( 'Export Demo', 'menu title', 'xkit' ) ),
			'edit_theme_options',
			$this->export_page_slug,
			array( &$this, 'export_page_html' )
		);
	}

	/*
	 * Admin export page html
	 */
	public function export_page_html() {
		// Export form
		?>
		<div id="import-page" class="wrap">

			<?php $this->show_notices(); ?>

			<div class="import-page-content">
				<div class="header-box">
					<div class="head-title"><?php esc_html_e( 'Create Demo', 'xkit' ); ?></div>
					<div class="logo"><img src="<?php echo get_template_directory_uri() . '/framework/modules/importer/assets/images/header-logo.png'; ?>" /></div>
				</div>

				<div class="export-wrap">
					<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" enctype="multipart/form-data">
						<?php wp_nonce_field('export-nonce'); ?>

						<!-- Theme Info -->
						<div class="theme-info export-section">

							<table class="form-table">
								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Title', 'xkit' ); ?>
									</th>
									<td>
										<input type="text" value="" name="demo_info[name]">
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Slug', 'xkit' ); ?>
									</th>
									<td>
										<input type="text" value="" name="demo_info[id]">
										<p class="description"><?php echo wp_kses_post( __( 'The slug is the URL-friendly version of the demo. It is usually all lowercase and contains only letters, numbers, and hyphens. <strong>Default: Create From Title.</strong>', 'xkit' ) ); ?></p>
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Screenshot', 'xkit' ); ?>
									</th>
									<td>
										<div class="styled-file">
											<div class="file-label"><?php esc_html_e( 'The file is not selected', 'xkit' ); ?></div>
											<div class="select-button"><?php esc_html_e( 'Browse', 'xkit' ); ?></div>
											<input type="file" name="screenshot" accept="image/*,image/jpeg">
										</div>

									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Live preview URL', 'xkit' ); ?>
									</th>
									<td>
										<input type="url" value="" name="demo_info[live_preview_uri]">
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Description', 'xkit' ); ?>
									</th>
									<td>
										<textarea class="large-text" cols="50" rows="5" name="demo_info[description]"></textarea>
										<p class="description"><?php esc_html_e( 'In a few words, explain what this demo is about.', 'xkit' ); ?></p>
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Version', 'xkit' ); ?>
									</th>
									<td>
										<input type="text" value="" name="demo_info[version]">
										<p class="description"><?php echo esc_html__( 'Default:', 'xkit' ) . ' ' . $this->default_demo_version; ?></p>
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Autor Name', 'xkit' ); ?>
									</th>
									<td>
										<input type="text" value="" name="demo_info[author]">
										<p class="description"><?php echo esc_html__( 'Default:', 'xkit' ) . ' ' . $this->default_author_name; ?></p>
									</td>
								</tr>

								<tr class="form-field">
									<th scope="row">
										<?php esc_html_e( 'Autor Url', 'xkit' ); ?>
									</th>
									<td>
										<input type="url" value="" name="demo_info[authorAndUri]">
										<p class="description"><?php echo esc_html__( 'Default:', 'xkit' ) . ' ' . $this->default_author_uri; ?></p>
									</td>
								</tr>
							</table>
						</div>

						<!-- Export Settings -->
						<div class="export-settings export-section">
							<table class="form-table">
								<tr class="form-field form-title">
									<th scope="row" colspan="2">
										<h3 class="title"><span class="dashicons dashicons-admin-page"></span> <?php esc_html_e( 'Export Settings', 'xkit' ); ?></h3>
									</th>
								</tr>

								<tr class="form-field last-child">
									<th scope="row">
										<?php esc_html_e( 'Export Type', 'xkit' ); ?>
									</th>

									<td>
										<select id="export_type" name="export_type">
											<option value="save_demo"><?php esc_html_e( 'Save as Demo', 'xkit' ); ?></option>
											<option value="download_demo"><?php esc_html_e( 'Download Demo', 'xkit' ); ?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div class="form-actions">
							<input type="submit" class="button button-primary" value="<?php esc_html_e('Export Demo', 'xkit' ); ?>" name="demo_export" />
						</div>
					</form>
				</div>
			</div>
		</div><!-- .wrap -->
		<?php
	}


	/*
	 * Instance
	 */
	public static function get() {
		if (!is_null(self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new self;

		return self::$instance;
	}
}

$Xkit_Demo_Export = new Xkit_Demo_Export();