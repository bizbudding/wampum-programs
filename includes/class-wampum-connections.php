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
 * Main plugin class.
 *
 * @since   1.0.0
 *
 * @package Wampum
 */
class Wampum_Connections {

	function __construct() {
		add_action( 'p2p_init', array( $this, 'register_p2p_connections') );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'piklist_save_field-connect_resource_to_step', array( $this, 'connect_resource_to_step' ), 10, 1 );
	}

	/**
	 * Register Posts to Posts connections
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_p2p_connections() {

	    p2p_register_connection_type( array(
	        'name'            => 'program_to_membership_plan',
	        'from'            => get_taxonomy( 'wampum_program' )->object_type,
	        // 'from'            => 'wampum_program',
	        'to'              => 'post',
	        'can_create_post' => false,
	        'sortable'        => 'any',
	        // 'admin_box'       => array(
				// 'show'		=> 'any',
				// 'context'	=> 'side',
			// ),
	        // 'admin_column'   => true,
	        // 'admin_dropdown' => true,
	        // 'reciprocal'     => true,
	        // 'title'          => array(
	        //     'from' => Wampum_Content_Types::plural_name('wampum_step'),
	        //     'to'   => Wampum_Content_Types::singular_name('wampum_step') . ' ' . Wampum_Content_Types::plural_name('wampum_resource'),
	        // ),
	        // 'from_labels' => array(
	        //     'singular_name' => Wampum_Content_Types::plural_name('wampum_resource'),
	        // ),
	        // 'to_labels' => array(
	        //     'singular_name' => __( 'Item', 'wampum' ),
	        //     'search_items'  => __( 'Search items', 'wampum' ),
	        //     'not_found'     => __( 'No items found.', 'wampum' ),
	        //     'create'        => __( 'Create Connections', 'wampum' ),
	        // ),
	    ) );

	    p2p_register_connection_type( array(
	        'name'            => 'resources_to_steps',
	        'from'            => 'wampum_resource',
	        'to'              => 'wampum_step',
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
	            'from' => Wampum_Content_Types::plural_name('wampum_step'),
	            'to'   => Wampum_Content_Types::singular_name('wampum_step') . ' ' . Wampum_Content_Types::plural_name('wampum_resource'),
	        ),
	        'from_labels' => array(
	            'singular_name' => Wampum_Content_Types::plural_name('wampum_resource'),
	        ),
	        // 'to_labels' => array(
	        //     'singular_name' => __( 'Item', 'wampum' ),
	        //     'search_items'  => __( 'Search items', 'wampum' ),
	        //     'not_found'     => __( 'No items found.', 'wampum' ),
	        //     'create'        => __( 'Create Connections', 'wampum' ),
	        // ),
	    ) );
	}

	/**
	 * Register Javascript file(s)
	 * Use via wp_enqueue_script('wampum-select2'); in piklist files
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'wampum-select2',  WAMPUM_PLUGIN_URI . 'js/select2.min.js', array( 'jquery' ), '4.0.1', true );
	}

	/**
	 * Register CSS file(s)
	 * Use via wp_enqueue_style('wampum-select2'); in piklist files
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( 'wampum-select2', WAMPUM_PLUGIN_URI . 'css/select2.min.css', array(), '4.0.1' );
	}

	/**
	 * Create a resource and connect it to a step
	 *
	 * @param  array  $fields  the piklist field values
	 *
	 * @since  1.0.0
	 *
	 * @return void|WP_Error
	 */
	public static function connect_resource_to_step( $fields ) {

		// Value of step_id field from /parts/meta-boxes/wampum_step.php
	    $to = absint($fields['step_id']['value']);

	    // Existing resources field check
	    $existing_resources = isset($fields['existing_resources']['request_value']) ? $fields['existing_resources']['request_value'] : null;

	    if ( $existing_resources ) {
			foreach ( $existing_resources as $from ) {
				self::connect( 'resources_to_steps', $from, $to );
			}
	    }

	    // New resources field check
	    $new_resources = isset($fields['add_resource']['request_value']) ? $fields['add_resource']['request_value'] : null;

	    if ( $new_resources ) {

			foreach ( $new_resources as $resource ) {

				// Title field check
				$title = isset($resource['post_title']) ? sanitize_text_field($resource['post_title']) : null;

				// If title is null, skip this item in the loop
				if ( ! $title ) {
					continue;
				}

				// Content and files field checks
				$content = isset($resource['post_content']) ? sanitize_text_field($resource['post_content']) : null;
				$files   = isset($resource['resource_files']) ? $resource['resource_files'] : null;

				// Create new resource
				$data = array(
					'post_type'		=> 'wampum_resource',
					'post_status'	=> 'publish',
					'post_title'	=> $title,
					'post_content'	=> $content,
				);
				$from = wp_insert_post( $data, true );

				if ( ! is_wp_error( $from ) ) {
					if ( $files ) {
						// Add media to the new post's meta
						update_post_meta( $from, 'wampum_resource_files', $files );
					}
					// Connect new post to topic
					self::connect( 'resources_to_steps', $from, $to );
				}

			}
		}
	}

	/**
	 * Connect one object to another
	 *
	 * @param  string  $type  p2p connection type
	 * @param  int     $from  object ID getting connected from
	 * @param  int     $to    object ID getting connected to
	 *
	 * @since  1.0.0
	 *
	 * @return int|WP_Error   connection ID or error
	 */
	public static function connect( $type, $from, $to ) {
		$connection_id = p2p_type( 'resources_to_steps' )->connect( $from, $to, array(
		    'date' => current_time('mysql')
		));
		return $connection_id;
	}

}
