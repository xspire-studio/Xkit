<?php
/**
 * Updates the default widgets page of the admin area.
 * There are some HTML to be added for having all the functionality, so we
 * include it at the begining of the page, and it's placed later via js.
 */
?>

<div id="cs-widgets-extra">

	<?php /*
	============================================================================
	===== WIDGET head
	============================================================================
	*/ ?>
	<div id="cs-title-options">
		<h2><?php esc_html_e( 'Sidebars', 'xkit' ); ?></h2>
		<div id="cs-options" class="csb cs-options">
			<button type="button" class="button button-primary cs-action btn-create-sidebar">
				<i class="dashicons dashicons-plus-alt"></i>
				<?php esc_html_e( 'Create a new sidebar', 'xkit' ); ?>
			</button>
			<?php /*<a href="#" class="cs-action btn-export"><?php esc_html_e( 'Import / Export Sidebars', 'xkit' ); ?></a>*/ ?>
			<?php
			/**
			 * Show additional functions in the widget header.
			 */
			do_action( 'xkit_cs_widget_header' );
			?>
		</div>
		<div class="clear"></div>
	</div>


	<?php /*
	============================================================================
	===== LANGUAGE
	============================================================================
	*/ ?>
	<script>
	csSidebarsData = {
		'title_edit': "<?php esc_html_e( 'Edit [Sidebar]', 'xkit' ); ?>",
		'title_new': "<?php esc_html_e( 'New Custom Sidebar', 'xkit' ); ?>",
		'btn_edit': "<?php esc_html_e( 'Save Changes', 'xkit' ); ?>",
		'btn_new': "<?php esc_html_e( 'Create Sidebar', 'xkit' ); ?>",
		'title_delete': "<?php esc_html_e( 'Delete Sidebar', 'xkit' ); ?>",
		'title_location': "<?php esc_html_e( 'Define where you want this sidebar to appear.', 'xkit' ); ?>",
		'title_export': "<?php esc_html_e( 'Import / Export Sidebars', 'xkit' ); ?>",
		'custom_sidebars': "<?php esc_html_e( 'Custom Sidebars', 'xkit' ); ?>",
		'theme_sidebars': "<?php esc_html_e( 'Theme Sidebars', 'xkit' ); ?>",
		'ajax_error': "<?php esc_html_e( 'Couldn\'t load data from WordPress...', 'xkit' ); ?>",
		'lbl_replaceable': "<?php esc_html_e( 'This sidebar can be replaced on certain pages', 'xkit' ); ?>",
		'replace_tip': "<?php esc_html_e( 'Activate this option to replace the sidebar with one of your custom sidebars.', 'xkit' ); ?>",
		'filter': "<?php esc_html_e( 'Filter...', 'xkit' ); ?>",
		'replaceable': <?php echo json_encode( (object) Xkit_CustomSidebars::get_options( 'modifiable' ) ); ?>
	};
	</script>


	<?php /*
	============================================================================
	===== TOOLBAR for custom sidebars
	============================================================================
	*/ ?>
	<div class="cs-custom-sidebar cs-toolbar">
		<a
			class="cs-tool delete-sidebar"
			data-action="delete"
			href="#"
			title="<?php esc_html_e( 'Delete this sidebar.', 'xkit' ); ?>"
			>
			<i class="dashicons dashicons-trash"></i>
		</a>
		<span class="cs-separator">|</span>
		<a
			class="cs-tool"
			data-action="edit"
			href="#"
			title="<?php esc_html_e( 'Edit this sidebar.', 'xkit' ); ?>"
			>
			<?php esc_html_e( 'Edit', 'xkit' ); ?>
		</a>
		<span class="cs-separator">|</span>
		<a
			class="cs-tool"
			data-action="location"
			href="#"
			title="<?php esc_html_e( 'Where do you want to show the sidebar?', 'xkit' ); ?>"
			>
			<?php esc_html_e( 'Sidebar Location', 'xkit' ); ?>
		</a>
		<span class="cs-separator">|</span>
	</div>


	<?php /*
	============================================================================
	===== TOOLBAR for theme sidebars
	============================================================================
	*/ ?>
	<div class="cs-theme-sidebar cs-toolbar">
		<label
			for="cs-replaceable"
			class="cs-tool btn-replaceable"
			data-action="replaceable"
			data-on="<?php esc_html_e( 'This sidebar can be replaced on certain pages', 'xkit' ); ?>"
			data-off="<?php esc_html_e( 'This sidebar will always be same on all pages', 'xkit' ); ?>"
			>
			<span class="icon"></span>
			<input
				type="checkbox"
				id=""
				class="has-label chk-replaceable"
				/>
			<span class="is-label">
				<?php esc_html_e( 'Allow this sidebar to be replaced', 'xkit' ); ?>
			</span>
		</label>
		<span class="cs-separator">|</span>
		<span class="">
			<a
				class="cs-tool"
				data-action="location"
				href="#"
				title="<?php esc_html_e( 'Where do you want to show the sidebar?', 'xkit' ); ?>"
				>
				<?php esc_html_e( 'Sidebar Location', 'xkit' ); ?>
			</a>
			<span class="cs-separator">|</span>
		</span>
	</div>


	<?php /*
	============================================================================
	===== DELETE SIDEBAR confirmation
	============================================================================
	*/ ?>
	<div class="cs-delete">
	<?php load_template( get_template_directory() . '/framework/modules/sidebar-generator/views/widgets-delete.php' ); ?>
	</div>


	<?php /*
	============================================================================
	===== ADD/EDIT SIDEBAR
	============================================================================
	*/ ?>
	<div class="cs-editor">
	<?php load_template( get_template_directory() . '/framework/modules/sidebar-generator/views/widgets-editor.php' ); ?>
	</div>

	<?php /*
	============================================================================
	===== LOCATION popup.
	============================================================================
	*/ ?>
	<div class="cs-location">
	<?php load_template( get_template_directory() . '/framework/modules/sidebar-generator/views/widgets-location.php' ); ?>
	</div>

 </div>
