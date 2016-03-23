<?php
/**
 * Wampum Widgets
 *
 * @package   Wampum_Widgets
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Wampum Widgets
 *
 * @package Wampum Widgets
 * @author  Mike Hemberger
 */
final class Wampum_Widgets {

	/** Singleton *************************************************************/

	/**
	 * @var   Wampum_Settings The one true Wampum_Settings
	 * @since 1.0.0
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Widgets;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	public function init() {
		// require_once('widgets/class-wampum-widget-program-steps.php');
		// Register our new widget
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
	    register_widget( 'Wampum_Widget_Program_Steps' );
	}

}
