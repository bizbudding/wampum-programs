<?php
/**
 * CPT Archive Toolbar for Genesis
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@thestizmedia.com.com>
 * @link      https://github.com/JiveDig/cptast-genesis/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum {

	public function run() {
		if ( 'genesis' !== basename( get_template_directory() ) ) {
			// add_action( 'admin_init', array( $this, 'deactivate' ) );
			// return;
		}
		add_action( 'init', array( $this, 'check_piklist' ) );
		add_action( 'init', array( $this, 'register_post_types') );
		add_action( 'init', array( $this, 'register_p2p_connections') );
		add_action( 'piklist_save_field-connect_resource_to_lesson', array( $this, 'create_and_connect_resource_to_lesson' ), 10, 1 );
	}

	/**
	 * Deactivates the plugin if Genesis isn't running
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		deactivate_plugins( WAMPUM_BASENAME );
		add_action( 'admin_notices', array( $this, 'error_message' ) );
	}

	/**
	 * Error message if we're not using the Genesis Framework.
	 *
	 * @since 1.0.0
	 */
	public function error_message() {

		$error = sprintf( __( 'Sorry, Wampum works only with the Genesis Framework. It has been deactivated.', 'cptast-genesis' ) );

		echo '<div class="error"><p>' . esc_attr( $error ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

	}

	/**
	 * Check if Piklist is installed
	 *
	 * @since  1.0.0
	 */
	function check_piklist() {
		if ( ! is_admin() || ! piklist_checker::check(__FILE__) ) {
			return;
		}
	}

	/**
	 * Set up text domain for translations
	 *
	 * @since TODO
	 */
	public function load_textdomain() {
		// load_plugin_textdomain( 'cptast-genesis', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register custom post stypes
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_post_types() {
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

	/**
	 * Register Posts to Posts connections
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_p2p_connections() {

	    p2p_register_connection_type( array(
	        'name'            => 'lessons_to_programs',
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
	        'name'            => 'resources_to_lessons',
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