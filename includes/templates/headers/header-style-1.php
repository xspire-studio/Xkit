<!-- Header Content -->
<div class="header-content clearfix">
	<div class="container">
		<?php xkit_do_controller( 'logotype' ); ?>
	</div>
</div>

<!-- Main Menu -->
<div class="menu-box menu-left var-bg-menu clearfix">
	<div class="container">
		<div class="menu-container clearfix">

			<!-- Custom Items -->
			<div class="custom-menu-items var-color-menu">
				
				<!-- Mobile Menu Icon -->
				<div class="custom-item var-color-menu">
					<div class="item-link menu-mobile-btn">
						<div class="mobile-menu-toogle">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>
				</div>

				<?php
					// Menu Search
					if ( xkit_get_theme_option( 'header_search', true ) ) {
						?>
							<div class="custom-item open-on-click var-color-menu">
								<div class="item-link menu-search-btn">
									<i class="icomoon icon-search"></i>
								</div>
								<div class="item-dropdown var-bg-menu">
									<?php get_search_form(); ?>
								</div>
							</div>
						<?php
					}
					
					// Menu Cart
					if ( xkit_get_theme_option( 'header_cart', true ) && class_exists( 'WooCommerce' ) ) {
						?>
							<div class="custom-item open-on-hover var-color-menu">
								<a class="item-link menu menu-cart-btn" href="<?php echo WC()->cart->get_cart_url(); ?>">
									<i class="icomoon icon-cart"></i>
									
									<?php $count_items = count( (array) WC()->cart->get_cart() ); ?>
									<span class="count-items <?php echo esc_attr( $count_items > 0 ? 'no-items' : '' ); ?>"><?php echo esc_attr( $count_items ); ?></span>
								</a>
								<div class="item-dropdown var-bg-menu">
									<?php get_template_part('includes/templates/mini-cart'); ?>
								</div>
							</div>
						<?php
					}

					// WMPL Dropdown
					if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
						?>
							<div class="custom-item open-on-hover var-color-menu">
								<div class="item-link menu-lang-btn">
									<i class="icomoon icon-earth"></i>
									<?php echo ICL_LANGUAGE_CODE; ?>
								</div>
								<div class="item-dropdown var-bg-menu">
									<?php xkit_the_languages_list(); ?>
								</div>
							</div>
						<?php
					}
				?>
			</div>

			<!-- Main Menu -->
			<?php if ( has_nav_menu( 'main_menu' ) ) { ?>
				<nav class="main-menu" <?php xkit_get_schema_markup( 'nav', true); ?>>
					<?php wp_nav_menu( array( 'theme_location'  => 'main_menu', 'container' => '' ) ); ?>
				</nav>
			<?php } ?>
		</div>
	</div>
</div>