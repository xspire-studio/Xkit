<?php get_header(); ?>

	<main <?php xkit_get_schema_markup( 'main', true ); ?>>
		<div class="container">
			<div class="row">
				<?php xkit_do_controller( 'aside', 'main' ); ?>
				
				<div id="main-content" class="<?php xkit_do_controller( 'content_class' ); ?>">
					
					<?php xkit_do_controller( 'breadcrumbs' ); ?>
					
					<?php if( $page_title = xkit_get_controller( 'title' ) ) { ?>
						<h1 class="post-title" <?php xkit_get_schema_markup( 'title', true ); ?>><?php echo wp_kses_post( $page_title ); ?></h1>
					<?php }	?>
					
					<?php
						$include_tax   = xkit_get_theme_option( 'search_tax', '' );
						$include_posts = xkit_get_theme_option( 'search_posts', '' );
						$post_types     = array( 'post' );
						$post__not_in  = array();

						if ( !empty( $include_tax ) ) {
							foreach ( $include_tax as $tax_item ) {
								array_push( $post_types, $tax_item );
							}
						}

						if ( !empty( $include_posts ) ) {
							foreach ( $include_posts as $post_item ) {
								array_push( $post__not_in, $post_item );
							}
						}
						
						query_posts( array(
							'post_status'  => 'publish',
							'paged'        => get_query_var( 'paged' ),
							'post_type'    => $post_types,
							's'            => get_search_query(),
							'post__not_in' => $post__not_in
						) );
					?>
					
					<?php if( have_posts() ): ?>
						<div class="row">
							<?php	
								while ( have_posts() ) : the_post();
									get_template_part( 'includes/templates/' . xkit_get_controller( 'post_content_template' ) );
								endwhile;
							?>
						</div>
					<?php else: ?>
						<div class="empty-result"><?php echo esc_html__( 'Posts Not Found.', 'xkit' ); ?></div>
					<?php endif; ?>
				</div>
				
				<?php xkit_do_controller( 'aside', 'secondary' ); ?>
			</div>
		</div>
	</main>

<?php get_footer(); ?>