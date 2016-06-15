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
		add_action( 'init', 			array( $this, 'add_rewrite_tags' ), 0, 2 );
		add_action( 'init', 			array( $this, 'register_post_types'), 0 );
		// add_action( 'init', array( $this, 'register_taxonomies') );

		// Filters

		// add_action( 'init', 		   array( $this, 'ads_rewrite' ) );
		add_filter( 'post_type_link',  array( $this, 'post_type_link' ), 10, 2 );

		// Support
		add_post_type_support( 'wc_membership_plan', 'post-thumbnails' );
	}

	// function ads_rewrite() {
	// 	global $wp_rewrite;
	// 	// $queryarg = 'post_type=ads&p=';
	// 	// $wp_rewrite->add_rewrite_tag('%cpt_id%', '([^/]+)', $queryarg);
	// 	$wp_rewrite->add_rewrite_tag( '%wampum_program%', '([^/]+)' );
	// 	$wp_rewrite->add_rewrite_tag( '%wampum_step_program%', '([^/]+)' );
	// 	$wp_rewrite->add_rewrite_tag( '%wampum_step%', '([^/]+)' );
	// 	$wp_rewrite->add_permastruct( 'wampum_step_program', '/%wampum_step_program%/', false );
	// }

	// function wampum_permalinks( $post_link, $id = 0, $leavename, $sample ) {
	// 	global $wp_rewrite;
	// 	$post = &get_post($id);
	// 	if ( is_wp_error( $post ) )
	// 		return $post;
	// 	$newlink = $wp_rewrite->get_extra_permastruct('myposttype');
	// 	$newlink = str_replace("%post_id%", $post->ID, $newlink);
	// 	$newlink = home_url(user_trailingslashit($newlink));
	// 	return $newlink;
	// }


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
			'enter_title_here' => 'Enter ' . $this->singular_name($program) . ' Name',
			'menu_icon'		   => 'dashicons-feedback',
			'rewrite' => array(
		        // 'permastruct' => $this->get_program_base_slug() . '/%wampum_program%',
		        'permastruct' => $this->get_program_base_slug() . '/%postname%',
		        // 'slug' => $this->get_program_base_slug() . '/%wampum_program%',
		    ),
		    'has_archive' => apply_filters( 'wampum_program_has_archive', false ),
			'supports' 	  => apply_filters( 'wampum_program_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
		  //   'admin_cols'  => array(
				// 'programs_to_steps' => array(
				//     'title'      => $this->plural_name(self::STEP),
				//     'connection' => 'programs_to_steps',
				//     'link'       => 'edit',
				// ),
				// 'programs_to_resources' => array(
				//     'title'      => $this->plural_name(self::RESOURCE),
				//     'connection' => 'programs_to_resources',
				//     'link'       => 'edit',
				// ),
		  //   ),
	    ), $this::default_names()[$program] );

	    // Steps
	    $step = self::STEP;
	    register_extended_post_type( $step, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($step) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'rewrite'			=> array(
		        // 'permastruct' => $this->get_program_base_slug() . '/%wampum_step_program%/%wampum_step%',
		        'permastruct' => $this->get_program_base_slug() . '/%wampum_program%/%postname%',
		        // 'slug' => $this->get_program_base_slug() . '/%wampum_step_program%',
		        // 'slug' => $this->get_program_base_slug() . '/%wampum_program%',
		    ),
		    'has_archive' 		=> apply_filters( 'wampum_step_has_archive', false ),
			'supports'			=> apply_filters( 'wampum_step_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
		    // 'admin_cols' 		=> array(
				// 'programs_to_steps' => array(
				//     'title'      => $this->plural_name(self::PROGRAM),
				//     'connection' => 'programs_to_steps',
				//     'link'       => 'edit',
				// ),
				// 'steps_to_resources' => array(
				//     'title'      => $this->plural_name(self::RESOURCE),
				//     'connection' => 'steps_to_resources',
				//     'link'       => 'edit',
				// ),
		    // ),
	    ), $this::default_names()[$step] );

	    // Resources
	    $resource = self::RESOURCE;
	    register_extended_post_type( $resource, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name($resource) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
		    'has_archive' 		=> apply_filters( 'wampum_resource_has_archive', false ),
			'supports'			=> apply_filters( $resource . '_supports', array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$resource] );

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
	    // register_extended_taxonomy( self::PROGRAM, array('wc_membership_plan',self::STEP), array(
	    	// 'meta_box' => false,
            // 'rewrite'  => array( 'slug' => 'programs' ),
            // 'show_ui' => false,
        // ), $this::default_names()[self::PROGRAM] );
	}

	/**
	 * Add rewrite tags to available permalinks tags
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function add_rewrite_tags() {
		// add_rewrite_tag( '%wampum_step_program%', '([^/]+)' );
		add_rewrite_tag( '%wampum_program%', '([^/]+)' );
		// add_rewrite_tag( '%wampum_step%', '([^/]+)' );
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
	// public function custom_permalinks( $post_link, $post ) {
	// 	if ( 'publish' !== $post->post_status ) {
	//         return $post_link;
	// 	}
	//     if ( self::PROGRAM === $post->post_type ) {
	// 	    $post_link = str_replace( $post->post_type, $this->get_program_base_slug(), $post_link );
	//     }
	//     if ( self::STEP === $post->post_type ) {
	// 	    $post_link = str_replace( '%wampum_step_program%', $this->get_step_program_slug($post->ID), $post_link );
	//     }
	//     return $post_link;
	// }

	public function post_type_link( $url, $post ) {
	    // Bail if we can't find the rewrite tag
	    // if ( strpos('%wampum_program%', $url ) === FALSE ) {
	    if ( strpos( $url, '%wampum_program%' ) === FALSE ) {
			return $url;
	    }
	    // Make sure our object and ID are set correctly
	    if ( is_object($post) ) {
			$post_id = $post->ID;
	    } else {
			$post_id = $post;
			$post	 = get_post($post_id);
	    }
	    // Bail if not a post object or not a published post
	    if ( ! is_object($post) || 'publish' !== $post->post_status ) {
			return $url;
	    }
	    if ( $post->post_type == self::STEP ) {
			// $mystring = 'abc';
			// $findme   = 'a';
			// $pos = strpos($mystring, $findme);
			// $bodytag = str_replace("%body%", "black", "<body text='%body%'>");
			$slug = $this->get_step_program_slug($post);
			$slug = 'some-program';
			$url  = str_replace( '%wampum_program%', $slug, $url );
	    }
	  //   if ( $post->post_type == self::PROGRAM ) {
			// $slug = $this->get_program_base_slug($post->ID);
			// $url  = str_replace( '%wampum_program%', $slug, $url );
	  //   }
	    return $url;
	}

	/**
	 * Have WordPress match postname to any of our public post types (post, page, race)
	 * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
	 * By default, core only accounts for posts and pages where the slug is /post-name/
	 */
	// public function parse_request_trick( $query ) {
	//     if ( ! $query->is_main_query() || is_admin() ) {
	//         return $query;
	//     }
	//     // Only noop our very specific rewrite rule match
	//     if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
	//         return $query;
	//     }
	//     // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
	//     if ( ! empty( $query->query['name'] ) ) {
	//         $query->set( 'post_type', array( 'post', 'page', self::PROGRAM, self::STEP ) );
	//     }
	//     return $query;
	// }

	// public function get_post_meta( $key = '' ) {
	//     if ( ! $key ) {
	//         return $this->post_meta;
	//     }
	//     if ( array_key_exists( $key, (array) $this->post_meta ) ) {
	//         return $this->post_meta[ $key ];
	//     }
	//     return false;
	//     // $this->post_meta = get_post_meta( $this->post_id, $key );

	//     // return $this->post_meta[ $key ];
	// }

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
