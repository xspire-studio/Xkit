<?php
/*
 * Module Name: ACF Theme Options
 * Version: 1.0.0
 * Author: Xspire
*/


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ){

	/*
	 *  Add addons for ACF: Custom tab / subtab
	 */
	load_template( get_template_directory() . '/framework/modules/acf-theme-options/inc/acf-field-type-tab-options.php', true );
	load_template( get_template_directory() . '/framework/modules/acf-theme-options/inc/acf-field-type-subtab-options.php', true );


	/*
	 *  Register theme options page
	 */
	function acf_register_theme_options_page() {
		add_theme_page( esc_html__( 'Theme Options', 'xkit' ), esc_html__( 'Theme Options', 'xkit' ), 'edit_posts', 'theme-options', 'acf_theme_options_page_callback' );
	}
	add_action( 'admin_menu', 'acf_register_theme_options_page' );


	/*
	 *  Add item toolbar "Theme options"
	 */
	function acf_toolbar_theme_options( $wp_admin_bar ) {
		$args = array(
			'id'    => 'theme_options',
			'title' => '<span class="ab-icon dashicons-admin-appearance" style="top: 2px;"></span> ' . esc_html__( 'Theme Options', 'xkit' ),
			'href'  => admin_url( 'themes.php?page=theme-options' ),
			'meta'  => array( 'class' => 'theme-options' )
		);
		$wp_admin_bar->add_node( $args );
	}
	add_action( 'admin_bar_menu', 'acf_toolbar_theme_options', 40 );


	/*
	 *  Active theme import\framework defaults options
	 */
	function acf_after_switch_theme_import_defaults_options( $counter ) {
		$filename_defaults_options = get_template_directory() . '/includes/synchronize/defaults_options.opt';

		if ( file_exists( $filename_defaults_options ) ) {
			$puck_options = xkit_file_load_content( $filename_defaults_options );

			 if( $puck_options ){

				$array_options = @json_decode( $puck_options );

				if( $array_options ){
					if( $counter == 1 ){
						xkit_set_theme_options( $array_options ); 
					}

					update_option( 'theme-option-defaults', $array_options );
				}
			}
		}
	}
	add_action( 'xkit_counter_theme_active', 'acf_after_switch_theme_import_defaults_options', 10, 1 );

	function acf_after_framework_active_import_defaults_options(){
		acf_after_switch_theme_import_defaults_options( 1 );
	}
	add_action( 'xkit_framework_active', 'acf_after_framework_active_import_defaults_options' );


	/*
	 *  Save theme options
	 */
	function acf_save_theme_options( $post_id ){
		ob_start();

			acf()->input->save_post( 'options' );

			do_action( 'acf_after_save_theme_options' );

		$buffer = ob_get_clean();

		if( !$buffer ){
			print( json_encode( array( 'type' => 'success', 'msg' => esc_html__( 'Settings saved successfully', 'xkit' ) ) ) );
		} else {
			print( $buffer );
		}

		die();
	}
	add_action( 'wp_ajax_save_theme_options', 'acf_save_theme_options' );
	add_action( 'wp_ajax_nopriv_save_theme_options', 'acf_save_theme_options' );


	/*
	 *  Export file options
	 */
	function acf_export_file_theme_options() {
		@ob_clean();

		$date = date( "Y-m-d" );

		header( "Content-type: text/plain" );
		header( "Content-disposition: attachment; filename=options-export-$date.opt");

		$theme_options  = xkit_get_theme_options();

		$pack = json_encode( $theme_options );

		print( $pack );

		die();
	}
	add_action( 'wp_ajax_nopriv_export_file_theme_options', 'acf_export_file_theme_options' );
	add_action( 'wp_ajax_export_file_theme_options', 'acf_export_file_theme_options' );


	/*
	 *  Import file options
	 */
	function acf_import_theme_options() {
		@ob_clean();

		if( isset( $_FILES['import-options'] ) && ( isset( $_FILES['import-options']['tmp_name'] ) ) && $_FILES['import-options']['tmp_name'] ){
			$puck_options = xkit_file_load_content( $_FILES['import-options']['tmp_name'] );

			$array_options = json_decode( $puck_options );

			if( $array_options ){

				if( json_encode( xkit_get_theme_options() ) != $puck_options ){
					xkit_set_theme_options( $array_options );

					print( json_encode( array( 'type' => 'success', 'msg' => esc_html__( 'All options have been imported successfully', 'xkit' ) ) ) );
				}
				else{
					print( json_encode(  array( 'type' => 'warning', 'msg' => esc_html__( 'Nothing has been imported...', 'xkit' )  ) ) );
				}
			}
			else{
				print( json_encode( array( 'type' => 'error', 'msg' => esc_html__( 'Options could not be saved', 'xkit' )  ) ) );
			}
		}
		else{
			print( json_encode( array( 'type' => 'error', 'msg' => esc_html__( 'Options could not be saved', 'xkit' )  ) ) );
		}

		die();
	}
	add_action( 'wp_ajax_nopriv_import_theme_options', 'acf_import_theme_options' );
	add_action( 'wp_ajax_import_theme_options', 'acf_import_theme_options' );


	/*
	 *  Save defaults options
	 */
	function acf_save_defaults_options( $post_id ){

		if( XKIT_THEME_DEBUG ){

			$theme_options  = xkit_get_theme_options();

			update_option( 'theme-option-defaults', $theme_options );

			xkit_file_save_content( get_template_directory() . '/includes/synchronize/defaults_options.opt', @json_encode( $theme_options ) );
		}
	}
	add_action( 'acf_after_save_theme_options' , 'acf_save_defaults_options' );


	/*
	 *  Restore defaults options
	 */
	function acf_restore_defaults_options() {
		@ob_clean();

		$defaults_options  = get_option( 'theme-option-defaults' );

		if( $defaults_options ){
			xkit_set_theme_options( $defaults_options );

			print( json_encode( array( 'type' => 'success', 'msg' => esc_html__( 'All options have been restored successfully', 'xkit' ) ) ) );
		}
		else{
			print( json_encode( array( 'type' => 'error', 'msg' => esc_html__( 'Can not restore options!', 'xkit' )  ) ) );
		}

		die();
	}
	add_action( 'wp_ajax_nopriv_restore_defaults_options', 'acf_restore_defaults_options' );
	add_action( 'wp_ajax_restore_defaults_options', 'acf_restore_defaults_options' );


	/*
	 *  Render tubs box
	 */
	function acf_render_tabs_box( $tabs_fabric = array(), $tabs_fabric_fields = array(), $return = true ){
		function acf_save_submit(){
			?>
				<div class="save-submit"><button><?php echo esc_html__( 'Save Settings', 'xkit' ); ?></button></div>
			<?php
		}

		ob_start();
		if( is_array( $tabs_fabric ) && $tabs_fabric ){

			$options_url = admin_url( 'themes.php?page=' . sanitize_text_field( @$_GET['page'] ) );

			if( isset( $_GET['tab'] ) && $_GET['tab']){
				$tab_query = sanitize_text_field( $_GET['tab'] );
			} else {
				$tab_query = false;
			}

			if( isset( $_GET['subtab'] ) && $_GET['subtab']){
				$subtab_query = sanitize_text_field( $_GET['subtab'] );
			} else {
				$subtab_query = false;
			}

			// No group fields
			if( $tabs_fabric_fields ){
				foreach( $tabs_fabric_fields as $key => $item ){
					if( $item['parent_key'] == '' ){
						print( $item['html'] );
					}
				}
			}
		?>
			<div class="theme_tabs">
				<ul>
					<?php
						$tab_counter = 0;
						foreach( $tabs_fabric as $tab ){
							$tab_data_slug = sanitize_title(esc_attr($tab['field']['label']));

							if( $tab_query && $tab_query == $tab_data_slug ){
								$tab_active = 'ui-tabs-active';
							} elseif( !$tab_query && $tab_counter == 0 ){
								$tab_active = 'ui-tabs-active';
							} else{
								$tab_active = '';
							}
							?>
								<li data-slug="<?php echo esc_attr( $tab_data_slug ); ?>" data-type="<?php echo esc_attr( $tab['field']['type'] ); ?>" 
								data-key="<?php echo esc_attr( $tab['field']['key'] ); ?>" class="<?php echo esc_attr( $tab_active ); ?>">

									<a href="<?php echo add_query_arg( array( 'tab' => $tab_data_slug ), $options_url ); ?>" rel="#<?php echo esc_attr( $tab['field']['key'] ); ?>" onclick="return false;" class="dashicons <?php echo esc_attr( $tab['field']['icon_tab'] ); ?>"><?php echo esc_attr( $tab['field']['label'] ); ?></a>
								</li>
							<?php
							$tab_counter++;
						}
					?>
					<li data-slug="import-export" class="<?php echo esc_attr( $tab_query == 'import-export' ? 'ui-tabs-active' : '' ); ?>">
						<a href="<?php echo add_query_arg( array( 'tab' => 'import-export' ), $options_url ); ?>" rel="#import-export" onclick="return false;" class="dashicons dashicons-migrate active"><?php echo esc_html__( 'Import Options', 'xkit' ); ?></a>
					</li>
				</ul>

				<div class="tabs-panel">
					<?php
					$tab_counter = 0;
					foreach( $tabs_fabric as $tab ):
						$tab_data_slug = sanitize_title(esc_attr($tab['field']['label']));

						if( $tab_query && $tab_query == $tab_data_slug ){
							$tab_active = 'style="display:block"';
						} elseif( !$tab_query && $tab_counter == 0 ){
							$tab_active = 'style="display:block"';
						} else{
							$tab_active = '';
						}
						?>
						<div id="<?php echo esc_attr( $tab['key'] ); ?>" class="tab" <?php echo wp_kses_post( $tab_active ); ?>>
							<?php
								// CONTENT
								if( $tabs_fabric_fields ){
									$tab_counter = 0;
									foreach( $tabs_fabric_fields as $key => $item ){
										if( $item['parent_key'] == $tab['key'] ){
											print( $item['html'] );
											$tab_counter++;
										}
									}

									if( $tab_counter > 0){
										acf_save_submit();
									}
								}
							?>

							<?php if( array_key_exists( 'subs', $tab ) && $tab['subs'] ): ?>
								<div class="theme_subtabs clearfix">
									<ul class="clearfix">
										<?php
											$subtab_counter = 0;
											foreach( $tab['subs'] as $subtab ){
												$subtab_data_slug = sanitize_title(esc_attr($subtab['field']['label']));

												if( $subtab_query && $subtab_query == $subtab_data_slug ){
													$subtab_active = 'ui-tabs-active';
												} elseif( !$subtab_query && $subtab_counter == 0 ){
													$subtab_active = 'ui-tabs-active';
												} else{
													$subtab_active = '';
												}
												?>
													<li data-slug="<?php echo esc_attr( $subtab_data_slug ); ?>" data-type="<?php echo esc_attr( $subtab['field']['type'] ); ?>" data-key="<?php echo esc_attr( $subtab['field']['key'] ); ?>" class="<?php echo esc_attr( $subtab_active ); ?>">
														<a href="<?php echo add_query_arg( array( 'tab' => $tab_data_slug, 'subtab' => $subtab_data_slug ), $options_url ); ?>" rel="#<?php echo esc_attr( $subtab['field']['key'] ); ?>" onclick="return false;"><?php echo esc_attr( $subtab['field']['label'] ); ?></a>
													</li>
												<?php
												$subtab_counter++;
											}
										?>
									</ul>

									<div class="tabs-panel clearfix">
										<?php
										$subtab_counter = 0;
										foreach( $tab['subs'] as $subtab ):
											$subtab_data_slug = sanitize_title(esc_attr($subtab['field']['label']));

											if( $subtab_query && $subtab_query == $subtab_data_slug ){
												$subtab_active = 'style="display:block"';
											} elseif( !$subtab_query && $subtab_counter == 0 ){
												$subtab_active = 'style="display:block"';
											} else{
												$subtab_active = '';
											}
										?>
											<div id="<?php echo esc_attr( $subtab['key'] ); ?>" class="subtab" <?php echo wp_kses_post( $subtab_active ); ?>>
												<?php
													// CONTENT
													if( $tabs_fabric_fields ){
														foreach( $tabs_fabric_fields as $key => $item ){
															if( $item['parent_key'] == $subtab['key'] ){
																print( $item['html'] );
															}
														}

														acf_save_submit();
													}
												?>
											</div>
										<?php $subtab_counter++; endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php $tab_counter++; endforeach; ?>
					<div id="import-export" class="tab options-settings" <?php echo wp_kses_post( $tab_query === 'import-export' ? 'style="display:block"' : '' ); ?>>
						<div class="section">
							<h3><?php echo esc_html__( 'Export Options', 'xkit' ); ?></h3>

							<div class="desc"><?php echo esc_html__( 'Here you can download the current settings of your theme. Keep this safe as you can use it as a backup should anything go wrong. Or you can use it to restore your settings on this site (or any other site).', 'xkit' ); ?></div>

							<a class="btn-link btn-export-options" href="#"><?php echo esc_html__( 'Export to file', 'xkit' ); ?></a>
						</div>

						<div class="section">
							<h3><?php echo esc_html__( 'Import Options', 'xkit' ); ?></h3>

							<div class="warning"><?php echo esc_html__( 'WARNING! This will overwrite any existing options, please proceed with caution!', 'xkit' ); ?></div>

							<div class="file">
								<div class="file-label"><?php echo esc_html__( 'The file is not selected', 'xkit' ); ?></div>
								<div class="select-button"><?php echo esc_html__( 'Browse', 'xkit' ); ?></div>
								<input class="import-options" name="import-options" type="file" />
							</div>

							<a class="btn-link btn-import-options" href="#"><?php echo esc_html__( 'Import from file', 'xkit' ); ?></a>
						</div>

						<?php if( get_option( 'theme-option-defaults' ) && !XKIT_THEME_DEBUG ) : ?>
							<div class="section">
								<h3><?php echo esc_html__( 'Advanced', 'xkit' ); ?></h3>

								<a class="btn-link btn-restore-defaults" href="#">
									<?php echo esc_html__( 'Restore Defaults', 'xkit' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
		}
		$tabs_html = ob_get_clean();

		if( !$return ){
			print( $tabs_html );
		}
		else {
			return $tabs_html;
		}
	}


	function acf_theme_options_page_callback() {
		/* Fix page_type for Theme options */
		if( isset( $GLOBALS['wp_filter'][ 'acf/location/rule_match/page_type' ] ) ){
			unset( $GLOBALS['wp_filter'][ 'acf/location/rule_match/page_type' ] );
		}

		/* Html header options */
		function acf_html_header_options(){
			ob_start();
			?>
			<div class="header-options">
				<div class="logo"></div>

				<div class="path"><?php echo esc_html__( 'Theme options', 'xkit' ); ?></div>

				<div class="save-submit"><button><?php echo esc_html__( 'Save Settings', 'xkit' ); ?></button></div>
			</div>
			<?php
			return ob_get_clean();
		}

		/* Html body options */
		?>
			<div id="primary" class="content-area">
				<div id="content" class="options-content load" role="main">
					<?php
						// vars
						$url = acf_get_current_url();


						// defaults
						$args = array(
							'id'					=> 'theme-options',
							'post_id'				=> 'options',
							'new_post'				=> false,
							'field_groups'			=> false,
							'fields'				=> false,
							'post_title'			=> false,
							'post_content'			=> false,
							'form'					=> true,
							'form_attributes'		=> array(),
							'return'				=> add_query_arg( 'updated', 'true', $url ),
							'html_before_fields'	=> '',
							'html_after_fields'		=> '',
							'submit_value'			=> esc_html__(' Update settings', 'xkit' ),
							'updated_message'		=> esc_html__( 'Post updated', 'xkit' ),
							'label_placement'		=> 'left',
							'instruction_placement'	=> 'label',
							'field_el'				=> 'div',
							'uploader'				=> 'wp',
							'honeypot'				=> true
						);

						$args['form_attributes'] = wp_parse_args( $args['form_attributes'], array(
							'id'					=> 'post',
							'class'					=> '',
							'action'				=> '',
							'method'				=> 'post',
						));


						// filter post_id
						$args['post_id'] = acf_get_valid_post_id( $args['post_id'] );


						// load values from this post
						$post_id = $args['post_id'];


						// new post?
						if( $post_id == 'new_post' ) {
							// dont load values
							$post_id = false;

							// new post defaults
							$args['new_post'] = acf_parse_args( $args['new_post'], array(
								'post_type' 	=> 'post',
								'post_status'	=> 'draft',
							));
						}


						// attributes
						$args['form_attributes']['class'] .= ' acf-form';


						// vars
						$field_groups = array();
						$fields = array();


						// post_title
						if( $args['post_title'] ) {

							$fields[] = acf_get_valid_field(array(
								'name'		=> '_post_title',
								'label'		=> 'Title',
								'type'		=> 'text',
								'value'		=> $post_id ? get_post_field('post_title', $post_id) : '',
								'required'	=> true
							));
						}


						// post_content
						if( $args['post_content'] ) {
							$fields[] = acf_get_valid_field(array(
								'name'		=> '_post_content',
								'label'		=> 'Content',
								'type'		=> 'wysiwyg',
								'value'		=> $post_id ? get_post_field('post_content', $post_id) : ''
							));
						}


						// specific fields
						if( $args['fields'] ) {
							foreach( $args['fields'] as $selector ) {
								// append field ($strict = false to allow for better compatibility with field names)
								$fields[] = acf_maybe_get_field( $selector, $post_id, false );
							}
						} elseif( $args['field_groups'] ) {
							foreach( $args['field_groups'] as $selector ) {
								$field_groups[] = acf_get_field_group( $selector );
							}
						} elseif( $args['post_id'] == 'new_post' ) {
							$field_groups = acf_get_field_groups( $args['new_post'] );
						} else {
							$field_groups = acf_get_field_groups(array(
								'post_id' => $args['post_id']
							));
						}


						//load fields based on field groups
						if( !empty($field_groups) ) {
							foreach( $field_groups as $field_group ) {
								$field_group_fields = acf_get_fields( $field_group );
								if( !empty($field_group_fields) ) {
									foreach( array_keys($field_group_fields) as $i ) {
										$fields[] = acf_extract_var($field_group_fields, $i);
									}
								}
							}
						}


						// honeypot
						if( $args['honeypot'] ) {

							$fields[] = acf_get_valid_field(array(
								'name'		=> '_validate_email',
								'label'		=> 'Validate Email',
								'type'		=> 'text',
								'value'		=> '',
								'wrapper'	=> array(
									'style'	=> 'display:none;'
								)
							));

						}

						// updated message
						if( !empty($_GET['updated']) && $args['updated_message'] ) {
							echo '<div id="message" class="updated"><p>' . wp_kses_post( $args['updated_message'] ) . '</p></div>';
						}


						// uploader (always set incase of multiple forms on the page)
						acf_update_setting('uploader', $args['uploader']);
					?>

					<form <?php echo acf_esc_attr( $args['form_attributes']); ?>>
						<?php
						// render post data
						acf_form_data(array( 
							'post_id'	=> $args['post_id'],
							'nonce'		=> 'acf_form' 
						));

						?>
						<div class="acf-hidden">
							<?php acf_hidden_input(array( 'name' => '_acf_form', 'value' => xkit_encode_data64(json_encode($args)) )); ?>
						</div>
						<div class="acf-fields acf-form-fields -<?php echo esc_attr( $args['label_placement'] ); ?>">
							<?php

							// html before fields
							echo wp_kses_post( $args['html_before_fields'] );

							// --------------
							// RENDER
							// --------------

							// bail early if no fields
							if( empty($fields) ) return false;


							// remove corrupt fields
							$fields = array_filter($fields);

							$tabs_fabric = array();
							$tabs_fabric_fields = array();
							$tab_key = '';
							$tab_type = '';
							$acf_fields_html = '';

							// loop through fields
							foreach( $fields as $field ) {

								ob_start();

								// load value
								if( $field['value'] === null ) {
									$field['value'] = acf_get_value( $post_id, $field );
								}

								// set prefix for correct post name (prefix + key)
								$field['prefix'] = 'acf';

								// render
								acf_render_field_wrap( $field, $args['field_el'], $args['instruction_placement'] );

								$acf_field_html = ob_get_clean();

								$acf_fields_html .= $acf_field_html;

								// tabs_fabric
									if( in_array( $field['type'], array( 'tab-options', 'subtab-options' ) ) ){
										$tab_key = $field['key'];
										$tab_type = $field['type'];

										// add item in $tabs_fabric
										if( $field['type'] == 'tab-options' ){
											$tabs_fabric[] = array(
												'key'   => $tab_key,  
												'type'  => $tab_type,
												'field' => $field,
												'html'  => $acf_field_html
											);
										}
										elseif( $field['type'] == 'subtab-options' ){
											if( $tabs_fabric ){
												$tabs_fabric[ count( $tabs_fabric ) - 1 ]['subs'][] = array(
													'key'   => $tab_key,  
													'type'  => $tab_type,
													'field' => $field,
													'html'  => $acf_field_html
												);
											}
										}
									}
									else{
										$tabs_fabric_fields[] = array(
											'parent_key'  => $tab_key,  
											'parent_type' => $tab_type,
											'field'       => $field,
											'html'        => $acf_field_html
										);
									}
							} // end loop fields


							if( $tabs_fabric && $tabs_fabric_fields ){
								$acf_fields_html = acf_render_tabs_box( $tabs_fabric, $tabs_fabric_fields );

								print( acf_html_header_options() . $acf_fields_html );
							} else {
								print( acf_html_header_options() . $acf_fields_html );
							}


							// html after fields
							echo wp_kses_post( $args['html_after_fields'] );
							?>

						</div><!-- acf-form-fields -->


						<div class="acf-form-submit">
							<input type="submit" class="button button-primary button-large" value="<?php echo esc_attr( $args['submit_value'] ); ?>" />
							<span class="acf-spinner"></span>
						</div>
					</form>
				</div>
			</div>
		<?php
	}


	/*
	 *  Add script and style for theme-options
	 */
	add_action( 'admin_init', function() {
		if( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'theme-options' ) ){
			$translation_array = array( 
				'download_file' => esc_html__( 'Download file options', 'xkit' ),
				'error_occurred' => esc_html__( 'An error occurred, the files couldn\'t be sent!', 'xkit' ),
				'are_you_sure' => esc_html__( 'Are you sure? Resetting will loose all custom values!', 'xkit' ),
			);
			wp_localize_script( 'jquery', 'acfMsgTO', $translation_array );

			wp_enqueue_style( 'acf-toastr-css', get_template_directory_uri() . '/framework/modules/acf-theme-options/css/toastr.css' );
			wp_enqueue_style( 'acf-css-theme-options', get_template_directory_uri() . '/framework/modules/acf-theme-options/css/acf-theme-options.css', array( 'acf-input', 'acf-field-group' ) );

			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'acf-toastr-js', get_template_directory_uri() . '/framework/modules/acf-theme-options/js/toastr.min.js' );
			wp_enqueue_script( 'acf-js-theme-options', get_template_directory_uri() . '/framework/modules/acf-theme-options/js/acf-theme-options.js' );

			acf_form_head();
		}
	});


	/*
	 *  Add filter location rules theme_options
	 */
	function acf_location_rules_theme_options( $choices )
	{
		$choices[ esc_html__( 'Forms', 'xkit' ) ]['theme-options'] = esc_html__('Theme options', 'xkit' );

		return $choices;
	}
	add_filter( 'acf/location/rule_types', 'acf_location_rules_theme_options' );


	/*
	 *  Add filter location rules values for theme_options
	 */
	function acf_location_rules_values_theme_options( $choices )
	{
		$choices[ 0 ] = esc_html__( 'General page', 'xkit' );

		return $choices;
	}
	add_filter( 'acf/location/rule_values/theme-options', 'acf_location_rules_values_theme_options' );


	/*
	 *  Add filter location rules match for theme_options
	 */
	function acf_location_rules_match_theme_options( $match, $rule, $options )
	{
		if( strpos( $_SERVER['REQUEST_URI'], 'themes.php') ){
			if( $rule['operator'] == "==" ){
				if( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'theme-options' ) ){
					$match = true;
				}
			}
			elseif( $rule['operator'] == "!=" ){
				if( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] != 'theme-options' ) ){
					$match = true;
				}
			}
		}

		return $match;
	}
	add_filter( 'acf/location/rule_match/theme-options', 'acf_location_rules_match_theme_options', 10, 3 );
}
?>