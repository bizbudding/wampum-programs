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
 * Account nav and content for Wampum
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
	 * @var string
	 */
	protected $name = 'profile';

	/**
	 * Associative array of menu item items ['slug'] => 'Name'
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $items = array(
		'about'			=> 'About',
		'edit'			=> 'Edit Posts',
		'edit_profile'	=> 'Edit Profile',
		'purchases'		=> 'My Purchases',
	);

	/**
	 * Set default tab
	 *
	 * @var string
	 */
	protected $default = 'edit';

	/**
	 * Menu class(es)
	 *
	 * @var string
	 */
	protected $classes = 'menu';

	/**
	 * Loading display
	 *
	 * @var mixed
	 */
	protected $loading = '<i class="fa fa-spinner fa-pulse"></i>';

	public function __construct() {
		// parent::__construct();
		$this->script_dir   = get_stylesheet_directory_uri() . '/assets/js/';
		$this->template_dir = get_stylesheet_directory() . '/template-parts/';

	}

	protected function get_about_content() {
		$content  = '<div>';
		$content .= '<h2>This is the About tab</h2>';
		$content .= '<p>I\'d like to see some about tab here. How about you?</p>';
		$content .= '</div>';
		return $content;
	}

	protected function get_edit_content() {
		$content  = '<div>';
		$content .= '<h2>This is the Edit tab</h2>';
		$content .= '<p>Edit me, or edit you? Whatever you want.</p>';
		$content .= '</div>';
		return $content;
	}

	protected function get_edit_profile_content() {
		// ob_start();
		// echo do_shortcode('[piklist_form form="edit-profile" add_on="wampum"]');
		// echo piklist_form::get('edit-profile');

		// return piklist_form::render_form('edit-profile');

		// return ob_get_contents();
		// ob_clean();
		// return piklist::render('forms/edit-profile');
	}

	protected function get_purchases_content() {
		$content  = '<div>';
		$content .= '<h2>This is the Purchases tab</h2>';
		$content .= '<p>This is going to be really perfect right now.</p>';
		$content .= '</div>';
		return $content;
	}

}
