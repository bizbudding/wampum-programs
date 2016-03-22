<?php
/**
 * Posts to Posts Connections via WP-API
 *
 * @package   P2P_Restful_Connection
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger <mike@thestizmedia.com>
 * @license   GPL-2.0+
 * @version   1.0.0
 */

/**
 * Steps To Extend This Class
 *
 * 1. Register your p2p connection
 * 2. Create new child class with properties below
 * 3. Optionally override any methods you want
 * 4. Create/modify js file at $script_url location to handle ajax
 * 5. Add link with appropriate data attributes where you want to make the connection
 *    Example:
 *    <div class="connection-wrap"><a data-from-id="<?php echo $from_id; ?>" data-to-id="<?php echo $to_id; ?>" class="button connection" href="#"><?php echo $text; ?></a></div>
 *
 */

if ( ! class_exists( 'P2P_Restful_Connection' ) )  {
	/**
	 * P2P Connections.
	 *
	 * When using in a plugin/theme, create a new class that extends this one and just overrides the properties
	 * Optionally
	 *
	 * @package P2P_Restful_Connection
	 * @author  Mike Hemberger
	 */
	class P2P_Restful_Connection {

		/**
		 * Name of the p2p connection.
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $connection_name = 'posts_to_pages';

		/**
		 * URL of the JS file you will need to create.
		 *
		 * @since 1.1.0
		 */
		protected $script_url = 'path/to/script/filename.js';

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
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_rest_endpoint' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
			// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		public static function get_link( $step_id ) {
			parent::enqueue_scripts();
			return self::get_step_progress_button( $step_id );
		}

	    /**
	     * Register our AJAX & other JavaScript/CSS files.
	     *
		 * @since  1.0.0
		 *
		 * @return void
	     */
	    public function register_scripts() {
	        wp_register_script(  $this->connection_name, $this->script_url, array('jquery'), '1.0.0', true );
	        wp_localize_script( $this->connection_name, 'restful_p2p_connection_vars', $this->get_ajax_data() );
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
	        $ajax_data = array(
				'root'		=> esc_url_raw( rest_url() ),
				'nonce'		=> wp_create_nonce( 'wp_rest' ),
				'success'	=> true,
				'failure'	=> false,
	        );
	        $additional_data = $this->additional_ajax_data();
	        return array_merge( $ajax_data, $additional_data );
	    }

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Add conditional checks when to enqueue scripts
	     *
	     * @since  1.0.0
	     *
	     * @return array
	     */
	    public static function enqueue_scripts() {
	    	// Conditional checks when to enqueue
	    	wp_enqueue_script( $this->connection_name );
	    }

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Add additional ajax data for use in your javascript
	     *
	     * @since  1.0.0
	     *
	     * @return array
	     */
	    public function additional_ajax_data() {
	    	return array();
	    }

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Check if connection can be made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data to check against
	     *
	     * @return array
	     */
		public function can_connect( $data ) {
			// Do not allow existing connection to be made twice
			if ( $this->connection_exists( $data['from_id'], $data['to_id'] ) ) {
				return false;
			}
			return true;
		}

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Check if connection can be made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data to check against
	     *
	     * @return array
	     */
		public function can_disconnect( $data ) {
			return true;
		}

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Run some code before a connection is made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data
	     *
	     * @return array
	     */
		public function before_connect( $data ) {
			// Do some things
		}

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Run some code before a connection is made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data
	     *
	     * @return array
	     */
		public function after_connect( $data ) {
			// Do some things
		}

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Run some code before a connection is made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data
	     *
	     * @return array
	     */
		public function before_disconnect( $data ) {
			// Do some things
		}

	    /**
	     * ************************************************
	     * OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS!
	     *
	     * Run some code before a connection is made
	     *
	     * @since  1.0.0
	     *
	     * @param  array  $data  array of data
	     *
	     * @return array
	     */
		public function after_disconnect( $data ) {
			// Do some things
		}

		/**
		 * Add custom endpoint to add connection
		 *
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function register_rest_endpoint() {

		    register_rest_route( 'restful-p2p/v1', '/connect/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'connect' ),
				'args'	   => array(
		            'from_id' => array(
						'validate_callback' => 'is_numeric'
		            ),
		            'to_id' => array(
		                'validate_callback' => 'is_numeric'
		            ),
		        ),
		    ));

		    register_rest_route( 'restful-p2p/v1', '/disconnect/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'disconnect' ),
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
		 * Connection 2 objects
		 *
		 * @since   1.0.0
		 *
		 * @param 	array $data Options for the function.
		 * @return  int|object
		 */
		public function connect( $data ) {

			// trace($data);

			// Send error if cannot create connection
			if ( ! $this->can_connect( $data ) ) {
				// trace($data);
				return array(
					'success' => false,
					'message' => $this->messages['can_connect_fail']
				);
			}

			// Optionally run other code
			$this->before_connect( $data );

			// Create connection
			$p2p = p2p_type( $this->connection_name )->connect( $data['from_id'], $data['to_id'], array(
			    'date' => current_time('mysql')
			) );

			// If error, return WP_Error
			if ( is_wp_error( $p2p ) ) {
				// Fail
				return array(
					'success' => false,
					'message' => $p2p->get_error_message(),
				);
			} else {

				// Optionally run other code
				$this->after_connect( $data );

				// Success
				return array(
					'success' => true,
					'id'	  => $p2p,
					'message' => $this->messages['connect_success'],
				);
			}
		}

		/**
		 * Disconnect 2 objects
		 *
		 * @since   1.0.0
		 *
		 * @param 	array $data Options for the function.
		 * @return  int|object
		 */
		public function disconnect( $data ) {

			// Send error if cannot delete connection
			if ( ! $this->can_disconnect( $data ) ) {
				return array(
					'success' => false,
					'message' => $this->messages['can_disconnect_fail']
				);
			}

			// Optionally run other code
			$this->before_disconnect( $data );

			// Remove connection
			$p2p = p2p_type( $this->connection_name )->disconnect( $data['from_id'], $data['to_id'] );

			// If error, return WP_Error
			if ( is_wp_error( $p2p ) ) {
				// Fail
				return array(
					'success' => false,
					'message' => $p2p->get_error_message(),
				);
			} else {

				// Optionally run other code
				$this->after_disconnect( $data );

				// Success
				return array(
					'success' => true,
					'message' => $this->messages['disconnect_success'],
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
	    public function connection_exists( $data ) {
			return p2p_connection_exists( $this->connection_name, array('from' => $data['from_id'], 'to' => $data['to_id'] ) );
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
