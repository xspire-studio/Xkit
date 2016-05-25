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
					
					<?php xkit_do_controller( 'archive_description' ); ?>
					
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