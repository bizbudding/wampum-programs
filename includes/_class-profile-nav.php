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
class Wampum_Profile_Nav extends JiveDig_Content_Swap {

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
	protected $items;

	/**
	 * Set default tab
	 *
	 * @var string
	 */
	protected $default = 'about';

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
	}

	protected function get_about_content() {
		$content  = '<div>';
		$content .= '<h2>This is the About tab</h2>';
		$content .= '<p>I\'d like to see some about tab here. How about you?</p>';
		$content .= '</div>';
		return $content;
	}

	protected function get_caldera_content() {
		ob_start();
		echo do_shortcode('[caldera_form id="CF56c1eaac42cd6"]');
		return ob_get_clean();
	}

	protected function get_woo_content() {
		ob_start();
		echo do_shortcode('[woocommerce_my_account]');
		return ob_get_clean();
	}

	protected function get_edit_content() {
		$content  = '<div>';
		$content .= '<h2>This is the Edit tab</h2>';
		$content .= '<p>Edit me, or edit you? Whatever you want.</p>';
		$content .= '</div>';
		return $content;
	}

	protected function get_edit_profile_content() {
		// return 'Edit Profile Content';
		// return piklist_form::render_form('edit-profile','wampum', true);
		ob_start();
		echo piklist_form::render_form('edit-profile','wampum');
		return ob_get_clean();
	}

	protected function get_purchases_content() {
		$content  = '<div>';
		$content .= '<h2>This is the Purchases tab</h2>';
		$content .= '<p>This is going to be really perfect right now.</p>';
		$content .= '</div>';
		return $content;
	}

}
