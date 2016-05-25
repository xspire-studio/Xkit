<?php
/**
 * Contents of the Import/Export popup in the widgets screen.
 *
 * This file is included in widgets.php.
 */
?>

<div class="wpmui-form module-export">
	<h2 class="no-pad-top"><?php esc_html_e( 'Export', 'xkit' ); ?></h2>
	<form class="frm-export">
		<input type="hidden" name="do" value="export" />
		<p>
			<i class="dashicons dashicons-info light"></i>
			<?php esc_html_e( 'This will generate a complete export file containing all your sidebars and the current sidebar configuration.', 'xkit' ); ?>
		</p>
		<p>
			<label for="description"><?php esc_html_e( 'Optional description for the export file:', 'xkit' ); ?></label><br />
			<textarea id="description" name="export-description" placeholder="" cols="80" rows="3"></textarea>
		</p>
		<p>
			<button class="button-primary">
				<i class="dashicons dashicons-download"></i> <?php esc_html_e( 'Export', 'xkit' ); ?>
			</button>
		</p>
	</form>
	<hr />
	<h2><?php esc_html_e( 'Import', 'xkit' ); ?></h2>
	<form class="frm-preview-import">
		<input type="hidden" name="do" value="preview-import" />
		<p>
			<label for="import-file"><?php esc_html_e( 'Export file', 'xkit' ); ?></label>
			<input type="file" id="import-file" name="data" />
		</p>
		<p>
			<button class="button-primary">
				<i class="dashicons dashicons-upload"></i> <?php esc_html_e( 'Preview', 'xkit' ); ?>
			</button>
		</p>
	</form>
	<div class="pro-layer">
		<?php printf(
			wp_kses_post( __(	'Import / Export functionality is available<br />in the <b>PRO</b> version of this plugin.<br /><a href="%1$s" target="_blank">Learn more</a>'	, 'xkit' ) ),
				Xkit_CustomSidebars::$pro_url
		); ?>
	</div>
</div>