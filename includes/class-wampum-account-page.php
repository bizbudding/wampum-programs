<?php
/**
 * Wampum Account Page
 *
 * @package   Wampum_Account_Page
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
 * @package Wampum_Account_Page
 * @author  Mike Hemberger
 */
class Wampum_Account_Page extends JiveDig_Restful_Content_Swap {

	/**
	 * Menu name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'account';

	/**
	 * Associative array of menu item items ['slug'] => 'Name'
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $items = array(
		'programs' => 'My Programs',
		'caldera'  => 'Caldera',
		'woo'	   => 'Woo',
	);

	/**
	 * Set default tab
	 *
	 * @var string
	 */
	protected $default = 'programs';

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
		// $this->script_dir   = get_stylesheet_directory_uri() . '/assets/js/';
	}

	protected function get_programs_content() {
		// global $wampum_account_page, $restricted_content, $customer_membership;

		ob_start();
		$plans         = wc_memberships_get_membership_plans();
		$active_member = array();

		foreach ( $plans as $plan ) {
			$active = wc_memberships_is_user_active_member( get_current_user_id(), $plan );
			echo '<pre>';
		    print_r($active);
		    echo '</pre>';
			array_push( $active_member, $active );
		}

		if ( ! in_array( true, $active_member ) ) {
			return 'You have not purchased any programs';
		}

		$programs = get_terms('wampum_program', array(
		    'hide_empty' => 0,
		));

		$output = '';

		foreach ( $programs as $program ) {
			// echo '<pre>';
		 //    print_r($program);
		 //    echo '</pre>';
			$output .= '<h2>' . $program->name . '</h2>';
		}

		return $output;

		// $customer_membership = wc_memberships_get_user_membership(get_current_user_id());
		$customer_memberships = wc_memberships_get_user_memberships(get_current_user_id());


		foreach ( $customer_memberships as $membership ) {
			$plan = wc_memberships_get_membership_plan($membership->plan_id);
			$section = wc_memberships_get_members_area_sections($membership->plan_id);
			// echo '<pre>';
		 //    var_dump($section);
		 //    echo '</pre>';
		}
		// echo '<pre>';
	 //    print_r($customer_memberships);
	 //    echo '</pre>';
		// echo '<pre>';
	 //    var_dump($restricted_content);
	 //    echo '</pre>';
		// $user_id = get_current_user_id();
		wampum_get_template_part('account/programs');
		return ob_get_clean();
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
