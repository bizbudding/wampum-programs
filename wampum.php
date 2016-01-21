<?php
/*
Plugin Name: Wampum
Plugin URI: http://bizbudding.com
Plugin Type: Piklist
Description: The core Wampum plugin
Version: 1.0.0
Author: Mike Hemberger
Author URI: http://bizbudding.com
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Define plugin directory constants
define( 'WAMPUM_CORE_DIR', dirname( __FILE__ ) );
define( 'WAMPUM_INCLUDES_DIR', WAMPUM_CORE_DIR . '/includes/' );
define( 'WAMPUM_PLUGIN_NAME', dirname( plugin_basename( __FILE__ ) ) );

/**
 * Check if Piklist is installed
 *
 * @since  1.0.0
 */
add_action('init', 'wampum_piklist_checker');
function wampum_piklist_checker() {
	if ( ! is_admin() ) {
		return;
	}
	// Include Piklist checker file
	include_once( WAMPUM_INCLUDES_DIR . 'vendor/class-piklist-checker.php');

	if ( ! piklist_checker::check(__FILE__) ) {
		return;
	}
}

require_once( WAMPUM_INCLUDES_DIR . 'vendor/extended-cpts.php' );
require_once( WAMPUM_INCLUDES_DIR . 'vendor/extended-taxos.php' );
// if ( ! function_exists( 'p2p_register_connection_type' ) ) {
	// require_once( WAMPUM_INCLUDES_DIR . 'vendor/wp-lib-posts-to-posts/autoload.php' );
// }
require_once( WAMPUM_INCLUDES_DIR . 'post-types.php' );
require_once( WAMPUM_INCLUDES_DIR . 'post-types.php' );
