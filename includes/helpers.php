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
 * @param  string  $slug
 * @param  string  $name
 * @param  boolean $load
 *
 * @return mixed
 */
function wampum_get_template_part( $slug, $name = null, $load = true ) {
    global $wampum_template_loader;
    $wampum_template_loader->get_template_part( $slug, $name, $load );
}

function wampum_get_program_steps( $program_id ) {
	global $wampum_content_types;
	return $wampum_content_types->get_program_steps( $program_id );
}

// NO LONGER NEEDED UNLESS WE GO BACK TO PROGRAM AS TAXONOMY
function wampum_get_member_program_ids( $user_id ) {

	$terms = get_terms('wampum_program');

    $program_ids = array();
    foreach ( $terms as $term ) {
        $rules = wc_memberships()->rules->get_taxonomy_term_content_restriction_rules( 'wampum_program', $term->term_id );
        // If no rules are set on the page/post return early
        // if ( empty( $rules ) ) {
        //     return;
        // }
        // set variables as empty arrays
        // $access = $object_ids = array();
        // For each rule add the available object id's to the object_ids array
        foreach ( $rules as $rule ) {

            // get the membership plan object
            $membership_plan = wc_memberships_get_membership_plan( $rule->get_membership_plan_id() );
            // check whether the current user has access to the membership plan
            $access = wc_memberships_is_user_active_member( $user_id, $membership_plan );
            // if the user has access assign the ID to the object ID array
            // or if the user is the admin
            // if ( $access || current_user_can( 'manage_options' ) ) {
            if ( $access ) {
                $program_ids[] = $term->term_id;
            }
            // echo '<pre>';
            // print_r($access);
            // // print_r($rules['data:WC_Memberships_Membership_Plan_Rule:private']['membership_plan_id']);
            // echo '</pre>';

        }
    }
    return $program_ids;
}

/**
 * DO WE NEED THIS FOR ANYTHING?!?!?!?!?
 *
 * Get the page/post IDs or taxonomy term IDs for restricted content
 * for chosen WooCommerce Memberships levels which have been assigned to a post or page.
 *
 * @param  string $content_type | accepts either 'taxonomy' or 'post_type' object keys
 * @param  int    $post_id The ID of the post or page
 * @return array  post/pageID's or taxonomy term ID's
 */
function wampum_get_membership_term_ids( $content_type, $post_id ) {
	// check if woomembership plugin function exists
	if ( ! function_exists( 'wc_memberships_get_membership_plan' ) ) {
		return;
	}
	// Get the rules set in each page / post
	$rules = wc_memberships()->rules->get_post_content_restriction_rules( $post_id );
	// If no rules are set on the page/post return early
	if ( empty( $rules ) ) {
		return;
	}
	// set variables as empty arrays
	$access = $object_ids = array();
	// For each rule add the available object id's to the object_ids array
	foreach ( $rules as $rule ) {
		// get the membership plan object
		$membership_plan = wc_memberships_get_membership_plan( $rule->get_membership_plan_id() );
		// check whether the current user has access to the membership plan
		$access = wc_memberships_is_user_active_member( get_current_user_id(), $membership_plan );
		// if the user has access assign the ID to the object ID array
		// or if the user is the admin
		if ( $access || current_user_can( 'manage_options' ) ) {
			// if content restriction rules are set get the rules per membership plan
			$content_restriction_rules = isset( $membership_plan ) ? $membership_plan->get_content_restriction_rules() : array();
			foreach ( $content_restriction_rules as $restriction_rule ) {
				// get the object IDs associated with the restriction rules
				if ( is_object( $restriction_rule ) && $content_type === $restriction_rule->get_content_type() ) {
					foreach ( $restriction_rule->get_object_ids() as $id ) {
						$object_ids[] = $id;
					}
				}
			}
		}
	}
	// returns an array of content type IDs
	return $object_ids;
}