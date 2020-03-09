<?php
/*
Plugin Name: MS WA-Broadcast
Description: Plugin campaign & broadcast whatsapp | Support: 8:00am - 4:00pm Email: tom.wpdev@gmail.com 
Author: Minha Studio
Version: 1.0.0
Author URI: tompradana.wordpress.com
*/

if ( !function_exists( 'add_action' ) ) {
	exit;
}

// constant
$version = '1.0.0';
define( 'MS_WABRDOADCAST_DIR', plugin_dir_path( __FILE__ ) );
define( 'MS_WABRDOADCAST_ENV', 'staging' );
if ( MS_WABRDOADCAST_ENV === 'staging' ) {
	$version = time();
}
define( 'MS_WABRDOADCAST_VERSION', $version );

// includes
include( MS_WABRDOADCAST_DIR . 'includes/classes/class.ms-wabroadcast.php' );
