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

add_action( 'wampum_after_content', 'wampum_do_step_progress_link' );
function wampum_do_step_progress_link() {
	if ( ! is_user_logged_in() && ! is_singular('wampum_step') ) {
		return;
	}
	echo Wampum()->step_progress->maybe_get_step_progress_link( get_current_user_id(), get_the_ID() );
}

add_action( 'wampum_after_content', 'wampum_do_step_prev_next_links' );
function wampum_do_step_prev_next_links() {
	if ( ! is_singular('wampum_step') ) {
		return;
	}
	Wampum()->connections->prev_next_connection_links( 'programs_to_steps', get_the_ID() );
}
