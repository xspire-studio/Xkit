<?php
/**
 * Breadcrumbs
 *
 * @package Xkit
 * @subpackage Snippet Breadcrumbs
 *
 * @version: 1.1
 *
 * 1.0  - method get_output();
 * 2.0  - method build_crumbs();
 * 3.0  - method get_single_crumb();
 * 4.0  - method get_home_crumb();
 * 5.0  - method get_blog_crumb();
 * 6.0  - method get_search_crumb();
 * 7.0  - method get_404_crumb();
 * 8.0  - method get_page_crumb();
 * 9.0  - method get_attachment_crumb();
 * 10.0 - method get_post_crumb();
 * 11.0 - method get_cpt_crumb();
 * 12.0 - method get_category_crumb();
 * 13.0 - method get_tag_crumb();
 * 14.0 - method get_tax_crumb();
 * 15.0 - method get_year_crumb();
 * 16.0 - method get_month_crumb();
 * 17.0 - method get_day_crumb();
 * 18.0 - method get_author_crumb();
 * 19.0 - method get_post_type_crumb();
 * 20.0 - method get_root_link();
 * 21.0 - method wrap_current_crumb()
 * 22.0 - method get_term_parents();
 * 23.0 - method get_breadcrumb_link();
 * 25.0 - function xkit_get_breadcrumbs();
 */



/*
 * Class to control breadcrumbs display.
 */
class Xkit_Snippet_Breadcrumbs {

	/*
	 * Settings array, a merge of provided values and defaults.
	 *
	 * @var array Holds the breadcrumb arguments
	 */
	protected $args = array();


	/*
	 * Constructor. Set up cacheable values and settings.
	 */
	public function __construct() {

		/* Default arguments */
		$this->args = array(
			// HTML
			'before'          => '<div class="breadcrumbs" vocab="http://schema.org/" typeof="BreadcrumbList">',
			'after'           => '</div>',
			'crumb_before'    => '<span property="itemListElement" typeof="ListItem">',
			'crumb_after'     => '</span>',

			// Separator
			'sep'             => ' / ',
			'list_sep'		  => ', ',

			// Current
			'current_before'  => '<span class="current-item" property="itemListElement" typeof="ListItem"><span property="name">',
			'current_after'   => '</span></span>',

			// Settings
			'heirarchial_attachments' => true,
			'heirarchial_categories'  => true,
			'root_link'				  => 'post_type_archive', // Custom link -  array( 'url' => '', 'title' => '' )

			// Labels
			'labels' => array(
				'home'          => esc_html__( 'Home', 'xkit' ),
				'before_crumbs' => '',
				'author'    	=> '',
				'category'  	=> '',
				'tag'       	=> esc_html__( 'Archives for ', 'xkit' ),
				'date'      	=> '',
				'search'    	=> esc_html__( 'Search Results for ', 'xkit' ),
				'tax'       	=> '',
				'post_type' 	=> '',
				'404'       	=> esc_html__( 'Not found: ', 'xkit' )
			)
		);

	}


	/*
	 * Return the final completed breadcrumb in markup wrapper.
	 *
	 * @param  array  $args
	 * @return string HTML
	 */
	public function get_output( $args = array() ) {
		// Merge Crumbs
		$crumb_args = array_merge( $this->args, $args );
		
		// Rewrite Labels
		if( !empty( $args['labels'] ) ) {
			$crumb_args['labels'] = array_merge( $crumb_args['labels'], $args['labels'] );
		}

		// Filter Crumbs
		$this->args = apply_filters( 'xkit_breadcrumbs_args', $crumb_args );

		return $this->args['before'] . $this->args['labels']['before_crumbs'] . $this->build_crumbs() . $this->args['after'];
	}
	

	/*
	 * Return the correct crumbs for this query, combined together.
	 *
	 * @return string HTML
	 */
	protected function build_crumbs() {
		$crumbs = array();

		$crumbs = xkit_array_match( $crumbs, $this->get_home_crumb() );
		$crumbs = xkit_array_match( $crumbs, $this->get_root_link() );
		
		if ( is_home() ) {
			// Breadcrumbs end
		}
		elseif ( is_search() ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_search_crumb() );
		}
		elseif ( is_404() ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_404_crumb() );
		}
		elseif ( is_page() ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_page_crumb() );
		}
		elseif ( is_singular() ) {
			if ( is_attachment() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_attachment_crumb() );
			} elseif ( is_singular( 'post' ) ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_post_crumb() );
			} else {
				$crumbs = xkit_array_match( $crumbs, $this->get_cpt_crumb() );
			}
		}
		elseif ( is_archive() ) {
			if ( is_category() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_category_crumb() );
			} elseif ( is_tag() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_tag_crumb() );
			} elseif ( is_tax() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_tax_crumb() );
			} elseif ( is_year() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_year_crumb() );
			} elseif ( is_month() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_month_crumb() );
			} elseif ( is_day() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_day_crumb() );
			} elseif ( is_author() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_author_crumb() );
			} elseif ( is_post_type_archive() ) {
				$crumbs = xkit_array_match( $crumbs, $this->get_post_type_crumb() );
			}
		}		

		// Filter the breadcrumbs
		$crumbs = apply_filters( 'xkit_breadcrumbs_build_crumbs_array', $crumbs, $this->args );
		$crumbs = join( $this->args['sep'], array_filter( $crumbs ) );
		$crumbs = apply_filters( 'xkit_breadcrumbs_build_crumbs_html', $crumbs, $this->args );

		// Add Shema Counter
		$crumbs = preg_replace_callback(
			"/(<span.*?typeof=\"ListItem\"[^>]*>)(.*?<\/span>.*?)(<\/span>)/si",
			function( $m ) {
				static $crumbs_counter = 0;
				$crumbs_counter++;

				return $m[1] . $m[2]  . '<meta property="position" content="' . $crumbs_counter . '">' . $m[3];
			},
			$crumbs
		);

		return $crumbs;
	}


	/*
	 * Return home breadcrumb.
	 *
	 * Default is Home, linked on all occasions except when is_home() is true.
	 *
	 * @return string HTML
	 */
	protected function get_home_crumb() {
		$crumbs = array();
		
		$url = ( 'page' === get_option( 'show_on_front' ) ) ? get_permalink( get_option( 'page_on_front' ) ) : trailingslashit( esc_url( home_url( '/' ) ) );
		
		if( is_home() && is_front_page() && $this->args['labels']['home'] ) {
			$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['home'] );
		}
		elseif( $url && $this->args['labels']['home'] ) {
			$crumbs[] = $this->get_breadcrumb_link( esc_attr( $url ), '', $this->args['labels']['home'] );
		}
		
		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_home_crumb', $crumbs, $this->args );
	}


	/*
	 * Return search results page breadcrumb.
	 *
	 * @return string HTML
	 */
	protected function get_search_crumb() {
		$crumbs = array();
		
		// Search crumb
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['search'] . '"' . esc_html( apply_filters( 'the_search_query', get_search_query() ) ) . '"' );
		
		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_search_crumb', $crumbs, $this->args );
	}


	/*
	 * Return 404 (page not found) breadcrumb.
	 *
	 * @return string HTML
	 */
	protected function get_404_crumb() {
		$crumbs = array();
		
		// 404 crumb
		if( $this->args['labels']['404'] ) {
			$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['404'] );
		}

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_404_crumb', $crumbs, $this->args );
	}


	/*
	 * Return Page breadcrumb.
	 *
	 * @return string HTML
	 */
	protected function get_page_crumb() {
		global $post;
		$crumbs = array();

		// Posts parent Hierarchy
		if ( $post->post_parent ) {
			if ( isset( $post->ancestors ) ) {
				if ( is_array( $post->ancestors ) ){
					$ancestors = array_values( $post->ancestors );
				} else {
					$ancestors = array( $post->ancestors );
				}
			} else {
				$ancestors = array( $post->post_parent );
			}

			foreach ( $ancestors as $ancestor ) {
				array_unshift(
					$crumbs,
					$this->get_breadcrumb_link(
						get_permalink( $ancestor ),
						'',
						get_the_title( $ancestor )
					)
				);
			}
		}	
		
		// Add the current page title
		$crumbs[] = $this->wrap_current_crumb( get_the_title( $post->ID ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_page_crumb', $crumbs, $this->args );
	}


	/*
	 * Get breadcrumb for single attachment, including any parent crumbs.
	 *
	 * @return string HTML.
	 */
	protected function get_attachment_crumb() {
		global $post;
		$crumbs = array();

		// Attachments crumbs
		if ( $this->args['heirarchial_attachments'] && !empty( $post->post_parent ) ) {
			
			// If showing attachment parent
			$attachment_parent = get_post( $post->post_parent );
			$crumbs[] = $this->get_breadcrumb_link(	get_permalink( $post->post_parent ), '', $attachment_parent->post_title	);
		}

		$crumbs[] = $this->wrap_current_crumb( single_post_title( '', false ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_attachment_crumb', $crumbs, $this->args );
	}


	/*
	 * Get breadcrumb for single post, including any parent (category) crumbs.
	 *
	 * @return string HTML.
	 */
	protected function get_post_crumb() {
		global $post;
		$crumbs = array();
		
		// Categories Crumb
		$categories = get_the_category();
		if ( !$this->args['heirarchial_categories'] ) {			
			$cat_list = array();
			foreach ( $categories as $category ) {
				$cat_list[] = $this->get_breadcrumb_link( get_category_link( $category->term_id ), '', $category->name );
			}
			$crumbs[] = join( $this->args['list_sep'], $cat_list );
		} else {
			$crumbs = xkit_array_match( $crumbs, $this->get_term_parents( $categories[0]->cat_ID, 'category', true ) );
		}
		
		// Current Post Crumb
		$crumbs[] = $this->wrap_current_crumb( get_the_title( $post->ID ) );
		
		// Return Crumb
		return apply_filters( 'xkit_breadcrumbs_post_crumb', $crumbs, $this->args );
	}


	/*
	 * Get breadcrumb for single custom post type entry, including any parent (CPT name) crumbs.
	 *
	 * @return string HTML.
	 */
	protected function get_cpt_crumb() {
		global $post;
		$crumbs = array();
		$post_terms = array();

		// Post terms Crumb
		$post_taxonomies     = get_object_taxonomies( $post ); // Get post taxonomies
		$filtered_taxonomies = array_intersect( $post_taxonomies, get_taxonomies( array( 'hierarchical' => true, 'public' => true ) ) ); // Filter hierarchical & public taxonomies
		$post_taxonomy 		 = array_shift( $filtered_taxonomies ); // Get first taxonomy

		if( $post_term = get_the_terms( $post->ID, $post_taxonomy ) ) {
			$post_term = array_shift( $post_term );
		}
		
		if ( !empty( $post_term->term_id ) ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_term_parents( $post_term->term_id, $post_taxonomy, true ) );
		}
		
		// Post ancestors ( parent posts )
		if ( $post->post_parent > 0 ) {
			$ancestors = array_reverse( get_post_ancestors( $post->ID ) );

			foreach ( $ancestors as $ancestor ) {
				if( $ancestor != $post->ID ) {
					$crumbs[] = $this->get_breadcrumb_link( get_permalink( $ancestor ), '', get_the_title( $ancestor ) );
				}
			}
		}
		
		// Current Post Crumb
		$crumbs[] = $this->wrap_current_crumb( get_the_title( $post->ID ) );
		
		// Return crumbs
		return apply_filters( 'xkit_breadcrumbs_cpt_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the category archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_category_crumb() {
		$crumbs = array();
		$current_category = get_queried_object();

		// Parent Categories
		if( $current_category->parent > 0 ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_term_parents( $current_category->parent, $current_category->taxonomy ) );
		}
		
		// Current Category
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['category'] . $current_category->name );
	
		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_category_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the tag archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_tag_crumb() {
		$crumbs = array();
		
		// Current Tag Crumbs
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['tag'] . single_term_title( '', false ) );
		
		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_tag_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the taxonomy archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_tax_crumb() {
		global $wp_query;
		$crumbs = array();
		$current_term = $wp_query->get_queried_object();

		// Parent Terms
		if( intval( $current_term->parent ) > 0 ) {
			$crumbs = xkit_array_match( $crumbs, $this->get_term_parents( $current_term->parent, $current_term->taxonomy ) );
		}
		
		// Current Term
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['tax'] . $current_term->name );
		
		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_tax_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the year archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_year_crumb() {
		$crumbs = array();
		$year = get_query_var( 'm' ) ? get_query_var( 'm' ) : get_query_var( 'year' );
		
		// Year crumb
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['date'] . $year );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_year_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the month archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_month_crumb() {
		$crumbs = array();

		// Year crumb
		$year = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 0, 4 ) : get_query_var( 'year' );
		$crumbs[] = $this->get_breadcrumb_link( get_year_link( $year ), '', $year );
		
		// Month crumb
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['date'] . single_month_title( ' ', false ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_month_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the day archive crumb.
	 *
	 * @global mixed  $wp_locale The locale object, used for getting the
	 *                           auto-translated name of the month for month or
	 *                           day archives.
	 *
	 * @return string HTML
	 */
	protected function get_day_crumb() {
		global $wp_locale;
		$crumbs = array();
		$year  = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 0, 4 ) : get_query_var( 'year' );
		$month = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 4, 2 ) : get_query_var( 'monthnum' );
		$day   = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 6, 2 ) : get_query_var( 'day' );
		
		// Year crumb
		$crumbs[] = $this->get_breadcrumb_link(	get_year_link( $year ),	'',	$year );
		
		// Month crumb
		$crumbs[] = $this->get_breadcrumb_link(	get_month_link( $year, $month ), '', $wp_locale->get_month( $month ) );
		
		// Day crumb
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['date'] . $day . date( 'S', mktime( 0, 0, 0, 1, $day ) ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_day_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the author archive crumb.
	 *
	 * @global WP_Query $wp_query Query object.
	 *
	 * @return string HTML
	 */
	protected function get_author_crumb() {
		global $wp_query;
		$crumbs = array();
		
		// Author Crumbs
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['author'] . esc_html( $wp_query->queried_object->display_name ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_author_crumb', $crumbs, $this->args );
	}


	/*
	 * Return the post type archive crumb.
	 *
	 * @return string HTML
	 */
	protected function get_post_type_crumb() {
		$crumbs = array();
		
		// Post Type Archive Crumb
		$crumbs[] = $this->wrap_current_crumb( $this->args['labels']['post_type'] . esc_html( post_type_archive_title( '', false ) ) );

		// Return Crumbs
		return apply_filters( 'xkit_breadcrumbs_post_type_crumb', $crumbs, $this->args );
	}


	/*
	 * Get root link
	 *
	 * @param  array  $crumbs
	 * @param  string $post_type
	 * @return array  Crumbs included root link
	 */
	protected function get_root_link( $crumbs = array(), $post_type = '' ) {
		
		if( !$post_type ) {
			$post_type = get_post_type();
			
			if( $post_type ) {
				$post_type_object = get_post_type_object( $post_type );	
			}
		}
		
		/* Filter root link */
		$root_link = apply_filters( 'xkit_breadcrumbs_root_link', $this->args['root_link'] );

		/* Root link */
		if( $root_link == 'post_type_archive' && !empty( $post_type_object ) && $post_type_object->has_archive && !is_post_type_archive() && !is_search() ) {
			if ( $cpt_archive_link = get_post_type_archive_link( $post_type ) ) {
				$crumbs[] = $this->get_breadcrumb_link(	$cpt_archive_link, '', $post_type_object->labels->name );
			} else {
				$crumbs[] = $this->wrap_current_crumb( $post_type_object->labels->name );
			}
		}
		elseif( is_array( $root_link ) ) {
			if( isset( $root_link['title'] ) && $root_link['title'] ) {
				
				if( isset( $root_link['url'] ) && $root_link['url'] ) {
					$crumbs[] = $this->get_breadcrumb_link(	esc_attr( $root_link['url'] ), '', esc_attr( $root_link['title'] ) );
				}
				else {
					$crumbs[] = $this->wrap_current_crumb( $root_link['title'] );
				}
			}
		}

		return $crumbs;
	}


	/*
	 * Return recursive linked crumbs of category, tag or custom taxonomy parents.
	 *
	 * @param  int    $parent_id  Initial ID of object to get parents of
	 * @param  string $taxonomy   Name of the taxonomy. May be 'category', 'post_tag' or something custom
	 * @param  bool   $link       Whether to link last item in chain. Default false
	 * @param  array  $visited    Array of IDs already included in the chain
	 * @return string HTML of crumbs
	 */
	public function get_term_parents( $parent_id, $taxonomy, $link = true, $visited = array() ) {
		$crumbs = array();
		$parent = get_term( (int)$parent_id, $taxonomy );

		if ( is_wp_error( $parent ) ) {
			return array();
		}

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$crumbs = xkit_array_match( $crumbs, $this->get_term_parents( $parent->parent, $taxonomy, true, $visited ) );
		}

		if ( $link && !is_wp_error( get_term_link( get_term( $parent->term_id, $taxonomy ), $taxonomy ) ) ) {
			$crumbs[] = $this->get_breadcrumb_link(
				get_term_link( get_term( $parent->term_id, $taxonomy ), $taxonomy ),
				'',
				$parent->name
			);
		} else {
			$crumbs[] = $this->wrap_current_crumb( $parent->name );
		}

		return $crumbs;
	}


	/*
	 * Return anchor link for a single crumb.
	 *
	 * @param  string $url     URL for href attribute.
	 * @param  string $title   Title attribute.
	 * @param  string $content Linked content.
	 * @return string HTML for anchor link and optional separator.
	 */
	public function get_breadcrumb_link( $url, $title, $content ) {
		$title = $title ? ' title="' . esc_attr( $title ) . '"' : '';
		$link = $this->args['crumb_before'] . sprintf( '<a property="item" typeof="WebPage" href="%s"%s><span property="name">%s</span></a>', esc_attr( $url ), $title, $content ) . $this->args['crumb_after'] ;

		// Filter the anchor link for a single breadcrumb.
		return apply_filters( 'xkit_breadcrumbs_link', $link, $url, $title, $content, $this->args );
	}


	/*
	 * Get current breadcrumb, including any parent crumbs.
	 *
	 * @param  string $text
	 * @return string HTML
	 */
	public function wrap_current_crumb( $text ) {
		$crumbs = $this->args['current_before'] . esc_html( $text ) . $this->args['current_after'];

		// Filter the current crumb
		return apply_filters( 'xkit_breadcrumbs_current_crumb', $crumbs, $this->args );
	}
}


/*
 * Helper function for the Breadcrumb Class.
 *
 * @param array $args Breadcrumb arguments
 */
function xkit_get_breadcrumbs( $args = array() ) {

	// Breadcrumbs NavXT compatibility
	if( function_exists('bcn_display') ) {
		?>
			<div class="breadcrumbs breadcrumbs-navxt" typeof="BreadcrumbList" vocab="http://schema.org/">
				<?php bcn_display(); ?>
			</div>
		<?php
		return;
	}

	// Yoast breadcrumbs compatibility
	if( function_exists('yoast_breadcrumb') ) {
		$yoast_breadcrumbs = yoast_breadcrumb( '<div class="breadcrumbs yoast-breadcrumbs">', '</div>' );

		if( !empty( $yoast_breadcrumbs ) ) {
			return;
		}
	}

	// Create breadcrumbs
	$the_breadcrumbs = new Xkit_Snippet_Breadcrumbs;
	$html = $the_breadcrumbs->get_output( $args );

	print $html;
}