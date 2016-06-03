<?php
/**
 * Wampum Helpers
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com.com>
 * @link      https://github.com/JiveDig/wampum/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

/**
 * Helper function to get the excerpt with max character length
 * Example: the_excerpt_max_charlength(140);
 *
 * @param  $charlength the amount of characters to include
 * @return string
 */
function wampum_get_truncated_content( $content, $charlength ) {

	$charlength++;

	if ( mb_strlen( $content ) > $charlength ) {
		$subex	 = mb_substr( $content, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut	 = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			return mb_substr( $subex, 0, $excut ) . '[&hellip;]';
		} else {
			return $subex . '[&hellip;]';
		}
		return '[&hellip;]';
	} else {
		return $content;
	}
}

/**
 * Check if current user can view a specific 'wampum_step'
 *
 * @since  1.0.0
 *
 * @param  int  $post_id  ID of the post to check access to
 *
 * @return bool
 */
function wampum_user_can_view_step( $user_id, $step_id ) {
	return Wampum()->membership->can_view_step( $user_id, $step_id );
}

/**
 * Check if current user can view a specific post
 * I think it only works with posts, maybe pages, but not CPT's
 *
 * @since  1.0.0
 *
 * @param  int  $post_id  ID of the post to check access to
 *
 * @return bool
 */
function wampum_user_can_view_post( $user_id, $post_id ) {
	return Wampum()->membership->can_view_post( $user_id, $post_id );
}

/**
 * Check if user is an active member of a particular membership plan
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int         $user_id  Optional. Defaults to current user.
 * @param  int|string  $plan     Membership Plan slug, post object or related post ID
 *
 * @return bool True, if is an active member, false otherwise
 */
function wampum_is_user_active_member( $user_id = null, $plan ) {
	return wc_memberships_is_user_active_member( $user_id, $plan );
}

/**
 * Check if user is a member of a particular membership plan
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int         $user_id  Optional. Defaults to current user.
 * @param  int|string  $plan     Membership Plan slug, post object or related post ID
 *
 * @return bool True, if is a member, false otherwise
 */
function wampum_is_user_member( $user_id = null, $plan ) {
	return wc_memberships_is_user_member( $user_id, $plan );
}

/**
 * Get all content restriction rules for a plan
 * @uses   /woocommerce-memberships/includes/class-wc-memberships-membership-plan.php
 *
 * @since  1.0.0
 *
 * @param  object $plan membership plan object
 *
 * @return array  Array of content restriction rules
 */
function wampum_get_plan_content_restriction_rules( $plan ) {
	return $plan->get_plan()->get_content_restriction_rules();
}

/**
 * Main function for returning a user membership
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * Supports getting user membership by membership ID, Post object
 * or a combination of the user ID and membership plan id/slug/Post object.
 *
 * If no $id is provided, defaults to getting the membership for the current user.
 *
 * @since  1.0.0
 *
 * @param  mixed $id   Optional. Post object or post ID of the user membership, or user ID
 * @param  mixed $plan Optional. Membership Plan slug, post object or related post ID
 *
 * @return WC_Memberships_User_Membership
 */
function wampum_get_user_membership( $id = null, $plan = null ) {
	return wc_memberships_get_user_membership( $id, $plan );
}

/**
 * Get all memberships for a user
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int   $user_id  Optional. Defaults to current user.
 * @param  array $args
 *
 * @return WC_Memberships_User_Membership[]|null array of user memberships
 */
function wampum_get_user_memberships( $user_id = null, $args = array() ) {
	return wc_memberships_get_user_memberships( $user_id, $args );
}

/**
 * Helper function to get template part
 *
 * wampum_get_template_part( 'account', 'page' );
 *
 * This will try to load in the following order:
 * 1: wp-content/themes/theme-name/wampum/account-page.php
 * 2: wp-content/themes/theme-name/wampum/account.php
 * 3: wp-content/plugins/wampum/templates/account-page.php
 * 4: wp-content/plugins/wampum/templates/account.php.
 *
 * @since  1.0.0
 *
 * @param  string  		 $slug
 * @param  string  		 $name
 * @param  boolean 		 $load
 * @param  string|array  $data  optional array of data to pass into template
 *
 * $data param MUST be called $data, not any other variable name
 * $data MUST be an array
 *
 * @return mixed
 */
function wampum_get_template_part( $slug, $name = null, $load = true, $data = '' ) {
    if ( is_array($data) ) {
	    Wampum()->templates->set_template_data( $data );
	}
    Wampum()->templates->get_template_part( $slug, $name, $load );
}

function wampum_get_program_steps_list( $program_object_or_id ) {
	// global $wampum_content_types;
	return Wampum()->content->get_program_steps_list( $program_object_or_id );
}

function wampum_get_program_steps( $program_object_or_id ) {
	// global $wampum_content_types;
	return Wampum()->content->get_program_steps( $program_object_or_id );
}

function wampum_get_step_program( $step_object_or_id ) {
	// global $wampum_content_types;
	return Wampum()->content->get_step_program( $step_object_or_id );
}

function wampum_get_user_programs( $user_id ) {
	return Wampum()->membership->get_programs( $user_id );
}

// function wampum_get_connection_button( $type, $from, $to, $text_connected, $text_disconnected ) {
// 	global $wampum_connections;
// 	return $wampum_connections->connection_button( $type, $from, $to, $text_connected, $text_disconnected );
// }
