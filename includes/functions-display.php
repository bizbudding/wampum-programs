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
	if ( wampum_is_child( $post_id ) ) {
		echo wampum_get_prev_next_links( $post_id );
	} else {
		echo wampum_get_first_step_link( $post_id );
	}
}

/**
 * Display Resources on single programs, styled like a popup
 *
 * @since  1.1.1
 *
 * @return null|mixed
 */
add_action( 'wampum_popups', 'wampum_do_resource_popup' );
function wampum_do_resource_popup() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	if ( ! isset($_GET['resource']) ) {
		return;
	}
	$resource_id = absint($_GET['resource']);
	if ( ! ( current_user_can('wc_memberships_access_all_restricted_content') || current_user_can( 'wc_memberships_view_restricted_post_content', $resource_id ) ) ) {
		return;
	}

	// Get the resource post object
	$post = get_post($resource_id);

	// Bail if not a post object
	if ( ! $post ) {
		return;
	}

	// Bail if not a resource
	if ( $post->post_type != 'wampum_resource' ) {
		return;
	}

	$content = '';
    $content .= '<h2>' . $post->post_title . '</h2>';
	if ( has_post_thumbnail( $post->ID ) ) {
		$image_size = apply_filters( 'wampum_resource_image_size', 'medium' );
		$content .= '<div class="featured-image">';
		$content .= get_the_post_thumbnail( $post->ID, $image_size );
		$content .= '</div>';
	}
	$post_content .= $post->post_content;
	if ( isset($GLOBALS['wp_embed']) ) {
	    // If the content contains something we can oEmbed, do it.
	    $content .= wpautop($GLOBALS['wp_embed']->autoembed($post_content));
	} else {
		$content .= wpautop($post_content);
	}
	$file = get_post_meta( $post->ID, 'wampum_resource_file', true );
	if ( $file ) {
		$content .= '<p class="button-wrap"><a target="_blank" class="button wampum-resource-button" href="' . wp_get_attachment_url($file) . '">Download</a></p>';
	}
	$width = apply_filters( 'wampum_resource_popup_width', '800' );
	wampum_popup( $content, array( 'width' => $width ) );

}

/**
 * Display Resources on single resources
 *
 * @since  1.1.1
 *
 * @return null|mixed
 */
// add_action( 'wampum_after_content', 'wampum_do_resource_button' );
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
