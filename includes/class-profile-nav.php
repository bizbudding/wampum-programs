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
class Wampum_Profile_Nav extends JiveDig_Query_String_Nav {

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
		'about'		=> 'About',
		'edit'		=> 'Edit Profile',
		'purchases'	=> 'Purchases',
	);

	protected $classes = 'menu genesis-nav-menu';

	public function __construct() {

		// parent::__construct();

		$this->directory = get_stylesheet_directory() . '/template-parts';
	}

}