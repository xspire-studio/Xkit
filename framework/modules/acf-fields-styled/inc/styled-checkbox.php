<?php

/*
*  ACF Styled Checkbox Field Class
*
*  All the logic for this field type
*
*  @class 		acf_field_checkbox
*  @extends		acf_field
*  @package		ACF
*  @subpackage	Fields
*/


function acf_add_field_checkbox(){

	if( ! class_exists('acf_field_checkbox') ) :

		class acf_field_checkbox extends acf_field {


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
				$this->name = 'checkbox';
				$this->label = esc_html__("Checkbox" , 'xkit' );
				$this->category = 'choice';
				$this->defaults = array(
					'layout'		=> 'vertical',
					'choices'		=> array(),
					'default_value'	=> '',
					'toggle'		=> 0
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

				// decode value (convert to array)
				$field['value'] = acf_get_array($field['value'], false);


				// hiden input
				acf_hidden_input(array(
					'type'	=> 'hidden',
					'name'	=> $field['name'],
				));


				// vars
				$i = 0;
				$li = '';
				$all_checked = true;


				// checkbox saves an array
				$field['name'] .= '[]';


				// foreach choices
				if( !empty($field['choices']) ) {

					foreach( $field['choices'] as $value => $label ) {

						// increase counter
						$i++;


						// vars
						$atts = array(
							'type'	=> 'checkbox',
							'id'	=> $field['id'], 
							'name'	=> $field['name'],
							'value'	=> $value,
						);


						// is choice selected?
						if( in_array($value, $field['value']) ) {

							$atts['checked'] = 'checked';

						} else {

							$all_checked = false;

						}


						if( isset($field['disabled']) && acf_in_array($value, $field['disabled']) ) {

							$atts['disabled'] = 'disabled';

						}


						// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
						if( $i > 1 ) {

							$atts['id'] .= '-' . $value;

						}


						// append HTML
						$li .= '<li><label><input ' . acf_esc_attr( $atts ) . '/><span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span>' . $label . '</label></li>';
					}


					// toggle all
					if( $field['toggle'] ) {

						// vars
						$label = esc_html__("Toggle All" , 'xkit' );
						$atts = array(
							'type'	=> 'checkbox',
							'class'	=> 'acf-checkbox-toggle'
						);


						// custom label
						if( is_string($field['toggle']) ) {

							$label = $field['toggle'];

						}


						// checked
						if( $all_checked ) {

							$atts['checked'] = 'checked';

						}


						// append HTML
						$li = '<li><label><input ' . acf_esc_attr( $atts ) . '/><span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span>' . $label . '</label></li>' . $li;
					}

				}


				// class
				$field['class'] .= ' acf-checkbox-list';
				$field['class'] .= ($field['layout'] == 'horizontal') ? ' acf-hl' : ' acf-bl';


				// return
				echo '<ul ' . acf_esc_attr(array( 'class' => $field['class'] )) . '>' . $li . '</ul>';

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
				$field['default_value'] = acf_encode_choices($field['default_value']);


				// choices
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Choices', 'xkit' ),
					'instructions'	=> esc_html__('Enter each choice on a new line.', 'xkit' ) . '<br /><br />' . esc_html__('For more control, you may specify both a value and label like this:', 'xkit' ). '<br /><br />' . esc_html__('red : Red', 'xkit' ),
					'type'			=> 'textarea',
					'name'			=> 'choices',
				));


				// default_value
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Default Value', 'xkit' ),
					'instructions'	=> esc_html__('Enter each default value on a new line', 'xkit' ),
					'type'			=> 'textarea',
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
						'vertical'		=> esc_html__("Vertical" , 'xkit' ), 
						'horizontal'	=> esc_html__("Horizontal" , 'xkit' )
					)
				));


				// layout
				acf_render_field_setting( $field, array(
					'label'			=> esc_html__('Toggle', 'xkit' ),
					'instructions'	=> esc_html__('Prepend an extra checkbox to toggle all choices', 'xkit' ),
					'type'			=> 'radio',
					'name'			=> 'toggle',
					'layout'		=> 'horizontal', 
					'choices'		=> array(
						1				=> esc_html__("Yes" , 'xkit' ),
						0				=> esc_html__("No" , 'xkit' ),
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
				$field['default_value'] = acf_decode_choices($field['default_value']);


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
			*
			*  @param	$value - the value which will be saved in the database
			*  @param	$post_id - the $post_id of which the value will be saved
			*  @param	$field - the field array holding all the field options
			*
			*  @return	$value - the modified value
			*/
			public function update_value( $value, $post_id, $field ) {

				// validate
				if( empty($value) ) {

					return $value;

				}


				// array
				if( is_array($value) ) {
					// save value as strings, so we can clearly search for them in SQL LIKE statements
					$value = array_map('strval', $value);
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

		new acf_field_checkbox();

	endif;
}
acf_add_field_checkbox();
?>
