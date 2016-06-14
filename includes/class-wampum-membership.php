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
		add_action( 'plugins_loaded', 	      array( $this, 'woo_subscriptions_remove_deprecation_handlers' ), 0 );
		add_filter( 'auth_cookie_expiration', array( $this, 'stay_logged_in' ) );
		add_action( 'wp_head', 				  array( $this, 'access_redirect' ) );
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
	private function woo_subscriptions_remove_deprecation_handlers() {
		add_filter( 'woocommerce_subscriptions_load_deprecation_handlers', '__return_false' );
	}

	/**
	 * Stay logged in for n number of weeks
	 *
	 * @since  1.3.0
	 *
	 * @param  int  $expirein  number of weeks to stay logged in for
	 *
	 * @return int  time in seconds to stay logged in
	 */
	private function stay_logged_in( $expirein ) {
		$weeks = 24;
		$weeks = apply_filters( 'wampum_stay_logged_in', $weeks );
		return WEEK_IN_SECONDS * $weeks; // 24 weeks in seconds
	}

	/**
	 * Redirect steps/programs if they are restricted and user doesn't have access
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function access_redirect() {

	    if ( ! is_singular( array( 'wampum_program','wampum_step') ) ) {
	    	return;
	    }

	    // Bail if super user
	    if ( is_user_logged_in() && current_user_can('wc_memberships_access_all_restricted_content') ) {
	    	return;
	    }

    	$post_id = get_the_ID();
	    if ( is_singular('wampum_step') ) {
	    	$post_id = Wampum()->content->get_step_program_id( get_queried_object() );
	    }

	    // Bail if the program is not restricted at all
	    if ( ! Wampum()->membership->is_post_content_restricted( $post_id ) ) {
	    	return;
	    }

	    // If user is logged in, check if they have access to the program
	    if ( is_user_logged_in() ) {

		    $post_object = get_post($post_id);
		    $user_id     = get_current_user_id();
		    $programs    = $this->get_programs( $user_id );

		    // Bail, user has access
		    if ( in_array( $post_object, $programs ) ) {
		    	return;
		    }

		}

	    // Add the step program restricted message to steps
	    if ( is_singular('wampum_step') ) {
		    add_filter( 'the_content', array( $this, 'step_restricted_message' ) );
		}

	    // Filter the restricted message
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
	public function step_restricted_message( $content ) {
    	$post_id = Wampum()->content->get_step_program_id( get_queried_object() );
		return wc_memberships()->frontend->get_content_restricted_message($post_id);
	}

    /**
     * Get the restricted message, with product link(s) and maybe login form
     *
     * @since  1.3.0
     *
     * @return mixed
     */
    function noaccess_restricted_message( $message, $post_id, $products ) {

    	$open  = '<div class="wampum-noaccess-container">';
    	$open .= '<div class="wampum-noaccess-wrap">';
		$open .= '<div class="wampum-noaccess-message">';

			$home = '<a style="float:left;" href="' . home_url() . '">← Go Home</a>';

			$account = '';
			if ( is_user_logged_in() ) {
				$account = '<a style="float:right;" href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '">My Account →</a>';
			}

			$links = '<p style="text-align:center;overflow:hidden;">' . $home . $account . '</p>';

				if ( $products ) {

					$message .= '<div class="noaccess-products">';

						$message .= '<h2>Get Access Now</h2>';

				    	foreach ( $products as $product_id ) {

		 					$product = new WC_Product($product_id);

			    			$message .= '<p class="noaccess-product">';
			 					$message .= '<a class="button" href="' . get_permalink($product_id) . '">' . get_the_title( $product_id ) . ' - ' . $product->get_price_html() . '</a>';
			    				$message .= $product->post_excerpt;
							$message .= '</p>';
						}

					$message .= '</div>';

		    	}

		    // Show login form if not logged in
		    if ( ! is_user_logged_in() ) {

		    	$message .= '<h2>Already have access?</h2>';

				$message .= '<div class="noaccess-login">';
					$message .= '<h3>Login</h3>';
					$message .= wp_login_form( array( 'echo' => false ) );
				$message .= '</div>';

			}

    	$close  = '</div>';
    	$close .= '</div>';
    	$close .= '</div>';

	    return $open . $links . $message . $close;
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
			    background-color: rgba(250,250,250,0.98);
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
			    background-color: transparent;
			    text-align: center;
			    position: absolute;
			    width: 100%;
			    height: 100%;
			    left: 0;
			    top: 0;
			    padding: 10px 20px 30px!important;
			    margin: 0 !important;
			    box-sizing: border-box;
			    overflow: auto;
			}
			.wampum-noaccess-message {
			    position: relative;
			    max-width: 400px;
			    top: 40%;
				-webkit-transform: translate(0, -50%);transform: translate(0, -50%);
			    height: auto;
			    margin: 30px auto;
			    z-index: 1045;
			}
			.wampum-noaccess-message h2 {
				font-size: 24px;
				margin-bottom: 8px;
			}
			.noaccess-products {
				margin: 30px 0;
			}
			.noaccess-product,
			.noaccess-login p {
				margin-bottom: 8px !important;
				overflow: hidden !important;
			}
			.noaccess-login {
				background-color: #fff;
				text-align: left;
				padding: 20px;
				border: 1px solid #e6e6e6;
				border-radius: 3px;
			}
			.noaccess-product .button,
			.noaccess-login input[type="submit"] {
				display: block !important;
				width: 100% !important;
				line-height: 1.2 !important;
				white-space: normal !important;
			}
		</style>
		<?php
    }

	/**
	 * Get a member's purchased programs
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
	public function is_post_content_restricted( $post_id ) {
		return wc_memberships_is_post_content_restricted( $post_id );
	}

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

	public static function can_view_program( $user_id, $program_id ) {
		$programs = $this->get_programs( $user_id );
		if ( ! $programs ) {
			return false;
		}
		foreach ( $programs as $program ) {
			if ( $program->ID == $program_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * NOT WORKING AND NOT USED
	 *
	 * Check if current user can view a specific post/cpt
	 *
	 * @since  1.0.0
	 *
	 * @param  int  $post_id  ID of the post to check access to
	 *
	 * @return bool
	 */
	public static function can_view_step( $user_id, $step_id ) {
		$step = get_post($step_id);
		$program = '';
		return;
	}

	/**
	 * Check if current user can view a specific post
	 *
	 * @since  1.0.0
	 *
	 * @param  int  $post_id  ID of the post to check access to
	 *
	 * @return bool
	 */
	public static function can_view_post( $user_id, $post_id ) {
		return wc_memberships_user_can( $user_id, 'view', array( 'post' => $post_id ) );
	}

	public static function get_login_form( $args ) {
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
