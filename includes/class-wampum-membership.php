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
		}
		return self::$instance;
	}

	/**
	 * TODO: REWRITE IF USING PROGRAMS AS A CPT!!!!!!!!!
	 *
	 *
	 * Get a members purchased programs
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $user_id  (required) User ID
	 * @param  string   $return   (optional) data to return for each term
	 *                            use one of 'term_id', 'name', etc (defaults to entire term object)
	 * @return array
	 */
	public function get_programs( $user_id, $data = 'object' ) {
	// public function get_programs( $user_id ) {

		$programs = get_terms('wampum_program');

		if ( ! $programs ) {
			return;
		}

	    foreach ( $programs as $program ) {
	    	// Get program rules
	        $rules = wc_memberships()->rules->get_taxonomy_term_content_restriction_rules( 'wampum_program', $program->term_id );
	        // If no rules, skip this term and move on to the next
	        if ( ! $rules ) {
	            // continue;
	        }
	        // For each rule add the available object id's to the object_ids array
	        foreach ( $rules as $rule ) {
	            // get the membership plan object
	            $membership_plan = wc_memberships_get_membership_plan( $rule->get_membership_plan_id() );
	            // check whether the current user has access to the membership plan
	            $access = wc_memberships_is_user_active_member( $user_id, $membership_plan );
	            // if the user has access assign the ID to the object ID array
	            if ( $access ) {
	                $member_programs[] = $program;
	                // At least one rule gives access, move on to the next
	                // continue;
	            }
	        }

	    }
	    return $member_programs;
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
		$program_id = Wampum()->content_types->get_step_program_id( $step_id );
		if ( $program_id ) {
			return wc_memberships_user_can( $user_id, 'view', array( 'post' => $program_id ) );
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
	public static function can_view( $user_id, $post_id ) {
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
