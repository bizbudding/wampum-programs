<?php
/**
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com>
 * @license   GPL-2.0+
 * @link      http://bizbudding.com.com
 * @copyright 2016 The Stiz Media, LLC
 *
 * @wordpress-plugin
 * Plugin Name:       Wampum
 * Plugin URI:        TBD
 * Plugin Type: 	  Piklist
 * Description: 	  The core Wampum plugin
 * Author:            Mike Hemberger
 * Author URI:        http://bizbudding.com
 * Text Domain:       wampum
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           1.0.0
 * Gitlab URI:		  TBD
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! defined( 'WAMPUM_CORE_DIR' ) ) {
	define( 'WAMPUM_CORE_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_INCLUDES_DIR' ) ) {
	define( 'WAMPUM_INCLUDES_DIR', WAMPUM_CORE_DIR . '/includes/' );
}
if ( ! defined( 'WAMPUM_BASENAME' ) ) {
	define( 'WAMPUM_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

function wampum_require() {
	$files = array(
		'class-wampum',
		'vendor/class-piklist-checker',
		'vendor/class-jivedig-querystring-nav',
		'vendor/extended-cpts',
		'vendor/extended-taxos',
		'class-profile-nav',
		'actions',
		'post-types',
	);
	foreach ( $files as $file ) {
		require WAMPUM_INCLUDES_DIR . $file . '.php';
	}
}
wampum_require();
$wampum = new Wampum();
$wampum->run();
