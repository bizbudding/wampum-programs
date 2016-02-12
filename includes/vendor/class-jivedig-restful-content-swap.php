<?php
/**
 * Create an inner nav menu using query strings on the same page to load alternate content
 *
 * @package   JiveDig_Restful_Content_Swap
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 * @version   1.0.0
 */

if ( ! class_exists( 'JiveDig_Restful_Content_Swap' ) )  {
	/**
	 * Query String Menu
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package JiveDig_Restful_Content_Swap
	 * @author  Mike Hemberger
	 */
	class JiveDig_Restful_Content_Swap {

		/**
		 * Menu name
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $slug = 'menu-name';

		/**
		 * Associative array of menu item items ['slug'] => 'Name'
		 *
		 * @since 1.0.0
		 *
		 * @type array
		 */
		protected $items = array(
					'about' => array(
						'name'		 => 'About',
						'loggedin'	 => false,
						'capability' => null,
					),
					'edit'  => array(
						'name'		 => 'Edit Posts',
						'loggedin'	 => false,
						'capability' => null,
					),
				);

		/**
		 * Get the menu classes
		 *
		 * @since 1.1.0
		 *
		 * @type bool
		 */
		protected $classes = 'menu genesis-nav-menu';

		/**
		 * Get the script directory path to look for scripts
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $script_dir = 'path-to-scripts/';

		/**
		 * Get the directory path to look for template files
		 *
		 * @since 1.0.0
		 *
		 * @type string
		 */
		protected $template_dir = 'path-to-template-parts/';


		public function restful() {
			add_action( 'rest_api_init', array( $this, 'register_rest_endpoints' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		/**
		 * Add custom endpoint to add connection
		 *
		 * @since   1.0
		 * @return  void
		 */
		public function register_rest_endpoints() {
		    register_rest_route( 'restfulcontentswap/v1', '/content/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_rest_content' ),
		    ));
		}

		public function register_scripts() {
			// wp_register_script( 'restfulcontentswap', trailingslashit($this->script_dir) . $this->slug . '.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script( 'restfulcontentswap', trailingslashit($this->script_dir) . $this->slug . '.js', array('jquery'), '1.0.0', true );
		    wp_localize_script( 'restfulcontentswap', 'restfulcontentswap', $this->get_ajax_data() );
		}

		public function get_ajax_data() {
		    return array(
				'root'		=> esc_url_raw( rest_url() ),
				'nonce'		=> wp_create_nonce( 'wp_rest' ),
				'json_dir'	=> 'restfulcontentswap/v1/content/',
				'content'   => $this->get_content($this->items),
				'success'	=> __( 'Successfully successful!', 'your-text-domain' ),
				'failure'	=> __( 'Failurely failure!', 'your-text-domain' ),
		    );
		}

		public function get_rest_content() {
			return '<h1>Wordup</h1>';
		}

		/**
		 * Display the menu
		 *
		 * @since  1.0.0
		 *
		 * @return mixed
		 */
		public function menu() {
			echo $this->get_menu( $this->items );
		}

		// TODO: Add the slug as data-attribute so we can grab it with jQuery and update the query string
		// How about DOC title when doing this? Read up on pushState.
		// CSS-Tricks had a good write up, read it again

		/**
		 * Build the menu and menu items
		 *
		 * @since  1.0.0
		 *
		 * @param  array  	  $items  menu item slugs and names
		 * @return mixed|void
		 */
		protected function get_menu( $items ) {
			// Bail if no menu items or not an array
			if ( ! $items && ! is_array($items) ) {
				return;
			}
			// Get the name
			$name = $this->get_menu_name();
			// Start output
			$output = '';

			$output .= '<ul id="menu-' . $name . '" class="' . $this->classes . '">';
			foreach( $items as $slug => $values ) {
				// If user can't view this item, skip it and move on to the next one
				if ( ! $this->can_view_item( $values ) ) {
					continue;
				}
				// Set our slug
				$slug	= $this->sanitize_slug($slug);
				// Maybe active tab
				$active	= $this->is_tab($slug) ? ' active' : '';
				// Continue to output the menu
				$output .= '<li class="menu-item menu-item-' . $slug . $active . '">';
					$output .= '<a href="?' . $name . '=' . $slug . '">';
					$output .= sanitize_text_field($values['name']);
					$output .= '</a>';
				$output .= '</li>';
			}
			$output .= '</ul>';

			return $output;
		}

		public function content() {
			echo $this->get_content($this->items);
		}

		protected function get_ajax_content( $items ) {
			foreach( $items as $slug => $values ) {
			// if ( $this->is_tab($slug) && $this->can_view_item( $values ) ) {
				$this->get_template_part( $slug );
			// }
			}
		}

		protected function get_content( $items ) {
			foreach( $items as $slug => $values ) {
				if ( $this->is_tab($slug) && $this->can_view_item( $values ) ) {
					$this->get_template_part( $slug );
				}
			}
		}

		/**
		 * ALLOW THIS ONE TO BE OVERWRITTEN TO USE NEW TEMPLATE METHODS & FUNCTIONS
		 * @param  [type] $slug [description]
		 * @return [type]       [description]
		 */
		protected function get_template_part( $slug ) {
			if ( ! file_exists( $this->get_template_file( $slug ) ) ) {
				return;
			}
			include_once( $this->get_template_file( $slug ) );
		}

		protected function get_template_file( $slug ) {
			return trailingslashit($this->template_dir) . $this->sanitize_slug( $slug ) . '.php';
		}

		/**
		 * Get a sanitized version of the slug
		 *
		 * @since  1.0.0
		 *
		 * @return string
		 */
		protected function get_menu_name() {
			return $this->sanitize_slug( $this->slug );
		}

		protected function can_view_item( $item_values ) {
			$login_required	= isset( $item_values['loggedin'] ) ? $item_values['loggedin'] : false;
			$cap_required	= isset( $item_values['capability'] ) ? $item_values['capability'] : false;
			if ( $this->has_loggedin_access($login_required) && $this->has_capability_access($cap_required) ) {
				return true;
			}
			return false;
		}

		/**
		 * [has_logged_in_access description]
		 *
		 * @param  bool  $loggedin [description]
		 *
		 * @return bool
		 */
		protected function has_loggedin_access( $login_required ) {
			if ( $login_required ) {
				if ( is_user_logged_in() ) {
					return true;
				} else {
					return false;
				}
			}
			// login not required
			return true;
		}

		/**
		 * [has_capability_access description]
		 *
		 * @param  string  $capability current_user_can() capability
		 *
		 * @return bool
		 */
		protected function has_capability_access( $cap_required ) {
			if ( $cap_required ) {
				if ( is_user_logged_in() && current_user_can($cap_required) ) {
					return true;
				} else {
					return false;
				}
			}
			// capability not required
			return true;
		}

		/**
		 * Get a lowercase and sanitized version of the slug
		 * Converts spaces to dashes
		 *
		 * @since  1.0.0
		 *
		 * @uses   sanitize_title()
		 *
		 * @param  string $slug
		 *
		 * @return string
		 */
		protected function sanitize_slug( $slug ) {
			return sanitize_title( strtolower( $slug ) );
		}

		/**
		 * Check if on a specific tab in your menu/template
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $menu_item_slug the page you want to show content for
		 *
		 * @return boolean
		 */
		public function is_tab( $menu_item_slug ) {
			if ( isset($_GET[$this->slug]) && $menu_item_slug === $_GET[$this->slug] ) {
				return true;
			}
			return false;
		}
	}
}
