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

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types') );
		// add_action( 'init', array( $this, 'register_taxonomies') );
		add_filter( 'post_type_link', array( $this, 'custom_permalinks' ), 1, 2 );
		add_action( 'pre_get_posts', array( $this, 'parse_request_trick' ) );
		add_action( 'template_redirect', array( $this, 'single_step_redirect' ) );

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
		$program = self::PROGRAM;
	    register_extended_post_type( $program, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($program) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_program_supports', array('title','editor','genesis-cpt-archives-settings') ),
			'rewrite'			=> array( 'slug' => self::PROGRAM, 'with_front' => false ),
	    ), $this::default_names()[$program] );

	    // Steps
	    register_extended_post_type( self::STEP, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name(self::STEP) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_step_supports', array('title','editor','genesis-cpt-archives-settings') ),
			// 'rewrite'			=> array( 'slug' => 'programs/%wampum_program%', 'with_front' => false ),
			'rewrite'			=> array( 'slug' => '%wampum_program%', 'with_front' => false ),
			// 'rewrite'			=> array( 'slug' => self::STEP, 'with_front' => false ),
			// 'taxonomies'		=> array(self::PROGRAM),
			// 'has_archive'		=> 'programs',
		    'admin_cols' 		=> array(
		        // A taxonomy terms column
		        // self::PROGRAM => array(
		        //     'taxonomy' => self::PROGRAM,
		        //     'link'	   => 'list',
		        // ),
		        // A post field column
	            'post_date' => array(
	                'title'      => __( 'Publish Date', 'Date', 'pixie-article' ),
	                'post_field' => 'post_date',
	            ),
		    ),
		    // 'admin_filters' 	=> array(
		    //     self::PROGRAM => array(
		    //         'title'    => $this->singular_name(self::PROGRAM),
		    //         'taxonomy' => self::PROGRAM,
		    //     ),
		    // ),
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
	    // register_extended_taxonomy( self::PROGRAM, 'wc_membership_plan', array(
	    register_extended_taxonomy( self::PROGRAM, array('wc_membership_plan',self::STEP), array(
	    	'meta_box' => false,
            'rewrite'  => array( 'slug' => 'programs' ),
            // 'show_ui' => false,
        ), $this::default_names()[self::PROGRAM] );

	}

	/**
	 * Rewrite permalinks for better content structure
	 *
	 * TODO: Set Yoast SEO breadcrumbs to match this new structure???
	 *
	 * @since  1.0.0
	 *
	 * @link   http://kellenmace.com/remove-custom-post-type-slug-from-permalinks/
	 *
	 * @param  idk    $post_link  the default post link
	 * @param  object $post       the post object
	 *
	 * @return string|url
	 */
	public function custom_permalinks( $post_link, $post ) {
		if ( 'publish' !== $post->post_status ) {
	        return $post_link;
		}
	    if ( 'wampum_program' === $post->post_type ) {
		    $post_link = str_replace( "/{$post->post_type}/", '/', $post_link );
	    }
	    if ( 'wampum_step' === $post->post_type ) {
	    	// If no connected program, set base to 'step' since we're using template_redirect if no program anyway
    		$slug = $this->get_connected_program_slug($post) ? $this->get_connected_program_slug($post) : 'step';
		    $post_link = str_replace( '%wampum_program%', $slug, $post_link );
	    }
	    return $post_link;
	}

	/**
	 * Get the first (and hopefully only) connected program slug
	 *
	 * @since  1.0.0
	 *
	 * @param  object|int   $object_or_id  the post object or ID to get connected item from
	 *
	 * @return string|null
	 */
	public function get_connected_program_slug( $object_or_id ) {
		$connected = get_posts( array(
			'connected_type'	=> 'steps_to_programs',
			'connected_items'	=> $object_or_id,
			'nopaging'			=> true,
			'posts_per_page'	=> 1,
			'suppress_filters'	=> false,
		));
		if ( $connected ) {
			return array_shift($connected)->post_name;
		}
		return null;
	}

	public function single_step_redirect() {
	    if ( ! is_singular(self::STEP) ) {
	    	return;
	    }
	    if ( null === $this->get_connected_program_slug( get_the_ID() ) ) {
	        wp_redirect( home_url() );
	        exit();
	    }
	}

	/**
	 * Have WordPress match postname to any of our public post types (post, page, race)
	 * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
	 * By default, core only accounts for posts and pages where the slug is /post-name/
	 */
	public function parse_request_trick( $query ) {
	    if ( ! $query->is_main_query() || is_admin() ) {
	        return $query;
	    }
	    // Only noop our very specific rewrite rule match
	    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
	        return $query;
	    }
	    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
	    if ( ! empty( $query->query['name'] ) ) {
	        $query->set( 'post_type', array( 'post', 'page', self::PROGRAM ) );
	    }
	    return $query;
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
