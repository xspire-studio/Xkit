<?php

add_action( 'xkit_cs_init', array( 'Xkit_CustomSidebarsWidgets', 'instance' ) );

/**
 * Extends the widgets section to add the custom sidebars UI elements.
 */
class Xkit_CustomSidebarsWidgets extends Xkit_CustomSidebars {

	/**
	 * Returns the singleton object.
	 *
	 * @since  2.0
	 */
	public static function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new Xkit_CustomSidebarsWidgets();
		}

		return $Inst;
	}

	/**
	 * Constructor is private -> singleton.
	 *
	 * @since  2.0
	 */
	private function __construct() {
		if ( is_admin() ) {
			add_action(
				'widgets_admin_page',
				array( $this, 'widget_sidebar_content' )
			);

			add_action(
				'admin_head-widgets.php',
				array( $this, 'init_admin_head' )
			);
		}
	}

	/**
	 * Adds the additional HTML code to the widgets section.
	 */
	public function widget_sidebar_content() {
		load_template( get_template_directory() . '/framework/modules/sidebar-generator/views/widgets.php' );
	}

	/**
	 * Initialize the admin-head for the widgets page.
	 *
	 * @since  2.0.9.7
	 */
	public function init_admin_head( $classes ) {
		add_filter(
			'admin_body_class',
			array( $this, 'admin_body_class' )
		);
	}

	/**
	 * Add a class to the body tag.
	 *
	 * @since  2.0.9.7
	 */
	public function admin_body_class( $classes ) {
		$classes .= ' no-auto-init ';
		return $classes;
	}

};
