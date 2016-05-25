<?php
/**
 * Related Posts
 *
 * @package Xkit
 * @subpackage Snippet Related Posts
 *
 * @version: 1.0
 *
 * 1.0 - method rp_get_posts();
 * 2.0 - method get_related_by_tags();
 * 3.0 - method get_related_by_current_term();
 * 4.0 - method get_related_by_parent_term();
 * 5.0 - method get_related();
 * 6.0 - function xkit_get_related_posts();
 */

class Xkit_RelatedPosts {

	/*
	 * Post ID
	 *
	 * @var int
	 */
	public $post_id = null;


	/*
	 * Count Posts
	 *
	 * @var int
	 */
	public $count_posts = 3;


	/*
	 * Parent Terms
	 *
	 * @var array
	 */
	public $parent_terms = array();


	/*
	 * Related Posts
	 *
	 * @var array
	 */
	private $related_posts = array();


	/*
	 * Constructor. Set up cacheable values and settings.
	 */
	public function __construct( $options = array() ) {
		global $post;

		// Set Options
		foreach( $options as $var_key => $var_value ) {
			$this->$var_key = $var_value;
		}

		// Set Post ID
		if( empty( $this->post_id ) && is_object( $post ) ) {
			$this->post_id = intval( $post->ID );
		}

		// Set Post Type
		$this->post_type = get_post_type( $this->post_id );
	}


	/*
	 * Get Posts
	 *
	 * @param  array $query
	 */
	private function rp_get_posts( $query ) {
		$new_related_posts = new WP_Query( $query );
		if( isset( $new_related_posts->posts ) && is_array( $new_related_posts->posts ) ){
			$this->related_posts = array_merge( $this->related_posts, $new_related_posts->posts );
		}
	}


	/*
	 * Get Related By Tags
	 */
	public function get_related_by_tags() {
		if( count( $this->related_posts ) < $this->count_posts ) {
			$tags = wp_get_post_tags( $this->post_id );

			if( !empty( $tags) ) {

				// Tags ID's
				$tag_ids = array();
				foreach( $tags as $individual_tag ) { 
					$tag_ids[] = $individual_tag->term_id;
				}

				// Needed count posts
				$this->needed_post_count = $this->count_posts - count( $this->related_posts );

				// Query
				$args = array(
					'tag__in' 			=> $tag_ids,
					'post__not_in' 		=> array( $this->post_id ),
					'posts_per_page'	=> $this->needed_post_count,
					'post_type'			=> $this->post_type
				 );

				// Result
				$this->rp_get_posts( $args );
			}
		}
	}


	/*
	 * Get Related By Current Term
	 */
	public function get_related_by_current_term() {
		if( count( $this->related_posts ) < $this->count_posts ) {

			// Exclude posts
			$exclude_posts = array( $this->post_id );
			foreach( $this->related_posts as $related_post ) {
				$exclude_posts[] = $related_post->ID;
			}

			// Needed count posts
			$this->needed_post_count = $this->count_posts - count( $this->related_posts );


			// Post terms
			if( !empty( $this->post_terms ) ) {
				$terms_array = array();
				$tax_data = array();

				// Terms array
				foreach( $this->post_terms as $post_term ) {
					$terms_array[ $post_term->taxonomy ][] = $post_term->term_id;
				}

				// Tax data
				foreach( $terms_array as $p_tax => $p_terms ) {

					if( is_array( $p_terms) ) {
						$terms_array[ $p_tax ] = array_unique( $terms_array[ $p_tax ] );
					}

					$tax_data[] = array(
						'taxonomy'  => $p_tax,
						'field' 	=> 'id',
						'terms'     => $terms_array[ $p_tax ],
					 );
				}

				// Query
				if( !empty( $tax_data ) ) {
					$args = array(
						'post__not_in' 	 => $exclude_posts,
						'posts_per_page' => $this->needed_post_count,
						'post_type'		 => get_post_type( $this->post_id ),
						'tax_query' 	 => array(
							'relation' => 'OR'
						)
					);
					$args['tax_query'] = array_merge( $args['tax_query'], $tax_data );

					// Result
					$this->rp_get_posts( $args );
				}
			}
		}
	}


	/*
	 * Get Related By Current Term
	 */
	public function get_related_by_parent_term() {
		if( count( $this->related_posts ) < $this->count_posts ) {

			// Exclude posts
			$exclude_posts = array( $this->post_id );
			foreach( $this->related_posts as $related_post ) {
				$exclude_posts[] = $related_post->ID;
			}

			// Needed count posts
			$this->needed_post_count = $this->count_posts - count( $this->related_posts );

			// Parent terms
			if( !empty( $this->post_terms ) ) {
				$terms_array = array();
				$tax_data = array();

				// Terms array
				foreach( $this->post_terms as $post_term ) {
					if( $post_term->parent != 0 ) {
						$terms_array[ $post_term->taxonomy ][] = $post_term->parent;
					}
				}

				// Tax data
				foreach( $terms_array as $p_tax => $p_terms ) {

					if( is_array( $p_terms) ) {
						$terms_array[ $p_tax ] = array_unique( $terms_array[ $p_tax ] );
					}

					$tax_data[] = array(
						'taxonomy'  => $p_tax,
						'field' 	=> 'id',
						'terms'     => $terms_array[ $p_tax ],
					 );
				}

				// Query
				if( !empty( $tax_data ) ) {
					$args = array(
						'post__not_in' 	 => $exclude_posts,
						'posts_per_page' => $this->needed_post_count,
						'post_type'		 => get_post_type( $this->post_id ),
						'tax_query' 	 => array(
							'relation' => 'OR'
						)
					);
					$args['tax_query'] = array_merge( $args['tax_query'], $tax_data );

					// Result
					$this->rp_get_posts( $args );
				}

			}
		}
	}


	/*
	 * Get Related posts ID's
	 *
	 * @param  array $related_by
	 * @return array Related posts
	 */
	public function get_related( $related_by = array( 'all' ) ) {

		// Vars
		$taxonomies = get_taxonomies( '', 'names' );
		$this->post_terms = (array) wp_get_post_terms( $this->post_id, $taxonomies, array( 'fields' => 'all') );

		// Check Related By
		if( is_string( $related_by ) ) {
			$related_by = explode( ',', $related_by );
		}
		elseif( !is_array( $related_by ) ) {
			return;
		}

		if( in_array( 'all', $related_by ) ) {
			$related_by = array( 'tags', 'term', 'parent_term' );
		}

		$this->related_by = $related_by;

		// Related By Tags
		if( in_array( 'tags', $this->related_by ) ) {
			$this->get_related_by_tags();
		}

		// Related By Current Term
		if( in_array( 'term', $this->related_by ) ) {
			$this->get_related_by_current_term();
		}

		// Related By Parent Term
		if( in_array( 'parent_term', $this->related_by ) ) {
			$this->get_related_by_parent_term();
		}

	return $this->related_posts;
	}
}


/*
 * Returns the main instance.
 *
 * @param  array $args   Related posts settings
 * @return array Related posts
 */
function xkit_get_related_posts( $args = array() ) {
	global $post;
	$options = array(
		'post_id'		=> $post->ID,
		'count_posts' 	=> 3,
		'related_by'	=> 'all', // Possible type - array or string (delimiter: ","). Variables: all/tags/term/parent_term.
	);

	$options = array_merge( $options, $args );


	// Get Related Posts
	$related = new Xkit_RelatedPosts( array(
			'post_id'		=> $options['post_id'],
			'count_posts'	=> $options['count_posts']
		)
	);
	$related_posts = (array) $related->get_related( $options['related_by'] );
	wp_reset_query();

	return $related_posts;
}