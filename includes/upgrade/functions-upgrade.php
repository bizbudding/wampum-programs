<?php

/**
 * @see
 * To run this upgrade process, add the following functions temporarily to functions.php in child theme
 *
 * add_action( 'init', 'wampum_upgrade_register_post_types' );
 * add_action( 'p2p_init', 'wampum_upgrade_register_p2p_connections' );
 *
 * @see
 * Then, hooked in somewhere that you can refresh the page once, run the following functions 1 time
 *
 * wampum_upgrade_convert_steps_to_program_child_pages()
 * wampum_upgrade_convert_resources_to_acf_relationship()  // Run this after steps have been converted to program child pages
 * wampum_upgrade_convert_resource_files_to_new_key()
 *
 * @see
 * Then, delete all connections we no longer need
 * wampum_upgrade_delete_connections( 'wampum_program', 'programs_to_steps' );
 * wampum_upgrade_delete_connections( 'wampum_program', 'programs_to_resources' );
 * wampum_upgrade_delete_connections( 'wampum_program', 'steps_to_resources' ); // They used to be steps but already converted to program child pages
 */

function wampum_upgrade_convert_steps_to_program_child_pages() {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'wampum_program',
		'post_status'      => array('publish','draft','future'),
		'suppress_filters' => true
	);
	$programs = get_posts($args);

	if ( ! $programs ) {
		return;
	}

	foreach ( $programs as $program ) {
		$program_id = $program->ID;
		$connected_steps = wampum_upgrade_get_connected_items( 'programs_to_steps', $program );
		if ( ! $connected_steps ) {
			continue;
		}
		foreach ( $connected_steps as $step ) {
			$data = array(
				'ID'			=> $step->ID,
				'post_type'		=> 'wampum_program',
				'post_parent'	=> $program_id,
			);
			wp_update_post($data);
		}
	}
}

/**
 * RUN THIS AFTER STEPS ARE CONVERTED TO PROGRAM CHILD PAGES
 * @return [type] [description]
 */
function wampum_upgrade_convert_resources_to_acf_relationship() {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'wampum_program',
		'post_status'      => array('publish','draft','future'),
	);
	$programs = get_posts($args);
	if ( $programs ) {
		foreach ( $programs as $program ) {
			$connected_resources = wampum_upgrade_get_connected_items( 'programs_to_resources', $program );
			if ( ! $connected_resources ) {
				continue;
			}
			$resource_ids = wp_list_pluck( $connected_resources, 'ID' );
			update_field( 'wampum_resources', $resource_ids, $program->ID );
		}
	}
}

function wampum_upgrade_convert_resource_files_to_new_key() {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'wampum_resource',
		'post_status'      => array('publish','draft','future'),
		'suppress_filters' => true
	);
	$resources = get_posts($args);

	if ( ! $resources ) {
		return;
	}

	foreach ( $resources as $resource ) {
		$old_files = get_post_meta( $resource->ID, 'wampum_resource_files', true );
		if ( is_array($old_files) ) {
			$file_id = $old_files[0];
		} else {
			$file_id = $old_files;
		}
		if ( ! $file_id ) {
			continue;
		}
		update_post_meta( $resource->ID, 'wampum_resource_file', $file_id );
		delete_post_meta( $resource->ID, 'wampum_resource_files' );
	}
}

function wampum_upgrade_delete_connections( $post_type, $connection_name ) {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => $post_type,
		'suppress_filters' => true
	);
	$posts = get_posts($args);

	if ( ! $posts ) {
		return;
	}

	foreach ( $posts as $post ) {
		$post_id  = get_the_ID();
		$connections = wampum_upgrade_get_connected_items( $connection_name, $post_id );
		// Skip if none
		if ( ! $connections ) {
			continue;
		}
		foreach ( $connections as $connection ) {
			p2p_type( $connection_name )->disconnect( $post_id, $connection->ID );
		}
	}
}

function wampum_upgrade_get_connected_items( $connection, $object_or_id ) {
	$connected = get_posts( array(
		'connected_type'	=> $connection,
		'connected_items'	=> $object_or_id,
		'nopaging'			=> true,
		'suppress_filters'	=> false,
	) );
	if ( $connected ) {
		return $connected;
	}
	return false;
}

function wampum_upgrade_register_post_types() {
    register_extended_post_type( 'wampum_step', array(
		'menu_icon'			=> 'dashicons-feedback',
	    'has_archive' 		=> false,
		'supports'			=> array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings'),
    ), Wampum()->content->get_default_names()['wampum_step'] );
}

function wampum_upgrade_register_p2p_connections() {

	// STEPS TO PROGRAMS
    p2p_register_connection_type( array(
        'name'            => 'programs_to_steps',
        'from'            => 'wampum_program',
        'to'              => 'wampum_step',
        'cardinality' 	  => 'one-to-many',
        'can_create_post' => false,
        'sortable'        => 'any',
        'admin_box'       => array(
			'show'		=> 'any',
			'context'	=> 'side',
		),
        'admin_column'   => true,
        'admin_dropdown' => true,
        'title'          => array(
            'from' => 'Steps',
            'to'   => wampum_get_singular_name('wampum_program'),
        ),
        'from_labels' => array(
            'singular_name' => wampum_get_plural_name('wampum_program'),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect to ', 'wampum' ) . wampum_get_singular_name('wampum_program'),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Item', 'wampum' ),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect to Step', 'wampum' ),
        ),
        'fields' => apply_filters( 'wampum_programs_to_steps_connection_fields', array() ),
    ) );

    // RESOURCES TO STEPS
    p2p_register_connection_type( array(
        'name'            => 'steps_to_resources',
        'from'            => 'wampum_step',
        'to'              => 'wampum_resource',
        'can_create_post' => false,
        'sortable'        => 'any',
        'admin_box'       => array(
			'show'		=> 'any',
			'context'	=> 'side',
		),
        'admin_column'   => true,
        'admin_dropdown' => true,
        'title'          => array(
            'from' => wampum_get_plural_name('wampum_resource'),
            'to'   => 'Steps',
        ),
        'from_labels' => array(
            'singular_name' => __( 'Item', 'wampum' ),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect to Step', 'wampum' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Item', 'wampum' ),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect a ', 'wampum' ) . wampum_get_singular_name('wampum_resource'),
        ),
        'fields' => apply_filters( 'wampum_steps_to_resources_connection_fields', array() ),
    ) );

    // RESOURCES TO PROGRAMS
    p2p_register_connection_type( array(
        'name'            => 'programs_to_resources',
        'from'            => 'wampum_program',
        'to'              => 'wampum_resource',
        'can_create_post' => false,
        'sortable'        => 'any',
        'admin_box'       => array(
			'show'		=> 'any',
			'context'	=> 'side',
		),
        'admin_column'   => true,
        'admin_dropdown' => true,
        'title'          => array(
            'from' => wampum_get_singular_name('wampum_program') . ' ' . wampum_get_plural_name('wampum_resource'),
            'to'   => wampum_get_plural_name('wampum_program'),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Item', 'wampum' ),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect a ', 'wampum' ) . wampum_get_singular_name('wampum_program'),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Item', 'wampum' ),
            'search_items'  => __( 'Search items', 'wampum' ),
            'not_found'     => __( 'No items found.', 'wampum' ),
            'create'        => __( 'Connect to ', 'wampum' ) . wampum_get_singular_name('wampum_resource'),
        ),
        'fields' => apply_filters( 'wampum_programs_to_resources_connection_fields', array() ),
    ) );

    // USERS TO STEPS (incomplete/complete or viewed)
    p2p_register_connection_type( array(
		'name'		=> 'user_step_progress',
		'from'		=> 'user',
		'to'		=> 'wampum_step',
		'admin_box'	=> array(
			'show' => false,
		),
        'admin_column'   => false,
        'admin_dropdown' => false,
    ) );

}