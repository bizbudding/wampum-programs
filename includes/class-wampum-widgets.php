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
 * Account nav and content for Wampum
 *
 * @package Wampum Widgets
 * @author  Mike Hemberger
 */
class Wampum_Widgets {

	public function __construct() {
		// require_once('widgets/class-wampum-widget-program-steps.php');
		// Register our new widget
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
	    register_widget( 'Wampum_Widget_Program_Steps' );
	}

}