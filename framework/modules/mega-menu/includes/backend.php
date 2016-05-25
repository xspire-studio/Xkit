<?php
/*
 * Replace Default Menu Editor Walker
 */
function Xkit_Include_Walker() {
	class Xkit_Mega_Menu_Edit_Walker extends Walker_Nav_Menu_Edit {

		/*
		 * Start the element output.
		 *
		 * We're injecting our custom fields after the div.submitbox
		 *
		 * @see Walker_Nav_Menu::start_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Menu item args.
		 * @param int    $id     Nav menu ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$item_output = '';
			parent::start_el( $item_output, $item, $depth, $args, $id );

			// Item is column
			$is_column 	= get_post_meta( $item->ID, 'menu-item-is_column', true );
			if( $is_column <= 0 && !empty( $_REQUEST['item_type'] ) && $_REQUEST['item_type'] == 'column' ){
				$is_column = 1;
			}

			if( $is_column == 1 ) {
				$item_output = str_replace( '<p class="', '<p class="box-hidden ', $item_output );
				$item_output = str_replace( 'menu-item-actions', 'menu-item-actions box-hidden', $item_output );

				// Add column name
				$item_output = preg_replace( '/<span class=\"menu-item-title\">(.*?)<\/span>/si', '<span class="dashicons dashicons-menu"></span><span class="menu-item-title">' . esc_html__( 'Column', 'xkit' ) . '</span>', $item_output );
			}

			// Custom fields
			$item_fields = $this->get_fields( $item, $depth, $args, $id );

			// Add column btn
			if ( preg_match( '/item-menu_columns/', $item_fields ) ){
				$item_output = str_replace( '<a class="item-edit" id="edit-' . $item->ID, '<a href="#" class="add-column-btn">' . esc_html__( 'Add column', 'xkit' ) . '</a><a class="item-edit" id="edit-' . $item->ID, $item_output );
			}

			// Add custom fields
			$output .= preg_replace(
				// NOTE: Check this regex from time to time!
				'/(?=<p[^>]+class="[^"]*field-move)/',
				$item_fields,
				$item_output
			);

			// Add column class
			if( $is_column == 1 ) {
				$output = str_replace( 'id="menu-item-' .  $item->ID . '" class="menu-item', 'id="menu-item-' .  $item->ID . '" class="menu-item column-item', $output);
			}
		}


		/*
		 * Get custom fields
		 *
		 * @access protected
		 * @uses add_action() Calls 'menu_item_custom_fields' hook
		 *
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 * @param array  $args  Menu item args.
		 * @param int    $id    Nav menu ID.
		 *
		 * @return string Form fields
		 */
		protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
			ob_start();

			// Get menu item custom fields
			do_action( 'wp_nav_menu_item_custom_fields', $item, $depth, $args, $id );

			return ob_get_clean();
		}
	}
}


/*
 * Add Menu Fields
 */
class Xkit_AddMenuFields {

	/*
	 * Holds our custom fields
	 * @var  array
	 */
	public $fields = array();


	/*
	 * Menu location
	 *
	 * @var  string
	 */
	private $location = '';


	/*
	 * Font awesome icons
	 *
	 * @var  string
	 */
	private $array_font_awesome = array();


	/*
	 * Font icomoon icons
	 *
	 * @var  string
	 */
	private $array_font_icomoon = array();


	/*
	 * Constructor. Set up cacheable values and settings.
	 */
	public function __construct( $list_fields = array(), $location ) {

		$this->init( $list_fields, $location );
	}


	/*
	 * Initialize plugin
	 *
	 * @param array  $list_fields
	 * @param string $location
	 */
	public function init( $list_fields = array(), $location ) {

		// Set location
		$this->location = $location;
		$this->fields 	= $list_fields;

		if ( array_key_exists( 'menu_icon', $list_fields ) ) {

			// Load icons
			$this->array_font_awesome = xkit_get_list_font_awesome();
			$this->array_font_icomoon = xkit_get_list_font_icomoon();

			// Include icons scripts & styles
			add_action( 'xkit_admin_mega_menu_sripts', function() {
				wp_deregister_style( 'font-awesome' );
				wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/framework/assets/css/font-awesome.min.css', array(), XKIT_THEME_VERSION );

				wp_deregister_style( 'icomoon' );
				wp_enqueue_style( 'icomoon', get_template_directory_uri() . '/framework/assets/css/icomoon.css' );

				wp_enqueue_style( 'xkit-menu-style-font-icons', get_template_directory_uri() . '/framework/modules/mega-menu/assets/css/menu-font-icons.css', array(), XKIT_THEME_VERSION ); 
				wp_enqueue_script( 'xkit-menu-js-font-icons', get_template_directory_uri() . '/framework/modules/mega-menu/assets/js/menu-font-icons.js', array('jquery'), XKIT_THEME_VERSION, false  );
			} );
		}

		add_action( 'wp_nav_menu_item_custom_fields', array( &$this, 'display_menu_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( &$this, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( &$this, '_columns' ), 99 );
		add_filter( 'wp_nav_menu_args', array( &$this, 'set_mega_menu_walker' ), 10, 1 );
		add_filter( 'wp_edit_nav_menu_walker', array( &$this, 'filter_walker' ), 99 );


		// Check fields list save in database
		if( !empty( $list_fields ) ) {
			update_option( 'mega_menu_fields_' . $location, $list_fields );
		}
		else {
			delete_option( 'mega_menu_fields_' . $location, $list_fields );
		}
	}


	/*
	 * Replace default menu editor walker with ours
	 *
	 * We don't actually replace the default walker. We're still using it and
	 * only injecting some HTMLs.
	 *
	 * @wp_hook filter wp_edit_nav_menu_walker
	 * @param   string $walker Walker class name
	 * @return  string Walker class name
	 */
	public function filter_walker( $walker ) {
		$walker = 'Xkit_Mega_Menu_Edit_Walker';
		if ( ! class_exists( $walker ) ) {
			Xkit_Include_Walker();
		}

		return $walker;
	}


	/*
	 * wp_nav_menu_args | set_mega_menu_walker()
	 *
	 * Set Mega Menu Walker
	 *
	 * @param array $args 
	 */
	public function set_mega_menu_walker( $args ) {
		if( empty( $args['walker'] ) && $args['theme_location'] == $this->location ) {
			$args['walker'] = new Xkit_Mega_Nav_Menu;
		}

		return $args;
	}


	/*
	 * Set Mega Menu Walker
	 *
	 * @param int $item_id 
	 */
	public function set_column_field( $item_id ){
		global $xkit_columns_fields;

		if( !is_array( $xkit_columns_fields ) ) {
			$xkit_columns_fields = array();
		}

		$xkit_columns_fields[] = $item_id;
	}


	/*
	 * Display menu fields
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public function display_menu_fields( $item, $depth, $args, $id ) {

		global $_wp_registered_nav_menus, $xkit_columns_fields;


		// Check if column field exists
		if( in_array( $item->ID, (array) $xkit_columns_fields ) ) {
			return false;
		}

		// Get menu id
		$menu_id = 0;
		if( isset( $_REQUEST['menu'] ) ){
			$menu_id = intval( $_REQUEST['menu'] );
		}
		if( $menu_id <= 0 ){
			$menu_id = wp_get_post_terms( $item->ID, 'nav_menu' );

			if( !empty( $menu_id[0]->term_id ) ) {
				$menu_id = $menu_id[0]->term_id;
			}
		}

		if( empty( $menu_id ) ){
			return false;
		}

		// Check current menu. If it have mega menu
		$theme_locations = get_nav_menu_locations();

		if( empty( $theme_locations[ $this->location ] ) || $theme_locations[ $this->location ] != $menu_id ) {
			return false;
		}

		// Item is column
		$is_column	= intval( get_post_meta( $item->ID, 'menu-item-is_column', true ) );

		if( $is_column <= 0 && !empty( $_REQUEST['item_type'] ) && $_REQUEST['item_type'] == 'column' ){
			$is_column = 1;
		}

		if( $is_column === 1 ) {
			$replace_fields = array(
				'menu_column_title' => array(
					'type'		=> 'input',
					'label'		=> esc_html__( 'Column Title', 'xkit' ),
				),
				'menu_column_icon' => array(
					'type'		=> 'icons',
					'label'		=> esc_html__( 'Column Icon', 'xkit' ),
				)
			);

			// Add to items
			$this->set_column_field( $item->ID );
		}
		else{
			$replace_fields = array();
		}

		?>
		<div class="clear clear-after-box"></div>
		<div class="menu-location-box settings-box-<?php echo esc_attr( $this->location ); ?>">
			<?php
				if( $is_column !== 1 ) {
					?>
					<h3><?php echo esc_html__( 'Theme location', 'xkit' ) . ' <strong>"' . $_wp_registered_nav_menus[ $this->location ] . '"</strong>'; ?></h3>
					<?php 
				}
			?>
			<div class="location-box-content">
			<?php
				$default_params = array(
					'type' 			=> 'input',
					'label' 		=> '',
					'depth'			=> 'any',
					'default_value'	=> ''
				);

				if( !empty( $replace_fields ) ) {
					$custom_fields = $replace_fields;
				}
				else{
					$custom_fields = $this->fields;
				}

				// Each fields
				foreach ( $custom_fields as $_key => $params ) {
					if( $is_column == 1 ) {
						$key = sprintf( 'menu-item-%s', $_key );
					}
					else{
						$key = sprintf( 'menu-item-%s-' . $this->location . '', $_key );
					}

					// Field params
					$params 	= array_merge( $default_params, $params );
					$type 		= $params['type'];
					$label 		= $params['label'];
					$in_depth 	= $params['depth'];

					if( is_string( $in_depth ) && $in_depth !== 'any' ) {
						$in_depth = explode( ',', $in_depth );
					}

					if( is_int( $in_depth ) ) {
						$in_depth = array( $in_depth );
					}


					$block_id 	= sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  		= sprintf( '%s[%s]', $key, $item->ID );
					$class 		= sprintf( 'field-%s', $_key );
					$value 		= $params['default_value'];

					$item_data 	= get_post_meta( $item->ID, $key, true );

					if( !empty( $item_data ) ) {
						$value 	= $item_data['value'];
					}

					/* Fields list */
					if( ( is_array( $in_depth ) && in_array( $depth, $in_depth ) ) || $in_depth === 'any' ) {

						/* Input field */
						if( $type == 'input' ) {

							$show_item = apply_filters( 'xkit_admin_mega_menu_input_show', true, $_key, $item, $depth );
							if( $show_item ) {
								?>
									<p class="description description-wide <?php echo esc_attr( $class ) ?>">
										<?php printf(
											'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
											esc_attr( $block_id ),
											esc_html( $label ),
											esc_attr( $name ),
											esc_attr( $value )
										 ); ?>
									</p>
								<?php
							}
						} // input end


						/* Checkbox field */
						elseif( $type == 'checkbox' ) {

							$show_item = apply_filters( 'xkit_admin_mega_menu_checkbox_show', true, $_key, $item, $depth );
							if( $show_item ) {
								?>
									<p class="description description-wide <?php echo esc_attr( $class ) ?>">
										<?php printf(
											'<label for="%1$s">%2$s<br /><input type="checkbox" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" %5$s /></label>',
											esc_attr( $block_id ),
											esc_html( $label ),
											esc_attr( $name ),
											1,
											checked( $value, 1, false )
										 ); ?>
									</p>
								<?php
							}
						} // checkbox end


						/* Select field */
						elseif( $type == 'select' ) {

							$show_item = apply_filters( 'xkit_admin_mega_menu_select_show', true, $_key, $item, $depth );
							if( $show_item ) {
								$options = $params['options'];

								if( is_array( $options ) ) {
									?>
										<p class="description description-wide <?php echo esc_attr( $class ) ?>">
											<?php
												printf(
													'<label for="%1$s">%2$s<br /><select id="%1$s" class="widefat %1$s" name="%3$s">',
													esc_attr( $block_id ),
													esc_html( $label ),
													esc_attr( $name )
												 );

												foreach( $options as $option_value => $option_title ) {
													printf(
														'<option value="%1$s" %2$s>%3$s</option>',
														esc_attr( $option_value ),
														selected( $option_value, $value, false ),
														esc_html( $option_title )
													 );
												}

												echo '</select>';
											?>
										</p>
									<?php
								}
							}
						} // select end


						/* Icon field */
						elseif( $type == 'icons' ) {

							$show_item = apply_filters( 'xkit_admin_mega_menu_icons_show', true, $_key, $item, $depth );
							if( $show_item ) {

								// Create value
								if( empty( $value ) || $value == '1' || ! is_string( $value )  ) {
									$field_value = '';
								}
								else{
									$field_value = $value;
								}

								// Render Field
								?>
									<div class="description description-wide <?php echo esc_attr( $class ) ?>">
										<div class="icon-box">
											<label><?php echo esc_html( $label ); ?></label>

											<div class="clear"></div>

											<div class="icon-preview">
												<?php echo esc_attr( $field_value ) ? '<i class="item ' . esc_attr( $field_value ) . '"></i>' : ''; ?>
											</div>

											<input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" readonly >

											<div class="clear"></div>

											<div class="icons-container clear">
												<h3>Font awesome</h3>

												<?php foreach( $this->array_font_awesome as $icon ): ?>
													<i class="item <?php echo esc_attr( $icon ); ?> <?php echo esc_attr( $field_value == $icon ? 'active' : '' ); ?>" data-icon="<?php echo esc_attr( $icon ); ?>"></i>
												<?php endforeach; ?>

												<h3>Icomoon</h3>
												<?php foreach( $this->array_font_icomoon as $icon ): ?>
													<i class="item <?php echo esc_attr( $icon ); ?> <?php echo esc_attr( $field_value == $icon ? 'active' : '' ); ?>" data-icon="<?php echo esc_attr( $icon ); ?>"></i>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								<?php
							}

						} // icon field end

					}  // field list end
				} // each fields end
			?>
			<div class="clear clear-after-box"></div>
			</div>
		</div>
		<?php
	}


	/*
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {

		// Check current menu. If it have mega menu
		$theme_locations = get_nav_menu_locations();
		if( empty( $theme_locations[ $this->location ] ) || $theme_locations[ $this->location ] != $menu_id ) {
			return false;
		}

		// Check ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Security
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Update fields settings
		$default_params = array(
			'type' 	=> 'input',
			'label' => '',
			'depth'	=> 'any',
		);

		// Create column
		if( !empty( $_POST[ 'menu-item-is_column' ][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, 'menu-item-is_column', '1' );
		}

		// Item is column
		$is_column 	= get_post_meta( $menu_item_db_id, 'menu-item-is_column', true );

		if( $is_column <= 0 && !empty( $_REQUEST['item_type'] ) && $_REQUEST['item_type'] == 'column' ){
			$is_column = 1;
		}

		if( intval( $is_column ) === 1 ) {
			$replace_fields = array(
				'menu_column_title' => array(
					'type'		=> 'input',
					'label'		=> esc_html__( 'Column Title', 'xkit' ),
				),
				'menu_column_icon' => array(
					'type'		=> 'icons',
					'label'		=> esc_html__( 'Column Icon', 'xkit' ),
				)
			);
		}
		else{
			$replace_fields = array();
		}

		// Fields
		if( !empty( $replace_fields ) ) {
			$custom_fields = $replace_fields;
		}
		else{
			$custom_fields = $this->fields;
		}

		foreach ( $custom_fields as $_key => $params ) {
			$params = array_merge( $default_params, $params );
			if( $is_column == 1 ) {
				$key = sprintf( 'menu-item-%s', $_key );
			}
			else{
				$key = sprintf( 'menu-item-%s-' . $this->location . '', $_key );
			}

			// Create value
			$value = false;

			if( !empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ][ $menu_item_db_id ] );
			}

			if( $value !== false ) {

				// Update
				$item_data = array(
					'params'	=> $params,
					'value'		=> $value
				);
				
				update_post_meta( $menu_item_db_id, $key, $item_data );
			}
			else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}


	/*
	 * Add our fields to the screen options toggle
	 *
	 * @param  array $columns Menu item columns
	 * @return array Columns
	 */
	public function _columns( $columns ) {
		foreach( $this->fields as $field_name => $field_params ){
			if( !empty( $field_params['label'] ) ) {
				$columns[ $field_name ] = $field_params['label'];
			}
			else {
				$columns[ $field_name ] = $field_name;
			}
		}

		return $columns;
	}
}