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

	$data = wampum_get_user_programs();

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
 * Display step progress links
 *
 * @since  1.0.0
 *
 * @return null|mixed
 */
add_action( 'wampum_after_content', 'wampum_do_step_progress_link', 12 );
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
add_action( 'wampum_after_content', 'wampum_do_step_prev_next_links', 12 );
function wampum_do_step_prev_next_links() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	$post_id = get_the_ID();
	if ( wampum_is_child( $post_id ) ) {
		echo wampum_get_prev_next_links( $post_id );
	} else {
		echo wampum_get_first_step_link( $post_id );
	}
}
