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
 * Version:            0.0.41
 * GitHub Plugin URI:  https://github.com/JiveDig/wampum
 * GitHub Branch:	   master
 */

// * Plugin Type: 	      Piklist
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! defined( 'WAMPUM_PLUGIN_DIR' ) ) {
	define( 'WAMPUM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_INCLUDES_DIR' ) ) {
	define( 'WAMPUM_INCLUDES_DIR', WAMPUM_PLUGIN_DIR . '/includes/' );
}
if ( ! defined( 'WAMPUM_TEMPLATES_DIR' ) ) {
	define( 'WAMPUM_TEMPLATES_DIR', WAMPUM_PLUGIN_DIR . '/templates/' );
}
if ( ! defined( 'WAMPUM_PLUGIN_URI' ) ) {
	define( 'WAMPUM_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WAMPUM_BASENAME' ) ) {
	define( 'WAMPUM_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

function wampum_require() {
	$files = array(
		// 'lib/class-piklist-checker',
		'lib/class-tgm-plugin-activation',
		'lib/class-gamajo-template-loader',
		'lib/class-jivedig-content-swap',
		'lib/class-p2p-restful-connection',
		'lib/extended-cpts',
		'lib/extended-taxos',
		'class-wampum',
		'class-wampum-account-page',
		'class-wampum-content-types',
		'class-wampum-connections',
		'class-wampum-membership',
		'class-wampum-user-step-progress',
		'class-wampum-template-loader',
		'class-wampum-widgets',
		'widgets/class-wampum-widget-program-steps',
		'functions-display',
		'functions-helpers',
	);
	foreach ( $files as $file ) {
		require WAMPUM_INCLUDES_DIR . $file . '.php';
	}
}
wampum_require();

// Instantiate dependent classes
$wampum_account_page		= new Wampum_Account_Page();
$wampum_content_types		= new Wampum_Content_Types();
$wampum_connections			= new Wampum_Connections();
$wampum_membership			= new Wampum_Membership();
$wampum_user_step_progress	= new Wampum_User_Step_Progress();
$wampum_template_loader		= new Wampum_Template_Loader();
$wampum_widgets				= new Wampum_Widgets();

$wampum = new Wampum(
	$wampum_account_page,
	$wampum_content_types,
	$wampum_connections,
	$wampum_membership,
	$wampum_user_step_progress,
	$wampum_template_loader,
	$wampum_widgets
);
$wampum->run();

//
// add_action( 'wp_enqueue_scripts', 'wampum_init' );
// function wampum_init() {
//    $wampum_user_step_progress = new Wampum_User_Step_Progress();
// }
