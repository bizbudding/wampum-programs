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
 * @since   1.0.0
 *
 * @package Wampum
 */
class Wampum_Connections {

	/**
	 * @var Wampum_Connections The one true Wampum_Connections
	 * @since 1.4
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Connections;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	function init() {
		add_action( 'p2p_init', 			 array( $this, 'register_p2p_connections' ) );
		add_action( 'wp',			 		 array( $this, 'step_query_adjacent' ) );
		add_action( 'wp',			 		 array( $this, 'step_query_connections' ) );
		add_action( 'wp',			 		 array( $this, 'program_query_connections' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'piklist_save_field-connect_resource_to_step', array( $this, 'connect_resource_to_step' ), 10, 1 );
		// Plugins | Filters
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'program_in_yoast_breadcrumbs' ), 10, 1 );
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
	            'from' => Wampum_Content_Types::plural_name('wampum_step'),
	            'to'   => Wampum_Content_Types::singular_name('wampum_program'),
	        ),
	        'from_labels' => array(
	            'singular_name' => Wampum_Content_Types::plural_name('wampum_program'),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_program'),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_step'),
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
	            'from' => Wampum_Content_Types::singular_name('wampum_step') . ' ' . Wampum_Content_Types::plural_name('wampum_resource'),
	            'to'   => Wampum_Content_Types::plural_name('wampum_step'),
	        ),
	        'from_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_step'),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect a ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_resource'),
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
	            'from' => Wampum_Content_Types::singular_name('wampum_program') . ' ' . Wampum_Content_Types::plural_name('wampum_resource'),
	            'to'   => Wampum_Content_Types::plural_name('wampum_program'),
	        ),
	        'from_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect a ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_program'),
	        ),
	        'to_labels' => array(
	            'singular_name' => __( 'Item', 'wampum' ),
	            'search_items'  => __( 'Search items', 'wampum' ),
	            'not_found'     => __( 'No items found.', 'wampum' ),
	            'create'        => __( 'Connect to ', 'wampum' ) . Wampum_Content_Types::singular_name('wampum_resource'),
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

	    // USERS TO POSTS (Likes)
	    p2p_register_connection_type( array(
			'name'		=> 'user_post_likes',
			'from'		=> 'user',
			'to'		=> 'post',
			'admin_box'	=> array(
				'show' => false,
			),
	        'admin_column'   => false,
	        'admin_dropdown' => false,
	    ) );

	    // USERS TO POSTS (Bookmarks)
	    p2p_register_connection_type( array(
			'name'		=> 'user_post_bookmarks',
			'from'		=> 'user',
			'to'		=> 'post',
			'admin_box'	=> array(
				'show' => false,
			),
	        'admin_column'   => false,
	        'admin_dropdown' => false,
	    ) );

	}

	public function get_steps_from_program_query( $queried_object ) {
		return isset($queried_object->steps) ? $queried_object->steps : false;
	}

	public function get_steps_from_step_query( $queried_object ) {
		$step_program = $this->get_program_from_step_query($queried_object);
		return isset($step_program->steps) ? $step_program->steps : false;
	}

	public function get_program_from_step_query( $queried_object ) {
		return isset($queried_object->programs) ? $queried_object->programs[0] : false;
	}

	/**
	 * Add adjacent items to step query
	 *
	 * @since  1.0.0
	 *
	 * @return object default wp_query
	 */
	public function step_query_adjacent() {
		if ( ! is_singular( 'wampum_step' ) ) {
			return;
		}
		global $wp_query, $post;
		$items = array();
		if ( $adjacent = p2p_type( 'programs_to_steps' )->get_adjacent_items( $post ) ) {
			$items = $adjacent;
		}
	    $wp_query->adjacent = $items;
	    // echo '<pre>';
	    // print_r($wp_query);
	    // echo '</pre>';
	}

	/**
	 * Add program(s) to step query
	 *
	 * @since  1.0.0
	 *
	 * @return object default wp_query
	 */
	public function step_query_connections() {
		if ( ! is_singular( 'wampum_step' ) ) {
			return;
		}
		global $wp_query, $post;
		p2p_type( 'programs_to_steps' )->each_connected( $wp_query, array(), 'programs' );
		while ( $wp_query->have_posts() ) : $wp_query->the_post();
		    // Another level of nesting
		    p2p_type( 'programs_to_steps' )->each_connected( $post->programs, array(), 'steps' );
		    // Reset
		    wp_reset_postdata();
		endwhile;
	}

	/**
	 * Add steps to program query
	 *
	 * @since  1.0.0
	 *
	 * @return object default wp_query
	 */
	public function program_query_connections() {
		if ( ! is_singular( 'wampum_program' ) ) {
			return;
		}
		global $wp_query;
		p2p_type( 'programs_to_steps' )->each_connected( $wp_query, array(), 'steps' );
	}

	/**
	 * Register Javascript file(s)
	 * Use via wp_enqueue_script('wampum-select2'); in piklist files
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'wampum-select2',  WAMPUM_PLUGIN_URL . 'js/select2.min.js', array( 'jquery' ), '4.0.1', true );
	}

	/**
	 * Register CSS file(s)
	 * Use via wp_enqueue_style('wampum-select2'); in piklist files
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( 'wampum-select2', WAMPUM_PLUGIN_URL . 'css/select2.min.css', array(), '4.0.1' );
	}

	/**
	 * Create a resource and connect it to a step
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $fields  the piklist field values
	 *
	 * @return void|WP_Error
	 */
	public static function connect_resource_to_step( $fields ) {

		// Value of step_id field from /parts/meta-boxes/wampum_step.php
	    $to = absint($fields['step_id']['value']);

	    // Existing resources field check
	    $existing_resources = isset($fields['existing_resources']['request_value']) ? $fields['existing_resources']['request_value'] : false;

	    if ( $existing_resources ) {
			foreach ( $existing_resources as $from ) {
				$this->connect( 'steps_to_resources', $from, $to );
			}
	    }

	    // New resources field check
	    $new_resources = isset($fields['add_resource']['request_value']) ? $fields['add_resource']['request_value'] : false;

	    // trace($new_resources);

	    if ( $new_resources ) {

			foreach ( $new_resources as $resource ) {

				// Title field check
				$title = isset($resource['post_title']) ? sanitize_text_field($resource['post_title']) : false;

				// If title is false, skip this item in the loop
				if ( ! $title ) {
					continue;
				}

				// Content and files field checks
				$content = isset($resource['post_content']) ? sanitize_text_field($resource['post_content']) : false;
				$files   = isset($resource['resource_files']) ? $resource['resource_files'] : false;

				// Create new resource
				$data = array(
					'post_type'		=> 'wampum_resource',
					'post_status'	=> 'publish',
					'post_title'	=> $title,
					'post_content'	=> $content,
				);
				$from = wp_insert_post( $data, true );

				if ( ! is_wp_error( $from ) ) {
					if ( $files ) {
						// Add media to the new post's meta
						update_post_meta( $from, 'wampum_resource_files', $files );
					}
					// Connect new post to topic
					$this->connect( 'steps_to_resources', $from, $to );
				}

			}
		}
	}

    /**
     * Check if a connection exists
     *
     * @since   1.0.0
     *
     * @param  	string  $from  object connecting from
     * @param   string  $to    object connecting to
     *
     * @return  bool
     */
    public static function connection_exists( $type, $from, $to ) {
		return p2p_connection_exists( $type, array('from' => $from, 'to' => $to) );
    }

	/**
	 * Connect one object to another
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $type  p2p connection type
	 * @param  int     $from  object ID getting connected from
	 * @param  int     $to    object ID getting connected to
	 *
	 * @return int|WP_Error   connection ID or error
	 */
	public function connect( $type, $from, $to ) {
		$p2p = p2p_type( $type )->connect( $from, $to, array(
		    'date' => current_time('mysql')
		));
		return $p2p;
	}

	/**
	 * Disonnect one object from another
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $type  p2p connection type
	 * @param  int     $from  object ID getting connected from
	 * @param  int     $to    object ID getting connected to
	 *
	 * @return bool|WP_Error   true|1 or error
	 */
	public function disconnect( $type, $from, $to ) {
		$p2p = p2p_type( $this->connection_name )->disconnect( $from, $to );
		return $p2p;
	}

	public function connection_button( $type, $from, $to, $text_connect, $text_connected ) {
		$class = ' connect';
		$text  = $text_connect;
		if ( $this->connection_exists( $type, $from, $to ) ) {
			$class = ' connected';
			$text  = $text_connected;
		}
		return '<div class="wampum-connection-wrap"><a data-from-id="' . get_current_user_ID() . '" data-to-id="' . get_the_ID() . '" class="button wampum-connection' . $class . '" href="#">' . $text . '</a></div>';
	}

	public function prev_next_connection_links( $connection, $post_id ) {
		echo $this->get_prev_next_connection_links( $connection, $post_id );
	}

	public function get_prev_next_connection_links( $connection, $post_id ) {
		// Let's get it started
		$output = '';
		// Get parent, previous, and next connected posts
		$items = $this->get_adjacent_items( $connection, $post_id );
		// Bail if none
		if ( ! $items ) {
			return $output;
		}
		// Set markup for links
		$prev = $items['previous'] ? '<div class="pagination-previous alignleft"><a href="' . get_permalink( $items['previous'] ) . '">' . get_the_title( $items['previous'] ) . '</a></div>' : '';
		$next = $items['next'] ? '<div class="pagination-next alignright"><a href="' . get_permalink( $items['next'] ) . '">' . get_the_title( $items['next'] ) . '</a></div>' : '';
		// If previous or next link
		if ( $prev || $next ) {
			$output .= '<div class="wampum-pagination">';
			$output .= $prev . $next;
			$output .= '</div>';
		}
		// Send it home baby
		return $output;
	}

	public function get_adjacent_items( $connection, $post_id ) {
		if ( 'wampum_step' === get_post_type($post_id) ) {
			global $wp_query;
			$items = $wp_query->adjacent;
		} else {
			$items = p2p_type($connection)->get_adjacent_items($post_id);
		}
		// Bail if none
		if ( $items ) {
			return $items;
		}
		return false;
	}

	public function get_connected_items( $connection, $object_or_id ) {
		$connected = get_posts( array(
			'connected_type'	=> $connection,
			'connected_items'	=> $object_or_id,
			'nopaging'			=> true,
			'suppress_filters'	=> false,
		) );
		if ( $connected ) {
			return $connected;
		}
		return false;
	}

	// https://gist.github.com/QROkes/62e07eb167089c366ab9
	public function program_in_yoast_breadcrumbs( $links ) {
		// Bail if not singular step
		if ( ! is_singular('wampum_step') ) {
			return $links;
		}
		// Get step program
		$step_program = Wampum()->connections->get_program_from_step_query( get_queried_object() );
		if ( $step_program ) {
		    $new[] = array(
		        'url'  => get_the_permalink( $step_program->ID ),
		        'text' => get_the_title( $step_program->ID ),
		    );
		    // Remove middle item and add our new one in its place
		    array_splice( $links, 1, -1, $new );
		}
	    return $links;
	}

	// public function get_user_connected_items( $connection, $user_object_or_id ) {
	// 	$items = get_posts( array(
	// 		'connected_type'	=> $connection,
	// 		'connected_items'	=> $user_object_or_id,
	// 		'nopaging'			=> true,
	// 		'suppress_filters'	=> false,
	// 	) );
	// 	if ( $items ) {
	// 		return $items;
	// 	}
	// 	return false;
	// }

}
