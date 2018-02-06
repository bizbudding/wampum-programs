<?php
/**
 * Wampum - Programs
 *
 * @package   Wampum_Membership
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
		add_action( 'plugins_loaded',         array( $this, 'woo_subscriptions_remove_deprecation_handlers' ), 0 );
		add_action( 'template_redirect',      array( $this, 'access_redirect' ) );
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
		if ( is_user_logged_in() && current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {
			return;
		}

		$parent_program_id = wampum_get_top_parent_id();

		// Bail if viewing parent program, let Woo Memb do its thing.
		if ( get_the_ID() == $parent_program_id ) {
			return;
		}

		// Bail if the program is not restricted at all.
		if ( ! wc_memberships_is_post_content_restricted( $parent_program_id ) ) {
			return;
		}

		// Bail if user already has access.
		if ( current_user_can( 'wc_memberships_view_restricted_post_content', $parent_program_id ) ) {
			return;
		} elseif ( current_user_can( 'wc_memberships_view_delayed_post_content', $parent_program_id ) ) {
			return;
		}

		$restriction_mode = get_option( 'wc_memberships_restriction_mode' );

		// If resctriction mode is set to "Redirect to page"
		if ( 'redirect' === $restriction_mode ) {

			// Redirect to Content Restricted page
			$redirect_page_id = get_option( 'wc_memberships_redirect_page_id' );
			$redirect_url     = add_query_arg(
				array( 'r' => $parent_program_id ),
				$redirect_page_id ? get_permalink( $redirect_page_id ) : home_url()
			);
			wp_redirect( $redirect_url );
			exit;

		}
		// If resctriction mode is set to "Hide completely"
		elseif ( 'hide' === $restriction_mode ) {

			// Redirect home
			wp_redirect( home_url() );
			exit;

		}
		// If resctriction mode is set to "Hide content".
		elseif ( 'hide_content' ) {

			// Remove the post content.
			remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

			$content = '';
			if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $parent_program_id ) ) {
				$content = WC_Memberships_User_Messages::get_message_html( 'content_restricted', array( 'post_id' => $parent_program_id ) );
			} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $parent_program_id ) ) {
				$content = WC_Memberships_User_Messages::get_message_html( 'content_delayed', array( 'post_id' => $parent_program_id ) );
			}

			// Output the content.
			add_action( 'genesis_entry_content', function() use ( $content ) {
				$html = '';
				if ( wc_memberships()->get_restrictions_instance()->showing_excerpts() ) {
					$html .= get_the_excerpt( get_the_ID() );
				}
				$html .= $content;
				if ( true === (bool) apply_filters( 'wc_memberships_message_process_shortcodes', true, 'hide_content', array() ) ) {
					$html = do_shortcode( $html );
				}
				echo $html;
			});

		}

	}

}
