<?php
/**
 * The snippet rating
 *
 * @package Xkit
 * @subpackage Snippet rating
 *
 * 1.0  - function  xkit_action_post_rating()
 * 2.0  - function  xkit_has_already_voted()
 * 3.0  - function  xkit_get_cleaned_ip_voted()
 * 4.0  - function  xkit_consider_rating_average()
 * 5.0  - function  xkit_get_rating_average()
 * 6.0  - function  xkit_get_rating_count()
 * 7.0  - function  xkit_builder_stars_info()
 * 8.0  - function  xkit_builder_stars()
 * 9.0  - function  xkit_the_rating()
 * 10.0 - function  xkit_the_rating_stars()
 * 11.0 - hook      xkit_the_rating_after   | xkit_rating_enqueue_script()
 */



/*
 * The event handler Rating
 */
function xkit_action_post_rating(){
	$nonce = sanitize_text_field( $_POST['nonce'] );

	if ( ! wp_verify_nonce( $nonce, 'rating' ) ){
		die ( 'Busted!' );
	}

	if( isset( $_POST['post_id'] ) && isset( $_POST['val'] ) ) {
		$post_id     = (int) xkit_decrypt_data( 'rating', sanitize_text_field( $_POST['post_id'] ) );
		$user_rating = (int) $_POST['val'];
		$user_ip     = xkit_get_user_ip();

		$voted_IP = xkit_get_cleaned_ip_voted( $post_id );
		$voted_items = get_post_meta( $post_id, 'xkit_rating_items', true );

		if( !xkit_has_already_voted( $user_ip, $voted_IP ) ){
			$voted_IP[$user_ip] = time();
			$voted_items[] = $user_rating;

			$rating_average = xkit_consider_rating_average( $voted_items );
			$rating_count = count( $voted_items );

			update_post_meta( $post_id, 'xkit_rating_voted_IP', $voted_IP );
			update_post_meta( $post_id, 'xkit_rating_average', $rating_average );
			update_post_meta( $post_id, 'xkit_rating_items', $voted_items );
			update_post_meta( $post_id, 'xkit_rating_count', $rating_count );

			$data_ajax_response = array(
				'info'  => xkit_builder_stars_info( $rating_average, $rating_count ),
				'stars' => xkit_builder_stars( $rating_average ),
			);

			echo json_encode( array( 'response' => $data_ajax_response, 'msg' => esc_html__( 'Thank you!', 'xkit' ) ) );
		} else {
			echo json_encode( array( 'msg' => esc_html__( 'You have already voted!', 'xkit' ) ) );
		}
	} else {
		echo json_encode( array( 'msg' => esc_html__( 'ID is not valid', 'xkit' ) ) );
	}
	exit;
}
add_action( 'wp_ajax_nopriv_post_rating', 'xkit_action_post_rating' );
add_action( 'wp_ajax_post_rating', 'xkit_action_post_rating' );


/*
 * Whether a user has already voted ?
 * 
 * @param  string $user_ip
 * @param  array  $voted_IP
 * @return bool
 */
function xkit_has_already_voted( $user_ip, $voted_IP ){
	if( $user_ip && $voted_IP ){
		if( in_array( $user_ip, array_keys( $voted_IP ) ) ){
			return true;
		}
	}
	return false;
}


/*
 * Get an array of cleaned IP voted post.
 * 
 * @param  int $post_id
 * @return array
 */
function xkit_get_cleaned_ip_voted( $post_id ){
	$voted_IP = get_post_meta( $post_id, 'xkit_rating_voted_IP', true );

	if( $voted_IP ){

		$cleaned_IP = array();

		$now = time();

		foreach( $voted_IP as $ip => $time){
			if( round( ( $time - $now ) ) < (180*24*3600) ){
				$cleaned_IP[$ip] = $time;
			}
		}

		return $cleaned_IP;
	}
}


/*
 * Consider rating average.
 * 
 * @param  array $voted_array
 * @return int
 */
function xkit_consider_rating_average( $voted_array ){
	return round( array_sum( $voted_array ) / count( $voted_array ), 1 );
}


/*
 * Get the rating average by post.
 * 
 * @param int $post_id The post id.
 * @return int The post rating.
 */
function xkit_get_rating_average( $post_id ){
	$rating = (float) get_post_meta( $post_id, 'xkit_rating_average', true );

	return $rating;
}


/*
 * Get the number of votes by post.
 * 
 * @param  int $post_id The post id.
 * @return int The number of votes.
 */
function xkit_get_rating_count( $post_id ){
	$rating_count = (int) get_post_meta( $post_id, 'xkit_rating_count', true );

	return $rating_count;
}


/*
 * Html builder info stars.
 * 
 * @param  int $post_id
 * @return string The info by votes.
 */
function xkit_builder_stars_info( $rating_average = 0, $rating_count = 0 ){
	return sprintf( '%s/5 %s (%d %s)', $rating_average, esc_html__( 'stars', 'xkit' ), $rating_count, esc_html__( 'votes', 'xkit' ) );
}


/*
 * Html builder stars.
 * 
 * @param int $rating_average
 * @return string
 */
function xkit_builder_stars( $rating ){
	if( $rating > 5 ){
		$rating = 5;
	}

	ob_start();

	$number_of_full = floor( $rating );
	for ( $i = 1; $i <= $number_of_full; $i++ ) {
		echo '<i class="fa fa-star"></i>';
	}

	$number_of_half = round( $rating ) - floor( $rating );
	for ( $i = 1; $i <= $number_of_half; $i++ ) {
		echo '<i class="fa fa-star-half-o"></i>';
	}

	$number_of_empty = 5 - ( $number_of_full + $number_of_half );
	for ( $i = 1; $i <= $number_of_empty; $i++ ) {
		echo '<i class="fa fa-star-o"></i>';
	}

	return ob_get_clean();
}


/*
 * Rating - basic function.
 * 
 * @param int|string $post_id     The identifier may be a value or parameter 'auto'.
 * @param bool     	 $info_enable Show more information.
 * @param bool       $is_actived  The mode of voting.
 */
function xkit_the_rating( $post_id = 'auto', $info_enable = true, $is_actived = true ){
	if( !$post_id || $post_id == 'auto' ){
		$post_id = get_the_ID();
	}

	if( $post_id ){

		$user_ip = xkit_get_user_ip();
		$voted_IP = get_post_meta( $post_id, 'xkit_rating_voted_IP', true );

		if( xkit_has_already_voted( $user_ip, $voted_IP ) ){
			$voted = 'voted';
		} else {
			$voted = null;
		}

		$rating_average = xkit_get_rating_average( $post_id );
		$rating_count = xkit_get_rating_count( $post_id );

		do_action( 'xkit_the_rating_before', $post_id, $voted );
	?>
		<span class="box-rating <?php echo esc_attr( $is_actived ? 'actived ' : ' ' ); echo esc_attr( $voted ); ?>" 
			data-id="<?php echo xkit_encrypt_data( 'rating', $post_id ); ?>" 
			data-nonce="<?php echo wp_create_nonce('rating'); ?>"> 

			<?php if( $info_enable ): ?>
				<span class="info">
					<?php echo xkit_builder_stars_info( $rating_average, $rating_count ); ?>
				</span> 
			<?php endif; ?>

			<span class="stars"><?php echo xkit_builder_stars( $rating_average ); ?></span>

			<span class="loading" style="display:none;">
				<i class="fa fa-refresh fa-spin"></i>
			</span>
		</span>
	<?php
		do_action( 'xkit_the_rating_after', $post_id, $voted );
	} else {
		echo esc_html__( 'ID is not valid', 'xkit' );
	}
}


/*
 * Rating with no additional information.
 * 
 * @param int|string $post_id    The identifier may be a value or parameter 'auto'.
 * @param bool       $is_actived The mode of voting.
 */
function xkit_the_rating_stars( $post_id = 'auto', $is_actived = true ){
	xkit_the_rating( $post_id, false, $is_actived  );
}


/*
 * xkit_the_rating_after | xkit_rating_enqueue_script()
 *
 * Snippet scripts rating.
 */
function xkit_rating_enqueue_script(){
	if ( !wp_script_is( 'xkit-rating-script' ) ) {
		wp_enqueue_script( 'xkit-rating-script', get_template_directory_uri() . '/framework/assets/js/rating-script.js' );

		wp_localize_script( 'xkit-rating-script', 'objRating', array( 
			'already_voted' => esc_html__( 'You have already voted!', 'xkit' )
		));
	}
}
add_action( 'xkit_the_rating_after', 'xkit_rating_enqueue_script' );
?>