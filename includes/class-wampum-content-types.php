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
class Wampum_Content_Types {

	/**
	 * Name of registered post type.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const STEP = 'wampum_step';

	/**
	 * Name of registered post type.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const RESOURCE = 'wampum_resource';

	/**
	 * Name of registered taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const PROGRAM = 'wampum_program';

	function __construct() {
		add_action( 'init', array( $this, 'register_post_types') );
		add_action( 'init', array( $this, 'register_taxonomies') );
		add_filter( 'post_type_link', array( $this, 'custom_permalinks'), 1, 2 );
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
		// $program = self::PROGRAM;
	    // register_extended_post_type( $program, array(
		// 	'enter_title_here'	=> 'Enter ' . $this->singular_name($program) . ' Name',
		// 	'menu_icon'			=> 'dashicons-feedback',
		// 	'supports'			=> apply_filters( 'wampum_program_supports', array('title','editor','genesis-cpt-archives-settings') ),
	    // ), $this::default_names()[$program] );

	    // Steps
	    register_extended_post_type( self::STEP, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name(self::STEP) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_step_supports', array('title','editor','genesis-cpt-archives-settings') ),
			'rewrite'			=> array( 'slug' => 'programs/%wampum_program%', 'with_front' => false ),
			'taxonomies'		=> array(self::PROGRAM),
			'has_archive'		=> 'programs',
		    'admin_cols' 		=> array(
		        // A taxonomy terms column
		        self::PROGRAM => array(
		            'taxonomy' => self::PROGRAM,
		            'link'	   => 'list',
		        ),
		        // A post field column
	            'post_date' => array(
	                'title'      => __( 'Publish Date', 'Date', 'pixie-article' ),
	                'post_field' => 'post_date',
	            ),
		    ),
		    'admin_filters' 	=> array(
		        self::PROGRAM => array(
		            'title'    => $this->singular_name(self::PROGRAM),
		            'taxonomy' => self::PROGRAM,
		        ),
		    ),
	    ), $this::default_names()[self::STEP] );

	    // Resources
	    register_extended_post_type( self::RESOURCE, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name(self::RESOURCE) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( self::RESOURCE . '_supports', array('title','editor','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[self::RESOURCE] );

	}

	/**
	 * Register custom taxonomies
	 * Replace programs metabox with Piklist generated metabox
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_taxonomies() {
		// Programs
	    register_extended_taxonomy( self::PROGRAM, self::STEP, array(
            'rewrite' => array( 'slug' => 'programs' ),
            'show_ui' => false,
        ), $this::default_names()[self::PROGRAM] );

	}

	/**
	 * Rewrite permalinks for better content structure
	 * Need to set Yoast SEO breadcrumbs to match this new structre
	 *
	 * @since  1.0.0
	 *
	 * @param  idk    $post_link  the default post link
	 * @param  object $post       the post object
	 *
	 * @return string|url
	 */
	function custom_permalinks( $post_link, $post ){
	   // if ( false !== strpos( $post_link, '%wampum_program%' ) ) {
	   //      $program_term_term = get_the_terms( $post->ID, self::PROGRAM );
	   //      $post_link = str_replace( '%wampum_program%', array_pop( $program_term_term )->slug, $post_link );
	   //  }
	    // return $post_link;
	    if ( is_object( $post ) && $post->post_type == self::STEP ){
	        $terms = wp_get_object_terms( $post->ID, self::PROGRAM );
	        if ( $terms ) {
	            return str_replace( '%wampum_program%' , $terms[0]->slug , $post_link );
	        }
	    }
	    return $post_link;
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
	public static function singular_name( $post_type, $lowercase = false ) {
		$name = self::default_names()[$post_type]['singular'];
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
	public static function plural_name( $post_type, $lowercase = false ) {
		$name = self::default_names()[$post_type]['plural'];
		return ($lowercase) ? strtolower($name) : $name;
	}

	/**
	 * Set default name values for registered post types
	 * TODO: Allow for taxonomy name?
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function default_names() {

		$content_names = array(
			self::PROGRAM => array(
			   'singular' => _x('Program', 'wampum'),
			   'plural'   => _x('Programs', 'wampum'),
			),
			self::STEP => array(
			   'singular' => _x('Step', 'wampum'),
			   'plural'   => _x('Program Steps', 'wampum'),
			),
			self::RESOURCE => array(
			   'singular' => _x('Resource', 'wampum'),
			   'plural'   => _x('Resources', 'wampum'),
			),
		);
		return apply_filters( 'wampum_content_default_names', $content_names );
	}

}
