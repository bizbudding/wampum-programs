<?php
/**
 * Wampum Members
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
class Wampum_Members {

	// protected $user_id = '';

	public function __construct() {
		// $this->user_id = get_current_user_id();
	}

	/**
	 * Get a members purhcased programs
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

}