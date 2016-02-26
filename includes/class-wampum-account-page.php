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
class Wampum_Account_Page extends JiveDig_Content_Swap {

	protected $prefix = 'wampum_account';

	/**
	 * Menu name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'account';

	// protected $items_array = apply_filters( 'wampum_account_page_items', array(
	// 							'programs' => 'My Programs',
	// 							'caldera'  => 'Caldera',
	// 							'woo'	   => 'Woo',
	// 						));

	/**
	 * Associative array of menu item items ['slug'] => 'Name'
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $items = array(
						'programs'		=> 'My Programs',
						'caldera'		=> 'Caldera',
						'woo'			=> 'Woo',
						'edit_profile'	=> 'Edit Profile',
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
		$this->items = apply_filters( 'wampum_account_page_items', $this->items );
		$this->script_dir = WAMPUM_PLUGIN_URI . 'js/';
		add_action( 'after_setup_theme', array( $this, 'enable_restful_content' ) );
		add_action( 'get_header', array( $this, 'enqueue_scripts' ) );

		// $this->items = add_filter( 'wampum_account_page_items', array( $this, 'change_items' ) );
		// $this->items = apply_filters( 'wampum_account_page_items', array( $this, 'get_filtered_menu_items' ) );
		// $this->script_dir = get_stylesheet_directory_uri() . 'assets/js/';
		// $this->items = $this->get_menu_items();

		// $items = array();
		// $items_array = get_option('wampum_settings')['account_page_items'];
		// foreach ( $items_array as $key => $value ) {
		// 	if ( 'programs' === $value ) {
		// 		$items['programs'] = 'My Programs';
		// 	}
		// 	if ( 'caldera' === $value ) {
		// 		$items['caldera'] = 'Caldera';
		// 	}
		// 	if ( 'woo' === $value ) {
		// 		$items['woo'] = 'Woo';
		// 	}
		// 	if ( 'subscriptions' === $value ) {
		// 		$items['subscriptions'] = 'Subscriptions';
		// 	}
		// }

	}

	public function enable_restful_content() {
		$this->restful();
	}

	public function enqueue_scripts() {
		$page = get_option('wampum_settings')['account_page'];
		if ( ! is_page($page) ) {
			return;
		}
		$this->scripts();
	}

    protected function can_view( $slug ) {
    	if ( 'caldera' === $slug ) {
    		if ( ! is_user_logged_in() ) {
    			return false;
    		}
    		return true;
    	}
    	return true;
    }

	/**
	 * ******************************************************* *
	 *** OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS ***
	 * ******************************************************* *
	 *
	 * Example to allow filtering of menu items to add additional content
	 *
	 * 1. Define $prefix parameter in child class
	 * 2. Override this method
	 * 3. return "{$this->prefix}_get_{$slug}_content";
	 * 4. In custom plugin (or theme) return the content via that function
	 *
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	protected function get_content_method_name($slug) {
		return "{$this->prefix}_get_{$slug}_content";
	}

	// protected function get_programs_content() {
	// 	ob_start();
	// 	wampum_get_template_part('account/programs');
	// 	return ob_get_clean();
	// }

	// protected function get_caldera_content() {
	// 	ob_start();
	// 	echo do_shortcode('[caldera_form id="CF56c1eaac42cd6"]');
	// 	return ob_get_clean();
	// }

	// protected function get_woo_content() {
	// 	ob_start();
	// 	echo do_shortcode('[woocommerce_my_account]');
	// 	return ob_get_clean();
	// }

	// protected function get_edit_content() {
	// 	$content  = '<div>';
	// 	$content .= '<h2>This is the Edit tab</h2>';
	// 	$content .= '<p>Edit me, or edit you? Whatever you want.</p>';
	// 	$content .= '</div>';
	// 	return $content;
	// }

	// protected function get_edit_profile_content() {
	// 	return piklist_form::render_form('edit-profile','wampum', true);
	// }

	// protected function get_purchases_content() {
	// 	$content  = '<div>';
	// 	$content .= '<h2>This is the Purchases tab</h2>';
	// 	$content .= '<p>This is going to be really perfect right now.</p>';
	// 	$content .= '</div>';
	// 	return $content;
	// }

}

// global $wampum_account_page;
// $wampum_account_page->restful();
//
// $wampum_account_page = new Wampum_Account_Page();

/**
 * The following functions are outside of class since we overrode get_content_method_name()
 * This allows filtering of menu items and content outside of this class (custom plugins per site)
 */
function wampum_account_get_programs_content() {
	ob_start();
	wampum_get_template_part('account/programs');
	return ob_get_clean();
}

function wampum_account_get_caldera_content() {
	ob_start();
	echo do_shortcode('[caldera_form id="CF56c1eaac42cd6"]');
	return ob_get_clean();
}

function wampum_account_get_woo_content() {
	ob_start();
	echo do_shortcode('[woocommerce_my_account]');
	return ob_get_clean();
}

function wampum_account_get_edit_content() {
	$content  = '<div>';
	$content .= '<h2>This is the Edit tab</h2>';
	$content .= '<p>Edit me, or edit you? Whatever you want.</p>';
	$content .= '</div>';
	return $content;
}

function wampum_account_get_edit_profile_content() {
	// return 'Edit Profile Content';
	// return piklist_form::render_form('edit-profile','wampum', true);
	// ob_start();
	// echo piklist_form::render_form('edit-profile','wampum');
	return piklist_form::render_form('edit-profile','wampum', true);
	// return piklist_form::render_form('post','localthrive-core', true);
	// return ob_get_clean();
}

function wampum_account_get_purchases_content() {
	$content  = '<div>';
	$content .= '<h2>This is the Purchases tab</h2>';
	$content .= '<p>This is going to be really perfect right now.</p>';
	$content .= '</div>';
	return $content;
}

// add_action( 'after_setup_theme', 'wampum_account_page_register_rest_endpoint' );
// function wampum_account_page_register_rest_endpoint() {
// 	global $wampum_account_page;
// 	$wampum_account_page->restful();
// }

//
// add_action( 'get_header', 'wampum_do_account_page_scripts' );
// function wampum_do_account_page_scripts() {
// 	// Get account page ID
// 	$page = get_option('wampum_settings')['account_page'];
// 	if ( ! is_page($page) ) {
// 		return;
// 	}
// 	global $wampum_account_page;
// 	$wampum_account_page->scripts();
// 	// Wampum_Account_Page::scripts();
// 	// wp_enqueue_script( 'jivedigcontentswap' );
// }

// Add Account page menu/content
add_filter( 'the_content', 'wampum_account_page_content' );
function wampum_account_page_content($content) {
	// Get account page ID
	$page = get_option('wampum_settings')['account_page'];
	if ( is_page($page) ) {
		// global $wampum_account_page, $restricted_content, $customer_membership;
		global $wampum_account_page;
		$content .= $wampum_account_page->menu(false);
		$content .= $wampum_account_page->content(false);
	}
	return $content;
}
