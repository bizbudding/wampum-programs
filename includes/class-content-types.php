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
final class Wampum_Content_Types {
	/** Singleton *************************************************************/

	/**
	 * @var 	Wampum_Content_Types The one true Wampum_Content_Types
	 * @since 	1.0.0
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Content_Types;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	public function __construct() {
	}

	public function init() {
		// Actions
		add_action( 'init', array( $this, 'register_post_types'), 0 );
		// Support
		// add_post_type_support( 'wc_membership_plan', 'post-thumbnails' );
	}

	/**
	 * Register custom post stypes
	 *
	 * @see  	http://wordpress.stackexchange.com/questions/83531/custom-post-type-404s-with-rewriting-even-after-resetting-permalinks
	 * @see  	https://github.com/johnbillion/extended-cpts/wiki/Custom-permalink-structures
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_post_types() {

		// Programs
	    register_extended_post_type( 'wampum_program', array(
			'enter_title_here' 	  => 'Enter ' . $this->get_singular_name('wampum_program') . ' Name',
			'menu_icon'		   	  => 'dashicons-feedback',
			'exclude_from_search' => true,
			'hierarchical'		  => true,
		    'has_archive' 		  => apply_filters( 'wampum_program_has_archive', false ),
			'supports' 	  		  => apply_filters( 'wampum_program_supports', array('title','editor','excerpt','thumbnail','page-attributes','genesis-cpt-archives-settings') ),
			'rewrite' 			  => array( 'slug' => $this->get_slug('wampum_program') ),
			// 'show_in_rest'		  => true,
	    ), $this->get_default_names()['wampum_program'] );

	    // Resources
	    register_extended_post_type( 'wampum_resource', array(
			'enter_title_here'	=> 'Enter ' . $this->get_singular_name('wampum_resource') . ' Name',
			'menu_icon'			=> 'dashicons-list-view',
		    'has_archive' 		=> apply_filters( 'wampum_resource_has_archive', false ),
			'supports'			=> apply_filters( 'wampum_resource_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
			'rewrite' 		    => array( 'slug' => $this->get_slug('wampum_resource') ),
	    ), $this->get_default_names()['wampum_resource'] );

	}


	public function get_program_steps_list( $program_object_or_id ) {
		// $output = '';
		// $steps = $this->get_program_steps($program_object_or_id);
		// if ( $steps ) {
		// 	$output .= '<ul>';
		// 	foreach ( $steps as $step ) {
		// 		$output .= '<li><a href="' . get_permalink($step->ID) . '">' . $step->post_title . '</a></li>';
		// 	}
		// 	$output .= '</ul>';
		// }
		// return $output;
	}

	/**
	 * Get singular post type name
	 * TODO: Allow for taxonomy name?
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $post_type  registered post type name
	 *
	 * @return string
	 */
	public function get_singular_name( $post_type, $lowercase = false ) {
		$name = $this->get_default_names()[$post_type]['singular'];
		return ($lowercase) ? strtolower($name) : $name;
	}

	/**
	 * Get plural post type name
	 * TODO: Allow for taxonomy name?
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $post_type  registered post type name
	 *
	 * @return string
	 */
	public function get_plural_name( $post_type, $lowercase = false ) {
		$name = $this->get_default_names()[$post_type]['plural'];
		return ($lowercase) ? strtolower($name) : $name;
	}

	/**
	 * Get plural post type name
	 * TODO: Allow for taxonomy name?
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $post_type  registered post type name
	 *
	 * @return string
	 */
	public function get_slug( $post_type ) {
		$name = $this->get_default_names()[$post_type]['slug'];
		return $name;
	}

	/**
	 * Set default name values for registered post types
	 * TODO: Allow for taxonomy name?
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function get_default_names() {

		$content_names = array(
			'wampum_program' => array(
			   'singular' => _x('Program', 'wampum'),
			   'plural'   => _x('Programs', 'wampum'),
			   'slug'	  => _x('programs', 'wampum'),
			),
			'wampum_step' => array(
			   'singular' => _x('Step', 'wampum'),
			   'plural'   => _x('Steps', 'wampum'),
			   'slug'	  => _x('steps', 'wampum'),
			),
			'wampum_resource' => array(
			   'singular' => _x('Resource', 'wampum'),
			   'plural'   => _x('Resources', 'wampum'),
			   'slug'	  => _x('resources', 'wampum'),
			),
		);
		return apply_filters( 'wampum_content_default_names', $content_names );
	}

}