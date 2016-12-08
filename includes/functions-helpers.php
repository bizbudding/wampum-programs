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
 * TODO: ADD THIS TO class-membership.php instead!
 *
 * Add membership to user
 * If user doesn't exists, create one first
 *
 * $args = array(
 *      'plan_id'    => null, // required
 *      'user_email' => null, // required
 *      'user_login' => null,
 *      'user_pass'  => null,
 *      'first_name' => null,
 *      'last_name'  => null,
 *      'note'       => null,
 *      'login'      => true, // Auto-login if they are created during this process?
 * );
 *
 * @since 	1.4.7
 *
 * @param 	array  $args  Array of args when maybe creating a user and adding a membership to a user
 *
 * @return  bool|WP_Error  Whether a new user was created during the process
 */
function wampum_create_user_membership( $args ) {

	// Bail if Woocommerce is not active
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

    // Bail if Woo Memberships is not active
    if ( ! function_exists( 'wc_memberships' ) ) {
        return;
    }

    // Set defaults
    $defaults = array(
        'plan_id'    => null, // required
        'user_email' => null, // required
        'user_login' => null,
        'user_pass'  => null,
        'first_name' => null,
        'last_name'  => null,
        'note'       => null,
        'login'      => true, // Auto-login if they are created during this process?
    );
    $args = wp_parse_args( $args, $defaults );


    // Minimum data we need is a plan ID and user email
    if ( ! $args['plan_id'] || ! $args['user_email'] ) {
        return;
    }

    // By default set new user var to false
    $new_user = false;

    // If the email is already a registered user
    if ( $email_exists = email_exists( $args['user_email'] ) || $username_exists = username_exists( $args['user_login'] ) ) {
        // Get the user and and their ID
        if ( $email_exists ) {
            $user = get_user_by( 'email', $args['user_email'] );
        } elseif ( $username_exists ) {
            $user = get_user_by( 'login', $args['user_login'] );
        }
        if ( $user ) {
            $user_id = $user->ID;
        }
    }
    // Not a user
    else {
        // Set the new user data
        $userdata = array(
            'user_email' => $args['user_email'],
        );
        // If we don't have a login, use the email instead
        if ( ! $args['user_login'] ) {
            $userdata['user_login'] = $args['user_email'];
        } else {
            $userdata['user_login'] = $args['user_login'];
        }
        // If we don't have a password, generate one
        if ( ! $args['user_pass'] ) {
            $userdata['user_pass'] = wp_generate_password( $length = 12, $include_standard_special_chars = true );
        }
        // If we have a first name, set it
        if ( $args['first_name'] ) {
            $userdata['first_name'] = $args['first_name'];
        }
        // If we have a last name, set it
        if ( $args['last_name'] ) {
            $userdata['last_name'] = $args['last_name'];
        }
        // Create a new user
        $user_id = wp_insert_user( $userdata ) ;

        // If no error set the new user var to true
        if ( ! is_wp_error( $user_id ) ) {
            $new_user = true;
        } else {
            // It's an error, return it
            return $user_id;
        }

        // Get the user object
        $user = get_user_by( 'id', $user_id );

        // If we need to log in the user and the current user is not logged in
        if ( $user && $args['login'] && ! is_user_logged_in() ) {
            // Log them in!
            wp_set_current_user( $user_id, $user->user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $user->user_login );
        }
    }

    // If we have a user
    if ( $user_id ) {

        // If user is not an existing member of the plan
        if ( ! wc_memberships_is_user_member( $user_id, $args['plan_id'] ) ) {

            // Add the user to the membership
            $membership_args = array(
                'plan_id'   => $args['plan_id'],
                'user_id'   => $user_id,
            );
            $user_membership = wc_memberships_create_user_membership( $membership_args );

            // If there was an error, return it
            if ( is_wp_error( $user_membership ) ) {
            	return $user_membership;
            }

            // If we have a note, save it to the user membership
            if ( $args['note'] ) {
                // Add a note so we know how this was registered.
                $user_membership->add_note( $args['note'] );
	        }
        }

    }
    return $new_user;
}

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
 * @since  1.4.8
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

function wampum_is_program_progress_enabled( $program_id ) {
	return get_post_meta( $program_id, 'wampum_program_progress_enabled', true );
}

function wampum_get_program_progress_link( $program_or_step_id ) {
	return Wampum_Programs()->progress->get_program_progress_link( $program_or_step_id );
}

function wampum_get_prev_next_links( $post_id ) {
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

function wampum_get_first_step_link( $post_id ) {
	// Let's get it started
	$output = '';
	// Get first step ID
	$id = wampum_get_first_child_id( $post_id );
	// Bail if none
	if ( ! $id ) {
		return $output;
	}
	$next = '<div class="pagination-next alignright"><a href="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</a></div>';

	$output .= '<div class="wampum-pagination">';
	$output .= $next;
	$output .= '</div>';
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
 * TODO: Allow for taxonomy name?
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
 * Check if current user can view a specific 'wampum_step'
 *
 * @uses   Use via add_action( 'wampum_popups', 'my_function_name' )
 *
 * @since  1.4.6
 *
 * @param  string  $content  The content of the popup
 * @param  array   $args 	 Array of settings for the popup
 *
 * @return bool
 */
function wampum_popup( $content, $args ) {

	// Make sure CSS is loaded
	wp_enqueue_style('wampum');

	$defaults = array(
		'hidden' => false, // Maybe add display:none; to the HTML
		'width'	 => '400', // Max popup content width in pixels
	);
	$args = wp_parse_args( $args, $defaults );
	// Inline styles
	$underlay_style = ( true == $args['hidden'] ) ? 'display:none;' : '';
	$overlay_style 	= 'max-width:' . $args['width'] . 'px;';
	// Do it up!
    echo '<div class="wpopup" style="' . $underlay_style . '">';
        echo '<div class="wpopup-content" style="' . $overlay_style . '">';
            $url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );
            $current_url = $url[0];
            if ( $current_url ) {
                echo '<a class="wpopup-close" href="' . esc_url($current_url) . '">×<span class="screen-reader-text">Close Popup</span></a>';
            }
            echo $content;
        echo '</div>';
    echo '</div>';
}

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
	    Wampum_Programs()->templates->set_template_data( $data );
	}
    Wampum_Programs()->templates->get_template_part( $slug, $name, $load );
}
