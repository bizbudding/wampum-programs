<?php
/**
 * My Memberships tab of Wampum
 *
 */

wp_enqueue_style('wampum');

/**
 *
 *
 * @uses  /woocommerce-memberships/includes/wc-memberships-template-functions.php
 */
// add_filter( 'wc_memberships_members_area_my-memberships_actions', 'wampum_account_program_action_links_test', 10, 3 );
// function wampum_account_program_action_links_test( $default_actions, $user_membership, $object ) {
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
