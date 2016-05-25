<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<input type="search" name="s" value="<?php echo get_search_query() ?>" placeholder="<?php echo esc_html__( 'Search...', 'xkit' ); ?>" />
	<button type="submit" class="icomoon icon-search"></button>
</form>