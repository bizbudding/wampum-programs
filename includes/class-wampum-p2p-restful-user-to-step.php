<?php
/**
 * Wampum Account Page
 *
 * @package   Wampum_P2P_User_To_Step
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * TODO: Put something here
 *
 * @package Wampum_P2P_User_To_Step
 * @author  Mike Hemberger
 */
class Wampum_P2P_User_To_Step extends P2P_Restful_Connection {

	/**
	 * Name of the p2p connection.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $connection_name = 'users_to_steps';

	// protected $script_url = '/assets/js/restful_p2p.js';

	protected $messages = array(
			'connect_success'		=> 'Success!',
			'disconnect_success'	=> 'Success!',
			'can_connect_fail'		=> 'An error occurred',
			'can_disconnect_fail'	=> 'An error occurred',
		);

	public function __construct() {
		parent::__construct();
		$this->script_url = WAMPUM_PLUGIN_URI . '/js/restful_p2p.js';
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_callback_script' ) );
	}

	public function enqueue_callback_script() {
        wp_enqueue_script(  'some-name-here', WAMPUM_PLUGIN_URI . '/js/restful_p2p_user_to_step.js', array(), '1.0.0', true );
	}

}
