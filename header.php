<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php xkit_get_schema_markup( 'body', true ); ?>>

	<div id="wrapper" class="<?php xkit_do_controller( 'site_layout_class' ); ?>">
		
		<?php do_action( 'xkit_theme_top' ); ?>
		
		<header id="main-header" class="<?php xkit_the_theme_option( 'header_style', 'style_1' ); ?> <?php xkit_the_theme_option( 'header_fixed', 'normal' ); ?>" <?php xkit_get_schema_markup( 'header', true ); ?>>
			<?php
				$header_style = xkit_get_theme_option( 'header_style', 'style_1' );
				$header_path  = sprintf( 'includes/templates/headers/header-%s', xkit_convert_dashes( $header_style ) );

				get_template_part( $header_path ); 
			?>
		</header>

		<?php do_action( 'xkit_theme_content_before' ); ?>