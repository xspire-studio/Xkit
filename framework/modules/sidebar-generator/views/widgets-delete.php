<?php
/**
 * Contents of the Delete-sidebar popup in the widgets screen.
 *
 * This file is included in widgets.php.
 */
?>

<div class="wpmui-form">
	<div>
	<?php echo wp_kses_post( __( 'Please confirm that you want to delete the sidebar <strong class="name"></strong>.', 'xkit' ) ); ?>
	</div>
	<div class="buttons">
		<button type="button" class="button-link btn-cancel"><?php esc_html_e( 'Cancel', 'xkit' ); ?></button>
		<button type="button" class="button-primary btn-delete"><?php esc_html_e( 'Yes, delete it', 'xkit' ); ?></button>
	</div>
</div>