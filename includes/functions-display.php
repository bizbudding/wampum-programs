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

add_action( 'woocommerce_before_my_account', 'wampum_do_account_programs', 4 );
function wampum_do_account_programs() {

	echo '<h2>' . __( 'My', 'wampum' ) . ' ' . wampum_get_plural_name('wampum_program') . '</h2>';

	$data = wampum_get_user_programs( get_current_user_id() );

	if ( ! $data ) {
		$text = 'You don\'t have access to any ' . wampum_get_plural_name('wampum_program') . ' yet.';
		$text = apply_filters( 'wampum_account_programs_no_programs_text', $text );
		echo "<p>{$text}</p>";
	} else {
		// Get the account-programs.php template
		wampum_get_template_part( 'account', 'programs', true, $data );
	}

	do_action('wampum_account_after_programs');
}

/**
 * Display Resources on Programs and Steps
 *
 * @since  1.1.0
 *
 * @return null|mixed
 */
add_action( 'wampum_after_content', 'wampum_do_program_resource_list' );
function wampum_do_program_resource_list() {
	// Bail if not the right singular post type
	if ( ! is_singular( 'wampum_program' ) ) {
		return;
	}
	// Get the resources
	$data = get_field( 'wampum_resources' );
	// Bail if no resources
	if ( ! is_array($data) || empty($data) ) {
		return;
	}
	// Get the resource-list.php template
	wampum_get_template_part( 'resource', 'list', true, $data );
}

/**
 * Display step progress links
 *
 * @since  1.0.0
 *
 * @return null|mixed
 */
add_action( 'wampum_after_content', 'wampum_do_step_progress_link' );
function wampum_do_step_progress_link() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	echo wampum_get_program_progress_link( get_the_ID() );
}

/**
 * Display previous/next step links
 *
 * @since  1.0.0
 *
 * @return null|mixed
 */
add_action( 'wampum_after_content', 'wampum_do_step_prev_next_links' );
function wampum_do_step_prev_next_links() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	$post_id = get_the_ID();
	if ( ! wampum_is_step( $post_id ) ) {
		return;
	}
	echo wampum_get_prev_next_links();
}

/**
 * Display Resources on single resources
 *
 * @since  1.1.1
 *
 * @return null|mixed
 */
add_action( 'wampum_after_content', 'wampum_do_resource_button' );
function wampum_do_resource_button() {
	if ( ! is_singular('wampum_resource') ) {
		return;
	}
	$file = get_post_meta( get_the_ID(), 'wampum_resource_file', true );
	if ( ! $file ) {
		return;
	}
	echo '<p><a target="_blank" class="button wampum-resource-button" href="' . wp_get_attachment_url($file) . '">Download</a></p>';
}
