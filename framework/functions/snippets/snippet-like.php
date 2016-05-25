<?php
/**
 * The snippet like
 *
 * @package Xkit
 * @subpackage Snippet like
 *
 * 1.0 - function  xkit_action_post_like()
 * 2.0 - function  xkit_has_already_liked()
 * 3.0 - function  xkit_get_cleaned_ip_liked()
 * 4.0 - function  xkit_get_like_count()
 * 5.0 - function  xkit_the_like()
 * 6.0 - function  xkit_the_like_heart()
 * 7.0 - function  xkit_the_like_hands()
 * 8.0 - hook      xkit_the_like_after     | xkit_like_enqueue_script()
 */



/**
 * The event handler Post Like
 */
function xkit_action_post_like(){
	$nonce = sanitize_text_field( $_POST['nonce'] );

	if ( ! wp_verify_nonce( $nonce, 'like' ) ){
		die ( 'Busted!' );
	}

	if( isset( $_POST['post_id'] ) ) {
		$post_id = (int) xkit_decrypt_data( 'like', sanitize_text_field( $_POST['post_id'] ) );
		$user_ip = xkit_get_user_ip();

		$like_IP = xkit_get_cleaned_ip_liked( $post_id );
		$like_count = (int) get_post_meta( $post_id, 'xkit_like_count', true );

		if( !xkit_has_already_liked( $user_ip, $like_IP ) ){
			$like_IP[$user_ip] = time();

			if( isset( $_POST['event'] ) ){
				if( $_POST['event'] == '+' ){
					++$like_count;
				} elseif( $_POST['event'] == '-' ) {
					--$like_count;
				}
			}

			update_post_meta( $post_id, 'xkit_like_IP', $like_IP );
			update_post_meta( $post_id, 'xkit_like_count', $like_count );

			echo json_encode( array( 'like_count' => $like_count, 'msg' => esc_html__( 'Thank you!', 'xkit' ) ) );
		} else {
			echo json_encode( array( 'like_count' => $like_count, 'msg' => esc_html__( 'You have already voted!', 'xkit' ) ) );
		}
	} else {
		echo json_encode( array( 'msg' => esc_html__( 'ID is not valid', 'xkit' ) ) );
	}
	exit;
}
add_action( 'wp_ajax_nopriv_post_like', 'xkit_action_post_like' );
add_action( 'wp_ajax_post_like', 'xkit_action_post_like' );


/*
 * Whether a user has already voted ?
 * 
 * @param  string $user_ip
 * @param  array  $like_IP
 * @return bool
 */
function xkit_has_already_liked( $user_ip, $like_IP ){
	if( $user_ip && $like_IP ){
		if( in_array( $user_ip, array_keys( $like_IP ) ) ){
			return true;
		}
	}
	return false;
}


/*
 * Get an array of cleaned IP likes post.
 * 
 * @param  int $post_id
 * @return array
 */
function xkit_get_cleaned_ip_liked( $post_id ){
	$like_IP = get_post_meta( $post_id, 'xkit_like_IP', true );

	if( $like_IP ){

		$cleaned_IP = array();

		$now = time();

		foreach( $like_IP as $ip => $time){
			if( round( ( $time - $now ) ) < (180*24*3600) ){
				$cleaned_IP[$ip] = $time;
			}
		}

		return $cleaned_IP;
	}
}


/*
 * Get the number of likes.
 * 
 * @param  int $post_id The post id.
 * @return int The number of likes.
 */
function xkit_get_like_count( $post_id ){
	$like_count = (int) get_post_meta( $post_id, 'xkit_like_count', true );

	return $like_count;
}


/*
 * Voting - basic function.
 * 
 * @param int|string $post_id    The identifier may be a value or parameter 'auto'.
 * @param string     $style      The style may be a 'heart' or 'hands'.
 * @param bool       $is_actived The mode of voting.
 */
function xkit_the_like( $post_id = 'auto', $style = 'heart', $is_actived = true ){
	if( !$post_id || $post_id == 'auto' ){
		$post_id = get_the_ID();
	}

	if( $post_id ){

		$user_ip = xkit_get_user_ip();
		$like_IP = get_post_meta( $post_id, 'xkit_like_IP', true );

		if( xkit_has_already_liked( $user_ip, $like_IP ) ){
			$liked = 'liked';
		} else {
			$liked = null;
		}

		$counter = xkit_get_like_count( $post_id );

		if( $counter > 0 ){
			$status = 'positive';
		} elseif( $counter < 0 ){
			$status = 'negative';
		} else{
			$status = 'null';
		}

		do_action( 'xkit_the_like_before', $post_id, $liked );
	?>
		<span class="box-like <?php echo esc_attr( $is_actived ? 'actived ' : ' ' ); echo esc_attr( $liked ); ?>" 
			data-id="<?php echo xkit_encrypt_data( 'like', $post_id ); ?>" 
			data-nonce="<?php echo wp_create_nonce('like'); ?>"> 

			<?php if( $style == 'heart' ): ?>
				<span class="plus" data-event="+" title="<?php echo esc_html__( 'I like', 'xkit' ); ?>"><i class="fa fa-heart"></i></span>
				<span class="counter"><?php echo esc_attr( $counter ); ?></span> 
				<span class="loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>   
			<?php else: ?>
				<span class="up" data-event="+" title="<?php echo esc_html__( 'I like', 'xkit' ); ?>"><i class="fa fa-thumbs-o-up"></i></span>
				<span class="counter <?php echo esc_attr( $status ); ?>"><?php echo esc_attr( $counter ); ?></span>
				<span class="loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
				<span class="down" data-event="-"  title="<?php echo esc_html__( 'I not like', 'xkit' ); ?>"><i class="fa fa-thumbs-o-down"></i></span>
			<?php endif; ?>
		</span>
	<?php
		do_action( 'xkit_the_like_after', $post_id, $liked );
	} else {
		echo esc_html__( 'ID is not valid', 'xkit' );
	}
}


/*
 * Voting in the style of heart.
 * 
 * @param int|string $post_id    The identifier may be a value or parameter 'auto'.
 * @param bool       $is_actived The mode of voting.
 */
function xkit_the_like_heart( $post_id = 'auto', $is_actived = true ){
	xkit_the_like( $post_id, 'heart' );
}


/*
 * Voting in the style of hands.
 * 
 * @param int|string $post_id    The identifier may be a value or parameter 'auto'.
 * @param bool       $is_actived The mode of voting.
 */
function xkit_the_like_hands( $post_id = 'auto', $is_actived = true ){
	xkit_the_like( $post_id, 'hands' );
}


/*
 * xkit_the_like_after | xkit_like_enqueue_script()
 *
 * Snippet scripts likes.
 */
function xkit_like_enqueue_script(){
	if ( !wp_script_is( 'xkit-like-script' ) ) {
		wp_enqueue_script( 'xkit-like-script', get_template_directory_uri() . '/framework/assets/js/like-script.js' );

		wp_localize_script( 'xkit-like-script', 'objLike', array( 
			'already_voted' => esc_html__( 'You have already voted!', 'xkit' )
		));
	}
}
add_action( 'xkit_the_like_after', 'xkit_like_enqueue_script' );
?>