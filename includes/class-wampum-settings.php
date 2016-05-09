<?php
/**
 * Wampum Settings
 *
 * @package   Wampum_Settings
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Global settings for Wampum
 *
 * @package Wampum_Settings
 * @author  Mike Hemberger
 */
final class Wampum_Settings {

	/** Singleton *************************************************************/

	/**
	 * @var   Wampum_Settings The one true Wampum_Settings
	 * @since 1.0.0
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Settings;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Remove settings page
	 * As of 0.0.63 beta we stopped using a custom account menu in favor of default Woo
	 *
	 * @return void
	 */
	public function init() {
		// add_filter( 'piklist_admin_pages', array( $this, 'settings_page' ) );
	}

	// Add an admin settings page
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

	public function get_step_progress_settings( $program_id ) {
		return get_post_meta( $program_id, 'wampum_program_step_progress', true );
	}

}
