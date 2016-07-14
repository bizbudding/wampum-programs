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
 * @since   1.4.0
 *
 * @package Wampum
 */
class Wampum_Upgrade {

	/**
	 * @var Wampum_Upgrade The one true Wampum_Upgrade
	 * @since 1.4
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Upgrade;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	function init() {
		// if ( function_exists( 'p2p_register_connection_type' ) ) {
			// $this->create_upgrade_options_page();
			// add_filter( 'acf/update_value/key=field_5787af7ba4e69', array( $this, 'maybe_run_updater' ), 10, 3 );
			add_action( 'init', 		    						array( $this, 'register_post_types'), 0 );
			add_action( 'p2p_init', 								array( $this, 'register_p2p_connections' ) );
		// } else {
			// add_action( 'admin_notices', array( $this, 'wampum_upgrade_admin_notice' ) );
		// }
	}

	public function maybe_run_updater( $value, $post_id, $field ) {
		// if ( $value == false ) {
		// 	return $value;
		// }

		// Get Programs
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wampum_program',
			'post_status'      => array('publish','draft','future'),
			'suppress_filters' => true
		);
		$programs = get_posts($args);
		// Get Steps
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wampum_step',
			'post_status'      => array('publish','draft','future'),
			'suppress_filters' => true
		);
		$steps = get_posts($args);
		// Get Resources
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wampum_resource',
			'post_status'      => array('publish','draft','future'),
			'suppress_filters' => true
		);
		$resources = get_posts($args);

		$this->convert_steps_to_program_child_pages( $programs );
		$this->convert_resource_files_to_acf_repeater( $resources );
	}

	public function convert_steps_to_program_child_pages() {
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wampum_program',
			'post_status'      => array('publish','draft','future'),
			'suppress_filters' => true
		);
		$programs = get_posts($args);

		if ( ! $programs ) {
			return;
		}

		foreach ( $programs as $program ) {
			$program_id = $program->ID;
			$connected_steps = $this->get_connected_items( 'programs_to_steps', $program );
			foreach ( $connected_steps as $step ) {
				$data = array(
					'ID'			=> $step->ID,
					'post_type'		=> 'wampum_program',
					'post_parent'	=> $program_id,
				);
				// wp_update_post($data);
			}
		}
	}

	public function convert_resource_files_to_acf_repeater() {
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wampum_program',
			'post_status'      => array('publish','draft','future'),
			'suppress_filters' => true
		);
		$programs = get_posts($args);

		if ( ! $programs ) {
			return;
		}

		foreach ( $programs as $program ) {
			$old_resources = get_post_meta( $program->ID, 'wampum_resource_files', true );
			if ( is_array($old_resources) ) {
				$resource = $old_resources[0];
			} else {
				$resource = $old_resources;
			}
			if ( ! $resource ) {
				continue;
			}
			trace($resource);
			// https://www.advancedcustomfields.com/resources/update_row/
			$row = array(
				'file'	=> $resource,
			);
			// update_row( 'wampum_resource_files', 1, $row );
		}
	}

	public function create_upgrade_options_page() {
		if ( function_exists('acf_add_options_page') ) {
			acf_add_options_page(array(
				'page_title' => 'Wampum Upgrade P2P to program child pages',
				'menu_title' => 'Wampum Upgrade',
				'menu_slug'  => 'wampum_upgrade_child_pages',
				'capability' => 'manage_options',
				'redirect'	 => false
			));
		}
	}

	public function register_post_types() {
	    register_extended_post_type( 'wampum_step', array(
			'menu_icon'			=> 'dashicons-feedback',
		    'has_archive' 		=> false,
			'supports'			=> array('title','editor','excerpt','thumbnail','genesis-cpt-archives-settings'),
	    ), Wampum()->content->get_default_names()['wampum_step'] );
	}

	/**
	 * Register Posts to Posts connections
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_p2p_connections() {

		// STEPS TO PROGRAMS
	    p2p_register_connection_type( array(
	        'name'            => 'programs_to_steps',
	        'from'            => 'wampum_program',
	        'to'              => 'wampum_step',
	        'cardinality' 	  => 'one-to-many',
	        'can_create_post' => false,
	        'sortable'        => 'any',
	        'admin_box'       => array(
				'show'		=> 'any',
				'context'	=> 'side',
			),
	        'admin_column'   => true,
	        'admin_dropdown' => true,
	        'title'          => array(
	            'from' => 'Steps',
	            'to'   => wampum_get_singular_name('wampum_program'),
	        ),
	        'from_labels' => array(
	            'singular_name' => wampum_get_plural_name('wampum_program'),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . wampum_get_singular_name('wampum_program'),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to Step', 'wampum' ),
	        ),
	        'fields' => apply_filters( 'wampum_programs_to_steps_connection_fields', array() ),
	    ) );

	    // RESOURCES TO STEPS
	    p2p_register_connection_type( array(
	        'name'            => 'steps_to_resources',
	        'from'            => 'wampum_step',
	        'to'              => 'wampum_resource',
	        'can_create_post' => false,
	        'sortable'        => 'any',
	        'admin_box'       => array(
				'show'		=> 'any',
				'context'	=> 'side',
			),
	        'admin_column'   => true,
	        'admin_dropdown' => true,
	        'title'          => array(
	            'from' => wampum_get_plural_name('wampum_resource'),
	            'to'   => 'Steps',
	        ),
	        'from_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to Step', 'wampum' ),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect a ', 'wampum' ) . wampum_get_singular_name('wampum_resource'),
	        ),
	        'fields' => apply_filters( 'wampum_steps_to_resources_connection_fields', array() ),
	    ) );

	    // RESOURCES TO PROGRAMS
	    p2p_register_connection_type( array(
	        'name'            => 'programs_to_resources',
	        'from'            => 'wampum_program',
	        'to'              => 'wampum_resource',
	        'can_create_post' => false,
	        'sortable'        => 'any',
	        'admin_box'       => array(
				'show'		=> 'any',
				'context'	=> 'side',
			),
	        'admin_column'   => true,
	        'admin_dropdown' => true,
	        'title'          => array(
	            'from' => wampum_get_singular_name('wampum_program') . ' ' . wampum_get_plural_name('wampum_resource'),
	            'to'   => wampum_get_plural_name('wampum_program'),
	        ),
	        'from_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect a ', 'wampum' ) . wampum_get_singular_name('wampum_program'),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . wampum_get_singular_name('wampum_resource'),
	        ),
	        'fields' => apply_filters( 'wampum_programs_to_resources_connection_fields', array() ),
	    ) );

	    // USERS TO STEPS (incomplete/complete or viewed)
	    p2p_register_connection_type( array(
			'name'		=> 'user_step_progress',
			'from'		=> 'user',
			'to'		=> 'wampum_step',
			'admin_box'	=> array(
				'show' => false,
			),
	        'admin_column'   => false,
	        'admin_dropdown' => false,
	    ) );

	}

	public function wampum_upgrade_admin_notice() {
	    ?>
	    <div class="notice notice-error">
	        <p><?php _e( 'Wampum upgrade requires Posts to Posts plugin to be active! Upgrade will fail.', 'wampum' ); ?></p>
	    </div>
	    <?php
	}

}
