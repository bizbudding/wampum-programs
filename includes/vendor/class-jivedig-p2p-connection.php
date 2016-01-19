<?php
/**
 * Posts to Posts Connections via WP-API
 *
 * @package   JiveDig_Connections
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 * @version   1.0.0
 */

if ( ! class_exists( 'JiveDig_Connections' ) )  {
	/**
	 * P2P Connections.
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package JiveDig_Connections
	 * @author  Mike Hemberger
	 */
	class JiveDig_Connections {
		/**
		 * Name of the p2p connection.
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $connection_name = 'posts_to_pages';

		/**
		 * Object name for 'to'.
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $connection_to = 'post';

		/**
		 * Object name for 'from'.
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $connection_from = 'page';

		/**
		 * Location of the JS file you will need to create.
		 *
		 * @since 1.1.0
		 *
		 * @type string
		 */
		protected $script_location = plugin_dir_url( __FILE__ ) . '/' . $this->connection_name . '.js';

		/**
		 * Retrieve a template part.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function run() {
			add_action( 'p2p_init', 	 array( $this, 'register_p2p_connection' ) );
			add_action( 'rest_api_init', array( $this, 'register_rest_endpoint_create' ) );
			add_action( 'rest_api_init', array( $this, 'register_rest_endpoint_delete' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Register p2p connection
		 *
		 * @since  1.0.0
		 *
		 * @return voide
		 */
		public function register_p2p_connection() {
			p2p_register_connection_type( array(
				'name'				=> $this->connection_name,
				'from'				=> $this->connection_from,
				'to'				=> $this->connection_to,
				'can_create_post'	=> false,
				'admin_box'			=> array(
					'show'		=> 'to',		 // any, from, to, false
					'context'	=> 'side',  	 // side, advanced
				),
			) );
		}

		/**
		 * Add custom endpoint to add connection
		 *
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function register_rest_endpoint_create() {
		    register_rest_route( 'jdp2p/v1', '/create-connection/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'create_connection' ),
				'args'	   => array(
		            'from_id' => array(
						'validate_callback' => 'is_numeric'
		            ),
		            'to_id' => array(
		                'validate_callback' => 'is_numeric'
		            ),
		        ),
		    ));
		}

		/**
		 * Add custom endpoint to remove connection
		 *
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function register_rest_endpoint_delete() {
		    register_rest_route( 'beatminded/v1', '/delete-connection/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'delete_connection' ),
				'args'	   => array(
		            'from_id' => array(
						'validate_callback' => 'is_numeric'
		            ),
		            'to_id' => array(
		                'validate_callback' => 'is_numeric'
		            ),
		        ),
		    ));
		}

		/**
		 * Delete connection
		 *
		 * @since   1.0.0
		 *
		 * @param 	array $data Options for the function.
		 * @return  int|object
		 */
		public function create_connection( $data ) {

			// Send error if connection is already made
			if ( $this->connection_exists( $data['from_id'], $data['to_id'] ) ) {
				return array(
					'success' => false,
					'message' => 'Beat is already in this Collection!',
				);
			}

			// Create connection
			$p2p = p2p_type( $this->connection_name )->connect( $data['from_id'], $data['to_id'], array(
			    'date' => current_time('mysql')
			) );

			if ( is_wp_error( $p2p ) ) {

				return array(
					'success' => false,
					'message' => $collection_id->get_error_message(),
				);

			} else {

				return array(
					'success' => true,
					'id'	  => $p2p,
					'message' => '',
				);

			}

		}

		public function delete_connection( $data ) {

			// Send error if there is no existing connection
			if ( ! $this->connection_exists( $data['from_id'], $data['to_id'] ) ) {
				return array(
					'success' => false,
					'message' => '',
				);
			}

			// Remove connection
			$p2p = p2p_type( $this->connection_name )->disconnect( $data['from_id'], $data['to_id'] );

			// If error, return WP_Error
			if ( is_wp_error( $p2p ) ) {
				return array(
					'success' => false,
					'message' => '',
				);
			}

			return array(
				'success' => true,
				'message' => '',
			);
		}

	    /**
	     * Register our AJAX & other JavaScript/CSS files.
	     *
		 * @since  1.0.0
		 *
		 * @return void
	     */
	    public function enqueue_scripts() {
	        wp_enqueue_script(  $this->connection_name, $this->script_location, array(), '1.0.0', true );
	        wp_localize_script( $this->connection_name, 'beatm_collections_vars', $this->get_ajax_data() );
	    }

	   /**
	     * Get the AJAX data that WordPress needs to output.
	     * Used in wp_localize_script
	     *
	     * @return array
	     */
	    private function get_ajax_data() {

	        return array(
				'root'		=> esc_url_raw( rest_url() ),
				'nonce'		=> wp_create_nonce( 'wp_rest' ),
				'success'	=> __( 'Success', 'jivedig' ),
				'failure'	=> __( 'Your submission could not be processed.', 'jivedig' ),
				// 'to_id'		=> get_the_ID(), // the is used to get collection ID on single collections
	        );
	    }

	    /**
	     * Get the post ID sent by the AJAX request.
	     *
	     * @return int
	     */
	    private function get_from_id() {
	        return $this->sanitize_id( $_POST['from_id'] );
	    }


	    /**
	     * Get the post ID sent by the AJAX request.
	     *
	     * @return int
	     */
	    private function get_to_id() {
	        return $this->sanitize_id( $_POST['to_id'] );
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
}
