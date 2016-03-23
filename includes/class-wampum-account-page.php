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
final class Wampum_Account_Page extends JiveDig_Content_Swap {

	/** Singleton *************************************************************/

	/**
	 * @var Wampum_Account_Page The one true Wampum_Account_Page
	 * @since 1.0.0
	 */
	private static $instance;

	protected $prefix = 'wampum_account';

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
						'programs'		=> 'My Programs',
						'orders'		=> 'My Orders',
						'memberships'	=> 'My Memberships',
						// 'subscriptions'	=> 'My Subscriptions',
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

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Account_Page;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	public function init() {
		$this->items = apply_filters( 'wampum_account_page_items', $this->items );
		// $this->script_dir = WAMPUM_PLUGIN_URL . 'js/';
		// add_action( 'after_setup_theme', array( $this, 'enable_restful_content' ) );
		// add_action( 'get_header', array( $this, 'enqueue_scripts' ) );
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

    	if ( 'edit_profile' === $slug ) {
    		if ( ! is_user_logged_in() ) {
    			return false;
    		}
    		return true;
    	}
    	return true;
    }

    public function get_menu_item_url($slug) {
    	$page = get_option('wampum_settings')['account_page'];
    	return esc_url_raw( add_query_arg( $this->name, $slug, get_permalink($page) ) );
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

}

/**
 * The following functions are outside of class since we overrode get_content_method_name()
 * This allows filtering of menu items and content outside of this class (custom plugins per site)
 */
function wampum_account_get_orders_content() {
	ob_start();
	wampum_get_template_part('account/orders');
	return ob_get_clean();
}

function wampum_account_get_programs_content() {
	ob_start();
	wampum_get_template_part('account/programs');
	return ob_get_clean();
}

function wampum_account_get_memberships_content() {
	ob_start();
	wampum_get_template_part('account/memberships');
	return ob_get_clean();
}

function wampum_account_get_edit_profile_content() {
	$output  = '';
	$output .= '<h2>Edit Profile</h2>';
	$output .= wc_get_template_html( 'myaccount/form-edit-account.php', array(
		'user' => get_user_by( 'id', get_current_user_id() ),
	) );

	// $output .= '<h2>Edit Address</h2>';
	// $output .= do_shortcode('[woocommerce_edit_address]');
	// return piklist_form::render_form('edit-profile','wampum', true);
	return $output;
}

add_action( 'get_header', function() {
	// Get account page ID
	$page = get_option('wampum_settings')['account_page'];
	if ( ! is_page($page) ) {
		return;
	}
	add_filter( 'body_class', 'wampum_do_account_page_body_class' );
	// add_filter( 'the_content', 'wampum_do_account_page_content' );
	add_action( 'wampum_after_content', 'wampum_do_account_page_content' );
});
// Add custom body class to the head
function wampum_do_account_page_body_class($classes) {
	$classes[] = 'woocommerce';
	return $classes;
}
// Add Account page menu/content
function wampum_do_account_page_content() {
	if ( ! is_user_logged_in() ) {
		// global $wampum_membership;
		echo '<p>You must be logged in to view your account.</p>';
		echo Wampum()->membership->get_login_form();
		return;
	}
	wp_enqueue_style('wampum');
	echo Wampum()->account->menu(false);
	echo Wampum()->account->content(false);
}
