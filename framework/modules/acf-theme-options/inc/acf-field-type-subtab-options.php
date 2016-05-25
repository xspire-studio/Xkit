<?php
/*
 * Module Name: Advanced Custom Fields: Subtab Options
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {

		/*
		 *  Add addons for ACF: field subtab options
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field subtab options
			 */
			class acf_field_subtab_options extends acf_field {

				/**
				 * __construct
				 *
				 * This function will setup the field type data
				 *
				 * @type	function
				 * @date	5/03/2014
				 * @since	5.0.0
				 *
				 * @param	n/a
				 * @return	n/a
				 */
				public function __construct() {

					/*
					 *  name (string) Single word, no spaces. Underscores allowed
					 */

					$this->name = 'subtab-options';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'Subtab Options', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'layout';


					/*
					 *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					 */

					// do not delete!
					parent::__construct();

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
					?>
						<li data-type="<?php echo esc_attr($field['type']); ?>" data-key="<?php echo esc_attr($field['key']); ?>">
							<a href="#<?php echo esc_attr($field['key']); ?>" onclick="return false;"><?php echo esc_attr($field['label']); ?></a>
						</li>
					<?php
				}
			}

			new acf_field_subtab_options();
		}
	});


	/*
	 *  Add css style and script field admin_init
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-style-subtab-options', get_template_directory_uri() . '/framework/modules/acf-theme-options/css/acf-subtab-options.css', array( 'acf-input', 'acf-field-group' ) ); 
	});
}
?>