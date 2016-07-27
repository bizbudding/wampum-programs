<?php
/**
 * Wampum
 *
 * @package   Wampum_Program_Progress
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Restfully connect a user to a step
 *
 * @author  Mike Hemberger
 */
class Wampum_Program_Progress {

	/**
	 * Name of the p2p connection.
	 *
	 * @since 1.0.0
	 */
	protected $connection_name = 'user_program_progress';

	/**
	 * @since 1.0.0
	 *
	 * @var Wampum_Program_Progress The one true Wampum_Program_Progress
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Program_Progress;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Initiate all the things
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'p2p_init', 		  array( $this, 'register_p2p_connections' ) );
		add_action( 'rest_api_init', 	  array( $this, 'restful_p2p_register_rest_endpoint' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Register Posts to Posts connections
	 * Users to Programs (incomplete/complete or viewed)
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function register_p2p_connections() {
	    p2p_register_connection_type( array(
			'name'		=> $this->connection_name,
			'from'		=> 'user',
			'to'		=> 'wampum_program',
			'admin_box'	=> array(
				'show' => false,
			),
	        'admin_column'   => false,
	        'admin_dropdown' => false,
	    ) );
	}

	/**
	 * Register rest endpoint
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function restful_p2p_register_rest_endpoint() {

	    register_rest_route( 'restful-p2p/v1', '/connect/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'connect' ),
			'args'	   => array(
	            'from' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	            'to' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	        ),
	    ));

	    register_rest_route( 'restful-p2p/v1', '/disconnect/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'disconnect' ),
			'args'	   => array(
	            'from' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	            'to' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	        ),
	    ));
	}

	/**
	 * Register and localize script to be enqueued later
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
    public function register_scripts() {
        wp_register_script( $this->connection_name, WAMPUM_PLUGIN_URL . '/js/program-progress.js', array('jquery'), '1.0.0', true );
        wp_localize_script( $this->connection_name, 'restful_p2p_connection_vars', $this->get_ajax_data() );
    }

	/**
	 * Helper function to enqueue scripts
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
    public function enqueue_scripts() {
    	wp_enqueue_script( $this->connection_name );
    }

    /**
     * Get the AJAX data that WordPress needs to output.
     * Used in wp_localize_script
     *
     * @since  1.0.0
     *
     * @return array
     */
    private function get_ajax_data() {
		$post_id = get_the_ID();
		if ( wampum_is_step( $post_id ) ) {
			$program_id = wampum_get_step_program_id( $post_id );
		} else {
			$program_id = $post_id;
		}
        return array(
			'root'				=> esc_url_raw( rest_url() ),
			'nonce'				=> wp_create_nonce( 'wp_rest' ),
			'connect_text'		=> $this->get_connect_text($program_id),
			'connected_text'	=> $this->get_connected_text($program_id)
        );
    }

	/**
	 * Conditionally get the progress link/button
	 * This function is used in functions-display.php
	 *
	 * @since  1.0.0
	 *
	 * @param  $program_or_step_id  wampum_program ID
	 *
	 * @return string|null  connection link with data attributes for from/to
	 */
	public function get_program_progress_link( $program_or_step_id ) {
		if ( ! is_user_logged_in() ) {
			return;
		}
		if ( wampum_is_step( $program_or_step_id ) ) {
			$program_id = wampum_get_step_program_id( $program_or_step_id );
		} else {
			$program_id = $program_or_step_id;
		}
		if ( ! wampum_is_program_progress_enabled($program_id) ) {
			return;
		}
		return $this->get_link( get_current_user_id(), $program_or_step_id, $this->get_connect_text($program_id), $this->get_connected_text($program_id) );
	}

	/**
	 * Get the text to be displayed when not connected
	 *
	 * @since  1.0.0
	 *
	 * @param  $post_id  The post ID to check
	 *
	 * @return string 	 The button text
	 */
	public function get_connect_text( $post_id ) {
		$connect = get_post_meta( $post_id, 'wampum_program_progress_complete_text', true );
		if ( empty($connect) ) {
			$connect = __( 'Mark Complete', 'wampum' );
		}
		return $connect;
	}

	/**
	 * Get the text to be displayed when connected
	 *
	 * @since  1.0.0
	 *
	 * @param  $post_id  The post ID to check
	 *
	 * @return string 	 The button text
	 */
	public function get_connected_text( $post_id ) {
		$connected = get_post_meta( $post_id, 'wampum_program_progress_completed_text', true );
		if ( empty($connected) ) {
			$connected = __( 'Completed', 'wampum' );
		}
		return $connected;
	}

	/**
	 * Get the link to connect objects
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $from                 The ID of the from object
	 * @param  integer  $to                   The ID of the to object
	 * @param  string   $link_connect_text    The connect text
	 * @param  string   $link_connected_text  The connected text
	 *
	 * @return string
	 */
	public function get_link( $from, $to, $link_connect_text, $link_connected_text ) {
		$this->enqueue_scripts();
		return $this->get_connection_link_html( $from, $to, $link_connect_text, $link_connected_text );
	}

	/**
	 * Get the html link to connect objects
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $from                 The ID of the from object
	 * @param  integer  $to                   The ID of the to object
	 * @param  string   $link_connect_text    The connect text
	 * @param  string   $link_connected_text  The connected text
	 *
	 * @return string
	 */
	public function get_connection_link_html( $from, $to, $text_connect, $text_connected ) {
		$class = ' connect';
		$text  = $text_connect;
		if ( $this->connection_exists( $from, $to ) ) {
			$class = ' connected';
			$text  = $text_connected;
		}
		return '<div class="wampum-program-progress"><a data-from-id="' . $from . '" data-to-id="' . $to . '" class="button progress-button' . $class . '" href="#0">' . $text . '</a></div>';
	}

	/**
	 * Connect 2 objects
	 *
	 * @since   1.0.0
	 *
	 * @param 	array  $data  Associative array containing 'from' and 'to' connection IDs
	 *
	 * @return  array
	 */
	public function connect( $data ) {
		// Create connection
		$p2p = p2p_type( $this->connection_name )->connect( $data['from'], $data['to'], array(
		    'date' => current_time('mysql')
		) );
		// Success/Fail
		if ( is_wp_error( $p2p ) ) {
			// Fail
			return array(
				'success' => false,
				'message' => $p2p->get_error_message(),
			);
		} else {
			// Success
			return array(
				'success' => true,
			);
		}
	}

	/**
	 * Disconnect 2 objects
	 *
	 * @since   1.0.0
	 *
	 * @param 	array  $data  Associative array containing 'from' and 'to' connection IDs
	 *
	 * @return  array
	 */
	public function disconnect( $data ) {
		// Remove connection
		$p2p = p2p_type( $this->connection_name )->disconnect( $data['from'], $data['to'] );
		// Success/Fail
		if ( is_wp_error( $p2p ) ) {
			// Fail
			return array(
				'success' => false,
				'message' => $p2p->get_error_message(),
			);
		} else {
			// Success
			return array(
				'success' => true,
			);
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
    public function connection_exists( $from, $to ) {
		$data = array(
				'from' => $from,
				'to'   => $to,
			);
		return p2p_connection_exists( $this->connection_name, $data );
    }

    /**
     * Get the post ID sent by the AJAX request.
     *
     * @return int
     */
    private function get_from() {
        return $this->sanitize_id( $_POST['from'] );
    }


    /**
     * Get the post ID sent by the AJAX request.
     *
     * @return int
     */
    private function get_to() {
        return $this->sanitize_id( $_POST['to'] );
    }


    /**
     * Sanitize an ID sent by the AJAX request.
     *
     * @param   $id [<description>]
     *
     * @return int
     */
    private function sanitize_id( $id ) {
    	$post_id = 0;
        if ( isset($id) ) {
            $post_id = absint(filter_var($id, FILTER_SANITIZE_NUMBER_INT));
        }
        return $post_id;
    }
}
