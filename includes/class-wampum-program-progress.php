<?php
/**
 * Wampum Settings
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
 * Global settings for Wampum
 *
 * @package Wampum_Program_Progress
 * @author  Mike Hemberger
 */
final class Wampum_Program_Progress {

	/** Singleton *************************************************************/

	/**
	 * @var   Wampum_Program_Progress The one true Wampum_Program_Progress
	 * @since 1.0.0
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Program_Progress;
			// Methods
			self::$instance->init();
		}
		return self::$instance;
	}

	protected $messages = array(
			'connect_success'		=> 'Successfully Connected!',
			'disconnect_success'	=> 'Successfully Disconnected!',
			'can_connect_fail'		=> 'An error occurred during connection.',
			'can_disconnect_fail'	=> 'An error occurred during disconnect.',
		);

	/**
	 * Register custom endpoints
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function init() {
		// http://v2.wp-api.org/extending/modifying/
		add_action( 'rest_api_init', array( $this, 'register_step_progress_fields' ) );
	}

	public function get_step_progress_button( $type, $from, $to, $text_connect, $text_connected ) {
		$class = ' connect';
		$text  = $text_connect;
		// $data  = array(
		// 		'from' => $from,
		// 		'to'   => $to,
		// 	);
		if ( $this->connection_exists( $from, $to ) ) {
			$class = ' connected';
			$text  = $text_connected;
		}
		return '<div class="wampum-connection-wrap"><a data-from-id="' . $from . '" data-to-id="' . $to . '" class="button wampum-connection' . $class . '" href="#">' . $text . '</a></div>';
	}

    /**
     * Register our AJAX & other JavaScript/CSS files.
     *
	 * @since  1.0.0
	 *
	 * @return void
     */
    public function register_script() {
        wp_register_script(  'wampum-step-progress', $this->script_url, array('jquery'), '1.0.0', true );
    }

    /**
     * Localize script to pass php data to js
     *
	 * @since  1.0.0
	 *
	 * @return void
     */
    public function localize_script() {
        wp_localize_script( 'wampum-step-progress', 'wampum_step_progress_vars', $this->get_ajax_data() );
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
        return array(
			'root'		=> esc_url_raw( rest_url() ),
			'nonce'		=> wp_create_nonce( 'wp_rest' ),
			// 'success'	=> true,
			// 'failure'	=> false,
        );
    }

    /**
     * Add conditional checks when to enqueue scripts
     *
     * @since  1.0.0
     *
     * @return array
     */
    public function enqueue_scripts() {
    	// Conditional checks when to enqueue
    	wp_enqueue_script( $this->connection_name );
    }

	function register_step_progress_fields() {
	    register_api_field( 'user',
	        'wampum_completed_steps',
	        array(
	            'get_callback'    => array( $this, 'get_completed_steps' ),
	            'update_callback' => array( $this, 'update_completed_steps' ),
	            'schema'          => null,
	        )
	    );
	}

	/**
	 * Get the value of the "wampum_completed_steps" field
	 *
	 * @since
	 *
	 * @param array   		   $object 	  	Details of current post.
	 * @param string  		   $field_name  Name of field.
	 * @param WP_REST_Request  $request 	Current request
	 *
	 * @return mixed
	 */
	function get_completed_steps( $object, $field_name, $request ) {
	    return get_post_meta( $object[ 'id' ], $field_name, true );
	}

	/**
	 * Handler for adding a step to the 'wampum_completed_steps' array
	 *
	 * @since
	 *
	 * @param mixed   $value 	   The value of the field
	 * @param object  $object 	   The object from the response
	 * @param string  $field_name  Name of field
	 *
	 * @return bool
	 */
	function update_completed_steps( $value, $object, $field_name ) {
	    if ( ! $value || ! is_numeric( $value ) ) {
	        return;
	    }
	    $completed_steps = get_post_meta( $object->ID, $field_name, true );
	    // Bail if step ID is already connected
	    if ( in_array( $value, $completed_steps ) ) {
	    	return;
		}
	    $completed_steps = array_push( $completed_steps, $value );
	    return update_post_meta( $object->ID, $field_name, $completed_steps );
	}

}
