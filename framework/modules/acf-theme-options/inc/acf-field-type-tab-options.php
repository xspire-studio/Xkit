<?php
/*
 * Module Name: Advanced Custom Fields: Tab Options
 * Version: 1.0.0
 * Author: Xspire
 */


if( class_exists( 'acf_pro' ) && defined( 'XKIT_OPTIONS_MODULE_ENABLE' ) && XKIT_OPTIONS_MODULE_ENABLE ) {

	/*
	 *  Init module
	 */
	add_action( 'init', function() {

		/*
		 *  Add addons for ACF: field tab options
		 */
		if( class_exists( 'acf_field' ) ) {

			/*
			 *  Acf field tab options
			 */
			class acf_field_tab_options extends acf_field {

				/**
				 * __construct
				 *
				 * This function will setup the field type data
				 *
				 * @type	function
				 * @date	5/03/2014
				 * @since	5.0.0
				 *
				 * @param	n/a
				 * @return	n/a
				 */
				public function __construct() {

					/*
					 *  name (string) Single word, no spaces. Underscores allowed
					 */

					$this->name = 'tab-options';


					/*
					 *  label (string) Multiple words, can include spaces, visible when selecting a field type
					 */

					$this->label = esc_html__( 'Tab Options', 'xkit' );


					/*
					 *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
					 */

					$this->category = 'layout';


					/*
					 *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
					 */

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

					/*
					 *  acf_render_field_setting
					 */
					acf_render_field_setting( $field, array(
						'label'			=> esc_html__( 'Icon tab', 'xkit' ),
						'type'			=> 'select',
						'name'			=> 'icon_tab',
						'choices'		=> array(
							'dashicons-menu'                   =>    'dashicons-menu',
							'dashicons-admin-site'             =>    'dashicons-admin-site',
							'dashicons-dashboard'              =>    'dashicons-dashboard',
							'dashicons-admin-media'            =>    'dashicons-admin-media',
							'dashicons-admin-page'             =>    'dashicons-admin-page',
							'dashicons-admin-comments'         =>    'dashicons-admin-comments',
							'dashicons-admin-appearance'       =>    'dashicons-admin-appearance',
							'dashicons-admin-plugins'          =>    'dashicons-admin-plugins',
							'dashicons-admin-users'            =>    'dashicons-admin-users',
							'dashicons-admin-tools'            =>    'dashicons-admin-tools',
							'dashicons-admin-settings'         =>    'dashicons-admin-settings',
							'dashicons-admin-network'          =>    'dashicons-admin-network',
							'dashicons-admin-generic'          =>    'dashicons-admin-generic',
							'dashicons-admin-home'             =>    'dashicons-admin-home',
							'dashicons-admin-collapse'         =>    'dashicons-admin-collapse',
							'dashicons-filter'                 =>    'dashicons-filter',
							'dashicons-admin-customizer'       =>    'dashicons-admin-customizer',
							'dashicons-admin-multisite'        =>    'dashicons-admin-multisite',
							'dashicons-format-links'           =>    'dashicons-format-links',
							'dashicons-format-standard'        =>    'dashicons-format-standard',
							'dashicons-format-image'           =>    'dashicons-format-image',
							'dashicons-format-gallery'         =>    'dashicons-format-gallery',
							'dashicons-format-audio'           =>    'dashicons-format-audio',
							'dashicons-format-video'           =>    'dashicons-format-video',
							'dashicons-format-chat'            =>    'dashicons-format-chat',
							'dashicons-format-status'          =>    'dashicons-format-status',
							'dashicons-format-aside'           =>    'dashicons-format-aside',
							'dashicons-format-quote'           =>    'dashicons-format-quote',
							'dashicons-welcome-edit-page'      =>    'dashicons-welcome-edit-page',
							'dashicons-welcome-add-page'       =>    'dashicons-welcome-add-page',
							'dashicons-welcome-view-site'      =>    'dashicons-welcome-view-site',
							'dashicons-welcome-widgets-menus'  =>    'dashicons-welcome-widgets-menus',
							'dashicons-welcome-comments'       =>    'dashicons-welcome-comments',
							'dashicons-welcome-learn-more'     =>    'dashicons-welcome-learn-more',
							'dashicons-image-crop'             =>    'dashicons-image-crop',
							'dashicons-image-rotate'           =>    'dashicons-image-rotate',
							'dashicons-image-rotate-left'      =>    'dashicons-image-rotate-left',
							'dashicons-image-rotate-right'     =>    'dashicons-image-rotate-right',
							'dashicons-image-flip-vertical'    =>    'dashicons-image-flip-vertical',
							'dashicons-image-flip-horizontal'  =>    'dashicons-image-flip-horizontal',
							'dashicons-image-filter'           =>    'dashicons-image-filter',
							'dashicons-undo'                   =>    'dashicons-undo',
							'dashicons-redo'                   =>    'dashicons-redo',
							'dashicons-editor-bold'            =>    'dashicons-editor-bold',
							'dashicons-editor-italic'          =>    'dashicons-editor-italic',
							'dashicons-editor-ul'              =>    'dashicons-editor-ul',
							'dashicons-editor-ol'              =>    'dashicons-editor-ol',
							'dashicons-editor-quote'           =>    'dashicons-editor-quote',
							'dashicons-editor-alignleft'       =>    'dashicons-editor-alignleft',
							'dashicons-editor-aligncenter'     =>    'dashicons-editor-aligncenter',
							'dashicons-editor-alignright'      =>    'dashicons-editor-alignright',
							'dashicons-editor-insertmore'      =>    'dashicons-editor-insertmore',
							'dashicons-editor-spellcheck'      =>    'dashicons-editor-spellcheck',
							'dashicons-editor-expand'          =>    'dashicons-editor-expand',
							'dashicons-editor-contract'        =>    'dashicons-editor-contract',
							'dashicons-editor-kitchensink'     =>    'dashicons-editor-kitchensink',
							'dashicons-editor-underline'       =>    'dashicons-editor-underline',
							'dashicons-editor-justify'         =>    'dashicons-editor-justify',
							'dashicons-editor-textcolor'       =>    'dashicons-editor-textcolor',
							'dashicons-editor-paste-word'      =>    'dashicons-editor-paste-word',
							'dashicons-editor-paste-text'      =>    'dashicons-editor-paste-text',
							'dashicons-editor-removeformatting'=>    'dashicons-editor-removeformatting',
							'dashicons-editor-video'           =>    'dashicons-editor-video',
							'dashicons-editor-customchar'      =>    'dashicons-editor-customchar',
							'dashicons-editor-outdent'         =>    'dashicons-editor-outdent',
							'dashicons-editor-indent'          =>    'dashicons-editor-indent',
							'dashicons-editor-help'            =>    'dashicons-editor-help',
							'dashicons-editor-strikethrough'   =>    'dashicons-editor-strikethrough',
							'dashicons-editor-unlink'          =>    'dashicons-editor-unlink',
							'dashicons-editor-rtl'             =>    'dashicons-editor-rtl',
							'dashicons-editor-break'           =>    'dashicons-editor-break',
							'dashicons-editor-code'            =>    'dashicons-editor-code',
							'dashicons-editor-paragraph'       =>    'dashicons-editor-paragraph',
							'dashicons-editor-table'           =>    'dashicons-editor-table',
							'dashicons-align-left'             =>    'dashicons-align-left',
							'dashicons-align-right'            =>    'dashicons-align-right',
							'dashicons-align-center'           =>    'dashicons-align-center',
							'dashicons-align-none'             =>    'dashicons-align-none',
							'dashicons-lock'                   =>    'dashicons-lock',
							'dashicons-unlock'                 =>    'dashicons-unlock',
							'dashicons-calendar'               =>    'dashicons-calendar',
							'dashicons-calendar-alt'           =>    'dashicons-calendar-alt',
							'dashicons-visibility'             =>    'dashicons-visibility',
							'dashicons-hidden'                 =>    'dashicons-hidden',
							'dashicons-post-status'            =>    'dashicons-post-status',
							'dashicons-edit'                   =>    'dashicons-edit',
							'dashicons-trash'                  =>    'dashicons-trash',
							'dashicons-sticky'                 =>    'dashicons-sticky',
							'dashicons-external'               =>    'dashicons-external',
							'dashicons-arrow-up'               =>    'dashicons-arrow-up',
							'dashicons-arrow-down'             =>    'dashicons-arrow-down',
							'dashicons-arrow-left'             =>    'dashicons-arrow-left',
							'dashicons-arrow-right'            =>    'dashicons-arrow-right',
							'dashicons-arrow-up-alt'           =>    'dashicons-arrow-up-alt',
							'dashicons-arrow-down-alt'         =>    'dashicons-arrow-down-alt',
							'dashicons-arrow-left-alt'         =>    'dashicons-arrow-left-alt',
							'dashicons-arrow-right-alt'        =>    'dashicons-arrow-right-alt',
							'dashicons-arrow-up-alt2'          =>    'dashicons-arrow-up-alt2',
							'dashicons-arrow-down-alt2'        =>    'dashicons-arrow-down-alt2',
							'dashicons-arrow-left-alt2'        =>    'dashicons-arrow-left-alt2',
							'dashicons-arrow-right-alt2'       =>    'dashicons-arrow-right-alt2',
							'dashicons-leftright'              =>    'dashicons-leftright',
							'dashicons-sort'                   =>    'dashicons-sort',
							'dashicons-randomize'              =>    'dashicons-randomize',
							'dashicons-list-view'              =>    'dashicons-list-view',
							'dashicons-excerpt-view'           =>    'dashicons-excerpt-view',
							'dashicons-grid-view'              =>    'dashicons-grid-view',
							'dashicons-hammer'                 =>    'dashicons-hammer',
							'dashicons-art'                    =>    'dashicons-art',
							'dashicons-migrate'                =>    'dashicons-migrate',
							'dashicons-performance'            =>    'dashicons-performance',
							'dashicons-universal-access'       =>    'dashicons-universal-access',
							'dashicons-universal-access-alt'   =>    'dashicons-universal-access-alt',
							'dashicons-tickets'                =>    'dashicons-tickets',
							'dashicons-nametag'                =>    'dashicons-nametag',
							'dashicons-clipboard'              =>    'dashicons-clipboard',
							'dashicons-heart'                  =>    'dashicons-heart',
							'dashicons-megaphone'              =>    'dashicons-megaphone',
							'dashicons-schedule'               =>    'dashicons-schedule',
							'dashicons-wordpress'              =>    'dashicons-wordpress',
							'dashicons-wordpress-alt'          =>    'dashicons-wordpress-alt',
							'dashicons-pressthis'              =>    'dashicons-pressthis',
							'dashicons-update'                 =>    'dashicons-update',
							'dashicons-screenoptions'          =>    'dashicons-screenoptions',
							'dashicons-cart'                   =>    'dashicons-cart',
							'dashicons-feedback'               =>    'dashicons-feedback',
							'dashicons-cloud'                  =>    'dashicons-cloud',
							'dashicons-translation'            =>    'dashicons-translation',
							'dashicons-tag'                    =>    'dashicons-tag',
							'dashicons-category'               =>    'dashicons-category',
							'dashicons-archive'                =>    'dashicons-archive',
							'dashicons-tagcloud'               =>    'dashicons-tagcloud',
							'dashicons-text'                   =>    'dashicons-text',
							'dashicons-media-archive'          =>    'dashicons-media-archive',
							'dashicons-media-audio'            =>    'dashicons-media-audio',
							'dashicons-media-code'             =>    'dashicons-media-code',
							'dashicons-media-default'          =>    'dashicons-media-default',
							'dashicons-media-document'         =>    'dashicons-media-document',
							'dashicons-media-interactive'      =>    'dashicons-media-interactive',
							'dashicons-media-spreadsheet'      =>    'dashicons-media-spreadsheet',
							'dashicons-media-text'             =>    'dashicons-media-text',
							'dashicons-media-video'            =>    'dashicons-media-video',
							'dashicons-playlist-audio'         =>    'dashicons-playlist-audio',
							'dashicons-playlist-video'         =>    'dashicons-playlist-video',
							'dashicons-controls-play'          =>    'dashicons-controls-play',
							'dashicons-controls-pause'         =>    'dashicons-controls-pause',
							'dashicons-controls-forward'       =>    'dashicons-controls-forward',
							'dashicons-controls-skipforward'   =>    'dashicons-controls-skipforward',
							'dashicons-controls-back'          =>    'dashicons-controls-back',
							'dashicons-controls-skipback'      =>    'dashicons-controls-skipback',
							'dashicons-controls-repeat'        =>    'dashicons-controls-repeat',
							'dashicons-controls-volumeon'      =>    'dashicons-controls-volumeon',
							'dashicons-controls-volumeoff'     =>    'dashicons-controls-volumeoff',
							'dashicons-yes'                    =>    'dashicons-yes',
							'dashicons-no'                     =>    'dashicons-no',
							'dashicons-no-alt'                 =>    'dashicons-no-alt',
							'dashicons-plus'                   =>    'dashicons-plus',
							'dashicons-plus-alt'               =>    'dashicons-plus-alt',
							'dashicons-plus-alt2'              =>    'dashicons-plus-alt2',
							'dashicons-minus'                  =>    'dashicons-minus',
							'dashicons-dismiss'                =>    'dashicons-dismiss',
							'dashicons-marker'                 =>    'dashicons-marker',
							'dashicons-star-filled'            =>    'dashicons-star-filled',
							'dashicons-star-half'              =>    'dashicons-star-half',
							'dashicons-star-empty'             =>    'dashicons-star-empty',
							'dashicons-flag'                   =>    'dashicons-flag',
							'dashicons-info'                   =>    'dashicons-info',
							'dashicons-warning'                =>    'dashicons-warning',
							'dashicons-share'                  =>    'dashicons-share',
							'dashicons-share-alt'              =>    'dashicons-share-alt',
							'dashicons-share-alt2'             =>    'dashicons-share-alt2',
							'dashicons-twitter'                =>    'dashicons-twitter',
							'dashicons-rss'                    =>    'dashicons-rss',
							'dashicons-email'                  =>    'dashicons-email',
							'dashicons-email-alt'              =>    'dashicons-email-alt',
							'dashicons-facebook'               =>    'dashicons-facebook',
							'dashicons-facebook-alt'           =>    'dashicons-facebook-alt',
							'dashicons-networking'             =>    'dashicons-networking',
							'dashicons-googleplus'             =>    'dashicons-googleplus',
							'dashicons-location'               =>    'dashicons-location',
							'dashicons-location-alt'           =>    'dashicons-location-alt',
							'dashicons-camera'                 =>    'dashicons-camera',
							'dashicons-images-alt'             =>    'dashicons-images-alt',
							'dashicons-images-alt2'            =>    'dashicons-images-alt2',
							'dashicons-video-alt'              =>    'dashicons-video-alt',
							'dashicons-video-alt2'             =>    'dashicons-video-alt2',
							'dashicons-video-alt3'             =>    'dashicons-video-alt3',
							'dashicons-vault'                  =>    'dashicons-vault',
							'dashicons-shield'                 =>    'dashicons-shield',
							'dashicons-shield-alt'             =>    'dashicons-shield-alt',
							'dashicons-sos'                    =>    'dashicons-sos',
							'dashicons-search'                 =>    'dashicons-search',
							'dashicons-slides'                 =>    'dashicons-slides',
							'dashicons-analytics'              =>    'dashicons-analytics',
							'dashicons-chart-pie'              =>    'dashicons-chart-pie',
							'dashicons-chart-bar'              =>    'dashicons-chart-bar',
							'dashicons-chart-line'             =>    'dashicons-chart-line',
							'dashicons-chart-area'             =>    'dashicons-chart-area',
							'dashicons-groups'                 =>    'dashicons-groups',
							'dashicons-businessman'            =>    'dashicons-businessman',
							'dashicons-id'                     =>    'dashicons-id',
							'dashicons-id-alt'                 =>    'dashicons-id-alt',
							'dashicons-products'               =>    'dashicons-products',
							'dashicons-awards'                 =>    'dashicons-awards',
							'dashicons-forms'                  =>    'dashicons-forms',
							'dashicons-testimonial'            =>    'dashicons-testimonial',
							'dashicons-portfolio'              =>    'dashicons-portfolio',
							'dashicons-book'                   =>    'dashicons-book',
							'dashicons-download'               =>    'dashicons-download',
							'dashicons-upload'                 =>    'dashicons-upload',
							'dashicons-backup'                 =>    'dashicons-backup',
							'dashicons-clock'                  =>    'dashicons-clock',
							'dashicons-lightbulb'              =>    'dashicons-lightbulb',
							'dashicons-microphone'             =>    'dashicons-microphone',
							'dashicons-desktop'                =>    'dashicons-desktop',
							'dashicons-tablet'                 =>    'dashicons-tablet',
							'dashicons-smartphone'             =>    'dashicons-smartphone',
							'dashicons-phone'                  =>    'dashicons-phone',
							'dashicons-smiley'                 =>    'dashicons-smiley',
							'dashicons-index-card'             =>    'dashicons-index-card',
							'dashicons-carrot'                 =>    'dashicons-carrot',
							'dashicons-building'               =>    'dashicons-building',
							'dashicons-store'                  =>    'dashicons-store',
							'dashicons-album'                  =>    'dashicons-album',
							'dashicons-palmtree'               =>    'dashicons-palmtree',
							'dashicons-tickets-alt'            =>    'dashicons-tickets-alt',
							'dashicons-money'                  =>    'dashicons-money',
							'dashicons-thumbs-up'              =>    'dashicons-thumbs-up',
							'dashicons-thumbs-down'            =>    'dashicons-thumbs-down',
							'dashicons-layout'                 =>    'dashicons-layout'
						)
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
					?>
						<li data-type="<?php echo esc_attr($field['type']); ?>" data-key="<?php echo esc_attr($field['key']); ?>">
							<a href="#<?php echo esc_attr($field['key']); ?>" onclick="return false;" class="dashicons <?php echo esc_attr($field['icon_tab']); ?>"><?php echo esc_attr($field['label']); ?></a>
						</li>
					<?php
				}
			}

			new acf_field_tab_options();
		}
	});



	/*
	 *  Add css style and script field admin_init
	 */
	add_action( 'admin_init', function() {
		wp_enqueue_style( 'acf-style-tab-options', xkit_acf_normalize_slash( get_template_directory_uri() . '/framework/modules/acf-theme-options/css/acf-tab-options.css' ), array( 'acf-input', 'acf-field-group' ) );
		wp_enqueue_script( 'acf-js-tab-options', xkit_acf_normalize_slash( get_template_directory_uri() . '/framework/modules/acf-theme-options/js/acf-tab-options.js' ) );
	});
}
?>