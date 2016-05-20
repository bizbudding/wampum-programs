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

// add_action( 'wampum_after_content', 'wampum_do_program_resource_list_og' );
function wampum_do_program_resource_list_og() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	$resources = Wampum()->connections->get_resources_from_program_query( get_queried_object() );
	if ( ! $resources ) {
		return;
	}
   echo '<ul class="post-list" style="margin-left:0;">';

        echo '<li class="post-list-heading">';
            echo '<span class="post-heading post-title">Title</span>';
            echo '<span class="post-heading post-actions">Actions</span>';
        echo '</li>';

		foreach ( $resources as $resource ) {
			$buttons = '<a class="button list-button" href="' . get_permalink($resource->ID) . '">View</a>';

			$file = get_post_meta( $resource->ID, 'wampum_resource_files', true );
			if ( $file ) {
				$buttons .= '<a target="_blank" class="button list-button" href="' . wp_get_attachment_url($file) . '">Download</a>';
			}
			$title   = '<a class="post-item post-title" href="' . get_permalink($resource->ID) . '">' . $resource->post_title . '</a>';
			$actions = '<span class="post-item post-actions">' . $buttons . '</span>';
			echo '<li class="post-li">' . $title . $actions . '</li>';
		}

	echo '</ul>';
}

add_action( 'wampum_after_content', 'wampum_do_program_resource_list' );
function wampum_do_program_resource_list() {
	if ( ! is_singular('wampum_program') ) {
		return;
	}
	$resources = Wampum()->connections->get_resources_from_program_query( get_queried_object() );
	if ( ! $resources ) {
		return;
	}
   echo '<ul class="woocommerce table-table" style="margin-left:0;">';

        echo '<li class="table-tr">';
            echo '<span class="table-th table-title">Title</span>';
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
