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
 * Check if current user can view a specific 'wampum_step'
 *
 * @since  1.0.0
 *
 * @param  int  $post_id  ID of the post to check access to
 *
 * @return bool
 */
function wampum_user_can_view_step( $user_id, $step_id ) {
	return Wampum_Membership::can_view_step( $user_id, $step_id );
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
function wampum_user_can_view( $user_id, $post_id ) {
	return Wampum_Membership::can_view( $user_id, $post_id );
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

function wampum_get_program_steps_list( $program_id ) {
	global $wampum_content_types;
	return $wampum_content_types->get_program_steps_list( $program_id );
}

function wampum_get_program_steps( $program_id ) {
	global $wampum_content_types;
	return $wampum_content_types->get_program_steps( $program_id );
}

function wampum_get_user_programs( $user_id, $args = array() ) {
	// $args = array(
	// 	'posts_per_page'   => 5,
	// 	'offset'           => 0,
	// 	'category'         => '',
	// 	'category_name'    => '',
	// 	'orderby'          => 'date',
	// 	'order'            => 'DESC',
	// 	'include'          => '',
	// 	'exclude'          => '',
	// 	'meta_key'         => '',
	// 	'meta_value'       => '',
	// 	'post_type'        => 'post',
	// 	'post_mime_type'   => '',
	// 	'post_parent'      => '',
	// 	'author'	   => '',
	// 	'post_status'      => 'publish',
	// 	'suppress_filters' => true
	// );
	$args['post_type'] = 'wampum_program';
	$posts = get_posts( $args );
	if ( ! $posts ) {
		return;
	}
	$programs = array();
	foreach ( $posts as $post ) {
		if ( wampum_user_can_view( $user_id, $post->ID ) ) {
			$programs[] = $post;
		}
	}
	return $programs;
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