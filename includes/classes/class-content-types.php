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
	 * @var    Wampum_Content_Types The one true Wampum_Content_Types
	 * @since  1.0.0
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
		// Filters
		add_filter( 'mai_cpt_settings_post_types', array( $this, 'mai_post_types' ) );
		// Actions
		add_action( 'init',                        array( $this, 'register_post_types'), 0 );
		add_action( 'wp_head',                     array( $this, 'do_template_functions' ) );
	}

	public function mai_post_types( $post_types ) {
		$post_types['wampum_program'] = get_post_type_object( 'wampum_program' );
		return $post_types;
	}

	/**
	 * Register custom post stypes
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_post_types() {

		/***********************
		 *  Custom Post Types  *
		 ***********************/

		$labels = $this->get_default_names();
		$labels = $labels['wampum_program'];

		register_post_type( 'wampum_program',
			apply_filters( 'wampum_program_args', array(
				'exclude_from_search' => true,
				'has_archive'         => apply_filters( 'wampum_program_has_archive', false ),
				'hierarchical'        => true,
				'labels'              => array(
					'name'               => $labels['plural'],
					'singular_name'      => $labels['singular'],
					'menu_name'          => $labels['plural'],
					'name_admin_bar'     => $labels['singular'],
					'add_new'            => _x( 'Add New', 'wampum programs' , 'wampum-programs' ),
					'add_new_item'       => sprintf( __( 'Add New %s', 'wampum-programs' ), $labels['singular'] ),
					'new_item'           => sprintf( __( 'New %s' , 'wampum-programs' ), $labels['singular'] ),
					'edit_item'          => sprintf( __( 'Edit %s' , 'wampum-programs' ), $labels['singular'] ),
					'view_item'          => sprintf( __( 'View %s' , 'wampum-programs' ), $labels['singular'] ),
					'all_items'          => sprintf( __( 'All %s', 'wampum-programs' ), $labels['singular'] ),
					'search_items'       => sprintf( __( 'Search %s', 'wampum-programs' ), $labels['singular'] ),
					'parent_item_colon'  => sprintf( __( 'Parent %s:', 'wampum-programs' ), $labels['singular'] ),
					'not_found'          => sprintf( __( 'No %s found.', 'wampum-programs' ), $labels['singular'] ),
					'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'wampum-programs' ), $labels['singular'] ),
				),
				'menu_icon'          => 'dashicons-feedback',
				'public'             => true,
				'publicly_queryable' => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_ui'            => true,
				'rewrite'            => array( 'slug' => $labels['slug'], 'with_front' => false ),
				'supports'           => apply_filters( 'wampum_program_supports', array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'genesis-cpt-archives-settings', 'genesis-layouts' ) ),
				// 'taxonomies'         => array( 'wampum_program_template' ),
			)
		));

		/***********************
		 *  Custom Taxonomies  *
		 ***********************/

		register_taxonomy( 'wampum_program_template', 'wampum_program',
			apply_filters( 'wampum_program_template_args', array(
				'exclude_from_search' => true,
				'has_archive'         => false,
				'hierarchical'        => true,
				'labels' => array(
					'name'                       => _x( 'Templates'                           , 'wampum programs template general name', 'wampum-programs' ),
					'singular_name'              => _x( 'Template'                            , 'wampum programs template singular name' , 'wampum-programs' ),
					'search_items'               => __( 'Search Templates'                    , 'wampum-programs' ),
					'popular_items'              => __( 'Popular Templates'                   , 'wampum-programs' ),
					'all_items'                  => __( 'All Categories'                      , 'wampum-programs' ),
					'edit_item'                  => __( 'Edit Template'                       , 'wampum-programs' ),
					'update_item'                => __( 'Update Template'                     , 'wampum-programs' ),
					'add_new_item'               => __( 'Add New Template'                    , 'wampum-programs' ),
					'new_item_name'              => __( 'New Template Name'                   , 'wampum-programs' ),
					'separate_items_with_commas' => __( 'Separate Templates with commas'      , 'wampum-programs' ),
					'add_or_remove_items'        => __( 'Add or remove Templates'             , 'wampum-programs' ),
					'choose_from_most_used'      => __( 'Choose from the most used Templates' , 'wampum-programs' ),
					'not_found'                  => __( 'No Templates found.'                 , 'wampum-programs' ),
					'menu_name'                  => __( 'Templates'                           , 'wampum-programs' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				),
				'public'            => false,
				'rewrite'           => false,
				'show_admin_column' => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false,
				'show_ui'           => true,
			)
		));

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
			'taxonomy'   => 'wampum_program_template',
			'fields'     => 'id=>slug',
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
		return apply_filters( 'wampum_content_default_names', array(
			'wampum_program' => array(
				'singular' => _x( 'Program', 'wampum' ),
				'plural'   => _x( 'Programs', 'wampum' ),
				'slug'     => _x( 'programs', 'wampum' ),
			)
		) );
	}

}
