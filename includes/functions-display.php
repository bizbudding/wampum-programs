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

	$resources = '';
	// Get the resources
	if ( is_singular('wampum_program') ) {
		$resources = Wampum()->connections->get_resources_from_program_query( get_queried_object() );
	} elseif ( is_singular('wampum_step') ) {
		$resources = Wampum()->connections->get_resources_from_step_query( get_queried_object() );
	}

	// Bail if no resources
	if ( ! is_array($resources) ) {
		return;
	}

    echo '<ul class="wampum-resource-list woocommerce table-table" style="margin-left:0;">';

        echo '<li class="table-tr">';
            echo '<span class="table-th table-title">' . Wampum()->content->singular_name(get_post_type()) . ' ' . Wampum()->content->plural_name('wampum_resource') . '</span>';
            echo '<span class="table-th table-actions">Actions</span>';
        echo '</li>';

		foreach ( $resources as $resource ) {
			$buttons = '<a class="button list-button alignleft" href="' . get_permalink($resource->ID) . '">View</a>';

			$file = get_post_meta( $resource->ID, 'wampum_resource_files', true );
			if ( $file ) {
				$buttons .= '<a target="_blank" class="button list-button alignright" href="' . wp_get_attachment_url($file) . '">Download</a>';
			}
			$title   = '<a class="table-td table-title" href="' . get_permalink($resource->ID) . '">' . $resource->post_title . '</a>';
			$actions = '<span class="table-td table-actions">' . $buttons . '</span>';
			echo '<li class="table-tr">' . $title . $actions . '</li>';
		}

	echo '</ul>';
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
