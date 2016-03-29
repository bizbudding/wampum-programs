<?php
/**
 * Wampum
 *
 * @package   Wampum_P2P_User_Step_Progress
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Restfully connect a user to a step
 *
 * @package Wampum_P2P_User_Step_Progress
 * @author  Mike Hemberger
 */
final class Wampum_User_Step_Progress extends P2P_Restful_Connection {

	/**
	 * @since 1.0.0
	 *
	 * @var Wampum_User_Step_Progress The one true Wampum_User_Step_Progress
	 */
	private static $instance;

	/**
	 * Name of the p2p connection.
	 *
	 * @since 1.0.0
	 *
	 * @var   string
	 */
	protected $connection_name = 'user_step_progress';

	protected $from;

	protected $to;

	// protected $link_connect_text = 'Connect!';
	protected $link_connect_text;

	// protected $link_connected_text = 'Connected!';
	protected $link_connected_text;

	/**
	 * Our success/fail messages for this connection
	 *
	 * @since 1.0.0
	 *
	 * @var   array
	 */
	// protected $messages = array(
	// 		'connect_success'		=> 'Completed!',
	// 		'disconnect_success'	=> 'Mark Complete',
	// 		'can_connect_fail'		=> 'An error occurred',
	// 		'can_disconnect_fail'	=> 'An error occurred',
	// 	);

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_User_Step_Progress;
		}
		return self::$instance;
	}

	/**
	 * Run the parent constructor and locate our javascript file url
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->script_url = WAMPUM_PLUGIN_URL . '/js/restful_p2p.js';
	}

    /**
     * Get additional ajax data
     *
     * @return array
     */
	public function additional_ajax_data() {
		// return;
		return $this->get_connection_text( $this->to );
	}

	/**
	 * The main function for that returns Wampum_User_Step_Progress link
	 *
	 * @since  1.0.0
	 *
	 * @param  $from  User ID
	 * @param  $to    Step ID
	 *
	 * @return string|null  connection link with data attributes for from/to
	 */
	public function maybe_get_step_progress_link( $from, $to ) {
		if ( ! is_user_logged_in() ) {
			return;
		}
		// Get step program
		$program = Wampum()->connections->get_program_from_step_query( get_queried_object() );
		if ( ! $program ) {
			return;
		}
		// Get settings
		$settings = Wampum()->settings->get_step_progress_settings( $program->ID );
		// Bail if not what we need
		if ( ! $settings || ! array_key_exists( 'enabled', $settings ) ) {
			return;
		}
		// Bail if step progress link is not enabled
		if ( 'yes' !== $settings['enabled'] ) {
			return;
		}
		// Get text
		$connect    = ! empty($settings['connect_text']) ? sanitize_text_field($settings['connect_text']) : __( 'Mark Complete', 'wampum' );
		$connected  = ! empty($settings['connected_text']) ? sanitize_text_field($settings['connected_text']) : __( 'Completed', 'wampum' );
		// Return the link, via parent class method
		return $this->get_link( $from, $to, $connect, $connected );
	}

	/**
	 * The main function for that returns Wampum_User_Step_Progress link
	 *
	 * @since  1.0.0
	 *
	 * @param  $from  User ID
	 * @param  $to    Step ID
	 *
	 * @return string connection link with data attributes for from/to
	 */
	// public function get_step_progress_link( $from, $to ) {
	// 	$text = $this->get_connection_text( $to );
	// 	return $this->get_link( $from, $to, $text['connect_text'], $text['connected_text'] );
	// }

	public function get_connection_text( $queried_object ) {
		$program = Wampum()->connections->get_program_from_step_query( get_queried_object() );
		if ( ! $program ) {
			// Must be array for additional_ajax_data()
			return array();
		}
		$settings   = Wampum()->settings->get_step_progress_settings( $program->ID );
		$connect    = ! empty($settings['connect_text']) ? sanitize_text_field($settings['connect_text']) : __( 'Mark Complete', 'wampum' );
		$connected  = ! empty($settings['connected_text']) ? sanitize_text_field($settings['connected_text']) : __( 'Completed', 'wampum' );
		return array(
			'connect_text'   => $connect,
			'connected_text' => $connected,
		);
	}

	public function is_step_progress_enabled( $program_id ) {
		$settings = Wampum()->settings->get_step_progress_settings( $program_id );
		if ( $settings && array_key_exists( 'enabled', $settings ) ) {
			if ( 'yes' === $settings['enabled'] ) {
				return true;
			}
		}
		return false;
	}

}
