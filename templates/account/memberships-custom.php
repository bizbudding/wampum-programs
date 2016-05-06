<?php
/**
 * My Memberships tab of Wampum
 *
 */

wp_enqueue_style('wampum');

echo '<h2>My Memberships</h2>';

// $memberships = wc_memberships_get_user_memberships();
$memberships = wampum_get_user_memberships( get_current_user_id() );

// wc_get_template( 'myaccount/my-memberships.php', array(
// 	'customer_memberships' => $customer_memberships,
// 	'user_id'              => get_current_user_id(),
// ) );

if ( ! $memberships ) {
	$text = 'You don\'t have any memberships yet.';
	$text = apply_filters( 'wampum_account_memberships_no_memberships_text', $text );
	echo "<p>{$text}</p>";
} else {

	echo '<ul class="account-items" style="margin-left:0;">';

		// Heading
		echo '<li class="account-item account-heading">';
			echo '<span class="item-col item-image"></span>';
			echo '<span class="item-col item-content">Title</span>';
			echo '<span class="item-col item-start-date">Signed Up</span>';
			echo '<span class="item-col item-end-date">Expires</span>';
			echo '<span class="item-col item-status">Status</span>';
			if ( class_exists('WC_Subscriptions') ) {
				echo '<span class="item-col item-invoice-date">Next Bill on</span>';
			}
			echo '<span class="item-col item-actions"></span>';
		echo '</li>';

		foreach ( $memberships as $membership ) {

			// $membership = $membership->post;
			$plan = $membership->get_plan();
			// echo '<pre>';
		 //    print_r($membership);
		 //    echo '</pre>';

			$image_size = apply_filters('wampum_account_memberships_image_size', 'thumbnail');

			$image = '';
			if ( has_post_thumbnail( $plan->id ) ) {
			    $image = sprintf( '<a href="%s" title="%s">%s</a>',
					get_permalink( $plan->id ),
					the_title_attribute( 'echo=0' ),
					get_the_post_thumbnail( $plan->id, $image_size )
				);
			}

			$actions = Wampum()->membership->get_membership_actions( $membership );
			$item_actions = '';
			foreach ( $actions as $action ) {
				$item_actions .= '<a class="button" href="' . esc_url($action['url']) . '">' . esc_html($action['name']) . '</a>';
			}

			// Items
			echo '<li class="account-item">';
				// Image
				echo '<span class="item-col item-image">' . $image . '</span>';
				// Content/Title
				echo '<span class="item-col item-content">' . $plan->name . '</span>';
				// Start Date
				echo '<span class="item-col item-start-date">';
					if ( $start_date = $membership->get_local_start_date( 'timestamp' ) ) {
						echo '<time datetime="' . date( 'Y-m-d', $start_date ) . '" title="' . esc_attr( date_i18n( wc_date_format(), $start_date ) ) . '">' . date_i18n( wc_date_format(), $start_date ) . '</time>';
					} else {
						echo esc_html( 'N/A', 'wampum' );
					}
				echo '</span>';
				// End Date
				echo '<span class="item-col item-end-date">';
					if ( $end_date = $membership->get_local_end_date( 'timestamp' ) ) {
						echo '<time datetime="' . date( 'Y-m-d', $end_date ) . '" title="' . esc_attr( date_i18n( wc_date_format(), $end_date ) ) . '">' . date_i18n( wc_date_format(), $end_date ) . '</time>';
					} else {
						echo esc_html( 'N/A', 'wampum' );
					}
				echo '</span>';
				// Status
				echo '<span class="item-col item-status">' . esc_html( wc_memberships_get_user_membership_status_name( $membership->get_status() ) ) . '</span>';
				// Subscription
				if ( class_exists('WC_Subscriptions') ) {
					// THIS IS TOTALLY NOT WORKING
					// echo Wampum()->memberships->get_membership_subscription_column( $membership );
				}
				// Actions
				echo '<span class="item-col item-actions">' . $item_actions . '</span>';
					// Ask confirmation before cancelling a membership
					wc_enqueue_js("
						jQuery( document ).ready( function() {
							$( '.membership-actions' ).on( 'click', '.button.cancel', function( e ) {
								e.stopImmediatePropagation();
								return confirm( '" . esc_html__( 'Are you sure that you want to cancel your membership?', 'wampum' ) . "' );
							} );
						} );
					");
			echo '</li>';
		}

	echo '</ul>';

}

do_action('wampum_account_after_memberships');
