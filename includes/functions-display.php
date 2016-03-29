<?php
/**
 * Wampum
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com.com>
 * @link      https://github.com/JiveDig/wampum/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Force full width content layout
// add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// add_action( 'wampum_before_content', 'wampum_before_content_tests' );
function wampum_before_content_tests() {
	// if ( ! is_singular('wampum_step') ) {
	// 	return;
	// }
	$object = get_queried_object();
	// global $wp_query;
	// p2p_type( 'programs_to_steps' )->each_connected( $wp_query );
	echo '<pre>';
    print_r($object);
    // print_r($object->programs[0]->steps);
    echo '</pre>';
}

add_action( 'wampum_after_content', 'wampum_do_step_progress_link' );
function wampum_do_step_progress_link() {
	if ( ! is_user_logged_in() && ! is_singular('wampum_step') ) {
		return;
	}
	// echo Wampum()->step_progress->maybe_get_step_progress_link( get_current_user_id(), get_the_ID() );
	echo Wampum()->step_progress->maybe_get_step_progress_link( get_current_user_id(), get_the_ID() );
}

add_action( 'wampum_after_content', 'wampum_do_step_prev_next_links' );
function wampum_do_step_prev_next_links() {
	if ( ! is_singular('wampum_step') ) {
		return;
	}
	Wampum()->connections->prev_next_connection_links( 'programs_to_steps', get_the_ID() );
}
