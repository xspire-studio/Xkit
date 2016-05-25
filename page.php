<?php get_header(); ?>

	<main <?php xkit_get_schema_markup( 'main', true ); ?>>
		<div class="container">
			<div class="row">
				<?php xkit_do_controller( 'aside', 'main' ); ?>
				
				<div id="main-content" class="<?php xkit_do_controller( 'content_class' ); ?>">
					<?php while ( have_posts() ) : the_post(); ?>
					
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
							<?php if( $page_title = xkit_get_controller( 'title' ) ) { ?>
								<h1 class="post-title" <?php xkit_get_schema_markup( 'title', true ); ?>><?php echo wp_kses_post( $page_title ); ?></h1>
							<?php }	?>
							
							<?php xkit_do_controller( 'breadcrumbs' ); ?>
							
							<?php xkit_do_controller( 'thumbnail' ); ?>
							
							<?php the_content(); ?>
							
							<?php xkit_do_controller( 'post_link_pages' ); ?>
						</article>
						
						<?php xkit_do_controller( 'post_comments' ); ?>
						
					<?php endwhile; ?>
				</div>
				
				<?php xkit_do_controller( 'aside', 'secondary' ); ?>
			</div>
		</div>
	</main>

<?php get_footer(); ?>