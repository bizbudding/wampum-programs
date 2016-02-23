<?php
/**
 * Create an inner nav menu using query strings on the same page to load alternate content
 *
 *
 *
 * @package   JiveDig_Content_Swap
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 * @version   1.0.0
 */

if ( ! class_exists( 'JiveDig_Content_Swap' ) )  {
	/**
	 * Query String Menu
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package JiveDig_Content_Swap
	 * @author  Mike Hemberger
	 */
	class JiveDig_Content_Swap {

		/**
		 * Menu name
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $name = 'menu-name';

		/**
		 * Associative array of menu item items ['slug'] => 'Name'
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $items = array(
					'about' => 'About',
					'edit'  => 'Edit Posts',
				);

		/**
		 * Optionally show a default tab
		 * Will show content without query string
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $default = null;

		/**
		 * Get the menu classes
		 *
		 * @since 1.1.0
		 *
		 * @var bool
		 */
		protected $classes = 'menu';

		/**
		 * Get the script directory path to look for scripts
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $script_dir = 'path-to-scripts/';

		/**
		 * Loading text/html
		 *
		 * @since 1.0.0
		 *
		 * @var mixed
		 */
		protected $loading = 'Loading';

		public function __construct() {
			// add_action( 'rest_api_init', array( $this, 'register_rest_endpoints' ) );
			// add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		/**
		 * ****************************************************************************** *
		 *** OPTIONALLY ADD THIS METHOD IN YOUR TEMPLATE TO ENABLE RESTFUL CONTENT SWAP ***
		 * ****************************************************************************** *
		 *
		 * Register rest endpoints and scripts
		 *
		 * @since 1.0.0
		 *
		 * @return null
		 */
		public function restful() {
			add_action( 'rest_api_init', array( $this, 'register_rest_endpoints' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		/**
		 * Add custom endpoint to get the content
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function register_rest_endpoints() {
		    register_rest_route( 'jivedigcontentswap/v1', '/content/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_content' ),
		    ));
		}

		/**
		 * Register and localize restful content swap scripts
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function register_scripts() {
			// wp_register_script( 'jivedigcontentswap', trailingslashit($this->script_dir) . $this->name . '.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script( 'jivedigcontentswap', trailingslashit($this->script_dir) . $this->name . '.js', array('jquery'), '1.0.0', true );
		    wp_localize_script( 'jivedigcontentswap', 'jivedigcontentswap', $this->get_ajax_data() );
		}

		/**
		 * Get localize script data
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_ajax_data() {
		    return array(
				'root'		=> esc_url_raw( rest_url() ),
				'nonce'		=> wp_create_nonce( 'wp_rest' ),
				'json_dir'	=> 'jivedigcontentswap/v1/content/',
				'name'		=> $this->name,
				'loading'	=> $this->loading,
		    );
		}

		/**
		 * ******************************************************* *
		 *** OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS ***
		 * ******************************************************* *
		 *
		 * Check if user can view all or specific items
		 * Handles menu item and item content
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $item  content item slug
		 *
		 * @return bool
		 */
		protected function can_view( $slug ) {
			// Add conditionals when overriding in child class
			return true;
		}

		/**
		 * Display the menu
		 *
		 * @since  1.0.0
		 *
		 * @return mixed
		 */
		public function menu( $echo = true ) {
			if ( true === $echo ) {
				echo $this->get_menu( $this->items );
			} else {
				return $this->get_menu( $this->items );
			}
		}

		/**
		 * Build the menu and menu items
		 *
		 * @since  1.0.0
		 *
		 * @param  array  $items  menu item slugs and names
		 *
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
			// Build our menu
			$output .= '<ul id="' . $name . '-menu" class="' . $name . '-menu ' . $this->classes . '">';
			foreach( $items as $slug => $value ) {
				// If user can't view this item, skip it and move on to the next one
				if ( ! $this->can_view( $slug ) ) {
					continue;
				}
				// Set our slug
				$slug	= $this->sanitize_slug($slug);
				// Maybe active tab
				$active	= $this->is_active_item($slug) ? ' active' : '';
				// Continue to output the menu
				$output .= '<li class="menu-item menu-item-' . $slug . $active . '" data-item="' . $slug . '">';
					$output .= '<a href="?' . $name . '=' . $slug . '">';
					// $output .= '<a href="?' . $name . '=' . $slug . '#' . $name . '-menu">';
					$output .= sanitize_text_field($value);
					$output .= '</a>';
				$output .= '</li>';
			}
			$output .= '</ul>';
			// Return the menu
			return $output;
		}

		public function content( $echo = true ) {
			$content = '<div class="' . $this->get_menu_name() . '-content">';
				$items = $this->items;
				foreach ( $items as $slug => $value ) {
					// If user can't view this item, skip it and move on to the next one
					if ( ! $this->can_view( $slug ) ) {
						continue;
					}
					if ( $this->is_active_item($slug) ) {
						$content .= $this->get_content()[$slug];
					}
				}
			$content .= '</div>';
			// Output the content
			if ( true === $echo ) {
				echo $content;
			} else {
				return $content;
			}
		}

		/**
		 * Get item content
		 *
		 * @since  1.0.0
		 *
		 * @return mixed
		 */
		public function get_content() {
			// $content = array();
			$items = $this->items;
			foreach( $items as $slug => $value ) {
				if ( $this->can_view( $slug ) ) {
				// if ( $this->is_tab($slug) && $this->can_view( $slug ) ) {
					$method_name    = $this->get_content_method_name($slug);
					$content[$slug] = $method_name();
					// $content[$slug] = $this->get_content_method_name($slug);
					// $content[$slug] = $this->get_item_content($slug);
				}
			}
			return $content;
		}

		/**
		 * ******************************************************* *
		 *** OPTIONALLY OVERRIDE THIS METHOD IN YOUR CHILD CLASS ***
		 * ******************************************************* *
		 *
		 * Example to allow filtering of menu items to add additional content
		 *
		 * 1. Define $prefix parameter in child class
		 * 2. Override this method
		 * 3. return "{$this->prefix}_get_{$slug}_content";
		 * 4. In custom plugin (or theme) return the content via that function
		 *
		 * @since   1.0.0
		 *
		 * @return  string
		 */
		protected function get_content_method_name($slug) {
			return "get_{$slug}_content";
		}

		/**
		 * Get a sanitized version of the slug
		 *
		 * @since  1.0.0
		 *
		 * @return string
		 */
		protected function get_menu_name() {
			return $this->sanitize_slug( $this->name );
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
		 * Check if viewing active item
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $slug  slug of the item being viewed
		 *
		 * @return boolean
		 */
		protected function is_active_item( $slug ) {
			if ( $this->is_tab($slug) || $this->is_default($slug) ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if is default item
		 *
		 * @since  1.0.0v
		 *
		 * @param  string  $slug  slug of the item being viewed
		 *
		 * @return bool
		 */
		protected function is_default( $slug ) {
			if ( ! $this->is_a_tab() && $this->default === $slug ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if on a specific tab in your menu/template
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $menu_item_slug  the page you want to show content for
		 *
		 * @return bool
		 */
		protected function is_tab( $menu_item_slug ) {
			// if ( isset($_GET[$this->name]) && $menu_item_slug === $_GET[$this->name] ) {
			if ( $this->is_a_tab() && $menu_item_slug === $_GET[$this->name] ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if viewing a tab of the menu
		 *
		 * @since  1.0.0
		 *
		 * @return bool
		 */
		protected function is_a_tab() {
			if ( isset($_GET[$this->name]) ) {
				return true;
			}
			return false;
		}
	}
}
