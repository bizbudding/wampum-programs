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

	public function init() {
		add_action( 'init', array( $this, 'register_post_types') );
		// add_action( 'init', array( $this, 'register_taxonomies') );
		// add_filter( 'post_type_link', array( $this, 'custom_permalinks' ), 1, 2 );
		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 1, 2 );
		// add_action( 'pre_get_posts', array( $this, 'parse_request_trick' ) );
		// add_action( 'template_redirect', array( $this, 'single_step_redirect' ) );
		add_action( 'init', array( $this, 'add_rewrite_tags' ) );
		// add_action( 'wp_head', array( $this, 'add_connections_to_wp_query' ) );
		// add_filter( 'single_template', array( $this, 'step_single_template' ) );
		// add_filter( 'archive_template', array( $this, 'step_archive_template' ) );


		// Add custom archive support for CPT
		add_post_type_support( 'wc_membership_plan', 'post-thumbnails' );
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
			// 'hierarchical'		=> true,
			'menu_icon'			=> 'dashicons-feedback',
			// 'rewrite'			=> array( 'slug' => self::PROGRAM, 'with_front' => false ),
			// 'rewrite'			=> array( 'slug' => '%wampum_program%', 'with_front' => false ),
			// 'rewrite'			=> array( 'slug' => $this->get_program_base_slug(), 'with_front' => false ),
			'rewrite' => array(
		        'permastruct' => $this->get_program_base_slug() . '/%wampum_program%',
		        // 'permastruct' => 'base' . '/%wampum_program%',
		    ),
			// 'rewrite'			=> array( 'slug' => self::PROGRAM ),
			// 'query_var' => true,
			// 'rewrite' => false,
			'supports'			=> apply_filters( 'wampum_program_supports', array('title','editor','thumbnail','genesis-cpt-archives-settings') ),
	    ), $this::default_names()[$program] );

	    // Steps
	    // $step_base = $this->get_program_base_slug();
	    register_extended_post_type( self::STEP, array(
			'enter_title_here'	=> 'Enter ' . $this->singular_name(self::STEP) . ' Name',
			'menu_icon'			=> 'dashicons-feedback',
			'supports'			=> apply_filters( 'wampum_step_supports', array('title','editor','thumbnail','genesis-cpt-archives-settings') ),
			// 'rewrite'			=> array( 'slug' => 'programs/%wampum_program%', 'with_front' => false ),
			// 'rewrite'			=> array( 'slug' => trailingslashit($step_base) . '%wampum_program%', 'with_front' => true ),
			// 'rewrite'			=> array( 'slug' => '%wampum_step_program%', 'with_front' => false ),
			// 'rewrite'			=> array( 'slug' => self::STEP ),
			'rewrite' => array(
		        'permastruct' => $this->get_program_base_slug() . '/%wampum_step_program%/%wampum_step%',
		        // 'permastruct' => 'base' . '/%wampum_step_program%/%wampum_step%',
		    ),
			// 'query_var' => true,
			// 'rewrite' => false,
			// 'taxonomies'		=> array(self::PROGRAM),
			// 'has_archive'		=> 'programs',
		    // 'show_in_menu' => 'edit.php?post_type=wampum_program',
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
	    // register_extended_taxonomy( self::PROGRAM, array('wc_membership_plan',self::STEP), array(
	    // 	'meta_box' => false,
     //        'rewrite'  => array( 'slug' => 'programs' ),
     //        // 'show_ui' => false,
     //    ), $this::default_names()[self::PROGRAM] );

	}

	public function add_rewrite_tags() {

	    // Define desired permalink structure
	    // $post_type_rewrite = $this->get_program_base_slug() . '/%wampum_step_program%/%wampum_step%';

	    // Add rewrite functions
		add_rewrite_tag( '%wampum_program%', '([^/]+)' );
		add_rewrite_tag( '%wampum_step_program%', '([^/]+)' );
		// add_permastruct( 'wampum_step', $post_type_rewrite, false );

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
		// var_dump($post);
	    if ( self::PROGRAM === $post->post_type ) {
		    // $post_link = str_replace( "/{$post->post_type}/", '/', $post_link );
		    $post_link = str_replace( $post->post_type, $this->get_program_base_slug(), $post_link );
		    // $post_link = str_replace( '%wampum_program%', $this->get_program_base_slug(), $post_link );
	    }
	    if ( self::STEP === $post->post_type ) {
	    	// var_dump($post_link);
	    	// var_dump($this->get_step_program_slug($post->ID));
	    	// If no connected program, set base to 'step' since we're using template_redirect if no program anyway
    		// $slug = $this->get_step_program_slug($post) ? $this->get_step_program_slug($post) : 'step';
    		// $slug = $this->get_step_program_slug($post) ? $this->get_step_base_slug($post) : 'step';
			// var_dump($slug);
		    // $post_link = str_replace( '%wampum_program%', $slug, $post_link );
		    $post_link = str_replace( '%wampum_step_program%', $this->get_step_program_slug($post->ID), $post_link );
		    // $post_link = str_replace( $post->post_type, $slug, $post_link );
		    // $post_link = str_replace( $post->post_type, $this->get_step_program_slug($post->ID), $post_link );
	    	// var_dump($post_link);
	    }
	    return $post_link;
	}

	public function post_type_link( $post_link, $post = 0, $leavename = FALSE ) {
	    if ( strpos('%wampum_step_program%', $post_link ) === 'FALSE' ) {
			return $post_link;
	    }
	    if ( is_object($post) ) {
			$post_id = $post->ID;
	    } else {
			$post_id = $post;
			$post	 = get_post($post_id);
	    }
	    // Bail if not a post object or not a published post
	    if ( ! is_object($post) || 'publish' !== $post->post_status ) {
			return $post_link;
	    }
	    if ( $post->post_type === self::STEP ) {
			// $program	  = $this->get_step_base_slug($post_id);
			$program	  = $this->get_program_base_slug($post_id);
			$step_program = $this->get_step_program_slug($post_id);
			// $post_link	  = str_replace( '%wampum_program%', $program, $post_link );
			$post_link	  = str_replace( '%wampum_step_program%', $step_program, $post_link );
	    }
	    if ( $post->post_type === self::PROGRAM ) {
			$program	= $this->get_program_base_slug($post_id);
			$post_link	= str_replace( '%wampum_program%', $program, $post_link );
	    }
	    return $post_link;
	}

	public function single_step_redirect() {
	    if ( ! is_singular(self::STEP) ) {
	    	return;
	    }
	    if ( ! $this->get_step_program_slug( get_the_ID() ) ) {
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
	    // echo '<pre>';
	    // print_r($query);
	    // echo '</pre>';
	    // Only noop our very specific rewrite rule match
	    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
	        return $query;
	    }
	    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
	    if ( ! empty( $query->query['name'] ) ) {
	        $query->set( 'post_type', array( 'post', 'page', self::PROGRAM, self::STEP ) );
	    }
	    return $query;
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
			// echo '<pre>';
		    // print_r($program);
		    // echo '</pre>';
			return $program->ID;
		}
		return false;
	}

	public function get_step_program( $step_object_or_id ) {
		// $programs = p2p_type( 'programs_to_steps' )->set_direction( 'to' )->get_connected( $step_object_or_id );
		// if ( is_object($programs) ) {
		// 	return array_shift($programs->posts);
		// }

		// global $wampum_connections;
		// $items = $wampum_connections->get_adjacent_items( 'programs_to_steps', $step_object_or_id );
		$items = Wampum()->connections->get_adjacent_items( 'programs_to_steps', $step_object_or_id );
		// echo '<pre>';
	    // print_r($items);
	    // echo '</pre>';
		if ( $items['parent'] ) {
			// echo '<pre>';
		    // print_r($items['parent']);
		    // echo '</pre>';
		    // die();
			return $items['parent'];
		}
		return false;
	}

	// TODO: SIMILAR TO get_step_base_slug()
	public function get_program_base_slug() {
		$slug = sanitize_title_with_dashes(self::plural_name(self::PROGRAM));
		return apply_filters( 'wampum_program_base_slug', $slug );
	}


	public function get_step_base_slug($step_id) {
		$plural_name = sanitize_title_with_dashes($this->plural_name(self::STEP));
		$slug = $this->get_step_program_slug($step_id) ? $this->get_step_program_slug($step_id) : $plural_name;
		return apply_filters( 'wampum_step_base_slug', $slug );
	}

	// Switch to this?!?!?! http://www.billerickson.net/code/posts-2-posts-list-connections/
	public function get_program_steps_list($program_object_or_id) {
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

	function step_single_template($template){
		global $post;
	    if ( 'wampum_step' === $post->post_type  ) {
	    	$file = 'single-step.php';
	    	if ( file_exists( get_stylesheet_directory_uri() . 'wampum/' . $file ) ) {
	    		return get_stylesheet_directory_uri() . 'wampum/' . $file;
	    	}
	    	if ( file_exists( WAMPUM_TEMPLATES_DIR . $file ) ) {
	    		return WAMPUM_TEMPLATES_DIR . $file;
	    	}
	    }
	    return $template;
	}

	function step_archive_template($template){
		global $post;
	    if ( 'wampum_step' === $post->post_type  ) {
	    	$file = 'archive-step.php';
	    	if ( file_exists( get_stylesheet_directory_uri() . $file ) ) {
	    		return get_stylesheet_directory_uri() . $file;
	    	}
	    	if ( file_exists( WAMPUM_TEMPLATES_DIR . $file ) ) {
	    		return WAMPUM_TEMPLATES_DIR . $file;
	    	}
	    }
	    return $template;
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
