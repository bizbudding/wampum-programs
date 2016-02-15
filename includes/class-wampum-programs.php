<?php
/**
 * Wampum Programs
 *
 * @package   Wampum
 * @author    Mike Hemberger
 * @link      https://bizbudding.com
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum_Programs {

	/**
	 * Get a members purhcased programs
	 *
	 * @since  1.0.0
	 *
	 * @param  object|int|string  The term object, ID, or slug whose link will be retrieved
	 *
	 * @return string|WP_Error
	 */
	public function get_link( $program ) {
		return get_term_link( $program, 'wampum_program' );
	}

}
