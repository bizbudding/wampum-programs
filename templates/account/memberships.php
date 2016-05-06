<?php
/**
 * My Memberships tab of Wampum
 *
 */

wp_enqueue_style('wampum');

/**
 * Get members area URL
 *
 * This is a pluggable function in Woo Memberships
 * Wampum overrides it in this template to get the URL for the actual wampum_program
 * Unused parameters left in the function to enable Woo's default template to work with needing to override it
 *
 * @since  1.4.0 (Woo Memberships plugin version)
 *
 * @param  int|WC_Memberships_Membership_Plan $membership ID // ID ONLY FOR WAMPUM
 * @param  string $members_area_section Optional, which section of the members area to point to // NOT IN USE IN WAMPUM YET
 * @param  int|string $paged Optional, for paged sections // NOT IN USE IN WAMPUM YET
 *
 * @return string Unescaped URL
 */
// function wc_memberships_get_members_area_url( $membership, $members_area_section = '', $paged = '' ) {
	// echo '<pre>';
    // print_r($membership);
    // echo '</pre>';
// }

// wc_memberships_get_members_area_url( $customer_membership->get_plan_id(), $members_area[0] ) )

/**
 *
 *
 * @uses  /woocommerce-memberships/includes/wc-memberships-template-functions.php
 */
// add_filter( 'wc_memberships_my_memberships_column_names', 'wampum_account_membership_action_linksaslkfjsaljglkasjglk', 10, 1 );
// function wampum_account_membership_action_linksaslkfjsaljglkasjglk( $names ) {
	// return $default_actions;
	// echo '<pre>';
    // print_r($names);
    // echo '</pre>';
	// Remove the 'View' button until we figure out what to do
	// unset($default_actions['view']);
	// return $default_actions;
	// echo '<pre>';
	// print_r($default_actions);
    // echo '</pre>';
// }

$customer_memberships = wc_memberships_get_user_memberships();

if ( ! empty( $customer_memberships ) ) {

	wc_get_template( 'myaccount/my-memberships.php', array(
		'customer_memberships' => $customer_memberships,
		'user_id'              => get_current_user_id(),
	) );
}
