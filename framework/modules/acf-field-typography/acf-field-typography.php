<?php
/*
 * Module Name: Advanced Custom Fields: Typography
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {
		/*
		 *  Add addons for ACF: field typography
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Typography with Standart and GoogleFonts Field for Advanced Custom Fields
			 */
			class acf_field_typography extends acf_field {
				protected $google_fonts_data;
				protected $google_fonts_familys;
				protected $standart_fonts_familys;

				/*
				 * This function will setup the field type data
				 */
				public function __construct() {
					$this->name = 'typography';
					$this->label = esc_html__( 'Typography', 'xkit' );
					$this->category = 'choice';

					$full_list_fonts = array();

					/*
					 * Load json file for extra seting
					 */

					// standart fonts
					$safe_fonts = xkit_get_list_safe_fonts();

					$this->standart_fonts_familys = array(); 

						if( isset( $safe_fonts ) && is_array( $safe_fonts ) ){
							foreach ( $safe_fonts as $font_family ) {
								$this->standart_fonts_familys[$font_family] = $font_family;
							}
						}

					// google fonts
					$font_array_full = xkit_get_array_google_fonts();

					$this->google_fonts_data = array();

						if( isset( $font_array_full ) && is_array( $font_array_full ) ){
							foreach ( $font_array_full[0] as $key => $font_family ) {
								$this->google_fonts_data[$key]['family'] = $font_array_full[2][$key];
								$this->google_fonts_data[$key]['variants'] = explode( ',', $font_array_full[3][$key] );
							}
						}

					$this->google_fonts_familys = array();

						if( isset( $font_array_full[2] ) && is_array( $font_array_full[2] ) ){
							foreach ( $font_array_full[2] as $font_family ) {
								$this->google_fonts_familys[$font_family] = $font_family;
							}
						}

					$full_list_fonts['Standart fonts'] = $this->standart_fonts_familys;
					$full_list_fonts['Google fonts'] = $this->google_fonts_familys;

					/*
					 * Defaults (array) Array of default settings which are merged into the field object.
					 * These are used later in settings.
					 */
					$this->defaults = array(
						'show_font_familys'     => 1,
						'show_font_weight'      => 1,
						'show_backup_font'      => 1,
						'show_text_align'       => 1,
						'show_text_direction'   => 1,
						'show_font_size'        => 1,
						'show_line_height'      => 1,
						'show_font_style'       => 1,
						'show_preview_text'     => 1,
						'show_color_picker'     => 1,
						'show_letter_spacing'   => 0,
						'font-family'           => '',
						'font-weight'           => '400',
						'backup-font'           => 'Arial, Helvetica, sans-serif',
						'text-align'            => 'left',
						'direction'             => 'ltr',
						'font-size'             => 20,
						'line-height'           => 25,
						'letter-spacing'        => 0,
						'font-style'            => 'normal',
						'text_color'            => '#000000',
						'default_value'         => '',//pak
						'new_lines'             => '',
						'maxlength'             => '',
						'placeholder'           => '',
						'readonly'              => 0,
						'disabled'              => 0,
						'rows'                  => '',
						'full_list_fonts'       => $full_list_fonts,
						'stylefont' => array( 
									'100'       => '100',
									'300'       => '300',
									'400'       => '400',
									'600'       => '600',
									'700'       => '700',
									'800'       => '800'
								),
						'backupfont' => array(
									'Arial, Helvetica, sans-serif'                          => 'Arial, Helvetica, sans-serif',
									'"Arial Black", Gadget, sans-serif'                     => '"Arial Black", Gadget, sans-serif',
									'"Bookman Old Style", serif'                            => '"Bookman Old Style", serif',
									'"Comic Sans MS", cursive'                              => '"Comic Sans MS", cursive',
									'Courier, monospace'                                    => 'Courier, monospace',
									'Garamond, serif'                                       => 'Garamond, serif',
									'Georgia, serif'                                        => 'Georgia, serif',
									'Impact, Charcoal, sans-serif'                          => 'Impact, Charcoal, sans-serif',
									'"Lucida Console", Monaco, monospace'                   => '"Lucida Console", Monaco, monospace',
									'"Lucida Sans Unicode", "Lucida Grande", sans-serif'    => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
									'"MS Sans Serif", Geneva, sans-serif'                   => '"MS Sans Serif", Geneva, sans-serif',
									'"MS Serif", "New York", sans-serif'                    => '"MS Serif", "New York", sans-serif',
									'"Palatino Linotype", "Book Antiqua", Palatino, serif'  => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
									'Tahoma,Geneva, sans-serif'                             => 'Tahoma, Geneva, sans-serif',
									'"Times New Roman", Times,serif'                        => '"Times New Roman", Times, serif',
									'"Trebuchet MS", Helvetica, sans-serif'                 => '"Trebuchet MS", Helvetica, sans-serif',
									'Verdana, Geneva, sans-serif'                           => 'Verdana, Geneva, sans-serif',
								)
					);

					/*
					 *  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
					 *  var message = acf._e('typography', 'error');
					 */
					$this->l10n = array(
						'error'	=> esc_html__(' Error! Please enter a higher value', 'xkit' ),
					);


					// Do not delete!
					parent::__construct();

				}

				/*
				 * Extra settings for field.
				 */
				public function render_field_settings( $field ) {
					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Font Family ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'		=> 'horizontal',
						'name'			=> 'show_font_familys',
						'choices'		=>	array(
											1	=>	esc_html__( 'Yes', 'xkit' ),
											0	=>	esc_html__( 'No', 'xkit' )
										)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Font Family', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'font-family',
						'choices'		=>	$field['full_list_fonts']
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__(' Show Font Weight ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'		=>  'horizontal',
						'name'			=> 'show_font_weight',
						'choices'		=>	array(
											1	=>	esc_html__( 'Yes', 'xkit' ),
											0	=>	esc_html__( 'No', 'xkit' )
										)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Font Weight', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'font-weight',
						'choices'		=>	$field['stylefont']
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Backup Font ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'		=> 'horizontal',
						'name'			=> 'show_backup_font',
						'choices'		=>	array(
											1	=>	esc_html__( 'Yes', 'xkit' ),
											0	=>	esc_html__( 'No', 'xkit' )
										)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Backup Font', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'backup-font',
						'choices'		=>	$field['backupfont']
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Text Align ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'		=> 'horizontal',
						'name'			=> 'show_text_align',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Text Align', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'text-align',
						'choices'		=>	array(
							'inherit',
							'left'		=> 'left',
							'right'		=> 'right', 
							'center'	=> 'center', 
							'justify'	=> 'justify', 
							'inital'	=> 'inital')
					));


					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Text direction ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_text_direction',
						'choices'		=>	array(
											1	=>	esc_html__( 'Yes', 'xkit' ),
											0	=>	esc_html__( 'No', 'xkit' )
										)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Text direction', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'direction',
						'choices'		=>	array(  'ltr' => 'left to right',
													'rtl' => 'right to left',)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Font Size ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'  		=>  'horizontal',
						'name'			=> 'show_font_size',
						'choices'		=>	array(
											1	=>	esc_html__( 'Yes', 'xkit' ),
											0	=>	esc_html__( 'No', 'xkit' )
										)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Font Size', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'font-size',
						'append'		=> 'px',
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Line Height ?', 'xkit' ), 
						'instructions'	=> esc_html__( 'When line height don t load line height is 150%', 'xkit' ),
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_line_height',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Line Height', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'line-height',
						'append'		=> 'px',
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Letter Spacing ?', 'xkit' ), 
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_letter_spacing',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											)
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Letter Spacing', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'letter-spacing',
						'append'		=> 'px',
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Color Picker ?', 'xkit' ), 
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_color_picker',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											),
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Font Color', 'xkit' ),
						'type'			=> 'text',
						'name'			=> 'text_color',
						'append'		=> 'hex',
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Font Style ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_font_style',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											),
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Font Style', 'xkit' ),
						'type'			=> 'select',
						'ui'			=> 1,
						'name'			=> 'font-style',
						'choices'		=>	array(
												'normal'	=> 'normal',
												'italic'	=> 'italic',
												'oblique'	=> 'oblique',
											),
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Show Preview Text ?', 'xkit' ),
						'type'			=> 'radio',
						'layout'  		=> 'horizontal',
						'name'			=> 'show_preview_text',
						'choices'		=>	array(
												1	=>	esc_html__( 'Yes', 'xkit' ),
												0	=>	esc_html__( 'No', 'xkit' )
											),
					));
				}


				/*
				 * Show setings in field.
				 */
				public function render_field( $field ) {
					// convert value to array
					$field['value'] = $this->force_type_array( $field['value'] );


					// add empty value (allows '' to be selected)
					if( empty($field['value']) ){

						$field['value'][''] = '';
						$field['value']['font-family']	 = 	$field['font-family'];
						$field['value']['font-weight']	 = 	$field['font-weight'];
						$field['value']['backupfont']	 = 	$field['backup-font'];
						$field['value']['text-align']	 =	$field['text-align'];
						$field['value']['font-size']	 =	$field['font-size'];
						$field['value']['text-color']	 =	$field['text_color'];
						$field['value']['letter-spacing']=  $field['letter-spacing'];

						if ($field['show_line_height']) {
							$field['value']['line-height']	 =  $field['line-height'];
						} else {
							$field['value']['line-height']	 =	'150%';
						}

						$field['value']['direction']	 =	$field['direction'];
						$field['value']['font-style']	 =	$field['font-style'];
					}

					$field_value = $field['value'];

					$style = array( 'NORMAL 400', 'SMALL 200', 'BOLD 800' );
					$text_align =  array( 'inherit', 'left', 'right', 'center', 'justify', 'inital' );
					$text_direction = array(
												'ltr' => 'left to right',
												'rtl' => 'right to left'
											);
					$font_style = array(
											'normal'	=> 'normal',
											'italic'	=> 'italic',
											'oblique'	=> 'oblique'
										);
					$s = 0;
					$e = '';

					$defaults_fonts = $field['backupfont'];

					$fontf = preg_replace('/\s+/', '+', @$field_value['font-family']);

					/*
					 * Show render field
					 */
					?>
					<div class="rey_main">

						<div class="clearfix">
							<?php

							// Font Family selector
							if ( $field['show_font_familys'] ){
								?>
									<div class="acf-typography-subfield acf-typography-font-familys">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Font Family</label>

										<input name="<?php echo esc_attr( $field['name'] ); ?>[font-family]" id="<?php echo esc_attr( $field['key'] ); ?>attribute" class="select2-container font-familys" value="<?php echo @$field_value['font-family']; ?>" />
									</div>
								<?php
							}

							// Font Weight Selector
							if ( $field['show_font_weight'] & $field['show_font_familys'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-font-weight">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Font Weight</label>

										<input name="<?php echo esc_attr( $field['name'] ); ?>[font-weight]" id="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo @$field_value['font-weight']; ?>" class="select2-container font-weight select2-weight" type="hidden" />
									</div>
								<?php
							}

							// Backup Font Family
							if ( $field['show_backup_font'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-backup-font">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Backup Font Family</label>

										<select name="<?php echo esc_attr( $field['name'] ); ?>[backupfont]" class="js-select2">
											<?php  foreach ( $defaults_fonts as $k => $v ): ?>
												<option value="<?php echo esc_attr( $k ); ?>" <?php echo selected( @$field_value['backupfont'], esc_attr( $k ), false ); ?> ><?php echo esc_attr( $k ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php
							}

							// Text Align
							if ( $field['show_text_align'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-text-align">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Text Align</label>

										<select name="<?php echo esc_attr( $field['name'] ); ?>[text-align]" id="<?php echo esc_attr( $field['key'] ); ?>align"  class="js-select2 alignF">
											<?php  foreach ( $text_align as $k ): ?>
												<option value="<?php echo esc_attr( $k ); ?>" <?php echo selected( @$field_value['text-align'], esc_attr( $k ), false ); ?> ><?php echo esc_attr( $k ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php
							}

							// Text Direction
							if ( $field['show_text_direction'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-direction">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Text Direction</label>

										<select name="<?php echo esc_attr( $field['name'] ); ?>[direction]" class="js-select2">
											<?php  foreach ( $text_direction as $k => $v ): ?>
												<option value="<?php echo esc_attr( $k ); ?>" <?php echo selected( @$field_value['direction'], esc_attr( $k ), false ); ?> ><?php echo esc_attr( $v ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php
							}

							// Show font style
							if ( $field['show_font_style'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-font-style">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Font Style</label>

										<select name="<?php echo esc_attr( $field['name'] ); ?>[font-style]" id="<?php echo esc_attr( $field['key'] ); ?>-font-style"  class="js-select2 font-style">
											<?php  foreach ( $font_style as $k => $v ): ?>
												<option value="<?php echo esc_attr( $k ); ?>" <?php echo selected( @$field_value['font-style'], esc_attr( $k ), false ); ?> ><?php echo esc_attr( $v ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php
							}

							// Show font size
							if ( $field['show_font_size'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-font-size">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Font Size</label>

										<div class="acf-typography-field-font-size">
											<div class="acf-input-append">px</div>
											<div class="acf-input-wrap">
												<input class="sizeF" type="number" name="<?php echo esc_attr( $field['name'] ); ?>[font-size]" id="<?php echo esc_attr( $field['key'] ); ?>size" value="<?php echo @$field_value['font-size']; ?>" min="1" max="" step="any" placeholder="">
											</div>
										</div>
									</div>
								<?php
							}

							// Show line height
							if ( $field['show_line_height'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-font-line-height">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Line Height</label>

										<div class="acf-typography-field-line-height">
											<div class="acf-input-append">px</div>
											<div class="acf-input-wrap">
												<input class="lineF" type="number" name="<?php echo esc_attr( $field['name'] ); ?>[line-height]" id="<?php echo esc_attr( $field['key'] ); ?>line" value="<?php echo @$field_value['line-height']; ?>" min="1" max="" step="any" placeholder="">
											</div>
										</div>
									</div>
								<?php
							}

							// Show letter spacing
							if ( $field['show_letter_spacing'] ) {
								?>
									<div class="acf-typography-subfield acf-typography-font-line-height">
										<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Letter Spacing</label>

										<div class="acf-typography-field-line-height">
											<div class="acf-input-append">px</div>
											<div class="acf-input-wrap">
												<input class="letter-spacing" type="number" name="<?php echo esc_attr( $field['name'] ); ?>[letter-spacing]" id="<?php echo esc_attr( $field['key'] ); ?>-letter-spacing" value="<?php echo @$field_value['letter-spacing']; ?>" min="" max="" step="any" placeholder="">
											</div>
										</div>
									</div>
								<?php
							}

							?>
						</div>

						<?php
						// Show color picker
						if ( $field['show_color_picker'] ) {
							?>
								<div class="acf-background-subfield-color acf-typography-color">
									<label class="acf-typography-field-label" for="<?php echo esc_attr( $field['key'] ); ?>">Text Color</label>

									<div class="acf-typography-field-line-height">
										<div class="acf-input-wrap">
											<input data-id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>[text-color]" id="<?php echo esc_attr( $field['id'] ); ?>-color" class="rey-color text-color" type="text" value="<?php echo @$field_value['text-color']; ?>" data-default-color="#000" />
										</div>
									</div>
								</div>
							<?php
						}


						// Preview
						$css = $this->get_preview_css( $field );

						if ( $field['show_preview_text'] & $field['show_font_familys'] ) {
							?>
								<div class="acf-typography-preview">
									<label class="acf-typography-field-label">Preview Text:</label>

									<div class="preview_font ss" style="<?php echo esc_attr( $css ); ?>"></div>

									<?php
										$is_standart = in_array( $field['value']['font-family'], $this->standart_fonts_familys );

										if( !$is_standart ){
											$query_font = '&font=' . $field['value']['font-family'];
										} else {
											$query_font = '';
										}

										if( isset( $field['value']['font-weight' ] ) ){
											$query_font_weight = '&wi=' . $field['value']['font-weight' ];
										} else {
											$query_font_weight = '';
										}
									?>
									<?php
									$output = '<i{%tag%} class="acf-typography-preview-font" src="' . get_template_directory_uri() . '/framework/modules/acf-field-typography/preview.php?css=' . $css . $query_font .  $query_font_weight . '"></i{%tag%}>';
									echo str_replace( '{%tag%}', 'frame', $output );
									?>
								</div>
							<?php
						}
						?>
					</div>
					<?php
				}

				public function get_preview_css( $field ) {
					$css = '';
					if (!empty( $field['value'] ) ) {
						foreach( $field['value'] as $key=>$value ) {
							if ( !empty( $value ) ) {
								if ( $key != 'backupfont' ) {
									switch ( $key ) {
										case 'text-align':
											$css .= 'text-align' . ':' . $value . ';';
											break;
										case 'font-size':
											$css .= 'font-size' . ':' . $value . 'px;';
											break;
										case 'letter-spacing':
											$css .= 'letter-spacing' . ':' . $value . ';';
											break;
										case 'line-height':
											$css .= 'line-height' . ':'.  $value . 'px;';
											break;
										case 'font-style':
											$css .= 'font-style' . ':' . $value . ';';
											break;
										case 'text-color':
											$rgb= $this->hex2rgb( $value );
											$css .= 'color' . ':' . $rgb . ';';
											break;
										default:
											$css .= $key . ':' . $value . ';';
											break;
									}
								}
							}
						}
					}
					return $css;
				}

				/*
				 * Force type array
				 */
				public function force_type_array( $var ) {
					if( is_array( $var ) ) {
						return $var;
					}
					if( empty( $var ) && !is_numeric( $var ) ) {
						return array();
					}
					if( is_string( $var ) ) {
						return explode( ',', $var );

					}
					return array( $var );
				}

				/*
				 * Hex2rgb
				 */
				function hex2rgb( $hex ) {
					$hex = str_replace( "#", "", $hex );

					if( strlen( $hex ) == 3 ) {
						$r = hexdec( substr( $hex, 0, 1 ).substr ($hex, 0, 1) );
						$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1) );
						$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1) );
					} else {
						$r = hexdec( substr( $hex, 0, 2 ) );
						$g = hexdec( substr( $hex, 2, 2 ) );
						$b = hexdec( substr( $hex, 4, 2 ) );
					}
					$rgb = 'rgb(' . $r . "," . $g . "," . $b . ")";

					return $rgb;
				}

				/*
				 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
				 * Use this action to add CSS + JavaScript to assist your render_field() action.
				 */
				public function input_admin_head() {
					// register & include JS
					wp_enqueue_script( 'acf-input-typography', get_template_directory_uri() . '/framework/modules/acf-field-typography/js/acf-typography.js' );
					wp_localize_script(
						'acf-input-typography', 'typographyVar',
						array(
							"dir" => get_template_directory_uri() . '/framework/modules/acf-field-typography/',
							"listStandartFonts" => json_encode( array_values( $this->standart_fonts_familys ) ),
							"listGoogleFonts" => json_encode( $this->google_fonts_data ),	 
						)
					);

					// register & include CSS
					wp_enqueue_style( 'acf-input-typography',  get_template_directory_uri() . '/framework/modules/acf-field-typography/css/acf-typography.css' ); 

					wp_enqueue_media();
				}

				/*
				 * This filter is used to perform validation on the value prior to saving.
				 * All values are validated regardless of the field's required setting.
				 * This allows you to validate and return messages to the user if the value is not correct
				 */
				public function validate_value( $valid, $value, $field, $input ){
					if ( $field['required'] ) {
						if ( empty( $value['font-family'] ) || empty( $value['font-weight'] ) || empty( $value['backupfont'] ) || empty( $value['text-align'] )
							 || empty( $value['direction'] ) || empty( $value['font-style'] ) || empty( $value['font-size'] ) || empty( $value['line-height'] )
							 || empty( $value['text-color'] ) ) {
							$set = 0;
							$txt = esc_html__( 'The value is empty!! : ', 'xkit' );

							if( empty( $value['font-family'] ) & $field['show_font_familys'] ){
								$txt .= esc_html__( 'font family, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['font-weight']) & $field['show_font_weight'] ){
								$txt .= esc_html__( 'font weight, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['backupfont']) & $field['show_backup_font'] ){
								$txt .= esc_html__( 'backupfont, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['text-align']) & $field['show_text_align'] ){
								$txt .= esc_html__( 'text align, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['direction']) & $field['show_text_direction'] ){
								$txt .= esc_html__( 'direction, '  , 'xkit' );
								$set = 1;
							}

							if( empty( $value['font-style']) & $field['show_font_style'] ){
								$txt .= esc_html__( 'font style, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['font-size']) & $field['show_font_size'] ){
								$txt .= esc_html__( 'font size, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['line-height']) & $field['show_line_height'] ){
								$txt .= esc_html__(' line height, ', 'xkit' );
								$set = 1;
							}

							if( empty( $value['text-color']) & $field['show_color_picker'] ){
								$txt .= esc_html__( 'text color, ', 'xkit' );
								$set = 1;
							}

							if ( $set ) {
								$valid = $txt;
							}
						}
					}

					return $valid;
				}
			}
			new acf_field_typography();
		}
	});


	/*
	 *  Front-end enqueue google font
	 */
	function acf_typography_front_end( $value, $post_id, $field )
	{
		if( isset( $value['font-family'] ) && $value['font-family'] ){
			$select_font_family = $value['font-family'];

			$font_array_full = xkit_get_array_google_fonts();

			if( $font_array_full ){

				$google_font_exists = in_array( $select_font_family, $font_array_full[2] );

				if( $google_font_exists ){
					$gen_font = $value['font-family'];

					if( isset( $value['font-weight'] ) && $value['font-weight'] ){
						$gen_font .= ':' . $value['font-weight'];
					}

					xkit_wp_enqueue_google_font( $gen_font );
				}
			}
		}

		return $value;
	}
	add_filter( 'acf/load_value/type=typography', 'acf_typography_front_end', 10, 3 );
}
?>