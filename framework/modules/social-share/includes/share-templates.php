<?php
/**
 * Default template
 * 
 * @param  array $templates
 * @param  array $providers
 * @return array $templates
 */
function xkit_add_default_share_template( $templates, $providers ) {

	ob_start();
	?>
		<div class="social-share default total-counter clear">
			<ul>
				<?php
					foreach( $providers as $provider_name => $provider_params ){

						/* Popup sizes */
						$popup_sizes = array( '640', '480' );
						if( isset( $provider_params['popup_sizes'] ) ){
							$popup_sizes = $provider_params['popup_sizes'];
						}

						/* Item HTML */
						echo sprintf( 
							'<li class="share-item %s"><a href="%s" title="%s" onclick="window.open(this.href, \'%s-window\',\'left=20,top=20,width=%d,height=%d,toolbar=0,resizable=1\'); return false;"  target="_blank">%s</a></li>', 
							$provider_name, 
							$provider_params['share_url'],
							$provider_params['share_title'],
							$provider_name,
							$popup_sizes[0],
							$popup_sizes[1],
							$provider_params['icon']
						);
					}
				?>
			</ul>
		</div>
	<?php

	$html = ob_get_contents();
	ob_end_clean();


	/* Return templates */
	$templates['default'] = $html;

	return $templates;
}
add_filter( 'xkit_share_buttons_templates', 'xkit_add_default_share_template', 10, 2 );


/**
 * Flat template
 *
 * @param  array $templates
 * @param  array $providers
 * @return array $templates
 */
function xkit_add_flat_icons_template( $templates, $providers ) {

	ob_start();
	?>
		<div class="social-share flat with-counters clear">
			<ul>
				<?php
					foreach( $providers as $provider_name => $provider_params ){

						/* Popup sizes */
						$popup_sizes = array( '640', '480' );
						if( isset( $provider_params['popup_sizes'] ) ){
							$popup_sizes = $provider_params['popup_sizes'];
						}

						/* Item HTML */
						echo sprintf( 
							'<li class="share-item %s"><a href="%s" title="%s" onclick="window.open(this.href, \'%s-window\',\'left=20,top=20,width=%d,height=%d,toolbar=0,resizable=1\'); return false;"  target="_blank">%s</a></li>', 
							$provider_name, 
							$provider_params['share_url'],
							$provider_params['share_title'],
							$provider_name,
							$popup_sizes[0],
							$popup_sizes[1],
							$provider_params['icon']
						);
					}
				?>
			</ul>
		</div>
	<?php

	$html = ob_get_contents();
	ob_end_clean();


	/* Return templates */
	$templates['flat'] = $html;

	return $templates;
}
add_filter( 'xkit_share_buttons_templates', 'xkit_add_flat_icons_template', 10, 2 );