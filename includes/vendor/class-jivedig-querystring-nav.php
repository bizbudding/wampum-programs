<?php
/**
 * Create an inner nav menu using query strings on the same page to load alternate content
 *
 * @package   JiveDig_Query_String_Nav
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 * @version   1.0.0
 */

if ( ! class_exists( 'JiveDig_Query_String_Nav' ) )  {
	/**
	 * Query String Menu
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package JiveDig_Query_String_Nav
	 * @author  Mike Hemberger
	 */
	class JiveDig_Query_String_Nav {

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
					'about' => 'About',
					'edit'  => 'Edit Profile',
				);


		/**
		 * Whether to add genesis markup
		 *
		 * @since 1.1.0
		 *
		 * @type bool
		 */
		protected $genesis = true;

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
			// Start the counter
			$i = 1;
			// Start output
			$output = '';

			$output .= '<nav class="querystring-nav nav-' . $name . '">';
				$output .= '<div class="wrap">';

					$output .= '<ul id="menu-' . $name . '" class="' . $this->get_ul_classes() . '">';
					foreach( $items as $slug => $title ) {
						// Increment counter
						$i++;
						// Set our slug
						$slug	= $this->sanitize_slug($slug);
						// Maybe active tab
						$active	= $this->is_tab($slug) ? ' active' : '';
						//Set ID
						$id		= $name . '-' . $i;
						// Continue to output the menu
						$output .= '<li id="' . $id . '" class="menu-item menu-item-' . $slug . $active . '">';
							$output .= '<a href="?' . $name . '=' . $slug . '">';
							$output .= sanitize_text_field($title);
							$output .= '</a>';
						$output .= '</li>';
					}
					$output .= '</ul>';

				$output .= '</div>';
			$output .= '</nav>';

			return $output;
		}

		/**
		 * Get the class names for the unordered list
		 *
		 * @since  1.0.0
		 *
		 * @return string
		 */
		protected function get_ul_classes() {
			if ( true === $this->genesis ) {
				return 'menu genesis-nav-menu';
			} else {
				return 'menu';
			}
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

		public function get_content() {
			// Get our menu items array
			$items = $this->items;

			foreach( $items as $key => $value ) {

				if ( $this->is_tab($key) ) {

				}
			}
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
