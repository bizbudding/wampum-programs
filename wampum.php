<?php
/**
 * @package   Wampum
 * @author    BizBudding, INC <mike@bizbudding.com>
 * @license   GPL-2.0+
 * @link      http://bizbudding.com.com
 * @copyright 2016 BizBudding, INC
 *
 * @wordpress-plugin
 * Plugin Name:       Wampum
 * Plugin URI:        https://gitlab.com/jivedig/wampum
 * Plugin Type: 	  Piklist
 * Description: 	  The core Wampum plugin
 * Author:            Mike Hemberger
 * Author URI:        http://bizbudding.com
 * Text Domain:       wampum
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           0.0.11
 * Gitlab URI:		  https://gitlab.com/jivedig/wampum
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! defined( 'WAMPUM_PLUGIN_DIR' ) ) {
	define( 'WAMPUM_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_INCLUDES_DIR' ) ) {
	define( 'WAMPUM_INCLUDES_DIR', WAMPUM_PLUGIN_DIR . '/includes/' );
}
if ( ! defined( 'WAMPUM_BASENAME' ) ) {
	define( 'WAMPUM_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

function wampum_require() {
	$files = array(
		// 'vendor/class-piklist-checker',
		'vendor/class-tgm-plugin-activation',
		'vendor/class-gamajo-template-loader',
		'vendor/class-jivedig-querystring-nav',
		'vendor/extended-cpts',
		'vendor/extended-taxos',
		'class-wampum',
		'class-wampum-template-loader',
		'class-profile-nav',
	);
	foreach ( $files as $file ) {
		require WAMPUM_INCLUDES_DIR . $file . '.php';
	}
}
wampum_require();

// Instantiate dependent classes
$wampum_template_loader = new Wampum_Template_Loader();

$wampum = new Wampum(
	$wampum_template_loader
);
$wampum->run();
