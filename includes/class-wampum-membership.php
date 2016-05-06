<?php
/**
 * Wampum Membership
 *
 * @package   Wampum
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
		/**
		 * @uses  /woocommerce-memberships/includes/wc-memberships-template-functions.php
		 */
		add_filter( 'wc_memberships_my_memberships_column_names', array( $this, 'wampum_account_membership_action_links' ), 10, 1 );
		add_action( 'wc_memberships_my_memberships_column_wampum-membership-actions', array( $this, 'wampum_do_membership_template_action_buttons' ) );
	}

	public function wampum_account_membership_action_links( $names ) {
		// return $default_actions;
		// echo '<pre>';
	    // print_r($names);
	    // echo '</pre>';
		unset($names['membership-actions']);
		$names['wampum-membership-actions'] = 'Actions';

		// Remove the 'View' button until we figure out what to do
		// unset($default_actions['view']);
		// return $default_actions;
		// echo '<pre>';
		// print_r($names);
	    // echo '</pre>';
	    return $names;
	}

	public function wampum_do_membership_template_action_buttons( $membership ) {
		// echo Wampum()->membership->get_membership_actions( $membership );
		$actions = $this->get_membership_actions( $membership );
		$item_actions = '';
		foreach ( $actions as $key => $value ) {
			$item_actions .= '<a class="button ' . sanitize_html_class(strtolower($value['name'])) . '" href="' . esc_url($value['url']) . '">' . esc_html($value['name']) . '</a>';
		}
		echo $item_actions;
		// Ask confirmation before cancelling a membership
		echo wc_enqueue_js("
			jQuery( document ).ready( function() {
				$( '.wampum-membership-actions' ).on( 'click', '.button.cancel', function( e ) {
					e.stopImmediatePropagation();
					return confirm( '" . esc_html__( 'Are you sure that you want to cancel your membership?', 'wampum' ) . "' );
				} );
			} );
		");
	}

	/**
	 * Get a member's purchased programs
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $user_id  (required) User ID
	 * @param  string   $return   (optional) data to return for each term
	 *                            use one of 'term_id', 'name', etc (defaults to entire term object)
	 * @return array
	 */
	public function get_programs( $user_id ) {
		$memberships = wampum_get_user_memberships( $user_id );
		// Bail if no memberships
		if ( ! $memberships ) {
			return false;
		}
		// Set programs as empty array
		$programs = array();
		// Loop through memberships
		foreach ( $memberships as $membership ) {
			// Allowed statuses
			// View all statuses in woocommerce-memberships/includes/class-wc-memberships-user-memberships.php
		    $allowed_statuses = array(
		    	'wcm-active',
		    	'wcm-complimentary',
	    	);
	    	// Skip if not allowed to view based on status
	    	if ( ! in_array( $membership->status, $allowed_statuses ) ) {
	    		continue;
	    	}
	    	// Get the membership plan's restricted content
			$content = $membership->get_plan()->get_restricted_content();
			// Bail if no content
			if ( ! $content ) {
				return false;
			}
			foreach ( $content->posts as $post ) {
				// Skip if not a program
				if ( $post->post_type != 'wampum_program' ) {
					continue;
				}
				// Skip if already in our program array
				if ( in_array( $post, $programs ) ) {
					continue;
				}
				$programs[] = $post;
			}
		}
		return $programs;
	}

	/**
	 * Get membership actions
	 *
	 * Code taken from Woo Memberships wc-memberships-template-functions.php
	 *
	 * @param $membership object
	 */
	public function get_membership_actions( $membership ) {
		$actions = array();
		// Renew: Show only for expired memberships that can be renewed
		if ( $membership->is_expired() && $membership->get_plan()->has_products() ) {
			$actions['renew'] = array(
				'url'  => $membership->get_renew_membership_url(),
				'name' => __( 'Renew', 'wampum' ),
			);
		}
		// Cancel: Do not show for cancelled, expired or pending cancellation
		if ( ( ! $membership->is_cancelled() && 'pending' !== $membership->get_status() ) && ! $membership->is_expired() && current_user_can( 'wc_memberships_cancel_membership', $membership->get_id() ) ) {
			$actions['cancel'] = array(
				'url'  => $membership->get_cancel_membership_url(),
				'name' => __( 'Cancel', 'wampum' ),
			);
		}
		// View: Do not show for cancelled, paused memberships or memberships without a Members Area
		// if ( ! $membership->is_paused() && ! $membership->is_cancelled() && ! empty ( $members_area ) && is_array( $members_area ) ) {
		// 	$actions['view'] = array(
		// 		'url' => wc_memberships_get_members_area_url( $membership->get_plan_id(), $members_area[0] ),
		// 		'name' => __( 'View', 'woocommerce-memberships' ),
		// 	);
		// }
		return $actions;
	}

	/**
	 * THIS IS NOT WORKING - TESTED IN /templates/account/memberships-custom.php
	 * Get membership subscription
	 * Check if ( class_exists('WC_Subscriptions') ) before using this, I think?
	 *
	 * @param  [type] $membership [description]
	 * @return [type]             [description]
	 */
	public function get_membership_subscription_column( WC_Memberships_User_Membership $membership ) {
		// $subscription = wc_memberships()->user_memberships->subscriptions->get_user_membership_subscription( $membership->get_id() );
		$subscription = WC_Memberships_Integration_Subscriptions()->get_user_membership_subscription( $membership->get_id() );
		echo '<pre>';
	    print_r($subscription);
	    echo '</pre>';
		if ( $subscription && in_array( $membership->get_status(), array( 'active', 'free_trial' ) ) ) {
			$next_payment = $subscription->get_time( 'next_payment' );
		}
		$output  = '';
		$output .= '<span class="item-col item-end-date">';
			if ( $subscription && ! empty( $next_payment ) ) {
				$output .= date_i18n( wc_date_format(), $next_payment );
			} else {
				$output .= esc_html( 'N/A', 'woocommerce-memberships' );
			}
		$output .= '</span>';
		return $output;
	}

	/**
	 * Check if current user can view a specific post/cpt
	 *
	 * @since  1.0.0
	 *
	 * @param  int  $post_id  ID of the post to check access to
	 *
	 * @return bool
	 */
	public static function can_view_step( $user_id, $step_id ) {
		// $program_id = Wampum()->content->get_step_program_id( $step_id );
		$program  = Wampum()->connections->get_program_from_step_query( get_queried_object() );
		if ( $program ) {
			return wc_memberships_user_can( $user_id, 'view', array( 'post' => $program->ID ) );
		}
		return false;
	}

	/**
	 * Check if current user can view a specific post/cpt
	 *
	 * @since  1.0.0
	 *
	 * @param  int  $post_id  ID of the post to check access to
	 *
	 * @return bool
	 */
	public static function can_view_post( $user_id, $post_id ) {
		// if ( ! is_user_logged_in() ) {
		// 	return false;
		// }
		return wc_memberships_user_can( $user_id, 'view', array( 'post' => $post_id ) );
	}

	public static function get_login_form( $args ) {
		$args = array(
			'echo'           => false,
			// 'remember'       => true,
			// 'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			// 'form_id'        => 'wampum-loginform',
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'value_username' => '',
			'value_remember' => false,
		);
		return sprintf( '<div class="wampum-loginform">%s</div>', wp_login_form( $args ) );
	}

}
