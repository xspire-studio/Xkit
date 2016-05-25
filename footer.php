		<?php do_action( 'xkit_theme_content_after' ); ?>

		<footer <?php xkit_get_schema_markup( 'footer', true); ?>>
			<section class="clear cont">
				<div class="widget-area-list clear <?php echo xkit_get_theme_option( 'footer_layout', 'widget_3' ); ?>">
					<div class="widget-area first-widget-area">
						<?php xkit_do_controller( 'sidebar', 'first-footer-sidebar' ); ?>
					</div>

					<div class="widget-area second-widget-area">
						<?php xkit_do_controller( 'sidebar', 'second-footer-sidebar' ); ?>
					</div>

					<div class="widget-area third-widget-area">
						<?php xkit_do_controller( 'sidebar', 'third-footer-sidebar' ); ?>
					</div>
				</div>
			</section>

			<?php
				if ( $footer_copyright = xkit_get_theme_option( 'footer_copyright' ) ) {
					?>
					<div class="footer-text var-color-secondary"><?php echo wp_kses_post( $footer_copyright ); ?></div>
					<?php 
				}
			?>
		</footer>

		<?php do_action( 'xkit_theme_bottom' ); ?>

		<?php wp_footer(); ?>
	</div>
</body>
</html>