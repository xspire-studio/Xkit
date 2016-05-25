<?php
/*
 * Module Name: Advanced Custom Fields: Jquery Slider
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {

		/*
		 *  Add addons for ACF: field slider
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field slider
			 */
			class acf_field_slider extends acf_field {

				/*
				 *  __construct
				 *
				 *  This function will setup the field type data
				 *
				 *  @type	function
				 *  @date	5/03/2014
				 *  @since	5.0.0
				 *
				 *  @param	n/a
				 *  @return	n/a
				 */

				public function __construct() {

					/*
					 *  name (string) Single word, no spaces. Underscores allowed
					 */

					$this->name = 'slider';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'Slider', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'jquery';

					/*
					 *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					 */

					$this->defaults = array(
						'max' => 100,
						'min' => 0,
						'step' => 1,
						'default_value' => 50,
						'append_text' => ''
					);

					/*
					 *  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
					 *  var message = acf._e('slider-field', 'error');
					 */

					$this->l10n = array(
						'error'	=> esc_html__( 'Error! Please enter a higher value', 'xkit' ),
					);

					// do not delete!
					parent::__construct();

				}


				/*
				 *  render_field_settings()
				 *
				 *  Create extra settings for your field. These are visible when editing a field
				 *
				 *  @type	action
				 *  @since	3.6
				 *  @date	23/01/13
				 *
				 *  @param	$field (array) the $field being edited
				 *  @return	n/a
				 */
				public function render_field_settings( $field ) {
					/*
					 *  acf_render_field_setting
					 *
					 *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
					 *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
					 *
					 *  More than one setting can be added by copy/paste the above code.
					 *  Please note that you must also have a matching $defaults value for the field name (font_size)
					 */
					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Max', 'xkit' ),
						'instructions'	=> esc_html__( 'Set the maximum value', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'max'
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Min', 'xkit' ),
						'instructions'	=> esc_html__( 'Set the minimum value', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'min'
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Step', 'xkit' ),
						'instructions'	=> esc_html__( 'Set the step value', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'step'
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Value', 'xkit' ),
						'instructions'	=> esc_html__( 'Set the default value', 'xkit' ),
						'type'			=> 'number',
						'name'			=> 'default_value'
					));

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Append', 'xkit' ),
						'instructions'	=> esc_html__( 'Appears after the input', 'xkit' ),
						'type'			=> 'text',
						'name'			=> 'append_text'
					));
				}


				/*
				 *  render_field()
				 *
				 *  Create the HTML interface for your field
				 *
				 *  @param	$field (array) the $field being rendered
				 *
				 *  @type	action
				 *  @since	3.6
				 *  @date	23/01/13
				 *
				 *  @param	$field (array) the $field being edited
				 *  @return	n/a
				 */
				public function render_field( $field ) {
					/*
					 *  Create a simple text input using the 'font_size' setting.
					 */
					?>
						<div class="slider" id="<?php echo esc_attr( $field['id'] ); ?>-slider"></div>

						<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" 
								data-min="<?php echo (float) esc_attr( $field['min'] ); ?>" data-max="<?php echo (float) esc_attr( $field['max'] ); ?>" data-step="<?php echo (float) esc_attr( $field['step'] ); ?>"/>

						<?php if( isset( $field['append_text'] ) && $field['append_text'] ): ?>
							<span class="append-text"><?php echo wp_kses_post( $field['append_text'] ); ?></span>
						<?php endif; ?>

						<script>
							jQuery('#<?php echo esc_attr( $field['id'] ); ?>').siblings('.slider').slider({
								range: "min",
								min: jQuery('#<?php echo esc_attr( $field['id'] ); ?>').data('min'),
								max: jQuery('#<?php echo esc_attr( $field['id'] ); ?>').data('max'),
								step: jQuery('#<?php echo esc_attr( $field['id'] ); ?>').data('step'),
								value:  jQuery('#<?php echo esc_attr( $field['id'] ); ?>').val(),
								slide: function(event, ui) {
									jQuery('#<?php echo esc_attr( $field['id'] ); ?>').val(ui.value);
								}
							});
						</script>
					<?php
				}


				/*
				*  translate_field
				*
				*  This function will translate field settings
				*
				*  @type	function
				*  @date	8/03/2016
				*  @since	5.3.2
				*
				*  @param	$field (array)
				*  @return	$field
				*/
				
				public function translate_field( $field ) {
					
					// translate
					$field['append_text'] = acf_translate( $field['append_text'] );
					
					
					// return
					return $field;
					
				}

			}
			new acf_field_slider();
		}
	});


	/*
	 *  Add script and style
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-css-slider', get_template_directory_uri() . '/framework/modules/acf-field-type-slider/css/acf-slider.css', array( 'acf-input', 'acf-field-group' ) );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'acf-js-slider', get_template_directory_uri() . '/framework/modules/acf-field-type-slider/js/acf-slider.js', array( 'jquery', 'jquery-ui-slider' ) );
	});
}
?>