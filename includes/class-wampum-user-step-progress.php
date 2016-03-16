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
class Wampum_P2P_User_Step_Progress extends P2P_Restful_Connection {

	/**
	 * Name of the p2p connection.
	 *
	 * @since 1.0.0
	 *
	 * @var   string
	 */
	protected $connection_name = 'user_step_progress';

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

	/**
	 * Run the parent constructor and locate our javascript file url
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->script_url = WAMPUM_PLUGIN_URI . '/js/restful_p2p.js';
		add_action( 'get_header', array( $this, 'step_progress_button' ) );
	}

	public function additional_ajax_data() {
		return $this->get_step_progress_connected_text( get_the_ID() );
	}

	public function step_progress_button() {
		// Bail if not viewing a step
		if ( ! is_singular('wampum_step') ) {
			return;
		}
		add_filter( 'the_content', array( $this, 'do_step_progress_button' ) );
	}

	public function do_step_progress_button( $content ) {
		// Bail is user is not logged in
		if ( ! is_user_logged_in() ) {
			return $content;
		}
		return $content . $this->get_step_progress_button( get_the_ID() );
	}

	public function get_step_progress_button( $step_id ) {
		global $wampum_content_types;
		$program_id = $wampum_content_types->get_step_program_id($step_id);
		if ( ! $this->is_step_progress_enabled( $program_id ) ) {
			return false;
		}
		$enabled = get_post_meta( $program_id, 'wampum_program_step_progress' );
		$text    = $this->get_step_progress_connected_text( $step_id );
		// Return the connection button
		global $wampum_connections;
		return $wampum_connections->connection_button( $this->connection_name, get_current_user_ID(), $step_id, $text['connect_text'], $text['connected_text'] );
	}

	public function get_step_progress_connected_text( $step_id ) {
		global $wampum_content_types;
		$program_id = $wampum_content_types->get_step_program_id( $step_id );
		$enabled = $this->is_step_progress_enabled( $program_id );
		// piklist::pre($enabled);
		if ( ! $enabled  ) {
			return array();
		}
		$settings   = $this->get_step_progress_settings( $program_id );
		$connect    = ! empty($settings['connect_text']) ? sanitize_text_field($settings['connect_text']) : __( 'Mark Complete', 'wampum' );
		$connected  = ! empty($settings['connected_text']) ? sanitize_text_field($settings['connected_text']) : __( 'Completed', 'wampum' );
		return array(
			'connect_text'   => $connect,
			'connected_text' => $connected,
		);
	}

	public function is_step_progress_enabled( $program_id ) {
		$enabled = get_post_meta( $program_id, 'wampum_program_step_progress', true );
		// piklist::pre($enabled);
		if ( $enabled && isset($enabled['enabled']) ) {
			if ( 'yes' === $enabled['enabled'] ) {
				return true;
			}
		}
		return false;
	}

	public function get_step_progress_settings( $program_id ) {
		return get_post_meta( $program_id, 'wampum_program_step_progress', true );
	}

}
