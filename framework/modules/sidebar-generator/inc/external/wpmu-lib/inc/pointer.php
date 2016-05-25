<?php
/**
 * Code-snippet for WordPress pointers.
 * Used in function Xkit_WDev()->pointer()
 *
 * Variables:
 *   - $pointer_id
 *   - $html_el
 *   - $title
 *   - $body
 */
global $xkit_cs_pointer_item;
if( isset( $xkit_cs_pointer_item ) ) {
	extract( $xkit_cs_pointer_item ); // pointer_id, html_el, title, body
}

$code = "<div class=\"wpmui-pointer prepared\"><h3>" . $title . "</h3><p>" . $body . "</p></div>";
$code = str_replace( array("\r", "\n"), '', $code );
?>
<script>
	jQuery(document).ready(function() {
		if ( typeof( jQuery().pointer ) != 'undefined' ) {
			jQuery( '<?php echo esc_attr( $html_el ); ?>' ).pointer({
				content: '<?php echo wp_kses( $string, 'post' ); ?>',
				position: {
					edge: 'left',
					align: 'center'
				},
				close: function() {
					jQuery.post( ajaxurl, {
						pointer: '<?php echo esc_js( $pointer_id ) ?>',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');
			jQuery( '.wpmui-pointer.prepared' ).each(function() {
				var me = jQuery(this),
					ptr = me.closest('.wp-pointer');
				me.removeClass('wpmui-pointer prepared');
				ptr.addClass('wpmui-pointer');
			});
		}
	});
</script>