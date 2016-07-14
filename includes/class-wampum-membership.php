<?php
/**
 * Wampum Membership
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
final class Wampum_Membership {

	/**
	 * @var   Wampum_Membership The one true Wampum_Membership
	 * @since 1.4
	 */
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Membership;
			self::$instance->init();
		}
		return self::$instance;
	}

	public function init() {
		// Filters
		add_filter( 'auth_cookie_expiration', array( $this, 'stay_logged_in' ) );
		// Hooks
		add_action( 'plugins_loaded', 	      array( $this, 'woo_subscriptions_remove_deprecation_handlers' ), 0 );
		add_action( 'wp_head', 				  array( $this, 'access_redirect' ) );
	}

	/**
	 * Stay logged in for n number of weeks
	 *
	 * @since  1.3.0
	 *
	 * @param  int  $expirein  number of seconds to stay logged in for
	 *
	 * @return int  time in seconds to stay logged in
	 */
	public function stay_logged_in( $expirein ) {
		$seconds = 31556926; // 1 year in seconds
		$seconds = apply_filters( 'wampum_stay_logged_in', $seconds );
		return $seconds;
	}

	/**
	 * Do not load backward compatibility support in Subscriptions 2.0
	 *
	 * @since  1.0.0
	 *
	 * @link   https://support.woothemes.com/hc/en-us/articles/205214466
	 *
	 * @return void
	 */
	public function woo_subscriptions_remove_deprecation_handlers() {
		add_filter( 'woocommerce_subscriptions_load_deprecation_handlers', '__return_false' );
	}

	/**
	 * Redirect steps/programs if they are restricted and user doesn't have access
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function access_redirect() {

	    if ( ! is_singular('wampum_program') ) {
	    	return;
	    }

	    // Bail if super user
	    if ( is_user_logged_in() && current_user_can('wc_memberships_access_all_restricted_content') ) {
	    	return;
	    }

    	$post_id = get_the_ID();

    	if ( wampum_is_step() ) {
    		$post_id = wampum_get_step_program_id( $post_id );
    	}

    	if ( wampum_can_view( $post_id ) ) {
    		return;
    	}

	    // Bail if the program is not restricted at all
	 //    if ( ! $this->is_post_content_restricted( $post_id ) ) {
	 //    	return;
	 //    }

	 //    // If user is logged in, check if they have access to the program
	 //    if ( is_user_logged_in() ) {

		//     $post_object = get_post($post_id);
		//     $user_id     = get_current_user_id();
		//     $programs    = $this->get_programs( $user_id );

		//     // Bail, user has access
		//     if ( in_array( $post_object, $programs ) ) {
		//     	return;
		//     }

		// }

	    // This adds the restricted message to the content, while stripping out the default Woo markup around the notice
	    add_filter( 'the_content', array( $this, 'get_restricted_message' ) );

	    // Add our custom wampum restricted message, with markup
	    add_filter( 'wc_memberships_content_restricted_message', array( $this, 'noaccess_restricted_message' ), 10, 3 );

	    // Add styles
	    $this->noaccess_styles();

	}

    /**
     * When viewing a step, we need to get the restricted message for the restricted program associated with that step
     *
     * @since  1.3.0
     *
     * @return mixed
     */
	public function get_restricted_message( $content ) {
    	if ( is_main_query() ) {
	    	$post_id = get_the_ID();
		    if ( wampum_is_step() ) {
		    	$post_id = wampum_get_step_program_id( get_the_ID() );
		    }
			$content .= wc_memberships()->frontend->get_content_restricted_message($post_id);
		}
		return $content;
	}

    /**
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

    public function get_program_ids() {

    }

	/**
	 * Get a member's purchased programs
	 *
	 * ************************
	 * DO WE EVEN NEED THIS ANYMORE, SINCE USING wampum_can_view() ????????
	 * ************************
	 *
	 * @since  1.0.0
	 *
	 * @param  integer  $user_id  (required) User ID
	 * @param  string   $return   (optional) data to return for each term
	 *                            use one of 'term_id', 'name', etc (defaults to entire term object)
	 * @return array
	 */
	public function get_programs( $user_id ) {
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
			$content = $membership->get_plan()->get_restricted_content();
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

	/**
	 * Check if a post/page content is restricted
	 * Should work on all post types
	 *
	 * From wc-memberships-template-functions.php
	 *
	 * @param 	int   $post_id Optional. Defaults to current post
	 * @return 	bool  True, if content has restriction rules, false otherwise
	 */
	// public function is_post_content_restricted( $post_id ) {
	// 	return wc_memberships_is_post_content_restricted( $post_id );
	// }

	/**
	 * Get membership actions
	 * Used in /templates/account/memberships.php
	 *
	 * Code taken from Woo Memberships wc-memberships-template-functions.php
	 *
	 * @param $membership object
	 */
	public function get_membership_actions( $membership ) {
		$actions = array();
		// Renew: Show only for expired memberships that can be renewed
		if ( $membership->is_expired() && $membership->get_plan()->has_products() ) {
			$actions['renew'] = array(
				'url'  => $membership->get_renew_membership_url(),
				'name' => __( 'Renew', 'wampum' ),
			);
		}
		// Cancel: Do not show for cancelled, expired or pending cancellation
		if ( ( ! $membership->is_cancelled() && 'pending' !== $membership->get_status() ) && ! $membership->is_expired() && current_user_can( 'wc_memberships_cancel_membership', $membership->get_id() ) ) {
			$actions['cancel'] = array(
				'url'  => $membership->get_cancel_membership_url(),
				'name' => __( 'Cancel', 'wampum' ),
			);
		}
		// View: Do not show for cancelled, paused memberships or memberships without a Members Area
		// if ( ! $membership->is_paused() && ! $membership->is_cancelled() && ! empty ( $members_area ) && is_array( $members_area ) ) {
		// 	$actions['view'] = array(
		// 		'url' => wc_memberships_get_members_area_url( $membership->get_plan_id(), $members_area[0] ),
		// 		'name' => __( 'View', 'woocommerce-memberships' ),
		// 	);
		// }
		return $actions;
	}

	/**
	 * CONVERT THIS TO wp_list_pluck() and check if in_array()
	 *
	 * @param  [type] $user_id    [description]
	 * @param  [type] $program_id [description]
	 * @return [type]             [description]
	 */
	// public function can_view( $user_id, $wampum_program_id ) {
	// 	if ( is_singular('wampum_program') ) {
	// 		$programs = $this->get_programs( $user_id );
	// 		if ( ! $programs ) {
	// 			return false;
	// 		}
	// 		foreach ( $programs as $program ) {
	// 			if ( $program->ID == $program_id ) {
	// 				return true;
	// 			}
	// 		}
	// 	}
	// 	return false;
	// }

	public function get_login_form( $args ) {
		$args = array(
			'echo'           => false,
			// 'remember'       => true,
			'redirect'		 => $redirect,
			// 'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			// 'form_id'        => 'wampum-loginform',
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'value_username' => '',
			'value_remember' => false,
		);
		return sprintf( '<div class="wampum-loginform">%s</div>', wp_login_form( $args ) );
	}

}
