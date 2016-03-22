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

// add_filter( 'the_content', 'wampum_step_content' );
function wampum_step_content( $content ) {
	if ( ! is_main_query() && ! is_singular('wampum_step') ) {
		return $content;
	}
	if ( is_user_logged_in() ) {
		global $wampum_user_step_progress;
		$content .= $wampum_user_step_progress->get_step_progress_button( get_the_ID() );
	}
	global $wampum_connections;
	$content .= $wampum_connections->get_prev_next_connection_links( 'programs_to_steps', get_the_ID() );
	return $content;
}

add_action( 'wampum_after_content', 'wampum_do_step_progress_link' );
function wampum_do_step_progress_link() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$data = array(
			// 'connection_name'	=> 'user_step_progress',
			'from'				=> get_current_user_id(),
			'to'				=> get_the_ID(),
			// 'text_connect'		=> ,
			// 'text_connected'	=> ,
		);

	$wampum_user_step_progress = new Wampum_User_Step_Progress($data);
	// global $wampum_user_step_progress;
	// echo $wampum_user_step_progress->get_link( get_current_user_id(), get_the_ID() );
	echo $wampum_user_step_progress->get_link();
	// echo Wampum_User_Step_Progress::get_link( get_the_ID() );
}

add_action( 'wampum_after_content', 'wampum_do_step_prev_next_links' );
function wampum_do_step_prev_next_links() {
	// if ( ! is_singular('wampum_step') ) {
	// 	return $content;
	// }
	// global $wampum_connections;
	// $content = $wampum_connections->get_prev_next_connection_links( 'programs_to_steps', get_the_ID() );
	// echo Wampum_Connections::get_prev_next_connection_links( 'programs_to_steps', get_the_ID() );
}