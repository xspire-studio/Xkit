<?php
/*
 * Add Facebook provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_facebook_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'http://graph.facebook.com/?id=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );
	if( isset( $response_result['shares'] ) ) {
		$counter = intval( $response_result['shares'] );
	}
	else{
		$counter = 0;
	}

	$counters['facebook'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_facebook_counter', 10, 2 );

function xkit_add_facebook_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://www.facebook.com/sharer.php?u=' . $current_url;

	/* Add provider */
	$providers['facebook'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Facebook', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-facebook"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_facebook_share', 10, 2 );



/*
 * Add Twitter provider.
 * 
 * @param array  $providers
 * @param string $current_url
 * @return array $providers
 */
function xkit_add_twitter_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'https://twitter.com/share?url=' . $current_url . '&counturl=' . $current_url . '&text=' . urlencode( xkit_get_page_title() );

	/* Add provider */
	$providers['twitter'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Twitter', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-twitter"></i>',
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_twitter_share', 10, 2 );



/*
 * Add Google provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_google_counter( $counters, $current_url ) {

	/* Get Counter */
	$args = array(
		'method' 	=> 'POST',
		'timeout' 	=> 3,
		'blocking'	=> true,
		'headers'	=> array(
			'Content-Type' => 'application/json'
		),
		'body'		=> '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $current_url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
	);
	$response = wp_remote_post( 'https://clients6.google.com/rpc', $args );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );
	if( isset( $response_result[0]['result']['metadata']['globalCounts']['count'] ) ) {
		$counter = intval( $response_result[0]['result']['metadata']['globalCounts']['count'] );
	}
	else{
		$counter = 0;
	}

	$counters['google'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_google_counter', 10, 2 );

function xkit_add_google_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'https://plus.google.com/share?url=' . $current_url;

	/* Add provider */
	$providers['google'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Google+', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-google-plus"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_google_share', 10, 2 );



/*
 * Add Pinterest provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_pinterest_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'http://widgets.pinterest.com/v1/urls/count.json?callback=jsonp&url=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_body = str_replace( array( 'jsonp(', ')' ), '', $response);
	$response_result = json_decode( $response_body, true );
	if( isset( $response_result['count'] ) ) {
		$counter = intval( $response_result['count'] );
	}
	else{
		$counter = 0;
	}

	$counters['pinterest'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_pinterest_counter', 10, 2 );

function xkit_add_pinterest_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://pinterest.com/pin/create/bookmarklet/?url=' . $current_url;

	/* Add provider */
	$providers['pinterest'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Pinterest', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-pinterest"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_pinterest_share', 10, 2 );



/*
 * Add Vkontakte provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_vk_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'http://vk.com/share.php?act=count&index=1&url=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	preg_match( '/^VK.Share.count\(1, (\d+)\);$/i', $response, $response_result );

	if( isset( $response_result[1] ) ) {
		$counter = intval( $response_result[1] );
	}
	else{
		$counter = 0;
	}

	$counters['vkontakte'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_vk_counter', 10, 2 );

function xkit_add_vk_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://vk.com/share.php?url=' . $current_url;

	/* Add provider */
	$providers['vkontakte'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Vkontakte', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-vk"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_vk_share', 10, 2 );



/*
 * Add Odnoklassniki provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_odnoklassniki_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'https://connect.ok.ru/dk?st.cmd=extLike&uid=odklcnt0&ref=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	preg_match( '/^ODKL.updateCount\(\'odklcnt0\',\'(\d+)\'\);$/i', $response, $response_result );

	if( isset( $response_result[1] ) ) {
		$counter = intval( $response_result[1] );
	}
	else{
		$counter = 0;
	}

	$counters['odnoklassniki'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_odnoklassniki_counter', 10, 2 );

function xkit_add_odnoklassniki_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' . $current_url;

	/* Add provider */
	$providers['odnoklassniki'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Odnoklassniki', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-odnoklassniki"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_odnoklassniki_share', 10, 2 );



/*
 * Add Mail.ru provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_mailru_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'http://connect.mail.ru/share_count?url_list=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );
	$response_result = array_values( $response_result );
	if( isset( $response_result[0]['shares'] ) ) {
		$counter = intval( $response_result[0]['shares'] );
	}
	else{
		$counter = 0;
	}

	$counters['mailru'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_mailru_counter', 10, 2 );

function xkit_add_mailru_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://connect.mail.ru/share?url=' . $current_url;

	/* Add provider */
	$providers['mailru'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Mail.ru', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-at"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_mailru_share', 10, 2 );



/*
 * Add LinkedIn provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_linkedin_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'https://www.linkedin.com/countserv/count/share?format=json&url=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );

	if( isset( $response_result['count'] ) ) {
		$counter = intval( $response_result['count'] );
	}
	else{
		$counter = 0;
	}

	$counters['linkedin'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_linkedin_counter', 10, 2 );

function xkit_add_linkedin_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $current_url;

	/* Add provider */
	$providers['linkedin'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'LinkedIn', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-linkedin"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_linkedin_share', 10, 2 );



/*
 * Add Reddit provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_reddit_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'https://buttons.reddit.com/button_info.json?url=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );

	if( isset( $response_result['data']['children']['0']['data']['ups'] ) ) {
		$counter = intval( $response_result['data']['children']['0']['data']['ups'] );
	}
	else{
		$counter = 0;
	}

	$counters['reddit'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_reddit_counter', 10, 2 );

function xkit_add_reddit_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'https://www.reddit.com/submit?url=' . $current_url;

	/* Add provider */
	$providers['reddit'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Reddit', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-reddit-alien"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_reddit_share', 10, 2 );



/*
 * Add Tumblr provider.
 * 
 * @param array  $counters
 * @param string $current_url
 * @return array $counters
 */
function xkit_add_thumblr_counter( $counters, $current_url ) {

	/* Get Counter */
	$endpoint = 'http://api.tumblr.com/v2/share/stats?url=' . $current_url;
	$response = wp_remote_get( $endpoint, array( 'timeout' => 3 ) );
	$response = wp_remote_retrieve_body( $response );

	/* Create Counter */
	$response_result = json_decode( $response, true );

	if( isset( $response_result['response']['note_count'] ) ) {
		$counter = intval( $response_result['response']['note_count'] );
	}
	else{
		$counter = 0;
	}

	$counters['tumblr'] = $counter;

	return $counters;
}
add_filter( 'xkit_social_counters', 'xkit_add_thumblr_counter', 10, 2 );

function xkit_add_thumblr_share( $providers, $current_url ) {

	/* Share url */
	$share_url = 'http://tumblr.com/widgets/share/tool?canonicalUrl=' . $current_url;

	/* Add provider */
	$providers['tumblr'] = array(
		'share_url'		=> $share_url,
		'share_title'	=> esc_html__( 'Tumblr', 'xkit' ),
		'popup_sizes'	=> array( 640, 480 ),
		'icon'			=> '<i class="social-icon fa fa-tumblr"></i>'
	);

	return $providers;
}
add_filter( 'xkit_social_providers', 'xkit_add_thumblr_share', 10, 2 );