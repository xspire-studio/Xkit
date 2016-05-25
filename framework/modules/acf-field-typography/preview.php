<html>
	<head>
		<style>
			<?php
				if( isset( $_GET["font"] ) && $_GET["font"] ) {
					echo '@import url(http://fonts.googleapis.com/css?family=' . str_replace(' ', '+',  strip_tags( @$_GET["font"] ) ) . ':' . strip_tags( @$_GET["wi"] ) . ');';
				}
			?>

			.preview_style {
				<?php print( (string) $_GET["css"] ); ?>

				<?php if( !(isset( $_GET["font"] ) && $_GET["font"]) && (isset( $_GET["wi"] ) && $_GET["wi"]) ): ?>
					font-weight: <?php print( strip_tags( $_GET["wi"] ) ); ?>;
				<?php endif; ?>
			}
		</style>
	</head>
	<body>
		<div class="preview_style">Grumpy wizards make toxic brew for the evil Queen and Jack. <br /> 0 1 2 3 4 5 6 7 8 9</div>
	</body>
</html>