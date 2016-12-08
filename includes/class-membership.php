<?php
/**
 * Wampum Membership
 *
 * @package   Wampum - Programs
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 *
 * @package Wampum
 */
final class Wampum_Membership {

	/**
	 * @var   Wampum_Membership The one true Wampum_Membership
	 * @since 1.4
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Membership;
			self::$instance->init();
		}
		return self::$instance;
	}

	public function init() {
		// Filters
		add_filter( 'auth_cookie_expiration', array( $this, 'stay_logged_in' ) );
		// Hooks
		add_action( 'plugins_loaded', 	      array( $this, 'woo_subscriptions_remove_deprecation_handlers' ), 0 );
		add_action( 'template_redirect', 	  array( $this, 'access_redirect' ) );
	}

	/**
	 * Stay logged in for n number of weeks
	 *
	 * @since  1.3.0
	 *
	 * @param  int  $expirein  number of seconds to stay logged in for
	 *
	 * @return int  time in seconds to stay logged in
	 */
	public function stay_logged_in( $expirein ) {
		$seconds = 31556926; // 1 year in seconds
		$seconds = apply_filters( 'wampum_stay_logged_in', $seconds );
		return $seconds;
	}

	/**
	 * Do not load backward compatibility support in Subscriptions 2.0
	 *
	 * @since  1.0.0
	 *
	 * @link   https://support.woothemes.com/hc/en-us/articles/205214466
	 *
	 * @return void
	 */
	public function woo_subscriptions_remove_deprecation_handlers() {
		add_filter( 'woocommerce_subscriptions_load_deprecation_handlers', '__return_false' );
	}

	/**
	 * Redirect steps/programs if they are restricted and user doesn't have access
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function access_redirect() {

	    if ( ! is_singular('wampum_program') ) {
	    	return;
	    }

	    // Bail if super user
	    if ( is_user_logged_in() && current_user_can('wc_memberships_access_all_restricted_content') ) {
	    	return;
	    }

    	$post_id = wampum_get_top_parent_id();

	    // Bail if the program is not restricted at all
	    if ( ! wc_memberships_is_post_content_restricted( $post_id ) ) {
	    	return;
	    }

	    // Bail if user already has access
    	if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
    		return;
    	}

	   	$redirect_page_id = get_option( 'wc_memberships_redirect_page_id' );
		$redirect_url     = add_query_arg(
			array( 'r' => $post_id ),
			$redirect_page_id ? get_permalink( $redirect_page_id ) : home_url()
		);
		wp_redirect( $redirect_url );
		exit;

	    // This adds the restricted message to the content, while stripping out the default Woo markup around the notice
	    // add_filter( 'the_content', array( $this, 'get_restricted_message' ) );

	    // Add our custom wampum restricted message, with markup
	    // add_filter( 'wc_memberships_content_restricted_message', array( $this, 'noaccess_restricted_message' ), 10, 3 );

	    // Add styles
	    // $this->noaccess_styles();

	}

}
