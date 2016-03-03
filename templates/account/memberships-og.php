<?php
/**
 * My Memberships tab of Wampum
 *
 */

wp_enqueue_style('wampum');

/**
 *
 *
 * @uses  /woocommerce-memberships/includes/wc-memberships-template-functions.php
 */
add_filter( 'wc_memberships_members_area_my-memberships_actions', 'wampum_account_program_action_links', 10, 3 );
function wampum_account_program_action_links( $default_actions, $user_membership, $object ) {
	// Remove the 'View' button until we figure out what to do
	unset($default_actions['view']);
	return $default_actions;
	// echo '<pre>';
	// print_r($default_actions);
    // echo '</pre>';
}

global $wampum_membership;
$customer_memberships = wampum_get_user_memberships( get_current_user_id() );

foreach ( $customer_memberships as $customer_membership ) {
	$plan = wampum_get_user_membership($customer_membership);
	if ( ! $plan ) {
		continue;
	}
	$plan_id = $customer_membership->get_plan()->post->ID;

    $image = sprintf( '<a class="%s" href="%s" title="%s">%s</a>',
		'wp-post-image',
		get_permalink(),
		the_title_attribute( 'echo=0' ),
		get_the_post_thumbnail( $plan_id, 'thumbnail' )
	);
	?>
	<div class="wampum-membership">

		<!-- <div class="flex-cols flex-col-xs-2-10"> -->

			<!-- <div class="col"> -->
				<!-- <?php echo $image; ?> -->
			<!-- </div> -->

			<div class="col flex-cols flex-col-sm-3">
				<!-- <div style="display:inline-block;"><h3><?php echo esc_html( $customer_membership->get_plan()->get_name() ); ?></h3></div> -->
				<div style="width:100%;"><h3><?php echo esc_html( $customer_membership->get_plan()->get_name() ); ?></h3></div>

				<div class="col first membership-start-date">
					<h4>Signed Up</h4>
					<?php if ( $start_date = $customer_membership->get_local_start_date( 'timestamp' ) ) : ?>
						<time datetime="<?php echo date( 'Y-m-d', $start_date ); ?>" title="<?php echo esc_attr( date_i18n( wc_date_format(), $start_date ) ); ?>"><?php echo date_i18n( wc_date_format(), $start_date ); ?></time>
					<?php else : ?>
						<?php esc_html_e( 'N/A', 'wampum' ); ?>
					<?php endif; ?>
				</div>

				<!-- <div class="col flex-cols flex-col-sm-3"> -->
					<div class="col membership-end-date">
						<h4>Expires</h4>
						<?php if ( $end_date = $customer_membership->get_local_end_date( 'timestamp' ) ) : ?>
							<time datetime="<?php echo date( 'Y-m-d', $end_date ); ?>" title="<?php echo esc_attr( date_i18n( wc_date_format(), $end_date ) ); ?>"><?php echo date_i18n( wc_date_format(), $end_date ); ?></time>
						<?php else : ?>
							<?php esc_html_e( 'N/A', 'wampum' ); ?>
						<?php endif; ?>
					</div>

					<div class="col membership-status" style="text-align:left; white-space:nowrap;">
						<h4>Status</h4>
						<?php echo esc_html( wc_memberships_get_user_membership_status_name( $customer_membership->get_status() ) ); ?>
					</div>

					<div class="col membership-actions order-actions">
						<h4>Actions</h4>
						<?php
						global $post;
						echo wc_memberships_get_members_area_action_links( 'my-memberships', $customer_membership, $post );
						// Ask confirmation before cancelling a membership
						wc_enqueue_js("
							jQuery( document ).ready( function() {
								$( '.membership-actions' ).on( 'click', '.button.cancel', function( e ) {
									e.stopImmediatePropagation();
									return confirm( '" . esc_html__( 'Are you sure that you want to cancel your membership?', 'woocommerce-memberships' ) . "' );
								} );
							} );
						");
						?>
					</div>
				<!-- </div> -->

			</div>
		<!-- </div> -->
	</div>
	<?php
}
