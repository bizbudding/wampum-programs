<?php
/**
 * Wampum - Programs
 *
 * @package   Wampum_Template_Loader
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Template loader for Wampum
 *
 * @package Wampum
 * @author  Mike Hemberger
 */
final class Wampum_Template_Loader extends Gamajo_Template_Loader {

	/**
	 * @var   Wampum_Template_Loader The one true Wampum_Template_Loader
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Prefix for filter names.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $filter_prefix = 'wampum';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $theme_template_directory = 'wampum';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * In this case, `MEAL_PLANNER_PLUGIN_DIR` would be defined in the root plugin file as:
	 *
	 * ~~~
	 * define( 'MEAL_PLANNER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	 * ~~~
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $plugin_directory = WAMPUM_PLUGIN_DIR;

	/**
	 * Directory name where templates are found in this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * @since 1.1.0
	 *
	 * @type string
	 */
	protected $plugin_template_directory = 'templates'; // or includes/templates, etc.

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Template_Loader;
		}
		return self::$instance;
	}

}
