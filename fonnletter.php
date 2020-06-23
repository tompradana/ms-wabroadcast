<?php
/*
Plugin Name: Fonnletter
Description: Official plugin campaign & broadcast whatsapp from Fonnte
Author: Fonnte
Version: 1.1.0
Author URI: https://fonnte.com
*/

if ( !function_exists( 'add_action' ) ) {
	exit;
}

// constant
$version = '1.1.0';
define( 'FONNLETTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'FONNLETTER_ENV', 'staging' );
if ( FONNLETTER_ENV === 'staging' ) {
	$version = time();
}
define( 'FONNLETTER_VERSION', $version );

// includes
include_once( FONNLETTER_DIR . 'includes/classes/class.fonnletter.php' );

/**
 * [FONNLETTER description]
 */
function FONNLETTER() { 
	return new FONNLETTER_Plugin();
}

/**
 * Bckward compt
 */
$GLOBALS['fonnletter'] = FONNLETTER(); // new FONNLETTER_Plugin();