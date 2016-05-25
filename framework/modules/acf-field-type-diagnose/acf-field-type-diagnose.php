<?php
/*
 * Module Name: Advanced Custom Fields: System Diagnose
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {

		/*
		 *  Add addons for ACF: field diagnose
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field diagnose
			 */
			class acf_field_diagnose extends acf_field {

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

					$this->name = 'diagnose';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'System Diagnose', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'content';


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
						<div class="system-diagnose">
							<ul>
								<li><strong><?php esc_html_e( 'Theme Name', 'xkit' ); ?>:</strong> <?php echo XKIT_THEME_NAME; ?></li>
								<li><strong><?php esc_html_e( 'Theme Version', 'xkit' ); ?>:</strong> <?php echo XKIT_THEME_VERSION; ?></li>
								<li><strong><?php esc_html_e( 'Site URL', 'xkit' ); ?>:</strong> <?php echo esc_url( home_url( '/' ) ); ?></li>
								<li><strong><?php esc_html_e( 'WordPress Version', 'xkit' ); ?>:</strong> <?php echo is_multisite() ? 'WPMU ' . get_bloginfo( 'version' ) : 'WP ' . get_bloginfo( 'version' ); ?></li>
								<li><strong><?php esc_html_e( 'Web Server Info', 'xkit' ); ?>:</strong> <?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></li>

								<?php if ( function_exists( 'phpversion' ) ): ?>
									<li><strong><?php esc_html_e( 'PHP Version', 'xkit' ); ?>:</strong> <?php echo esc_html( phpversion() ); ?></li>
								<?php endif; ?>

								<?php if ( function_exists( 'size_format' ) ): ?>
									<li><strong><?php esc_html_e( 'WP Memory Limit', 'xkit' ); ?>:</strong> <?php
										$mem_limit = $this->nummerize( WP_MEMORY_LIMIT );

										if ( $mem_limit < 67108864 ) {
											echo '<span class="error">' . size_format( $mem_limit ) . esc_html__( ' - Recommended memory limit should be at least 64MB. Please refer to : ', 'xkit' ) . '<a target="_blank" href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP">' . esc_html__( 'Increasing memory allocated to PHP', 'xkit' ) . '</a> ' . esc_html__( 'for more information', 'xkit' ) . '</span>';
										} else {
											echo '<span>' . size_format( $mem_limit ) . '</span>';
										}
									?></li>

									<li><strong><?php esc_html_e( 'WP Max Upload Size', 'xkit' ); ?>:</strong> <?php echo size_format( wp_max_upload_size() ); ?></li>
								<?php endif; ?>

								<?php if ( function_exists( 'ini_get' ) ): ?>
									<li><strong><?php esc_html_e( 'PHP Time Limit', 'xkit' ); ?>:</strong> <?php echo ini_get( 'max_execution_time' ); ?></li>
								<?php endif; ?>

								<li><strong><?php esc_html_e( 'WP Debug Mode', 'xkit' ); ?>:</strong> <?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Enabled' : 'Disabled'; ?></li>
								<li><strong><?php esc_html_e( 'Theme Debug', 'xkit' ); ?>:</strong> <?php echo defined( 'XKIT_THEME_DEBUG' ) && XKIT_THEME_DEBUG ? 'Enabled' : 'Disabled'; ?></li>
							</ul>
						</div>
					<?php
				}

				public function nummerize( $size ) {
					$let = substr( $size, -1 );
					$ret = substr( $size, 0, -1 );
					switch ( strtoupper( $let ) ) {
					case 'P':
						$ret *= 1024;
					case 'T':
						$ret *= 1024;
					case 'G':
						$ret *= 1024;
					case 'M':
						$ret *= 1024;
					case 'K':
						$ret *= 1024;
					}
					return $ret;
				}
			}

			new acf_field_diagnose();
		}
	});


	/*
	 *  Add css style and script field admin_init
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-style-diagnose', get_template_directory_uri() . '/framework/modules/acf-field-type-diagnose/css/acf-diagnose.css', array( 'acf-input', 'acf-field-group' ) );
	});
}
?>