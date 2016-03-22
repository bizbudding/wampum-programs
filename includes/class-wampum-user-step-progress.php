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
class Wampum_User_Step_Progress extends P2P_Restful_Connection {

	/**
	 * Name of the p2p connection.
	 *
	 * @since 1.0.0
	 *
	 * @var   string
	 */
	protected $connection_name = 'user_step_progress';

	// protected $from;

	// protected $to;

	protected $link_connect_text = 'Connect!';

	protected $link_connected_text = 'Connected!';

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
	public function __construct( $data = array() ) {
		parent::__construct( $data );
		$this->script_url = WAMPUM_PLUGIN_URI . '/js/restful_p2p.js';

		// $text = $this->$this->get_connection_text( $this->to );

		// $this->link_connect_text   = $this->$this->get_connection_text( $this->to )['connect_text'];
		// $this->link_connected_text = $this->$this->get_connection_text( $this->to )['connected_text'];

		// $this->from = get_current_user_id();
		// $this->to   = get_the_ID();
	}

    /**
     * Get additional ajax data
     *
     * @return array
     */
	public function additional_ajax_data() {
		// return array(
		// 	'connect_text'   => $this->link_connect_text,
		// 	'connected_text' => $this->link_connected_text,
		// );
		// if ( ! $this->is_step_progress_page() ) {
			// return array();
		// }
		// step_id
		return $this->get_connection_text( $this->to );
		// return self::get_step_progress_connected_text( get_the_ID() );
	}

	public function get_connection_text( $step_id ) {
		global $wampum_content_types;
		$program_id = $wampum_content_types->get_step_program_id( $step_id );
		// $enabled = $this->is_step_progress_enabled( $program_id );
		// piklist::pre($enabled);
		// if ( ! $enabled  ) {
		// 	return array();
		// }
		$settings   = $this->get_step_progress_settings( $program_id );
		$connect    = ! empty($settings['connect_text']) ? sanitize_text_field($settings['connect_text']) : __( 'Mark Complete', 'wampum' );
		$connected  = ! empty($settings['connected_text']) ? sanitize_text_field($settings['connected_text']) : __( 'Completed', 'wampum' );
		return array(
			'connect_text'   => $connect,
			'connected_text' => $connected,
		);
	}

	public function is_step_progress_enabled( $program_id ) {
		$enabled = $this->get_step_progress_settings( $program_id );
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

	// public function is_step_progress_page() {
	// 	if ( is_singular('wampum_step') ) {
	// 		return true;
	// 	}
	// 	return false;
	// }

}
