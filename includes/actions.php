<?php
//
add_action( 'piklist_save_field-connect_resource_to_topic', 'wampum_save_some_new_resources_yo', 10, 1 );
function wampum_save_some_new_resources_yo( $fields ) {

	// echo '<pre>';
    // print_r($fields);
    // echo '</pre>';
    // die();

    $topic_id = absint($fields['topic_id']['value']);

	foreach ( $fields['add_resource']['request_value'] as $resource ) {

		// Create new post
		$data = array(
			'post_type'		=> 'wampum_resource',
			'post_status'	=> 'publish',
			'post_title'	=> sanitize_text_field($resource['post_title']),
			'post_content'	=> sanitize_text_field($resource['post_content']),
		);
		$post_id = wp_insert_post( $data, true );

		if ( ! is_wp_error( $post_id ) ) {
			// Connect new post to topic
			$connection_id = p2p_type( 'resources_to_lessons' )->connect( $post_id, $topic_id, array(
			    'date' => current_time('mysql')
			));
		}

	}
}