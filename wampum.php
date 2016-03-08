<?php
/**
 * @package   Wampum
 * @author    BizBudding, INC <mike@bizbudding.com>
 * @license   GPL-2.0+
 * @link      http://bizbudding.com.com
 * @copyright 2016 BizBudding, INC
 *
 * @wordpress-plugin
 * Plugin Name:        Wampum
 * Description: 	   The core Wampum plugin
 * Plugin URI:         https://github.com/JiveDig/wampum
 * Author:             Mike Hemberger
 * Author URI:         http://bizbudding.com
 * Text Domain:        wampum
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:            0.0.32
 * GitHub Plugin URI:  https://github.com/JiveDig/wampum
 * GitHub Branch:	   master
 */

// * Plugin Type: 	      Piklist
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! defined( 'WAMPUM_PLUGIN_DIR' ) ) {
	define( 'WAMPUM_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_INCLUDES_DIR' ) ) {
	define( 'WAMPUM_INCLUDES_DIR', WAMPUM_PLUGIN_DIR . '/includes/' );
}
if ( ! defined( 'WAMPUM_PLUGIN_URI' ) ) {
	define( 'WAMPUM_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_BASENAME' ) ) {
	define( 'WAMPUM_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

function wampum_require() {
	$files = array(
		// 'vendor/class-piklist-checker',
		'vendor/class-tgm-plugin-activation',
		'vendor/class-gamajo-template-loader',
		'vendor/class-jivedig-content-swap',
		'vendor/class-p2p-restful-connection',
		'vendor/extended-cpts',
		'vendor/extended-taxos',
		'class-wampum',
		'class-wampum-account-page',
		'class-wampum-content-types',
		'class-wampum-connections',
		'class-wampum-membership',
		'class-wampum-p2p-restful-user-to-step',
		// 'class-wampum-programs',
		'class-wampum-template-loader',
		'class-wampum-widgets',
		'widgets/class-wampum-widget-program-steps',
		'helpers',
	);
	foreach ( $files as $file ) {
		require WAMPUM_INCLUDES_DIR . $file . '.php';
	}
}
wampum_require();

// add_action( 'after_setup_theme', 'wampum_get_it_poppin' );
// function wampum_get_it_poppin() {
	// Instantiate dependent classes
	$wampum_account_page	= new Wampum_Account_Page();
	$wampum_content_types	= new Wampum_Content_Types();
	$wampum_connections		= new Wampum_Connections();
	$wampum_membership		= new Wampum_Membership();
	$wampum_p2p_usertostep  = new Wampum_P2P_User_To_Step();
	// $wampum_programs		= new Wampum_Programs();
	$wampum_template_loader	= new Wampum_Template_Loader();
	$wampum_widgets     	= new Wampum_Widgets();

	$wampum = new Wampum(
		$wampum_account_page,
		$wampum_content_types,
		$wampum_connections,
		$wampum_membership,
		$wampum_p2p_usertostep,
		// $wampum_programs,
		$wampum_template_loader,
		$wampum_widgets
	);
	$wampum->run();
// }
