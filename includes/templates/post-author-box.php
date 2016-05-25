<section class="author-box clear">
	<h3 class="block-title"><?php echo esc_html__( 'Author', 'xkit' ); ?> / <span <?php xkit_get_schema_markup( 'author', true ); ?>><?php the_author(); ?></span></h3>

	<div class="avatar">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
	</div>

	<p class="author-bio">
		<?php the_author_meta( 'description' ); ?>
	</p>
</section>