<?php 
	$maintenance_page = apply_filters( 'xkit_maintenance_page_html', 'default' );

	if( $maintenance_page == 'default' ):
?>
	<!doctype html>
	<html>
	<head>
		<?php
			$create_title = '{#title#}' . get_bloginfo('name') . '{#/title#}';

			echo str_replace( array( '{#', '#}' ), array( '<', '>' ), $create_title );
		?>

		<meta charset="utf-8">
		<meta name="description" content="<?php bloginfo('description'); ?>" />

		<link href="http://fonts.googleapis.com/css?family=Lobster&subset=cyrillic,latin" rel="stylesheet" />

		<link href="<?php echo get_template_directory_uri() . '/framework/modules/maintenance-mode/css/maintenance-page.css'; ?>" rel="stylesheet" />

		<?php do_action( 'xkit_maintenance_page_head' ); ?>
	</head>
	<body>
		<div id="wrapper">
			<div id="inner">
				<div class="content-wrap">
					<div class="content">
						<?php do_action( 'xkit_maintenance_page_before_cont' ); ?>

						<h1><?php echo wp_kses( get_option( 'maintenance_mode_title_text' ), array() ); ?></h1>

						<div class="text">
							<?php echo wp_kses( get_option( 'maintenance_mode_general_text' ), array( 'a' => array( 'href' => array(), 'title' => array() ), 'strong' => array(), 'em' => array() ) ); ?>
						</div>

						<?php do_action( 'xkit_maintenance_page_after_cont' ); ?>
					</div>
				</div>

				<div class="footer clearfix">
					<div class="text">
						<?php echo wp_kses( get_option( 'maintenance_mode_footer_text' ), array( 'a' => array( 'href' => array(), 'title' => array() ), 'strong' => array(), 'em' => array() ) ); ?>
					</div>

					<a class="login" href="<?php echo wp_login_url(); ?>" title="Admin">
						<span>&rsaquo;</span> <?php echo esc_html__( 'Admin', 'xkit' ); ?>
					</a>
				</div>
			</div>
		</div><!--/wrapper-->
	</body>
	</html>
<?php endif; // end if filter ?>