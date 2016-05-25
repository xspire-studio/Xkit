<?php

/**
 * Sitepress Multilingual Cms
 *
 * 1.0 - function xkit_the_languages_list()
 */


if( defined( 'ICL_SITEPRESS_VERSION' ) ) {

	/*
	 * Sitepress Multilingual Cms | Get Languages List
	 */
	function xkit_the_languages_list() {
		$languages = icl_get_languages( 'skip_missing=0' );

		if( !empty( $languages ) ) {
			?>
				<ul class="lang-list clear">
					<?php
						foreach( $languages as $elem ) {
							?>
								<li class="<?php echo esc_attr( $elem['active'] ? 'active' : '' ); ?>">
									<a href="<?php echo esc_url( $elem['url'] ); ?>">
										<img src="<?php echo esc_url( $elem['country_flag_url'] ); ?>" alt="<?php echo esc_attr( $elem['translated_name'] ); ?>">
										<?php echo esc_attr( $elem['translated_name'] ); ?>
									</a>
								</li>
							<?php
						}
					?>
				</ul>
			<?php
		}
	}
}