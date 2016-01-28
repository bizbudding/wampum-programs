<?php
/**
 * Create a nav menu using query strings on the same page to load alternate content
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
	 * P2P Connections.
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
		protected $slug = 'profile-menu';

		/**
		 * Associative array of menu item items ['slug'] => 'Name'
		 *
		 * @since 1.0.0
		 *
		 * @type array
		 */
		protected $items = array();


		/**
		 * Whether to add genesis markup
		 *
		 * @since 1.1.0
		 *
		 * @type bool
		 */
		protected $genesis = true;

		public function menu() {
			echo $this->get_menu( $this->items );
		}

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
				$i++;
				$slug = $this->sanitize_slug($slug);
				$id = $name . '-' . $i;
				$output .= '<li id="' . $id . '" class="menu-item menu-item-' . $slug . '">';
				$output .= '<a href="?' . $name . '=' . $slug . '">';
				$output .= sanitize_text_field($name);
				$output .= '</a>';
				$output .= '</li>';
			}
			$output .= '</ul>';
			$output .= '</div>';
			$output .= '</nav>';
			return $output;
		}

		/**
		 * Check if on a specifi tab in your template
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

		protected function get_ul_classes() {
			if ( true === $this->genesis ) {
				return 'menu genesis-nav-menu';
			} else {
				return 'menu';
			}
		}

		protected function get_menu_name() {
			return $this->sanitize_slug( $this->slug );
		}

		/**
		 * Uses default WP function
		 * @param  [type] $slug [description]
		 * @return [type]       [description]
		 */
		protected function sanitize_slug( $slug ) {
			return sanitize_title( strtolower( $slug ) );
		}

	}
}
