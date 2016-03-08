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
 * Account nav and content for Wampum
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

	protected $script_url = '/assets/name.js';

	protected $messages = array(
			'connect_success'		=> 'Success!',
			'disconnect_success'	=> 'Success!',
			'can_connect_fail'		=> 'An error occurred',
			'can_disconnect_fail'	=> 'An error occurred',
		);

	public function __construct() {
		$this->script_url = get_stylesheet_directory_uri() . '/assets/js/p2presttest.js';
		add_action( 'genesis_entry_content', array($this, 'test_function') );
	}

	public function test_function() {
		echo 'IS IT WORKING?!?!?!';
	}

}
