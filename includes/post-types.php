<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Custom Post Types
 *
 * This function is referenced during activate in the main plugin file
 */
// add_action( 'init', 'wampum_register_post_types', 0 );
function wampum_register_post_types() {

    // Programs
    register_extended_post_type( 'wampum_program', array(
		'enter_title_here'	=> 'Enter Program Name',
		'menu_icon'			=> 'dashicons-feedback',
		'supports'			=> array('title','editor','genesis-cpt-archives-settings'),
        // 'admin_cols' => array(
            // 'type' => array(
                // 'taxonomy' => 'resource_cat'
            // )
        // ),
    ), array(
        'singular' => 'Program',
        'plural'   => 'Programs',
        'slug'     => 'programs'
    ) );

    // Lessons
    register_extended_post_type( 'wampum_lesson', array(
        'enter_title_here' => 'Enter Lesson Name',
        'menu_icon'        => 'dashicons-feedback',
        'supports'         => array('title','editor','genesis-cpt-archives-settings'),
    ), array(
        'singular' => 'Lesson',
        'plural'   => 'Lessons',
        'slug'     => 'topics'
    ) );

    // Resources
    register_extended_post_type( 'wampum_resource', array(
        'enter_title_here' => 'Enter Resource Name',
        'menu_icon'        => 'dashicons-feedback',
        'supports'         => array('title','editor','genesis-cpt-archives-settings'),
    ), array(
        'singular' => 'Resource',
        'plural'   => 'Resources',
        'slug'     => 'resources'
    ) );

    // News Categories
    // register_extended_taxonomy( 'news_cat', 'news', array(
        // 'singular' => 'News Category',
        // 'plural'   => 'News Categories',
        // 'rewrite'  => array( 'slug' => 'news-category' ),
    // ) );

}

// add_action( 'p2p_init', 'wampum_p2p_connection_types' );
function wampum_p2p_connection_types() {

    p2p_register_connection_type( array(
        'name'            => 'topics_to_programs',
        'from'            => 'wampum_lesson',
        'to'              => 'wampum_program',
        'can_create_post' => false,
        'sortable'        => 'any',
        'admin_box'       => array(
			'show'		=> 'to',
			'context'	=> 'side',
		),
        'admin_column'   => true,
        'admin_dropdown' => true,
        'reciprocal'     => true,
        'title'          => array(
            'from' => __( 'Programs', 'wampum' ),
            'to'   => __( 'Lessons', 'wampum' )
        ),
        'from_labels' => array(
            'singular_name' => __( 'Lessons', 'wampum' ),
        ),
    ) );

    p2p_register_connection_type( array(
        'name'            => 'resources_to_topics',
        'from'            => 'wampum_resource',
        'to'              => 'wampum_lesson',
        'can_create_post' => false,
        'sortable'        => 'any',
        'admin_box'       => array(
			'show'		=> 'to',
			'context'	=> 'advanced',
		),
        'admin_column'   => true,
        'admin_dropdown' => true,
        'reciprocal'     => true,
        'title'          => array(
            'from' => __( 'Lessons', 'wampum' ),
            'to'   => __( 'Lesson Resources', 'wampum' )
        ),
        'from_labels' => array(
            'singular_name' => __( 'Resources', 'wampum' ),
        ),
        // 'to_labels' => array(
        //     'singular_name' => __( 'Item', 'wampum' ),
        //     'search_items'  => __( 'Search items', 'wampum' ),
        //     'not_found'     => __( 'No items found.', 'wampum' ),
        //     'create'        => __( 'Create Connections', 'wampum' ),
        // ),
    ) );
}
