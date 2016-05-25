<?php

/*
*  ACF Styled Radio Button Field Class
*
*  All the logic for this field type
*
*  @class 		acf_field_radio
*  @extends		acf_field
*  @package		ACF
*  @subpackage	Fields
*/


function acf_add_field_radio(){

	if( ! class_exists('acf_field_radio') ) :

		class acf_field_radio extends acf_field {


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

				// vars
				$this->name = 'radio';
				$this->label = esc_html__("Radio Button" , 'xkit' );
				$this->category = 'choice';
				$this->defaults = array(
					'layout'			=> 'vertical',
					'choices'			=> array(),
					'default_value'		=> '',
					'style_images'		=> 0,
					'other_choice'		=> 0,
					'save_other_choice'	=> 0,
				);


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

				// vars
				$i = 0;
				$checked = false;


				// class
				$field['class'] .= ' acf-styled-radio-list';
				$field['class'] .= ($field['layout'] == 'horizontal') ? ' acf-hl' : ' acf-bl';
				$field['class'] .= ($field['style_images']) ? ' style-images' : ' ';


				// e
				$e = '<ul ' . acf_esc_attr(array( 'class' => $field['class'] )) . '>';


				// other choice
				if( $field['other_choice'] ) {

					// vars
					$input = array(
						'type'		=> 'text',
						'name'		=> $field['name'],
						'value'		=> '',
						'disabled'	=> 'disabled'
					);


					// select other choice if value is not a valid choice
					if( !isset($field['choices'][ $field['value'] ]) ) {

						unset($input['disabled']);
						$input['value'] = $field['value'];
						$field['value'] = 'other';
					}


					$field['choices']['other'] = '</label><input type="text" ' . acf_esc_attr($input) . ' /><label>';
				}


				// require choices
				if( !empty($field['choices']) ) {

					// select first choice if value is not a valid choice
					if( !isset($field['choices'][ $field['value'] ]) ) {

						$field['value'] = key($field['choices']);

					}


					// foreach choices
					foreach( $field['choices'] as $value => $label ) {

						// increase counter
						$i++;


						// vars
						$atts = array(
							'type'	=> 'radio',
							'id'	=> $field['id'], 
							'name'	=> $field['name'],
							'value'	=> $value,
						);


						if( strval($value) === strval($field['value']) ) {

							$atts['checked'] = 'checked';
							$checked = true;

						}

						if( isset($field['disabled']) && acf_in_array($value, $field['disabled']) ) {

							$atts['disabled'] = 'disabled';

						}


						// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
						if( $i > 1 ) {

							$atts['id'] .= '-' . $value;

						}

						// if style radio
						if( $field['style_images'] ){

							$img_filename = $label;
							$img_label = '';

							if( strpos( $label, '||' ) ){
								$label_parts = explode( '||' , $label );

								$img_filename = trim( $label_parts[0] );
								$img_label = trim( $label_parts[1] );

								if( $img_label ){
									$img_label = '<span class="img-caption">' . $img_label . '</span>';
								}
							}

							$img_path = get_template_directory_uri() . '/images/' . $img_filename;

							// if is exist image
							if( xkit_is_url_exist( $img_path ) ){
								$img = '<img src="' . $img_path . '">' .  $img_label;
							} else {
								$img = '<img src="' . get_template_directory_uri() . '/framework/modules/acf-fields-styled/images/no-image.png' . '">' . esc_html__( '[Incorrect path to the image]', 'xkit' ) . $img_label;
							}

							// output image radio button
							if( !( $field['other_choice'] && $atts['value'] === 'other' ) ){
								$e .= '<li><label><input ' . acf_esc_attr( $atts ) . '/>' . $img . '</label></li>';
							}

						} else {
							// output standart radio button
							$e .= '<li><label><input ' . acf_esc_attr( $atts ) . '/><span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span>' . $label . '</label></li>';
						}

					}

				}


				$e .= '</ul>';

				print( $e );

			}


			/*
			*  render_field_settings()
			*
			*  Create extra options for your field. This is rendered when editing a field.
			*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
			*
			*  @type	action
			*  @since	3.6
			*  @date	23/01/13
			*
			*  @param	$field	- an array holding all the field's data
			*/
			public function render_field_settings( $field ) {

				// encode choices (convert from array)
				$field['choices'] = acf_encode_choices($field['choices']);


				// choices
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Choices', 'xkit' ),
					'instructions'	=> esc_html__('Enter each choice on a new line.', 'xkit' ) . '<br /><br />' . esc_html__('For more control, you may specify both a value and label like this:', 'xkit' ). '<br /><br />' . esc_html__('red : Red', 'xkit' ) . '<br /><br />' . esc_html__('If you want to use a radio image, activate this option and you may specify both a value and label like this:', 'xkit' ). '<br /><br />' . esc_html__('red : example.png || caption', 'xkit' ),
					'type'			=> 'textarea',
					'name'			=> 'choices',
					'rows'			=> '14',
				));


				// style_images
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Style images', 'xkit' ),
					'type'			=> 'true_false',
					'name'			=> 'style_images',
					'layout'		=> 'horizontal',
					'message'		=> esc_html__("Path to directory with pictures - yourtheme/images" , 'xkit' ),
					'instructions'	=> esc_html__("Do not use together with the option 'Other'. Also, if you use a horizontal layout of the display, the image resolution should be 48*48px!" , 'xkit' ),

				));


				// other_choice
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Other', 'xkit' ),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'other_choice',
					'message'		=> esc_html__("Add 'other' choice to allow for custom values" , 'xkit' )
				));


				// save_other_choice
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Save Other', 'xkit' ),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'save_other_choice',
					'message'		=> esc_html__("Save 'other' values to the field's choices" , 'xkit' )
				));


				// default_value
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Default Value', 'xkit' ),
					'instructions'	=> esc_html__('Appears when creating a new post', 'xkit' ),
					'type'			=> 'text',
					'name'			=> 'default_value',
				));


				// layout
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Layout', 'xkit' ),
					'instructions'	=> '',
					'type'			=> 'radio',
					'name'			=> 'layout',
					'layout'		=> 'horizontal', 
					'choices'		=> array(
						'vertical'		=> esc_html__('Vertical', 'xkit' ), 
						'horizontal'	=> esc_html__('Horizontal', 'xkit' )
					)
				));
			}


			/*
			*  update_field()
			*
			*  This filter is appied to the $field before it is saved to the database
			*
			*  @type	filter
			*  @since	3.6
			*  @date	23/01/13
			*
			*  @param	$field - the field array holding all the field options
			*  @param	$post_id - the field group ID (post_type = acf)
			*
			*  @return	$field - the modified field
			*/
			public function update_field( $field ) {

				// decode choices (convert to array)
				$field['choices'] = acf_decode_choices($field['choices']);


				// return
				return $field;
			}


			/*
			*  update_value()
			*
			*  This filter is appied to the $value before it is updated in the db
			*
			*  @type	filter
			*  @since	3.6
			*  @date	23/01/13
			*  @todo	Fix bug where $field was found via json and has no ID
			*
			*  @param	$value - the value which will be saved in the database
			*  @param	$post_id - the $post_id of which the value will be saved
			*  @param	$field - the field array holding all the field options
			*
			*  @return	$value - the modified value
			*/
			public function update_value( $value, $post_id, $field ) {

				// save_other_choice
				if( $field['save_other_choice'] ) {

					// value isn't in choices yet
					if( !isset($field['choices'][ $value ]) ) {

						// get ID if local
						if( !$field['ID'] ) {

							$field = acf_get_field( $field['key'], true );

						}


						// bail early if no ID
						if( !$field['ID'] ) {

							return $value;

						}


						// update $field
						$field['choices'][ $value ] = $value;


						// save
						acf_update_field( $field );

					}

				}


				// return
				return $value;
			}


			/*
			*  load_value()
			*
			*  This filter is appied to the $value after it is loaded from the db
			*
			*  @type	filter
			*  @since	5.2.9
			*  @date	23/01/13
			*
			*  @param	$value - the value found in the database
			*  @param	$post_id - the $post_id from which the value was loaded from
			*  @param	$field - the field array holding all the field options
			*
			*  @return	$value - the value to be saved in te database
			*/
			public function load_value( $value, $post_id, $field ) {
				// must be single value
				if( is_array($value) ) {

					$value = array_pop($value);

				}

				// return
				return $value;

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
				$field['choices'] = acf_translate( $field['choices'] );
				
				
				// return
				return $field;
				
			}

		}

		new acf_field_radio();

	endif;

}
acf_add_field_radio();
?>