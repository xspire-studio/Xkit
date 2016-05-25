<?php

/**
 * Widget About
 *
 * @package Sweet Diamond
 * @subpackage Sweet_Diamond_Widget_About
 *
 * 1.0 - method __construct()
 * 2.0 - method upload_scripts()
 * 3.0 - method widget()
 * 4.0 - method update()
 * 5.0 - method form()
 */


class Sweet_Diamond_Widget_About extends WP_Widget {

	public $image_field = 'image';

	public function __construct() {

		$widget_ops = array(
			'theme_panel' => true,
			'classname'   => 'sweet_diamond_widget_about',
			'description' => esc_html__( 'Displays a short information about you.', 'xkit' )
		);

		parent::__construct( 'sweet_diamond_widget_about',  esc_html__( 'About', 'xkit' ), $widget_ops );

		add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts') );
	}


	/*
	 * Upload the Javascripts for the media uploader
	 */
	public function upload_scripts()
	{
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'sweet-diamond-upload_media_widget',  get_template_directory_uri() . '/framework/assets/js/upload-media.js', array( 'jquery' ) );

		wp_enqueue_style('thickbox' );
	}


	/*
	 * Front-end display of widget
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$title      = !empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : esc_html__( 'About Me', 'xkit' );
		$image      = !empty( $instance['image'] ) ? $instance['image'] : '';
		$welcome    = !empty( $instance['welcome'] ) ? $instance['welcome'] : 'Hi, I\'m BLOGGER';
		$desc       = !empty( $instance['desc'] ) ? $instance['desc'] : '';
		$link       = !empty( $instance['link'] ) ? $instance['link'] : '';
		$link_text  = !empty( $instance['link_text'] ) ? $instance['link_text'] : 'CLICK FOR MY FULL BIO';

		$image = str_replace( '%THEME_URI%', get_template_directory_uri(), $image );

		echo wp_kses_post( $before_widget );

		if ( $title ) {
			echo wp_kses_post( $before_title . $title . $after_title );
		}
		?>
			<div class="widget-about-container">
				<?php
					$filter_about = apply_filters( 'sweet_diamond_widget_about_entry_html', 'about', $instance );

					if( $filter_about == 'about' ){
				?>
					<div class="thumb">
						<a href="<?php echo esc_url( $link ); ?>">
							<?php if( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" />
							<?php endif; ?>
						</a>
					</div>

					<h3 class="name"><?php echo esc_attr( $welcome ); ?></h3>

					<div class="desc"><?php echo esc_attr( $desc ); ?></div>
					<div class="more">
						<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_attr( $link_text ); ?></a>
					</div>
				<?php } // end if filter ?>
			</div>
		<?php

		echo wp_kses_post( $after_widget );
	}


	/*
	 * Sanitize widget form values as they are saved
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['image']               = wp_kses_post( $new_instance['image'] );
		$instance['welcome']             = wp_kses_post( $new_instance['welcome'] );
		$instance['desc']                = wp_kses_post( $new_instance['desc'] );   
		$instance['link']                = wp_kses_post( $new_instance['link'] );
		$instance['link_text']           = wp_kses_post( $new_instance['link_text']);

		return $instance;
	}


	/*
	 * Back-end widget form
	 */
	public function form( $instance ) {
		$title      = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'About Me', 'xkit' );
		$image      = isset( $instance['image'] ) ? $instance['image'] : '';
		$welcome    = isset( $instance['welcome'] ) ? $instance['welcome'] : 'Hi, I\'m BLOGGER';
		$desc       = isset( $instance['desc'] ) ? $instance['desc'] : '';
		$link       = isset( $instance['link'] ) ? $instance['link'] : '';
		$link_text  = isset( $instance['link_text'] ) ? $instance['link_text'] : 'CLICK FOR MY FULL BIO';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'xkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image:', 'xkit' ); ?></label>
			<input name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" class="widefat" type="text" size="36" value="<?php echo esc_attr( $image ); ?>" />
			<input class="upload_image_button" type="button" value="Upload Image" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'welcome' ) ); ?>"><?php esc_html_e( 'Welcome:', 'xkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'welcome' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'welcome' ) ); ?>" type="text" value="<?php echo esc_attr( $welcome ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php esc_html_e( 'Description:', 'xkit' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" cols="20" rows="5"><?php echo esc_attr( $desc ); ?></textarea></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link full bio:', 'xkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>"><?php esc_html_e( 'Link text:', 'xkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_text' ) ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" /></p>
		<?php
	}
}


/* 
 * Register the widget
 */
add_action( 'widgets_init', function(){
	register_widget( 'Sweet_Diamond_Widget_About' );
});