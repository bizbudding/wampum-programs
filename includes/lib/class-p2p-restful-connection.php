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
 *    <div class="connection-wrap"><a data-from-id="<?php echo $from; ?>" data-to-id="<?php echo $to; ?>" class="button connection" href="#"><?php echo $text; ?></a></div>
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
	abstract class P2P_Restful_Connection {

		/**
		 * Name of the p2p connection.
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $connection_name = 'posts_to_pages';

		// protected $from = null;

		// protected $to = null;

		// protected $link_connect_text = 'Connect';

		protected $link_connected_text = 'Connected';

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
		}

		public function get_link( $from, $to, $link_connect_text, $link_connected_text ) {
			$this->enqueue_scripts();
			return $this->get_connection_link( $this->connection_name, $from, $to, $link_connect_text, $link_connected_text );
		}

		public function get_connection_link( $type, $from, $to, $text_connect, $text_connected ) {
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
	    public function enqueue_scripts() {
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
			if ( $this->connection_exists( $data['from'], $data['to'] ) ) {
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
		            'from' => array(
						'validate_callback' => 'is_numeric'
		            ),
		            'to' => array(
		                'validate_callback' => 'is_numeric'
		            ),
		        ),
		    ));

		    register_rest_route( 'restful-p2p/v1', '/disconnect/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'disconnect' ),
				'args'	   => array(
		            'from' => array(
						'validate_callback' => 'is_numeric'
		            ),
		            'to' => array(
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
			$p2p = p2p_type( $this->connection_name )->connect( $data['from'], $data['to'], array(
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
			$p2p = p2p_type( $this->connection_name )->disconnect( $data['from'], $data['to'] );

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
}
