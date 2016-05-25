<?php
/**
 * Debugging functionality for, well...debugging.
 *
 * @package Xkit
 * @subpackage Debug
 *
 * 1.0 - function xkit_dump();
 * 2.0 - function xkit_dump_d()
 * 3.0 - function xkit_dump_console();
 */



/*
 * Dumps human-readable information about a variable (output)
 *
 * @param mixed $data
 */
function xkit_dump( $data ) {
	?>
		<pre style="
			display: block;
			border: 1px solid #ccc;
			background: whitesmoke;
			margin: 15px 10px;
			padding: 15px;
			font: 14px courier;

			white-space: pre-wrap;
			white-space: -moz-pre-wrap;
			white-space: -pre-wrap;
			white-space: -o-pre-wrap;
			word-wrap: break-word;
		 "><?php
				$trace = debug_backtrace( false );
				$offset = ( @$trace[2]['function'] === 'dump_d' ) ? 2 : 0;

				if( $output = @$trace[1 + $offset]['class'] ){
					echo wp_kses_post( '<span style="color:red">(class - "' . $output . '")</span>:' );
				}

				if( $output = @$trace[1 + $offset]['function'] ){
					if( !in_array( $output, array( 'include', 'include_once', 'require', 'require_once' ) ) ){
						echo wp_kses_post( '<span style="color:blue">(function - "' . $output . '")</span>:' );
					}
				}

				if( $output = @$trace[0 + $offset]['line'] ){
					echo wp_kses_post( '(line:' . $output . ') ' );
				}

				if( $output = @$trace[0 + $offset]['file'] ){
					echo wp_kses_post( '<span style="color:green;">' . $output. '</span>' . "\n" );
				}

				if ( ! empty($data) ) {
					print_r( $data );
				}
			?>
		</pre>
	<?php
}


/*
 * Dumps human-readable information about a variable and equivalent to exit (output)
 *
 * @param mixed $data
 */
function xkit_dump_d( $data ) {
	call_user_func_array( 'xkit_dump', func_get_args() );
	die();
}


/*
 * The script creates a dummy console object with a log method for when firebug is disabled/not available (output)
 *
 * @param mixed $data
 */
function xkit_dump_console( $data ) {
	$output  =  explode( "\n", print_r( $data, true) );

	if( $output ){
	?>
		<script>
			//<![CDATA[
				if(!console){
					var console = { log:function(){} }
				}
				<?php
					foreach ( $output as $line ) {
						if ( trim( $line ) ) {
							$line    =    addslashes( $line );
							echo "console.log(\"{$line}\");";
						}
					}
				?>
			//]]>
		</script>
	<?php
	}
}