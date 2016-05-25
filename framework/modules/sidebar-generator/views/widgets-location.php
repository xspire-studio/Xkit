<?php
/**
 * Contents of the Location popup in the widgets screen.
 * User can define default locations where the custom sidebar will be used.
 *
 * This file is included in widgets.php.
 */

$sidebars = Xkit_CustomSidebars::get_sidebars( 'theme' );

/**
 * Output the input fields to configure replacements for a single sidebar.
 *
 * @since  2.0
 * @param  array $sidebar Details provided by Xkit_CustomSidebars::get_sidebar().
 * @param  string $prefix Category specific prefix used for input field ID/Name.
 * @param  string $cat_name Used in label: "Replace sidebar for <cat_name>".
 * @param  string $class Optinal classname added to the wrapper element.
 */
function xkit_show_replaceable( $sidebar, $prefix, $cat_name, $class = '' ) {
	$base_id = 'cs-' . $prefix;
	$inp_id = $base_id . '-' . $sidebar['id'];
	$inp_name = 'cs[' . $prefix . '][' . $sidebar['id'] . ']';
	$sb_id = $sidebar['id'];
	$class = (empty( $class ) ? '' : ' ' . $class);

	?>
	<div
		class="cs-replaceable <?php echo esc_attr( $sb_id . $class ); ?>"
		data-lbl-used="<?php esc_html_e( 'Replaced by another sidebar:', 'xkit' ); ?>"
		>
		<label for="<?php echo esc_attr( $inp_id ); ?>">
			<input type="checkbox"
				id="<?php echo esc_attr( $inp_id ); ?>"
				class="detail-toggle"
				/>
			<?php printf(
				__( 'As <strong>%1$s</strong> for selected %2$s', 'xkit' ),
				$sidebar['name'],
				$cat_name
			); ?>
		</label>
		<div class="details">
			<select
				class="cs-datalist <?php echo esc_attr( $base_id ); ?>"
				name="<?php echo esc_attr( $inp_name ); ?>[]"
				multiple="multiple"
				placeholder="<?php echo esc_attr(
					sprintf(
						esc_html__( 'Click here to pick available %1$s', 'xkit' ),
						$cat_name
					)
				); ?>"
			>
			</select>
		</div>
	</div>
	<?php

}

?>

<form class="frm-location wpmui-form">
	<input type="hidden" name="do" value="set-location" />
	<input type="hidden" name="sb" class="sb-id" value="" />

	<div class="cs-title">
		<h3 class="no-pad-top">
			<span class="sb-name">...</span>
		</h3>
	</div>
	<p>
		<i class="dashicons dashicons-info light"></i>
		<?php printf(
			wp_kses_post( __( 'To attach this sidebar to a unique Post or Page please visit that <a href="%1$s">Post</a> or <a href="%2$s">Page</a> & set it up via the sidebars metabox.', 'xkit' ) ),
			admin_url( 'edit.php' ),
			admin_url( 'edit.php?post_type=page' )
		); ?>
	</p>

	<?php
	/**
	 * =========================================================================
	 * Box 1: SINGLE entries (single pages, categories)
	 */
	?>
	<div class="wpmui-box">
		<h3>
			<a href="#" class="toggle" title="<?php esc_html_e( 'Click to toggle', 'xkit' ); /* This is a Wordpress default language */ ?>"><br></a>
			<span><?php esc_html_e( 'For all Single Entries matching selected criteria', 'xkit' ); ?></span>
		</h3>
		<div class="inside">
			<p><?php esc_html_e( 'These replacements will be applied to every single post that matches a certain post type or category.', 'xkit' ); ?>

			<div class="cs-half">
			<?php
			/**
			 * ========== SINGLE -- Categories ========== *
			 */
			foreach ( $sidebars as $sb_id => $details ) {
				$cat_name = esc_html__( 'categories', 'xkit' );
				xkit_show_replaceable( $details, 'cat', $cat_name );
			}
			?>
			</div>

			<div class="cs-half">
			<?php
			/**
			 * ========== SINGLE -- Post-Type ========== *
			 */
			foreach ( $sidebars as $sb_id => $details ) {
				$cat_name = esc_html__( 'Post Types', 'xkit' );
				xkit_show_replaceable( $details, 'pt', $cat_name );
			}
			?>
			</div>

		</div>
	</div>

	<?php
	/**
	 * =========================================================================
	 * Box 2: ARCHIVE pages
	 */
	?>
	<div class="wpmui-box">
		<h3>
			<a href="#" class="toggle" title="<?php esc_html_e( 'Click to toggle', 'xkit' ); /* This is a Wordpress default language */ ?>"><br></a>
			<span><?php esc_html_e( 'For Archives', 'xkit' ); ?></span>
		</h3>
		<div class="inside">
			<p><?php esc_html_e( 'These replacements will be applied to Archive Type posts and pages.', 'xkit' ); ?>

			<div class="cs-half">
				<?php
				/**
				 * ========== ARCHIVE -- Special ========== *
				 */
				foreach ( $sidebars as $sb_id => $details ) {
					$cat_name = esc_html__( 'Archive Types', 'xkit' );
					xkit_show_replaceable( $details, 'arc', $cat_name );
				}
				?>
			</div>
			<div class="cs-half">
				<?php
				/**
				 * ========== ARCHIVE -- Category ========== *
				 */
				foreach ( $sidebars as $sb_id => $details ) {
					$cat_name = esc_html__( 'Category Archives', 'xkit' );
					xkit_show_replaceable( $details, 'arc-cat', $cat_name );
				}
				?>
			</div>
		</div>
	</div>

	<div class="buttons">
		<button type="button" class="button-link btn-cancel"><?php esc_html_e( 'Cancel', 'xkit' ); ?></button>
		<button type="button" class="button-primary btn-save"><?php esc_html_e( 'Save Changes', 'xkit' ); ?></button>
	</div>
</form>
