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

    echo '<ul class="wampum-resource-list ftable" style="margin-left:0;">';

        echo '<li class="ftable-row">';
            echo '<span class="ftable-header">' . Wampum()->content->singular_name(get_post_type()) . ' ' . Wampum()->content->plural_name('wampum_resource') . '</span>';
        echo '</li>';

		foreach ( $resources as $resource ) {

			$buttons = '';
			$file = get_post_meta( $resource->ID, 'wampum_resource_files', true );
			if ( $file ) {
				$buttons .= '<a target="_blank" class="button ftable-button ftable-button-right" href="' . wp_get_attachment_url($file) . '">Download</a>';
			}
			$buttons .= '<a class="button ftable-button ftable-button-left" href="' . get_permalink($resource->ID) . '">View</a>';

			$image   = '';
			if ( has_post_thumbnail( $resource->ID ) ) {
				$image = sprintf( '<a class="ftable-cell ftable-image" href="%s" title="%s">%s</a>',
					get_permalink(),
					the_title_attribute( 'echo=0' ),
					get_the_post_thumbnail( $resource->ID, 'thumbnail' )
				);
			}
			$title = sprintf( '<a class="ftable-cell ftable-title" href="%s" title="%s">%s</a>',
				get_permalink($resource->ID),
				the_title_attribute( 'echo=0' ),
				$resource->post_title
			);
			$actions = '<span class="ftable-cell ftable-actions">' . $buttons . '</span>';
			echo '<li class="ftable-row">' . $image . $title . $actions . '</li>';
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
