<?php
/**
 * Wampum
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
 * Restfully connect a user to a step
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
	 * @var   string
	 */
	protected $connection_name = 'users_to_steps';

	/**
	 * Our success/fail messages for this connection
	 *
	 * @since 1.0.0
	 *
	 * @var   array
	 */
	protected $messages = array(
			'connect_success'		=> 'Completed!',
			'disconnect_success'	=> 'Mark Complete',
			'can_connect_fail'		=> 'An error occurred',
			'can_disconnect_fail'	=> 'An error occurred',
		);

	/**
	 * Run the parent constructor and locate our javascript file url
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->script_url = WAMPUM_PLUGIN_URI . '/js/restful_p2p.js';
	}

}
