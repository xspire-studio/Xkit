<?php
/*
 * Module Name: Advanced Custom Fields: Separator
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {
		/*
		 *  Add addons for ACF: field separator
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field separator
			 */
			class acf_field_separator extends acf_field {

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

					$this->name = 'separator';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'Separator', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'basic';


					/*
					*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					*/

					$this->defaults = array(
						'style_css'	=> '',
					);


					/*
					 *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					 */

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
						'label'			=> esc_html__( 'Style css', 'xkit' ),
						'type'			=> 'textarea',
						'name'			=> 'style_css',
						'rows'			=> '5',
						'new_lines'  	=> '',

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
					
					if( isset( $field['style_css'] ) && $field['style_css'] ){

						$field_selector = str_replace( '_', '-', $field['key'] );

						$style_css = $field['style_css'];

						$style_css = preg_replace( "/(.*?){/ui",  ".acf-fields .acf-field.acf-$field_selector $0", $style_css );

						print( "<style>$style_css</style>" );
					}
				}

			}
			new acf_field_separator();
		}
	});


	/*
	 *  Add css style field admin_init
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-style-separator', get_template_directory_uri() . '/framework/modules/acf-field-type-separator/css/acf-separator.css', array( 'acf-input', 'acf-field-group' ) );
	});
}
?>