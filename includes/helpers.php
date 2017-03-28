<?php
/**
 * Wampum Helpers
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com.com>
 * @link      https://github.com/JiveDig/wampum/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

/**
 * Check if post is a top level post
 *
 * @since  1.5.0
 *
 * @param  int  $post_id  The ID of the post to check (defaults to current)
 *
 * @return bool
 */
function wampum_is_top_level( $post_id = '' ) {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	$post = get_post( (int)$post_id );
	if ( $post && $post->post_parent == 0 ) {
		return true;
	}
	return false;
}

/**
 * Check if a post is a child of another post
 *
 * @since  1.5.0
 *
 * @param  int  $post_id  The ID of the post to check (defaults to current)
 *
 * @return bool
 */
function wampum_is_child( $post_id = '' ) {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	$post = $post_id ? get_post($post_id) : get_post();
	if ( $post->post_parent > 0 )	{
		return true;
	}
	return false;
}

/**
 * Get ID of a top level post
 *
 * @since  1.5.0
 *
 * @param  object|int   $step_object_or_id  the post object or ID to get connected item from
 *
 * @return string|bool
 */
function wampum_get_top_parent_id( $post_id = '' ) {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	$post = $post_id ? get_post($post_id) : get_post();
	if ( $post->post_parent > 0 )	{
		$ancestors	= get_post_ancestors($post->ID);
		$root		= count($ancestors)-1;
		$parent_id	= $ancestors[$root];
	} else {
		$parent_id = $post->ID;
	}
	return $parent_id;
}


/**
 * Get an array of step IDs for a given program
 *
 * @since  1.4.0
 *
 * @param  int    $program_id  The program to get the steps for
 *
 * @return array
 */
function wampum_get_children_ids( $post_id = '' ) {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	return wampum_get_children( $post_id, 'ids' );
}

/**
 * Get an array of step objects for a given program
 *
 * @since  1.4.0
 *
 * @param  int     $post_id  The program to get the steps for
 * @param  string  $return  	(Optional) The fields to return
 *
 * @return array
 */
function wampum_get_children( $post_id = '', $return = 'all') {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
    $post_type = get_post_type($post_id);
    $args = array(
		'post_type'					=> $post_type,
		'post_parent'				=> $post_id,
		'post_status'				=> 'publish',
		'posts_per_page'			=> -1,
		'fields'					=> $return,
		'orderby'					=> 'menu_order',
		'order'						=> 'ASC',
    );
    $posts = new WP_Query( $args );
    $children = array();
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) : $posts->the_post();
        	global $post;
        	if ( is_object($post) ) {
        		$post_id = $post->ID;
        	} else {
        		$post_id = $post;
        	}
			$children[] = $post;
        endwhile;
    }
    wp_reset_postdata();
    return $children;
}

/**
 * Get an array of program IDs the current user has access to
 *
 * @since  1.4.0
 *
 * @return array
 */
function wampum_get_user_program_ids() {
	return wampum_get_user_programs('ids');
}

/**
 * Get an array of programs the current user has access to
 *
 * @since  1.4.0
 *
 * @param  string   $return   (optional) data to return for each term
 *                            use one of 'term_id', 'name', etc (defaults to entire term object)
 * @return array
 */
function wampum_get_user_programs( $return = 'all') {
    $args = array(
		'post_type'					=> 'wampum_program',
		'post_parent'				=> 0,
		'post_status'				=> 'publish',
		'posts_per_page'			=> -1,
		'fields'					=> $return,
		'orderby'					=> 'title',
		'order'						=> 'ASC',
    );
    $posts = new WP_Query( $args );
    $programs = array();
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) : $posts->the_post();
	    	global $post;
	    	if ( is_object($post) ) {
	    		$post_id = $post->ID;
	    	} else {
	    		$post_id = $post;
	    	}
			if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
				$programs[] = $post;
			}
        endwhile;
    }
    wp_reset_postdata();
    return $programs;
}


/**
 * Get previous and next links with HTML
 *
 * @param  ID  $post_id  The post ID to find the links for
 *
 * @return string The HTML of the links
 */
function wampum_get_prev_next_links( $post_id = '' ) {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	// Let's get it started
	$output = '';
	// Get parent, previous, and next connected posts
	$items = wampum_get_sibling_ids( $post_id );
	// Bail if none
	if ( ! $items ) {
		return $output;
	}
	// Prevents things from breaking if step is not connected to a program
	$items['previous'] = isset($items['previous']) && ! empty($items['previous']) ? $items['previous'] : '';
	$items['next'] 	   = isset($items['next']) && ! empty($items['next']) ? $items['next'] : '';

	// Text filters
	$prev_text = apply_filters( 'wampum_prev_post_text', get_the_title( $items['previous'] ) );
	$next_text = apply_filters( 'wampum_next_post_text', get_the_title( $items['next'] ) );

	// Set markup for links
	$prev = $items['previous'] ? '<div class="pagination-previous alignleft"><a href="' . get_permalink( $items['previous'] ) . '">' . $prev_text . '</a></div>' : '';
	$next = $items['next'] ? '<div class="pagination-next alignright"><a href="' . get_permalink( $items['next'] ) . '">' . $next_text . '</a></div>' : '';
	// If previous or next link
	if ( $prev || $next ) {
		$output .= '<div class="wampum-pagination">';
		$output .= $prev . $next;
		$output .= '</div>';
	}
	// Send it home baby
	return $output;
}

/**
 * Get the previous or next post/cpt IDs
 * Great for child page navigation
 *
 * @param  integer  $post_id  (Optional) The post ID to get the siblings of. Defaults to current post.
 *
 * @return array
 */
function wampum_get_sibling_ids( $post_id = '' ) {
    if ( ! is_singular() ) {
    	return;
    }

    if ( ! $post_id ) {
    	$post_id = get_the_ID();
    }

    $post = get_post($post_id);

    // Bail if top level page, no siblings
    if ( $post->post_parent == 0 ) {
    	return;
    }

    $args = array(
		'post_type'					=> $post->post_type,
		'post_parent'				=> $post->post_parent,
		'posts_per_page'			=> 250,
		'fields'					=> 'ids',
		'order'						=> 'ASC',
		'orderby'					=> 'menu_order',
		'no_found_rows'				=> true,
		'update_post_meta_cache'	=> false,
		'update_post_term_cache'	=> false,
	);
    $siblings = new WP_Query( $args );

	$sibling_ids = '';

	$first_id = $last_id = '';

	if ( $siblings->have_posts() ) {
		$i = 1;
		$count = $siblings->found_posts;
	    while ( $siblings->have_posts() ) : $siblings->the_post();
	    	if ( $i == 1 ) {
	    		$first_id = get_the_ID();
	    	}
	    	if ( $i == $count ) {
	    		$last_id = get_the_ID();
	    	}
	    	$sibling_ids[] = get_the_ID();
	    	$i++;
	    endwhile;

	}
	wp_reset_postdata();

	// Bail if we got nothing
	if ( ! is_array($sibling_ids) ) {
		return;
	}

	$current = array_search($post->ID, $sibling_ids);

	// Make sure we're not on the first item in the array
	if ( $first_id == $post_id ) {
		$previous = '';
	} else {
		// Get the previous if we have it in the array
		$previous = array_key_exists( $current-1, $sibling_ids ) ? $sibling_ids[$current-1] : '';
	}

	// Make sure we're not on the last item in the array
	if ( $last_id == $post_id ) {
		$next = '';
	} else {
		// Get the next if we have it in the array
		$next = array_key_exists( $current+1, $sibling_ids ) ? $sibling_ids[$current+1] : '';
	}

    return array(
    	'previous' => $previous,
    	'next'     => $next,
	);
}

/**
 * Get the first child link
 *
 * @param  ID  $post_id  The post ID to find the first child of
 *
 * @return string  The HTML with the child link
 */
function wampum_get_first_child_link( $post_id ) {
	// Let's get it started
	$output = '';
	// Get first step ID
	$id = wampum_get_first_child_id( $post_id );
	// Bail if none
	if ( ! $id ) {
		return $output;
	}
	$next_text	= apply_filters( 'wampum_next_post_text', get_the_title( $id ) );
	$next		= '<div class="pagination-next alignright"><a href="' . get_permalink( $id ) . '">' . $next_text . '</a></div>';

	$output .= '<div class="wampum-pagination">';
	$output .= $next;
	$output .= '</div>';
	// Send it home baby
	return $output;
}

/**
 * Get the first child's ID
 * Used on program parent page to show entry pagination to the first step
 *
 * @since  1.4.2
 *
 * @param  int   $post_id  post ID (should be program parent)
 *
 * @return integer
 */
function wampum_get_first_child_id( $post_id = '' ) {
    $args = array(
		'post_type'					=> 'wampum_program',
		'post_parent'				=> $post_id,
		'post_status'				=> 'publish',
		'posts_per_page'			=> -1,
		'fields'					=> 'ids',
		'orderby'					=> 'menu_order',
		'order'						=> 'ASC',
    );
    $posts = new WP_Query( $args );
    $id = '';
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) : $posts->the_post();
     		$id = get_the_ID();
     		break;
        endwhile;
    }
	wp_reset_postdata();
    return $id;
}

/**
 * CURRENTLY UNUSED!
 *
 * Check if a user can view a piece of content on the site
 *
 * @see 	woocommerce-memberships/includes/class-wc-memberships-shortcodes.php
 *
 * @param   int  $post_id The post ID to check access to
 *
 * @return  bool
 */
function wampum_can_view( $post_id = '' ) {
	if ( empty($post_id) ) {
		$post_id = get_the_ID();
	}
	if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
		true;
	}
	return false;
}

/**
 * CURRENTLY UNUSED!
 *
 * Check if current user can view a specific post
 *
 * @since   1.0.0
 *
 * @param   int  $post_id  (optional)  The post ID to check access to
 *
 * @return  bool
 */
function wampum_can_view_post( $post_id = '' ) {
	$post = $post_id ? get_post($post_id) : get_post();
	$post_id = wampum_get_top_parent_id( $post->ID );
	return wc_memberships_user_can( $user_id, 'view', array( 'post' => $post_id ) );
}

/**
 * Get singular post type name
 *
 * @since  1.0.0
 *
 * @param  string  $post_type  registered post type name
 *
 * @return string
 */
function wampum_get_singular_name( $post_type, $lowercase = false ) {
	return Wampum_Programs()->content->get_singular_name( $post_type, $lowercase );
}

/**
 * Get plural post type name
 *
 * @since  1.0.0
 *
 * @param  string  $post_type  registered post type name
 *
 * @return string
 */
function wampum_get_plural_name( $post_type, $lowercase = false ) {
	return Wampum_Programs()->content->get_plural_name( $post_type, $lowercase );
}

/**
 * Get plural post type name
 *
 * @since  1.0.0
 *
 * @param  string  $post_type  registered post type name
 *
 * @return string
 */
function wampum_get_slug( $post_type ) {
	return Wampum_Programs()->content->get_slug( $post_type );
}

/**
 * Helper function to get the excerpt with max character length
 * Taken from Genesis genesis_truncate_phrase()
 * Example: wampum_get_truncated_content(140);
 *
 * @param 	string  $text           		A string to be shortened.
 * @param 	int     $max_characters 		The maximum number of characters to return.
 * @return 	string  Truncated string. 		Empty string if `$max_characters` is falsy.
 */
function wampum_get_truncated_content( $text, $max_characters ) {

	if ( ! $max_characters ) {
		return '';
	}

	$text = trim( $text );

	if ( mb_strlen( $text ) > $max_characters ) {

		// Truncate $text to $max_characters + 1.
		$text = mb_substr( $text, 0, $max_characters + 1 );

		// Truncate to the last space in the truncated string.
		$text_trim = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );

		$text = empty( $text_trim ) ? $text : $text_trim;

	}

	return $text;

}

/**
 * Helper function to get template part
 *
 * wampum_get_template_part( 'account', 'page' );
 *
 * This will try to load in the following order:
 * 1: wp-content/themes/theme-name/wampum/account-page.php
 * 2: wp-content/themes/theme-name/wampum/account.php
 * 3: wp-content/plugins/wampum/templates/account-page.php
 * 4: wp-content/plugins/wampum/templates/account.php.
 *
 * @since  1.0.0
 *
 * @param  string  		 $slug
 * @param  string  		 $name
 * @param  boolean 		 $load
 * @param  string|array  $data  optional array of data to pass into template
 *
 * $data param MUST be called $data, not any other variable name
 * $data MUST be an array
 *
 * @return mixed
 */
function wampum_get_template_part( $slug, $name = null, $load = true, $data = '' ) {
    if ( is_array($data) ) {
	    Wampum_Programs()->templates->set_template_data( $data );
	}
    Wampum_Programs()->templates->get_template_part( $slug, $name, $load );
}
