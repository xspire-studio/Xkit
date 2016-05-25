<article id="post-<?php the_ID(); ?>" <?php post_class( xkit_get_controller( 'post_content_class' ) ); ?>>
	
	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<span class="sticky-post"><?php _e( 'Featured', 'xkit' ); ?></span>
	<?php endif; ?>
	
	<!-- Post Title -->
	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
	</h2>
	
	<!-- Post Thumnail -->
	<?php xkit_do_controller( 'thumbnail' ); ?>
	
	<!-- Post Info -->
	<div class="entry-info">
		<span class="info-item entry-author">
			<?php esc_html_e( 'Posted by', 'xkit' ); ?>
			<span <?php xkit_get_schema_markup( 'author', true ); ?>><?php the_author_posts_link(); ?></span>
		</span>
		
		<span class="info-item entry-date">
			<?php esc_html_e( 'Posted on', 'xkit' ); ?>
			<?php xkit_do_controller( 'post_date' ); ?>
		</span>
		
		<?php 
			if ( 'post' === get_post_type() ) { 
				?>
				<span class="info-item entry-cats">
					<?php esc_html_e( 'Categories', 'xkit' ); ?>
					<?php echo get_the_category_list( ', ' ); ?>
				</span>
				
				<span class="info-item entry-tags">
					<?php esc_html_e( 'Tags', 'xkit' ); ?>
					<?php echo get_the_tag_list( '', ', ' ); ?>
				</span>
				<?php
			}
		?>
		
		<span class="info-item entry-comments">
			<?php xkit_do_controller( 'post_count_comments' ); ?>
		</span>
	</div>
	
	<!-- Post Content -->
	<div class="entry-content">
		<?php
			xkit_get_excerpt( array(
				'maxchar'    => 200,
				'more_link'  => false,
			) );
		?>
	</div>
	
	<!-- Read More Link -->	
	<div class="read-more"><a href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read more', 'xkit' ); ?></a></div>
</article>