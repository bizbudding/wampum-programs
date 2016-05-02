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
