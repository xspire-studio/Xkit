<?php
/*
 * Module Name: Advanced Custom Fields: Font Icons
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {

		/*
		 *  Add addons for ACF: field font icons
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field font icons
			 */
			class acf_field_font_icons extends acf_field {

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

					$this->name = 'font-icons';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'Icon packs', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'content';


					/*
					 *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					 */
					$this->defaults = array(
						'icon'	=> 'fa fa-glass',
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

					$full_list_icons['Font awesome'] = xkit_get_list_font_awesome();
					$full_list_icons['Icomoon'] = xkit_get_list_font_icomoon();

					/*
					 *  acf_render_field_setting
					 */

					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Icon', 'xkit' ),
						'type'			=> 'select',
						'name'			=> 'icon',
						'choices'		=> $full_list_icons
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

					$array_font_awesome = xkit_get_list_font_awesome();
					$array_font_icomoon = xkit_get_list_font_icomoon();

					$field_value = $field['value'] ? $field['value'] : $field['icon'];
					?>
						<div class="acf-render-field" data-name="icon-packs">
							<div class="icon-preview">
								<?php echo esc_attr( $field_value ) ? '<i class="item ' . esc_attr( $field_value ) . '"></i>' : ''; ?>
							</div>

							<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>" data-key="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo esc_attr( $field_value ); ?>" readonly >

							<div class="clearfix"></div>

							<div class="icons-container clear">
								<h3>Font awesome</h3>

								<?php foreach( $array_font_awesome as $icon ): ?>
									<i class="item <?php echo esc_attr( $icon ); ?> <?php echo esc_attr( $field_value == $icon ? 'active' : '' ); ?>" data-icon="<?php echo esc_attr( $icon ); ?>"></i>
								<?php endforeach; ?>

								<h3>Icomoon</h3>
								<?php foreach( $array_font_icomoon as $icon ): ?>
									<i class="item <?php echo esc_attr( $icon ); ?> <?php echo esc_attr( $field_value == $icon ? 'active' : '' ); ?>" data-icon="<?php echo esc_attr( $icon ); ?>"></i>
								<?php endforeach; ?>
							</div>
						</div>
					<?php
				}
			}

			new acf_field_font_icons();
		}
	});


	/*
	 *  Add css style and script field admin_init
	 */
	add_action( 'admin_init', function() {
		wp_deregister_style( 'font-awesome' );
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/framework/assets/css/font-awesome.min.css' );

		wp_deregister_style( 'icomoon' );
		wp_enqueue_style( 'icomoon', get_template_directory_uri() . '/framework/assets/css/icomoon.css' );

		wp_enqueue_style( 'acf-style-font-icons', get_template_directory_uri() . '/framework/modules/acf-field-type-font-icons/css/acf-font-icons.css', array( 'acf-input', 'acf-field-group' ) ); 
		wp_enqueue_script( 'acf-js-font-icons', get_template_directory_uri() . '/framework/modules/acf-field-type-font-icons/js/acf-font-icons.js' );
	});


	/*
	 *  Add css style and script field wp_enqueue_scripts
	 */
	add_action( 'wp_enqueue_scripts', function() {
		wp_deregister_style( 'font-awesome' );
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/framework/assets/css/font-awesome.min.css' );

		wp_deregister_style( 'icomoon' );
		wp_enqueue_style( 'icomoon', get_template_directory_uri() . '/framework/assets/css/icomoon.css' );
	});
}
?>