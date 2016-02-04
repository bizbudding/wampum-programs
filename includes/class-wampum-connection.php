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

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum_Connection {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'piklist_field_templates', array( $this, 'field_template') );
		add_action( 'piklist_save_field-connect_resource_to_lesson', array( $this, 'create_and_connect_resource_to_lesson' ), 10, 1 );
	}

	// Enqueue Javascript files
	public function enqueue_scripts() {
		wp_enqueue_script( 'wampum-select2',  WAMPUM_PLUGIN_URI . '/js/select2.min.js', array( 'jquery' ), '4.0.1', true );
	}

	// Enqueue CSS files
	public function enqueue_styles() {
		wp_enqueue_style( 'wampum-select2', WAMPUM_PLUGIN_URI . '/css/select2.min.css', array(), '4.0.1' );
	}

	function field_template($templates) {
		$templates['select2'] = array(
				'name'			=> __('Select2 Field', 'wampum'),
				'description'	=> __('', 'wampum'),
				'template'		=> '[field_wrapper]
                <div class="%1$s piklist-theme-field-container">
					<div class="piklist-theme-label">
						[field_label]
					</div>
					<div class="piklist-theme-field piklist-select2-field">
						[field]
						[field_description_wrapper]
						<p class="piklist-theme-field-description">[field_description]</p>
						[/field_description_wrapper]
					</div>
                </div>
              [/field_wrapper]');
		return $templates;
	}

	/**
	 * Create a resource and connect it to a lesson
	 *
	 * @param  array  $fields  the piklist field values
	 *
	 * @since  1.0.0
	 *
	 * @return void|WP_Error
	 */
	public function create_and_connect_resource_to_lesson( $fields ) {

		// Value of lesson_id field from /parts/meta-boxes/wampum_lesson.php
	    $topic_id = absint($fields['lesson_id']['value']);

		foreach ( $fields['add_resource']['request_value'] as $resource ) {

			// Create new resource
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

}
