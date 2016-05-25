<?php
/*
 * Header menu walker
 */
class Xkit_Mega_Nav_Menu extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		/* Check theme location */
		if( empty( $args->theme_location ) ) {
			return false;
		}

		/* Custom fields */
		$menu_icon 		= '';
		$hide_link      = false;
		$custom_classes = array();
		$has_thumbnail  = false;

		$mega_menu_fields = wp_cache_get( 'mega_menu_fields_' . $args->theme_location );
		if ( !$mega_menu_fields ) {
			$mega_menu_fields = get_option( 'mega_menu_fields_' . $args->theme_location );
			wp_cache_add( 'mega_menu_fields_' . $args->theme_location, $mega_menu_fields );
		}

		$default_params = array(
			'type' 	=> 'input',
			'label' => '',
			'depth'	=> 'any',
		);

		if( is_array( $mega_menu_fields ) ) {
			foreach ( $mega_menu_fields as $_key => $params ) {
				$key   		= sprintf( 'menu-item-%s-' . $args->theme_location, $_key );
				$item_data 	= get_post_meta( $item->ID, $key, true );
				$params		= array_merge( $default_params, $params );

				// Create depth param
				$in_depth = array();
				if( is_string( $params['depth'] ) && $params['depth'] !== 'any' ) {
					$in_depth = explode( ',', $params['depth'] );
				}
				elseif( is_int( $params['depth'] ) ) {
					$in_depth = array( $params['depth'] );
				}
				elseif( is_array( $params['depth'] ) ) {
					$in_depth = $params['depth'];
				}


				/* Fields list */
				if( $params['depth'] === 'any' || in_array( $depth, $in_depth ) ) {

					if( $item_data ) {

						// Columns
						if( $_key == 'menu_columns' ) {
							$custom_classes[] = 'columns-' . $item_data['value'];
						}

						// Item icon
						if( $_key == 'display_post_thumbnail' ) {

							if( $item_data['value'] == '1' ) {
								$has_thumbnail = true; 
							}
						}

						// Menu link
						if( $_key == 'menu_link' ) {
							$hide_link = true; 
						}

						// Item icon
						if( $_key == 'menu_icon' && !empty( $item_data['value'] ) ) {
							if( $item_data['value'] != 1 ) {
								$menu_icon = '<i class="fa ' . $item_data['value'] . '"></i>'; 
							}
						}
					}
				}
			}

		} // custom fields

		/* Check, if item is column */
		$is_column = get_post_meta( $item->ID, 'menu-item-is_column', true );
		if( $is_column == 1 ) {
			$custom_classes[] = 'menu-item-is-column';
		}


		/* Classes */
		$classes = empty( $item->classes ) ? array() : ( array ) $item->classes;
		foreach( $custom_classes as $custom_class ) {
			$classes[] = $custom_class;
		}

		/*
		 * Filter the CSS class( es ) applied to a menu item's list item element.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/*
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';	

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class']  = 'item-link';


		/*
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$attributes = '';
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}


		/*
		 * OUTPUT
		 */

		/* Before item HTML */
		$item_output = $args->before;

		/* Link */
		$link = '';

		// Link params before text
		if( $hide_link ) {
			$link .= '<div class="' . $atts['class'] . '">';
		}
		else {
			$link .= '<a' . $attributes . '>';
		}

		// Item text
		if( $depth == 0 ) {
			$link .= $args->link_before . $menu_icon . '<span class="head">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span>' . $args->link_after;
		}
		else {
			$link .= $args->link_before . $menu_icon . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}

		// Link params after text
		if( $hide_link ) {
			$link .= '</div>';
		}
		else {
			$link .= '</a>';
		}


		/* Column Title Html */
		if( $is_column == 1 ) {
			// Column title
			$column_title = '<span class="column-title">' . $args->link_before;

				// Column icon
				$column_icon = get_post_meta( $item->ID, 'menu-item-menu_column_icon', true );
				if( !empty( $column_icon['value'] ) ) {
					$column_title .= '<i class="fa ' . $column_icon['value'] . '"></i>';
				}

				// Column title
				$column_title_text  = get_post_meta( $item->ID, 'menu-item-menu_column_title', true );
				if( !empty( $column_title_text['value'] ) ) {
					$column_title .= '<span class="head-text">' . $column_title_text['value'] . '</span>';
				}

			$column_title .= $args->link_after . '</span>';

			// If column title empty
			if( empty( $column_icon['value'] ) && empty( $column_title_text['value'] ) ) {
				$item_output .= '<span class="column-title empty-column-title"></span>';
			}
			else {
				$item_output .= $column_title;
			}
		}

		/* Item With Post Thumbnail */
		elseif( $has_thumbnail ) {
			ob_start();
			?>
				<div class="item-has-thumbnail">
					<div class="item-thumbnail">
					<?php
						$thumb_args = array(
							'post_id' => $item->object_id
						);

						if( !$hide_link ) {
							$thumb_args['display_link'] = 'post';
						}

						$thumb_args = apply_filters( 'xkit_mega_menu_thumb_settings', $thumb_args );

						xkit_generate_post_thumbnail( $thumb_args, true );
					?>
					</div>

					<?php print( $link ); ?>
				</div>
			<?php
			$item_output .= ob_get_clean();
		}

		/* Default item */
		else {
			$item_output .= $link;
		}


		/* After item HTML */
		$item_output .= $args->after;


		/*
		 * Filter a menu item's starting output.
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}