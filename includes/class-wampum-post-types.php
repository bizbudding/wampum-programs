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
 * @package Wampum
 */
class Wampum_Post_Types {

	function __construct() {
		add_action( 'init', array( $this, 'register_post_types') );
		add_action( 'init', array( $this, 'register_p2p_connections') );
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
		$program = 'wampum_program';
	    register_extended_post_type( $program, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($program) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_program_supports', array('title','editor','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$program] );

	    // Lessons
	    $lesson = 'wampum_lesson';
	    register_extended_post_type( $lesson, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($lesson) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_lesson_supports', array('title','editor','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$lesson] );

	    // Resources
	    $resource = 'wampum_resource';
	    register_extended_post_type( $resource, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($resource) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_resource_supports', array('title','editor','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$resource] );

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
	            'from' => $this->plural_name('wampum_program'),
	            'to'   => $this->plural_name('wampum_lesson'),
	        ),
	        'from_labels' => array(
	            'singular_name' => $this->singular_name('wampum_lesson'),
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
	            'from' => $this->plural_name('wampum_lesson'),
	            'to'   => $this->singular_name('wampum_lesson') . ' ' . $this->plural_name('wampum_resource'),
	        ),
	        'from_labels' => array(
	            'singular_name' => $this->plural_name('wampum_resource'),
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
	 * Get singular post type name
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $post_type  registered post type name
	 *
	 * @return string
	 */
	public function singular_name( $post_type, $lowercase = false ) {
		$name = $this::default_names()[$post_type]['singular'];
		return ($lowercase) ? strtolower($name) : $name;
	}

	/**
	 * Get plural post type name
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $post_type  registered post type name
	 *
	 * @return string
	 */
	public function plural_name( $post_type, $lowercase = false ) {
		$name = $this::default_names()[$post_type]['plural'];
		return ($lowercase) ? strtolower($name) : $name;
	}

	/**
	 * Set default name values for registered post types
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function default_names() {
		$post_types = array(
			'wampum_program' => array(
			   'singular' => _x('Program', 'wampum'),
			   'plural'   => _x('Programs', 'wampum'),
			   'slug'	  => 'programs',
			),
			'wampum_lesson' => array(
			   'singular' => _x('Lesson', 'wampum'),
			   'plural'   => _x('Lessons', 'wampum'),
			   'slug'	  => 'lessons',
			),
			'wampum_resource' => array(
			   'singular' => _x('Resource', 'wampum'),
			   'plural'   => _x('Resources', 'wampum'),
			   'slug'	  => 'resources',
			),
		);
		return apply_filters( 'wampum_post_type_default_names', $post_types );
	}

}
