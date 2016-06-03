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

	echo '<h2>' . __( 'My', 'wampum' ) . Wampum()->content->plural_name('wampum_program') . '</h2>';

	$data = wampum_get_user_programs( get_current_user_id() );

	if ( ! $data ) {
		$text = 'You don\'t have access to any ' . Wampum()->content->plural_name('wampum_program') . ' yet.';
		$text = apply_filters( 'wampum_account_programs_no_programs_text', $text );
		echo "<p>{$text}</p>";
	} else {

	// Get the account-programs.php template
	wampum_get_template_part( 'account', 'programs', true, $data );

	 //    echo '<ul class="wampum-programs-list ftable" style="margin-left:0;">';

	 //        echo '<li class="ftable-row">';
	 //            echo '<span class="ftable-header">Program</span>';
	 //        echo '</li>';

		// 	foreach ( $programs as $program ) {

		// 		$image_size = apply_filters('wampum_account_programs_image_size', 'thumbnail');

		// 		$image = '';
		// 		if ( has_post_thumbnail( $program->ID ) ) {
		// 		    $image = sprintf( '<a class="ftable-cell ftable-image" href="%s" title="%s">%s</a>',
		// 				get_permalink( $program->ID ),
		// 				the_title_attribute( 'echo=0' ),
		// 				get_the_post_thumbnail( $program->ID, $image_size )
		// 			);
		// 		}
		// 		$desc 	 = wampum_get_truncated_content($program->post_excerpt, 140);
		// 		$title   = '<span class="ftable-cell ftable-title"><a href="' . get_permalink( $program->ID ) . '">' . $program->post_title . '</a>' . $desc . '</span>';
		// 		$buttons = '<a class="button ftable-button" href="' . get_permalink($program->ID) . '">View</a>';
		// 		$actions = '<span class="ftable-cell ftable-actions">' . $buttons . '</span>';
		// 		echo '<li class="ftable-row">' . $image . $title . $actions . '</li>';
		// 	}

		// echo '</ul>';

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
	if ( ! is_singular( array('wampum_program','wampum_step') ) ) {
		return;
	}

	$data = '';
	// Get the resources
	if ( is_singular('wampum_program') ) {
		$data = Wampum()->connections->get_resources_from_program_query( get_queried_object() );
	} elseif ( is_singular('wampum_step') ) {
		$data = Wampum()->connections->get_resources_from_step_query( get_queried_object() );
	}

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
	if ( ! is_user_logged_in() && ! is_singular('wampum_step') ) {
		return;
	}
	echo Wampum()->step_progress->maybe_get_step_progress_link( get_current_user_id(), get_the_ID() );
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
	if ( ! is_singular('wampum_step') ) {
		return;
	}
	Wampum()->connections->prev_next_connection_links( 'programs_to_steps', get_the_ID() );
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
	$file = get_post_meta( get_the_ID(), 'wampum_resource_files', true );
	if ( ! $file ) {
		return;
	}
	echo '<p><a target="_blank" class="button wampum-resource-button" href="' . wp_get_attachment_url($file) . '">Download</a></p>';
}
