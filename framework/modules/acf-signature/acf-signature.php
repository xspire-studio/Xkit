<?php
/*
 * Module Name: Signature ACF Field Groups
 * Version: 1.0
 * Author: Xspire
*/


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Signature activation
	 */
	function acf_signature_activation() {
		global $wpdb;

		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'acf-field-group'
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id = get_the_ID();

				$signature = (string) get_post_meta( $post_id, 'signature_acf_field', true );

				update_post_meta( $post_id, 'signature_acf_field', $signature );
			}
		}
		wp_reset_postdata();

	}
	add_action('xkit_framework_active', 'acf_signature_activation');


	/*
	 *  Adding to the ACF signature (BOX)
	 */
	function acf_signature_field_add_meta_box() {
		add_meta_box('signature_acf_field_sectionid', esc_html__( 'Signature group', 'xkit' ),
			'acf_signature_field_meta_box_callback',
			'acf-field-group'
		);
	}
	add_action( 'add_meta_boxes', 'acf_signature_field_add_meta_box' );


	/* Callback signature ACF */
	function acf_signature_field_meta_box_callback( $post ) {

		wp_nonce_field( 'signature_acf_field_meta_box', 'signature_acf_field_meta_box_nonce' );

		$value = get_post_meta( $post->ID, 'signature_acf_field', true );

		echo '<input type="text" id="signature_acf_field" name="signature_acf_field" value="' . esc_attr( $value ) . '" size="40" />';
	}


	/* Save signature ACF */
	function acf_signature_field_save_meta_box_data( $post_id ) {
		// Make sure that it is set.
		if ( isset( $_POST['signature_acf_field'] ) ){
			$signature = sanitize_text_field( $_POST['signature_acf_field'] );

			// Update the meta field in the database.
			update_post_meta( $post_id, 'signature_acf_field', $signature );
		}
	}
	add_action( 'save_post_acf-field-group', 'acf_signature_field_save_meta_box_data' );


	/*
	 *  Adding a signature in the column Field Groups
	 */
	add_filter( 'manage_edit-acf-field-group_columns', 'acf_adv_add_new_field_group_columns', 11 );
	function acf_adv_add_new_field_group_columns( $columns ) {

		$column_signature = array( 'signature' => esc_html__( 'Signature', 'xkit' ) );

		$columns = array_slice( $columns, 0, 1, true ) + $column_signature + array_slice( $columns, 1, NULL, true );

		return $columns;
	}

	add_action( 'manage_acf-field-group_posts_custom_column', 'acf_adv_manage_field_group_columns', 11, 2 );
	function acf_adv_manage_field_group_columns( $column , $id ) {
		global $wpdb;

		switch ($column) {
			case 'signature':
					$signature = get_post_meta( $id, 'signature_acf_field', true);

					if( $signature ){
						echo '<span class="circle"></span> <span class="signature">[' . esc_html( $signature ) . ']</span>';
					} else{
						echo '<span class="circle no"></span> <span class="signature no">' . esc_html__( 'No', 'xkit' ) . '</span>';
					}
				break;
			default:
				break;
		}
	}


	/*
	 * Setting sorting Field Groups
	 */
	function acf_field_group_column_orderby( $vars ) {
		if( !XKIT_THEME_DEBUG ){
			if ( isset( $vars['post_type'] ) && ( $vars['post_type'] == 'acf-field-group' ) ){
				$vars = array_merge( $vars, array(
					'meta_key' => 'signature_acf_field',
					'orderby' => array( 'meta_value' => 'ASC', 'menu_order' => 'ASC' )
				) );
			}
		}

		return $vars;
	}
	add_filter( 'request', 'acf_field_group_column_orderby' );


	/*
	 * Custom js and css for Field Groups
	 */
	function acf_custom_field_group_style() {
		if( isset( $_REQUEST['post_type'] ) && ( $_REQUEST['post_type'] == 'acf-field-group' ) ){
			wp_enqueue_style( 'acf-css-signature', get_template_directory_uri() . '/framework/modules/acf-signature/css/style.css' );

			wp_enqueue_script( 'acf-js-signature-hash', get_template_directory_uri() . '/framework/modules/acf-signature/js/color-hash.js' );
			wp_enqueue_script( 'acf-js-signature-init', get_template_directory_uri() . '/framework/modules/acf-signature/js/init-signature_acf.js' );
		}
	}
	add_action('admin_init', 'acf_custom_field_group_style'); 
}
?>