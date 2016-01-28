<?php
/**
 * CPT Archive Toolbar for Genesis
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@thestizmedia.com.com>
 * @link      https://github.com/JiveDig/cptast-genesis/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum {

	public function run() {
		if ( 'genesis' !== basename( get_template_directory() ) ) {
			add_action( 'admin_init', array( $this, 'deactivate' ) );
			return;
		}
		add_action('init', array( $this, 'check_piklist' ) );
	}

	/**
	 * Deactivates the plugin if Genesis isn't running
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		deactivate_plugins( WAMPUM_BASENAME );
		add_action( 'admin_notices', array( $this, 'error_message' ) );
	}

	/**
	 * Error message if we're not using the Genesis Framework.
	 *
	 * @since 1.0.0
	 */
	public function error_message() {

		$error = sprintf( __( 'Sorry, Wampum works only with the Genesis Framework. It has been deactivated.', 'cptast-genesis' ) );

		echo '<div class="error"><p>' . esc_attr( $error ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

	}

	/**
	 * Check if Piklist is installed
	 *
	 * @since  1.0.0
	 */
	function check_piklist() {
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
		// load_plugin_textdomain( 'cptast-genesis', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

}