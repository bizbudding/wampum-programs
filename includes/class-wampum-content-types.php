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
	 * @var Wampum_Content_Types The one true Wampum_Content_Types
	 * @since 1.0.0
	 */
	private static $instance;

	// protected $post_meta;

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
		add_action( 'init', 		  		array( $this, 'register_post_types'), 0 );
		// add_action( 'registered_post_type', array( $this, 'add_permastruct' ), 1, 2 );
		//
		// Filters
		add_filter( 'post_type_link', 		array( $this, 'post_type_link' ), 1, 3 );
		add_filter( 'redirect_canonical',	array( $this, 'redirect_steps' ), 10, 2 );


		// Support
		add_post_type_support( 'wc_membership_plan', 'post-thumbnails' );
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

		// Add rewrite tags
		$this->add_rewrite_tags();
		// Add rewrite rules
		$this->add_rewrite_rules();

		// Programs
		$program = self::PROGRAM;
	    register_extended_post_type( $program, array(
			'enter_title_here' => 'Enter ' . $this->singular_name($program) . ' Name',
			'menu_icon'		   => 'dashicons-feedback',
		    'rewrite' 		   => array(
		        'permastruct' => '/' . $this->get_program_base_slug() . '/%wampum_program%',
		        // 'slug' => $this->get_program_base_slug(),
		    ),
		    'has_archive' => apply_filters( 'wampum_program_has_archive', false ),
			'supports' 	  => apply_filters( 'wampum_program_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$program] );

	    // Steps
	    $step = self::STEP;
	    register_extended_post_type( $step, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($step) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
		    'rewrite' 			=> array(
		        'permastruct' => '/' . $this->get_program_base_slug() . '/%wampum_step_program%/%wampum_step%',
		        // 'slug' => $this->get_program_base_slug() . '%wampum_step_program%',
		    ),
		    'has_archive' 		=> apply_filters( 'wampum_step_has_archive', false ),
			'supports'			=> apply_filters( 'wampum_step_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
		    'admin_cols' 		=> array(
				'programs_to_steps' => array(
				    'title'      => $this->plural_name(self::PROGRAM),
				    'connection' => 'programs_to_steps',
				    'link'       => 'edit',
				),
				'steps_to_resources' => array(
				    'title'      => $this->plural_name(self::RESOURCE),
				    'connection' => 'steps_to_resources',
				    'link'       => 'edit',
				),
		    ),
	    ), $this::default_names()[$step] );

	    // Resources
	    $resource = self::RESOURCE;
	    register_extended_post_type( $resource, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($resource) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
		    'has_archive' 		=> apply_filters( 'wampum_resource_has_archive', false ),
			'supports'			=> apply_filters( $resource . '_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$resource] );

	    // REMOVE THIS BEFORE YOU PUSH THIS LIVE!!!!!!!!!!!!!!
	    flush_rewrite_rules();

	}

	/**
	 * Add rewrite tags
	 *
	 * @see    http://wordpress.stackexchange.com/questions/175110/nested-cpt-urls-posts-2-posts
	 * @see    http://wordpress.stackexchange.com/questions/61105/nested-custom-post-types-with-permalinks
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function add_rewrite_tags() {
		add_rewrite_tag( '%wampum_step_program%', '([^/]+)' );
	}

	/**
	 * Add rewrite rules
	 *
	 * @see    http://wordpress.stackexchange.com/questions/175110/nested-cpt-urls-posts-2-posts
	 * @see    http://wordpress.stackexchange.com/questions/61105/nested-custom-post-types-with-permalinks
	 *
	 * @since  TBD
	 *
	 * @return void
	 */
	public function add_rewrite_rules() {
	    add_rewrite_rule( '^' . $this->get_program_base_slug() . '/([^/]*)/([^/]*)/?','index.php?' . self::STEP . '=$matches[2]','top' );
		// add_rewrite_rule( '^' . $this->get_program_base_slug() . '/[^/]+/([^/]+)/?$', 'index.php?' . self::STEP . '=$matches[1]', 'top' );
	}

	/**
	 * Action fired after a CPT is registered in order to set up the custom permalink structure for the post type.
	 * Borrowed from Extended CPTs
	 *
	 * @see   https://github.com/johnbillion/extended-cpts/blob/master/extended-cpts.php#L590
	 *
	 * @param string $post_type Post type name.
	 * @param object $args      Arguments used to register the post type.
	 *
	 * @return void
	 */
	public function add_permastruct( $post_type, stdClass $args ) {
		if ( self::STEP != $post_type ) {
			return;
		}
		add_permastruct( 'wampum_step_program', '%wampum_step_program%' );
	}

	/**
	 * Filter the post type permalink in order to populate its rewrite tags.
	 * Borrowed from Extended CPTs
	 *
	 * @see    https://github.com/johnbillion/extended-cpts/blob/master/extended-cpts.php#L608
	 *
	 * @since  TBD
	 *
	 * @param  string   $post_link  The post's permalink.
	 * @param  WP_Post  $post       The post in question.
	 * @param  bool     $leavename  Whether to keep the post name.
	 * @param  bool     $sample     Is it a sample permalink.
	 *
	 * @return string             The post's permalink.
	 */
	public function post_type_link( $post_link, WP_Post $post, $leavename ) {
		// If it's not our post type, bail out
		if ( self::STEP != $post->post_type ) {
			return $post_link;
		}

		$replacements = array();

		if ( false !== strpos( $post_link, '%wampum_step_program%' ) ) {
			// $replacements['%wampum_step_program%'] = $this->get_step_program_slug($post);
			$replacements['%wampum_step_program%'] = $this->get_step_program_slug($post);
		}

		$post_link = str_replace( array_keys( $replacements ), $replacements, $post_link );
		return $post_link;
	}

	function redirect_steps( $redirect_url, $requested_url ) {
		if ( ! is_singular(self::STEP) ) {
			return $requested_url;
		}
		return $redirect_url;
	}

	/**
	 * Get the first (and hopefully only) connected program slug
	 *
	 * @since  1.0.0
	 *
	 * @param  object|int   $step_object_or_id  the post object or ID to get connected item from
	 *
	 * @return string|bool
	 */
	public function get_step_program_slug( $step_object_or_id ) {
		$program = $this->get_step_program( $step_object_or_id );
		if ( $program ) {
			return $program->post_name;
		}
		return sanitize_title_with_dashes($this->plural_name(self::STEP));
	}

	/**
	 * Get the first (and hopefully only) connected program ID
	 *
	 * @since  1.0.0
	 *
	 * @param  object|int   $step_object_or_id  the post object or ID to get connected item from
	 *
	 * @return string|bool
	 */
	public function get_step_program_id( $step_object_or_id ) {
		$program = $this->get_step_program( $step_object_or_id );
		if ( $program ) {
			return $program->ID;
		}
		return false;
	}

	/**
	 * Get the step program
	 *
	 * @since  1.0.0
	 *
	 * @param  object|id  $step_object_or_id
	 *
	 * @return object     the program objects
	 */
	public function get_step_program( $step_object_or_id ) {
		// Bail if not a step
		// if ( 'wampum_step' !== get_post_type($step_object_or_id) ) {
		// 	return;
		// }
		// Get adjacent items
		$items = Wampum()->connections->get_adjacent_items( 'programs_to_steps', $step_object_or_id );
		// If parent is a thing
		if ( isset($items['parent']) && ! empty($items['parent']) ) {
			return $items['parent'];
		}
		return false;
	}

	public function get_program_base_slug() {
		$slug = sanitize_title_with_dashes(self::plural_name(self::PROGRAM));
		return apply_filters( 'wampum_program_base_slug', $slug );
	}

	public function get_step_base_slug( $step_id)  {
		$plural_name = sanitize_title_with_dashes( $this->plural_name(self::STEP) );
		$slug = $this->get_step_program_slug($step_id) ? $this->get_step_program_slug($step_id) : $plural_name;
		return apply_filters( 'wampum_step_base_slug', $slug );
	}

	public function get_program_steps_list( $program_object_or_id ) {
		$output = '';
		$steps = $this->get_program_steps($program_object_or_id);
		if ( $steps ) {
			$output .= '<ul>';
			foreach ( $steps as $step ) {
				$output .= '<li><a href="' . get_permalink($step->ID) . '">' . $step->post_title . '</a></li>';
			}
			$output .= '</ul>';
		}
		return $output;
	}

	/**
	 * Get all steps connected to a program
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $program_object_or_id  the program Object or ID
	 *
	 * @return array|objects|bool
	 */
	public function get_program_steps($program_object_or_id) {
		// TRY TO USE get_steps_from_{post_type}_query in class-wampum-connections.php - it's MUCH faster
		//
		// TODO: CHANGE THIS TO get_steps_from_step_query() and get_steps_from_program_query()
		// CHECK IF IS OBJECT, IF ID THEN GET OBJECT FROM ID FIRST
		// CHECK IF POST TYPE IS STEP OR PROGRAM AND GET STEPS WITH EACH METHOD
		//
		$connected = get_posts( array(
			'connected_type'	=> 'programs_to_steps',
			'connected_items'	=> $program_object_or_id,
			'nopaging'			=> true,
			'suppress_filters'	=> false,
		) );
		if ( $connected ) {
			return $connected;
		}
		return false;
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
