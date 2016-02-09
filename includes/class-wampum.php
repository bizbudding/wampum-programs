<?php
/**
 * Wampum
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com.com>
 * @link      https://github.com/JiveDig/wampum/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum {

	public function run() {
		// add_action( 'init', array( $this, 'check_piklist' ) );
		add_action( 'tgmpa_register', array( $this, 'dependencies' ) );
		// Bail if Posts to Posts or Piklist are not active
		if ( ! ( function_exists( 'p2p_register_connection_type' ) || class_exists('Piklist') ) ) {
			add_action( 'admin_init', array( $this, 'deactivate' ) );
			return;
		}
		add_theme_support( 'genesis-connect-woocommerce' );
		// Add an admin settings page
		// add_filter( 'piklist_admin_pages', array( $this, 'settings_page' ) );
	}

	/**
	 * Deactivates the plugin if Genesis isn't running
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		deactivate_plugins( WAMPUM_BASENAME );
		// add_action( 'admin_notices', array( $this, 'error_message' ) );
	}

	/**
	 * Error message if we're not using the Genesis Framework.
	 *
	 * @since 1.0.0
	 */
	public function error_message() {

		$error = sprintf( __( 'Wampum dependent plugins are not installed. Wampum has been deactivated.', 'wampum' ) );

		echo '<div class="error"><p>' . esc_attr( $error ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

	}

	/**
	 * Dependent plugin check
	 * @link http://tgmpluginactivation.com/
	 *
	 * @since 1.0.0
	 *
	 * @return  mixed  admin notice if dependent plugins aren't active
	 */
	public function dependencies() {

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

	 		// Dependent plugins from the WordPress Plugin Repository.
	 		array(
				'name'				=> 'Piklist',
				'slug'				=> 'piklist',
				'required'			=> true,
				'version'			=> '0.9.9.7',
				'force_activation'	=> true,
			),
	 		array(
				'name'				=> 'Posts to Posts',
				'slug'				=> 'posts-to-posts',
				'required'			=> true,
				'version'			=> '1.6.5',
				'force_activation'	=> true,
			),

		);

		// TGM configuration array
	 	$config = array(
	 		'id'           => 'wampum',                 // Unique ID for hashing notices for multiple instances of TGMPA.
	 		'default_path' => '',                       // Default absolute path to bundled plugins.
	 		'menu'         => 'wampum-install-plugins', // Menu slug.
	 		'parent_slug'  => 'themes.php',             // Parent menu slug.
	 		'capability'   => 'edit_theme_options',     // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
	 		'has_notices'  => true,                     // Show admin notices or not.
	 		'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
	 		'dismiss_msg'  => '',                       // If 'dismissable' is false, this message will be output at top of nag.
	 		'is_automatic' => false,                    // Automatically activate plugins after installation or not.
	 		'message'      => '',                       // Message to output right before the plugins table.
	 	);

	 	tgmpa( $plugins, $config );
	}

	/**
	 * Check if Piklist is installed
	 *
	 * @since  1.0.0
	 */
	private function check_piklist() {
		if ( ! is_admin() || ! piklist_checker::check(__FILE__) ) {
			return;
		}
	}

	/**
	 * Set up text domain for translations
	 *
	 * @since TODO
	 */
	public function load_textdomain() {
		// load_plugin_textdomain( 'wampum', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function settings_page( $pages ) {
		$pages[] = array(
			'page_title'	=> __('Wampum Settings', 'wampum'),
			'menu_title'	=> __('Wampum', 'wampum'),
			'capability'	=> 'manage_options',
			'sub_menu'		=> 'options-general.php',
			'menu_slug'		=> 'wampum',
			'setting'		=> 'wampum_settings',
			// 'menu_icon'		=> plugins_url('piklist/parts/img/piklist-icon.png') ,
			// 'page_icon'		=> plugins_url('piklist/parts/img/piklist-page-icon-32.png'),
			// 'default_tab'	=> 'General',
			// 'single_line'	=> true,
			'save_text'		=> __('Save Changes', 'wampum'),
		);
		return $pages;
	}

}
