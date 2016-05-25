<?php
/**
 * Contents of the Add/Edit sidebar popup in the widgets screen.
 *
 * This file is included in widgets.php.
 */
?>

<form class="wpmui-form">
	<input type="hidden" name="do" value="save" />
	<input type="hidden" name="sb" id="csb-id" value="" />

	<div class="wpmui-grid-8 no-pad-top">
		<div class="col-3">
			<label for="csb-name"><?php esc_html_e( 'Name', 'xkit' ); ?></label>
			<input type="text" name="name" id="csb-name" maxlength="40" placeholder="<?php esc_html_e( 'Sidebar name here...', 'xkit' ); ?>" />
			<div class="hint"><?php esc_html_e( 'The name must be unique.', 'xkit' ); ?></div>
		</div>
		<div class="col-5">
			<label for="csb-description"><?php esc_html_e( 'Description', 'xkit' ); ?></label>
			<input type="text" name="description" id="csb-description" maxlength="200" placeholder="<?php esc_html_e( 'Sidebar description here...', 'xkit' ); ?>" />
		</div>
	</div>
	<hr class="csb-more-content" />
	<div class="wpmui-grid-8 csb-more-content">
		<div class="col-8 hint">
			<strong><?php esc_html_e( 'Caution:', 'xkit' ); ?></strong>
			<?php echo wp_kses_post( __( 'Before-after title-widget properties define the html code that will wrap the widgets and their titles in the sidebars, more info about them <a href="https://codex.wordpress.org/Function_Reference/register_sidebar" target="_blank">here</a>. Do not use these fields if you are not sure what you are doing, it can break the design of your site. Leave these fields blank to use the theme sidebars design.', 'xkit' ) ); ?>
		</div>
	</div>
	<?php
		$cs_args = array(
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			'before_widget' => '<div id="%1$s" class="%2$s widget clearfx">',
			'after_widget'  => '</div>'
		);
		$cs_args = apply_filters( 'xkit_cs_before_title', $cs_args );
	?>
	<div class="wpmui-grid-8 csb-more-content">
		<div class="col-4">
			<label for="csb-before-title"><?php esc_html_e( 'Before Title', 'xkit' ); ?></label>
			<textarea rows="4" name="before_title" id="csb-before-title"><?php echo wp_kses( $cs_args['before_title'], 'post' ); ?></textarea>
		</div>
		<div class="col-4">
			<label for="csb-after-title"><?php esc_html_e( 'After Title', 'xkit' ); ?></label>
			<textarea rows="4" name="after_title" id="csb-after-title"><?php echo wp_kses( $cs_args['after_title'], 'post' ); ?></textarea>
		</div>
	</div>
	<div class="wpmui-grid-8 csb-more-content">
		<div class="col-4">
			<label for="csb-before-widget"><?php esc_html_e( 'Before Widget', 'xkit' ); ?></label>
			<textarea rows="4" name="before_widget" id="csb-before-widget"><?php echo wp_kses( $cs_args['before_widget'], 'post' ); ?></textarea>
		</div>
		<div class="col-4">
			<label for="csb-after-widget"><?php esc_html_e( 'After Widget', 'xkit' ); ?></label>
			<textarea rows="4" name="after_widget" id="csb-after-widget"><?php echo wp_kses( $cs_args['after_widget'], 'post' ); ?></textarea>
		</div>
	</div>
	<div class="buttons">
		<label for="csb-more" class="wpmui-left">
			<input type="checkbox" id="csb-more" />
			<?php esc_html_e( 'Advanced - Edit custom wrapper code', 'xkit' ); ?>
		</label>

		<button type="button" class="button-link btn-cancel"><?php esc_html_e( 'Cancel', 'xkit' ); ?></button>
		<button type="button" class="button-primary btn-save"><?php esc_html_e( 'Create Sidebar', 'xkit' ); ?></button>
	</div>
</form>
