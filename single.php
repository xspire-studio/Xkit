<?php get_header(); ?>

	<main <?php xkit_get_schema_markup( 'main', true ); ?>>
		<div class="container">
			<div class="row">
				<?php xkit_do_controller( 'aside', 'main' ); ?>
				
				<div id="main-content" class="<?php xkit_do_controller( 'content_class' ); ?>">
					<?php while ( have_posts() ) : the_post(); ?>

						<?php xkit_do_controller( 'post_above_banner' ); ?>
						
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
							<?php 
								/* Title */
								if( $page_title = xkit_get_controller( 'title' ) ) {
									?>
										<h1 class="post-title" <?php xkit_get_schema_markup( 'title', true ); ?>><?php echo wp_kses_post( $page_title ); ?></h1>
									<?php 
								}
							?>

							<?php xkit_do_controller( 'breadcrumbs' ); ?>

							<?php xkit_do_controller( 'thumbnail' ); ?>
							
							<?php xkit_do_controller( 'post_meta' ); ?>
							
							<?php xkit_do_controller( 'post_rating' ); ?>

							<?php the_content(); ?>

							<?php xkit_do_controller( 'post_link_pages' ); ?>
						</article>

						<section class="post-bottom">
							<?php xkit_do_controller( 'post_share' ); ?>
			
							<?php xkit_do_controller( 'post_tags' ); ?>									
						</section>
						
						<?php xkit_do_controller( 'post_below_banner' ); ?>
						
						<?php xkit_do_controller( 'post_autor_box' ); ?>
						
						<?php xkit_do_controller( 'post_comments' ); ?>

					<?php endwhile; ?>
				</div>
				
				<?php xkit_do_controller( 'aside', 'secondary' ); ?>
			</div>
		</div>
	</main>

<?php get_footer(); ?>