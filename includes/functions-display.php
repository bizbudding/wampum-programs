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

add_action( 'wampum_after_content', 'wampum_do_step_progress_button' );
function wampum_do_step_progress_button() {
	// Bail is user is not logged in
	if ( ! is_user_logged_in() ) {
		return;
	}
	global $wampum_user_step_progress;
	echo $wampum_user_step_progress->get_step_progress_button( get_the_ID() );
}

add_action( 'wampum_after_content', 'wampum_do_prev_next_step_nav' );
function wampum_do_prev_next_step_nav() {
	if ( ! is_singular( array( 'wampum_step' ) ) ) {
		return;
	}
	global $wampum_connections;
	echo $wampum_connections->prev_next_connection_links( 'programs_to_steps', get_the_ID() );
}