<?php
define( 'SHORTINIT', true );

// Check Site Url
if( !isset( $_POST['site_path'] ) || !$_POST['site_path'] ){
	die();
}

// To the relative location of the wp-load.php
require_once( trim( $_POST['site_path'] ) . 'wp-load.php' );

// Typical headers
header('Content-Type: text/html');
send_nosniff_header();

// Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// Do ajax
$importing_status = intval( get_option( 'importing_status', 0 ) );
if( $importing_status == 1 ){
	$import_percent = get_option( 'import_percent', 1 );
	echo intval( $import_percent );
}
else{
	echo 0;
}

die();