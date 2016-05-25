<?php
/**
 * Adding oembed Providers settings
 *
 * @package Xkit
 * @subpackage Helper oembed-providers
 * @version: 1.0
 *
 * 1.0 - filter  oembed_dataparse | xkit_frameborder_oembed_filter()
 */



/*
 * oEmbed validator Filter
 *
 * @param  string  $return  The returned oEmbed HTML
 * @param  object  $data    A data object result from an oEmbed provider
 * @param  string  $url     The URL of the content to be embedded
 * @return string  oEmbed HTML
 */
function xkit_frameborder_oembed_filter( $return, $data, $url ) {
	$return = str_replace( 'frameborder="0" allowfullscreen', 'style="border: none"', $return );

	return $return;
}
add_filter( 'oembed_dataparse', 'xkit_frameborder_oembed_filter', 90, 3 );


/*
 * Add Instagram oEmbed
 */
wp_oembed_remove_provider( '#https?://instagr(\.am|am\.com)/p/.*#i' );
wp_oembed_add_provider( '#https?://(?:www.)?instagr(\.am|am\.com)/p/([^/]+)#i', 'http://api.instagram.com/oembed', true );


/*
 * Add MixСloud oEmbed
 */
wp_oembed_add_provider( '#https?://(?:www\.)?mixcloud\.com/\S*#i', 'http://www.mixcloud.com/oembed', true );


/*
 * Add SoundCloud oEmbed
 */
wp_oembed_add_provider( '#https?://(?:api\.)?soundcloud\.com/.*#i', 'http://soundcloud.com/oembed', true );


/*
 * Ted oEmbed
 */
wp_oembed_add_provider( '#https?://(www\.)?ted.com/talks/view/id/.+#i', 'http://www.ted.com/talks/oembed.json', true );
wp_oembed_add_provider( '#https?://(www\.)?ted.com/talks/[a-zA-Z\-\_]+\.html#i', 'http://www.ted.com/talks/oembed.json', true );