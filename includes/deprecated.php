<?php

// THIS FILE IS NOT EVEN INCLUDED, JUST HERE FOR REFERENCE

/**
 * CURRENTLY NO LONGER IN USE
 *
 * When viewing a step, we need to get the restricted message for the restricted program associated with that step
 *
 * @since  1.3.0
 *
 * @return mixed
 */
public function get_restricted_message( $content ) {
	// if ( is_main_query() ) {
    	$post_id = get_the_ID();
	    if ( wampum_is_step() ) {
	    	$post_id = wampum_get_step_program_id( get_the_ID() );
	    }
		// Check if user has access to restricted content
		if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
			$content .= wc_memberships()->get_frontend_instance()->get_content_restricted_message( $post_id );
		// Check if user has access to delayed content
		} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
			$content .= wc_memberships()->get_frontend_instance()->get_content_delayed_message( get_current_user_id(), $post_id );
		}
	// }
	return $content;
}

/**
 * CURRENTLY NO LONGER IN USE
 *
 * Get the restricted message, with product link(s) and maybe login form
 *
 * @since  1.3.0
 *
 * @return mixed
 */
function noaccess_restricted_message( $message, $post_id, $products ) {

	// MESSAGE
	$message = '<div class="noaccess-message">' . $message . '</div>';

	// OPEN
	$open  = '<div class="wampum-noaccess-container">';
	$open .= '<div class="wampum-noaccess-wrap">';
	$open .= '<div class="wampum-noaccess-content">';

		// LINKS
		$home    = '<a style="float:left;" href="' . home_url() . '">← Home</a>';
		$account = '';
		if ( is_user_logged_in() ) {
			$account = '<a style="float:right;" href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '">My Account →</a>';
		}
		$links = '<div class="noaccess-links">' . $home . $account . '</div>';

		// SELL
		$sell = '';

		if ( $products ) {

			$sell .= '<div class="noaccess-products">';

				$sell .= '<h2>Get Access Now</h2>';

		    	foreach ( $products as $product_id ) {

 					$product = new WC_Product($product_id);

	    			$sell .= '<div class="noaccess-product">';
	 					$sell .= '<a class="button" href="' . get_permalink($product_id) . '">' . get_the_title( $product_id ) . ' - ' . $product->get_price_html() . '</a>';
	    				$sell .= $product->post_excerpt;
					$sell .= '</div>';
				}

			$sell .= '</div>';

    	}

	    // LOGIN
	    $login = '';

	    if ( ! is_user_logged_in() ) {

			$login .= '<div class="noaccess-login">';
		    	$login .= '<h2>Already have access?</h2>';
				$login .= '<a style="float:none;display:block;" class="button" href="' . $this->get_restricted_content_redirect_url( get_the_ID() ) . '">Login</a>';
			$login .= '</div>';

		}

	// CLOSE
	$close  = '</div>';
	$close .= '</div>';
	$close .= '</div>';

    return $open . $links . $message . $sell . $login . $close;
}

/**
 * CURRENTLY NO LONGER IN USE
 *
 * Get a formatted login url with restricted content redirect URL
 *
 * If content is neither a singular content or a taxonomy term will default to user account page
 *
 * @since 1.4.0
 * @return string Escaped url
 */
public function get_restricted_content_redirect_url( $post_id ) {

	$url = wc_get_page_permalink( 'myaccount' );

	$url = add_query_arg( array(
		'wcm_redirect_to' => 'post',
		'wcm_redirect_id' => $post_id,
	), $url );

	return esc_url( $url );
}

/**
 * CURRENTLY NO LONGER IN USE
 *
 * Get styles for no access overlay and message
 *
 * @since  1.3.0
 *
 * @return string|css
 */
public function noaccess_styles() {
    ?>
    <style type="text/css">
    	body {
    		overflow: hidden !important;
    	}
		.wampum-noaccess-container {
		    background-color: rgba(250,250,250,0.9);
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    overflow: hidden;
		    position: fixed;
		    z-index: 1043;
		    overflow: hidden;
		    -webkit-backface-visibility: hidden;
		}

	    .wampum-noaccess-wrap {
		    text-align: center;
		    position: absolute;
		    width: 100%;
		    height: 100%;
		    left: 0;
		    top: 0;
		    padding: 10px !important;
		    margin: 0 !important;
		    box-sizing: border-box;
		    overflow: auto;
		}
		.wampum-noaccess-content {
			background-color: #fff;
			border: 1px solid #e6e6e6;
		    position: relative;
		    max-width: 400px;
		    top: 40%;
			-webkit-transform: translate(0, -40%);transform: translate(0, -40%);
		    height: auto;
		    padding: 10px 30px;
		    margin: 30px auto;
		    z-index: 1045;
		}
		.wampum-noaccess-content h2 {
			font-size: 24px;
			margin-bottom: 8px;
		}
		.noaccess-links,
		.noaccess-message,
		.noaccess-products,
		.noaccess-login {
			padding: 15px 0;
			overflow: hidden;
		}
		.noaccess-product {
			margin-bottom: 8px;
			overflow: hidden;
		}
		.noaccess-product .button,
		.noaccess-login .button {
			display: block;
			width: 100%;
			line-height: 1.2;
			white-space: normal;
		}
		.noaccess-login {
			margin-top: 20px;
		}
	</style>
	<?php
}


function disregard_this_its_only_here_for_temp_reference() {
	// $programs = wc_memberships()->frontend->get_user_restricted_posts('wampum_program');
	$programs = wc_memberships()->frontend->get_user_content_for_access_condition( 'restricted', 'posts', 'wampum_program' );


	$memberships = wampum_get_user_memberships( $user_id );
	// Bail if no memberships
	if ( ! $memberships ) {
		return false;
	}


	// Set programs as empty array
	$programs = array();
	// Loop through memberships
	foreach ( $memberships as $membership ) {
		// Allowed statuses
		// View all statuses in woocommerce-memberships/includes/class-wc-memberships-user-memberships.php
	    $allowed_statuses = array(
	    	'wcm-active',
	    	'wcm-complimentary',
    	);
    	// Skip if not allowed to view based on status
    	if ( ! in_array( $membership->status, $allowed_statuses ) ) {
    		continue;
    	}
    	// Get the membership plan's restricted content
		$content = $membership->get_plan()->get_restricted_content(1);
		// $content = $membership->get_restricted_content();


		// Bail if no content
		if ( ! $content ) {
			return false;
		}
		foreach ( $content->posts as $post ) {
			// Skip if not a program
			if ( $post->post_type != 'wampum_program' ) {
				continue;
			}
			// Skip if not a top level post (parent program)
			if ( $post->post_parent > 0 ) {
				continue;
			}
			// Skip if already in our program array
			if ( in_array( $post, $programs ) ) {
				continue;
			}
			$programs[] = $post;
		}
	}
	return $programs;
}