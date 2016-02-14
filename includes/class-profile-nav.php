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
		'about'		=> 'About',
		'edit'		=> 'Edit Posts',
		'purchases'	=> 'My Purchases',
	);

	protected $classes = 'menu genesis-nav-menu';
	// protected $classes = 'menu';

	protected $loading = '<i class="fa fa-spinner fa-pulse"></i>';

	public function __construct() {
		// parent::__construct();
		$this->script_dir   = get_stylesheet_directory_uri() . '/assets/js/';
		$this->template_dir = get_stylesheet_directory() . '/template-parts/';
	}

	protected function get_ajax_content( $items ) {
		$content = array();
		foreach( $items as $slug => $values ) {
			if ( $this->can_view( $values ) ) {
				$content = $this->get_all_content();
				// if ( 'about' === $slug ) {
				// 	$content[$slug] = $this->get_about_content();
				// } elseif ( 'edit' === $slug ) {
				// 	$content[$slug] = $this->get_edit_content();
				// } elseif ( 'purchases' === $slug ) {
				// 	$content[$slug] = $this->get_purchases_content();
				// }
			}
		}
		return $content;
	}

	protected function get_all_content() {
		return array(
			'about'		=> $this->get_about_content(),
			'edit'		=> $this->get_edit_content(),
			'purchases'	=> $this->get_purchases_content(),
		);
	}

	protected function get_about_content() {
		$content = '<div>';
		$content .= '<h2>This is the About tab</h2>';
		$content .= '</div>';
		return $content;
	}

	protected function get_edit_content() {
		$content = '<div>';
		$content .= '<h2>This is the Edit tab</h2>';
		$content .= '</div>';
		return $content;
	}

	protected function get_purchases_content() {
		$content = '<div>';
		$content .= '<h2>This is the Purchases tab</h2>';
		$content .= '</div>';
		return $content;
	}

}
