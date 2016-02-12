<?php
/**
 * Profile Nav Menu
 *
 * @package   Wampum_Profile_Nav
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Template loader for Meal Planner.
 *
 * Only need to specify class properties here.
 *
 * @package Wampum_Profile_Nav
 * @author  Mike Hemberger
 */
class Wampum_Profile_Nav extends JiveDig_Restful_Content_Swap {

	/**
	 * Menu name
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $slug = 'profile';

	/**
	 * Associative array of menu item items ['slug'] => 'Name'
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	protected $items = array(
		'about' => array(
			'name'		 => 'About',
			// 'loggedin'	 => false,
			// 'capability' => null,
		),
		'edit'  => array(
			'name'		 => 'Edit Posts',
			'loggedin'	 => true,
			// 'capability' => null,
		),
		'purchases'  => array(
			'name'		 => 'My Purchases',
			// 'loggedin'	 => false,
			// 'capability' => null,
		),
	);

	// protected $classes = 'menu genesis-nav-menu';
	protected $classes = 'menu';

	public function __construct() {

		// parent::__construct();

		$this->directory = get_stylesheet_directory() . '/template-parts';
	}

}