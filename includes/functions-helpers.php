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

function wampum_is_program( $post_id = '' ) {
	if ( empty($post_id) ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	$post = get_post( (int)$post_id );
	if ( 'wampum_program' == $post->post_type ) {
		if ( $post->post_parent == 0 ) {
			return true;
		}
	}
	return false;
}

function wampum_is_step( $post_id = '' ) {
	if ( empty($post_id) ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_the_ID();
	}
	$post = get_post( (int)$post_id );
	if ( 'wampum_program' == $post->post_type ) {
		if ( $post->post_parent > 0 ) {
			return true;
		}
	}
	return false;
}

/**
 * Get ID of a step program
 *
 * @since  1.0.0
 *
 * @param  object|int   $step_object_or_id  the post object or ID to get connected item from
 *
 * @return string|bool
 */
function wampum_get_step_program_id( $step_id ) {
	$step = get_post($step_id);
	if ( $step->post_parent > 0 ) {
		return $step->post_parent;
	}
	return false;
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
function wampum_get_program_step_ids( $program_id ) {
	return wampum_get_program_steps( $program_id, 'ids' );
}

/**
 * Get an array of step objects for a given program
 *
 * @since  1.4.0
 *
 * @param  int     $program_id  The program to get the steps for
 * @param  string  $return  	(Optional) The fields to return
 *
 * @return array
 */
function wampum_get_program_steps( $program_id, $return = 'all') {
    $args = array(
		'post_type'					=> 'wampum_program',
		'post_parent'				=> $program_id,
		'post_status'				=> 'publish',
		'posts_per_page'			=> -1,
		'fields'					=> $return,
		'orderby'					=> 'menu_order',
		'order'						=> 'ASC',
    );
    $posts = new WP_Query( $args );
    $steps = array();
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) : $posts->the_post();
        	global $post;
        	if ( is_object($post) ) {
        		$post_id = $post->ID;
        	} else {
        		$post_id = $post;
        	}
			if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
				$steps[] = $post;
			}
        endwhile;
    }
    wp_reset_postdata();
    return $steps;
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
    	global $post;
    	if ( is_object($post) ) {
    		$post_id = $post->ID;
    	} else {
    		$post_id = $post;
    	}
        while ( $posts->have_posts() ) : $posts->the_post();
			if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
				$programs[] = $post;
			}
        endwhile;
    }
    wp_reset_postdata();
    return $programs;
}

function wampum_is_program_progress_enabled( $program_id ) {
	return get_post_meta( $program_id, 'wampum_is_program_progress_enabled', true );
}

function wampum_get_prev_next_links( $post_id = '' ) {
	// Let's get it started
	$output = '';
	// Get parent, previous, and next connected posts
	$items = wampum_get_sibling_ids( $post_id );
	// Bail if none
	if ( ! $items ) {
		return $output;
	}
	// Prevents things from breaking if step is not connected to a program
	$items['previous'] = ! empty($items['previous']) ? $items['previous'] : '';
	$items['next'] 	   = ! empty($items['next']) ? $items['next'] : '';
	// Set markup for links
	$prev = $items['previous'] ? '<div class="pagination-previous alignleft"><a href="' . get_permalink( $items['previous'] ) . '">' . get_the_title( $items['previous'] ) . '</a></div>' : '';
	$next = $items['next'] ? '<div class="pagination-next alignright"><a href="' . get_permalink( $items['next'] ) . '">' . get_the_title( $items['next'] ) . '</a></div>' : '';
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

    if ( empty($post_id) ) {
    	$post_id = get_the_ID();
    }

    $post = get_post($post_id);

    $args = array(
		'post_type'		=> $post->post_type,
		'post_parent'	=> $post->post_parent,
		'fields'		=> 'ids',
		'order'			=> 'ASC',
		'orderby'		=> 'menu_order',
	);
    $siblings = new WP_Query( $args );

	$sibling_ids = array();

	$first_id = $last_id = '';

	if ( $siblings->have_posts() ) {
		$i = 0;
		$count = $siblings->found_posts;
	    while ( $siblings->have_posts() ) : $siblings->the_post();
	    	if ( $i == 0 ) {
	    		$first_id = get_the_ID();
	    	}
	    	if ( $i == $count ) {
	    		$last_id = get_the_ID();
	    	}
	    	$i++;
	    	$sibling_ids[] = get_the_ID();
	    endwhile;

	}
	wp_reset_postdata();

	$current = array_search($post->ID, $sibling_ids);

	// Make sure we're not on the first item in the array
	if ( $first_id == $post_id ) {
		$previous = '';
	} else {
		$previous = (int)$sibling_ids[$current-1];
	}

	// Make sure we're not on the last item in the array
	if ( $last_id == $post_id ) {
		$next = '';
	} else {
		$next = (int)$sibling_ids[$current+1];
	}

    return array(
    	'previous' => $previous,
    	'next'     => $next,
	);
}

/**
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

// Check if user has access to restricted content
// if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post->ID ) ) {
	// $output .= '<div class="wc-memberships-content-restricted-message">' . wc_memberships()->get_frontend_instance()->get_content_restricted_message( $post->ID ) . '</div>';
// Check if user has access to delayed content
// } elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post->ID ) ) {
	// $output .= '<div class="wc-memberships-content-delayed-message">' . wc_memberships()->get_frontend_instance()->get_content_delayed_message( get_current_user_id(), $post->ID ) . '</div>';
// }

/**
 * Check if current user can view a specific post
 *
 * @since   1.0.0
 *
 * @param   int  $post_id  (optional)  The post ID to check access to
 *
 * @return  bool
 */
function wampum_can_view_post( $post_id = null ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : false;
	}
	if ( wampum_is_step() ) {
		$post_id = wampum_get_step_program_id( $post_id );
	}
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
	return Wampum()->content->get_singular_name( $post_type, $lowercase );
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
	return Wampum()->content->get_plural_name( $post_type, $lowercase );
}

/**
 * Get plural post type name
 * TODO: Allow for taxonomy name?
 *
 * @since  1.0.0
 *
 * @param  string  $post_type  registered post type name
 *
 * @return string
 */
function wampum_get_slug( $post_type ) {
	return Wampum()->content->get_slug( $post_type );
}

/**
 * Helper function to get the excerpt with max character length
 * Example: the_excerpt_max_charlength(140);
 *
 * @param  $charlength the amount of characters to include
 * @return string
 */
function wampum_get_truncated_content( $content, $charlength ) {

	$charlength++;

	if ( mb_strlen( $content ) > $charlength ) {
		$subex	 = mb_substr( $content, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut	 = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			return mb_substr( $subex, 0, $excut ) . '[&hellip;]';
		} else {
			return $subex . '[&hellip;]';
		}
		return '[&hellip;]';
	} else {
		return $content;
	}
}

/**
 * Check if current user can view a specific 'wampum_step'
 *
 * @since  1.0.0
 *
 * @param  int  $post_id  ID of the post to check access to
 *
 * @return bool
 */
// function wampum_user_can_view_step( $user_id, $step_id ) {
// 	return Wampum()->membership->can_view_step( $user_id, $step_id );
// }

/**
 * Check if current user can view a specific post
 * I think it only works with posts, maybe pages, but not CPT's
 *
 * @since  1.0.0
 *
 * @param  int  $post_id  ID of the post to check access to
 *
 * @return bool
 */
// function wampum_user_can_view_post( $user_id, $post_id ) {
// 	return Wampum()->membership->can_view_post( $user_id, $post_id );
// }

/**
 * Check if user is an active member of a particular membership plan
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int         $user_id  Optional. Defaults to current user.
 * @param  int|string  $plan     Membership Plan slug, post object or related post ID
 *
 * @return bool True, if is an active member, false otherwise
 */
function wampum_is_user_active_member( $user_id = null, $plan ) {
	return wc_memberships_is_user_active_member( $user_id, $plan );
}

/**
 * Check if user is a member of a particular membership plan
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int         $user_id  Optional. Defaults to current user.
 * @param  int|string  $plan     Membership Plan slug, post object or related post ID
 *
 * @return bool True, if is a member, false otherwise
 */
function wampum_is_user_member( $user_id = null, $plan ) {
	return wc_memberships_is_user_member( $user_id, $plan );
}

/**
 * Get all content restriction rules for a plan
 * @uses   /woocommerce-memberships/includes/class-wc-memberships-membership-plan.php
 *
 * @since  1.0.0
 *
 * @param  object $plan membership plan object
 *
 * @return array  Array of content restriction rules
 */
function wampum_get_plan_content_restriction_rules( $plan ) {
	return $plan->get_plan()->get_content_restriction_rules();
}

/**
 * Main function for returning a user membership
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * Supports getting user membership by membership ID, Post object
 * or a combination of the user ID and membership plan id/slug/Post object.
 *
 * If no $id is provided, defaults to getting the membership for the current user.
 *
 * @since  1.0.0
 *
 * @param  mixed $id   Optional. Post object or post ID of the user membership, or user ID
 * @param  mixed $plan Optional. Membership Plan slug, post object or related post ID
 *
 * @return WC_Memberships_User_Membership
 */
function wampum_get_user_membership( $id = null, $plan = null ) {
	return wc_memberships_get_user_membership( $id, $plan );
}

/**
 * Get all memberships for a user
 * @uses   /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 *
 * @since  1.0.0
 *
 * @param  int   $user_id  Optional. Defaults to current user.
 * @param  array $args
 *
 * @return WC_Memberships_User_Membership[]|null array of user memberships
 */
function wampum_get_user_memberships( $user_id = null, $args = array() ) {
	return wc_memberships_get_user_memberships( $user_id, $args );
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
	    Wampum()->templates->set_template_data( $data );
	}
    Wampum()->templates->get_template_part( $slug, $name, $load );
}

// function wampum_get_program_steps_list( $program_object_or_id ) {
// 	return Wampum()->content->get_program_steps_list( $program_object_or_id );
// }

// function wampum_get_program_steps( $program_object_or_id ) {
// 	return Wampum()->content->get_program_steps( $program_object_or_id );
// }

// function wampum_get_step_program( $step_object_or_id ) {
// 	return Wampum()->content->get_step_program( $step_object_or_id );
// }

