<?php
/**
 * Wampum - Programs
 *
 * @package   Wampum_Content_Types
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
		add_action( 'init', 	array( $this, 'register_post_types'), 0 );
		add_action( 'wp_head', 	array( $this, 'do_template_functions' ) );
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
			'supports' 	  		  => apply_filters( 'wampum_program_supports', array('title','editor','excerpt','thumbnail','page-attributes','genesis-cpt-archives-settings','genesis-layouts') ),
			'rewrite' 			  => array( 'slug' => $this->get_slug('wampum_program') ),
			// 'show_in_rest'		  => true,
	    ), $this->get_default_names()['wampum_program'] );

		// Program Templates
		register_extended_taxonomy( 'wampum_program_template', 'wampum_program', array(
			'public'	=> false,
			'show_ui'	=> true,
		), array(
		    'singular' => 'Template',
		    'plural'   => 'Templates',
		) );

	}

	/**
	 * Try to output a function for each wampum_program_template a program is in
	 * These functions should be manually created, per-site, based on template terms used
	 * Example function wampum_do_template_{termslug_with_underscores_here}() { // $data = get_field('some_custom_field'); echo $data; }
	 *
	 * If you want to do this your own, ignore the function name and hook in yourself
	 *
	 * @since  1.5.0
	 *
	 * @return void
	 */
	public function do_template_functions() {
		if ( ! is_singular('wampum_program') ) {
			return;
		}
		// Get array of term slugs
		$terms = get_terms( array(
			'taxonomy'	 => 'wampum_program_template',
			'fields'	 => 'id=>slug',
			'hide_empty' => false,
		) );
		// Bail if no terms
		if ( ! $terms || is_wp_error($terms) ) {
			return;
		}
		// Create a function and output if it exists
		foreach ( $terms as $term_slug ) {
			// Convert term slug dashes to underscores for function name
			$slug_with_underscores = str_replace( '-', '_', $term_slug );
			// Build function name
		    $function = 'wampum_do_template_' . str_replace( '-', '_', $slug_with_underscores );
		    // Hook function in, if it exists
		    if ( function_exists( $function ) ) {
		        add_action( 'wampum_after_content', $function );
		    }
		}
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
			   'singular' => _x( 'Program', 'wampum' ),
			   'plural'   => _x( 'Programs', 'wampum' ),
			   'slug'	  => _x( 'programs', 'wampum' ),
			),
		);
		return apply_filters( 'wampum_content_default_names', $content_names );
	}

}
