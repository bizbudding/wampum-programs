<?php
/**
 * My Programs tab of Wampum
 *
 * @uses  /woocommerce-memberships/includes/frontend/class-memberships-frontend.php
 * @uses  /woocommerce-memberships/templates/myaccount/my-memberships.php
 */

wp_enqueue_style('wampum');

/**
 *
 *
 * @uses  /woocommerce-memberships/includes/wc-memberships-template-functions.php
 */
add_filter( 'wc_memberships_members_area_my-memberships_actions', 'wampum_account_program_action_links', 10, 3 );
function wampum_account_program_action_links( $default_actions, $user_membership, $object ) {

	$rules = $user_membership->get_plan()->get_content_restriction_rules();

	// echo '<pre>';
	// print_r($rules->object_ids);
    // echo '</pre>';

	// if ( wc_memberships_user_can( $user_membership->get_user_id(), 'view', array( 'post' => $object->ID ) ) ) {
	// 	$default_actions['view'] = array(
	// 		'url'  => get_permalink( $object->ID ),
	// 		'name' => __( 'View', 'woocommerce-memberships' ),
	// 	);
	// }

	// echo '<pre>';
	// print_r(wc_memberships()->rules->rules);
 //    // print_r($default_actions['view']['url']);
 //    echo '</pre>';
}

// global $wampum_member, $wampum_programs;
// $programs = $wampum_member->get_programs( get_current_user_id() );

// foreach ( $programs as $program ) {
//     echo '<div class="">';
//     echo '<a href="' . $wampum_programs->get_link($program) . '">' . $program->name . '</a>';
//     echo '</div>';
// }

$current_user_id	  = get_current_user_id();
$customer_memberships = wc_memberships_get_user_memberships( $current_user_id );

foreach ( $customer_memberships as $customer_membership ) {
	if ( ! $customer_membership->get_plan() ) {
		continue;
	}
	$membership_rules   = $customer_membership->get_plan()->get_content_restriction_rules();
	// $membership_objects = $membership_rules->object_ids;

	echo '<pre>';
    print_r($membership_rules->posts);
    echo '</pre>';

	// if ( $rules->object_ids )
		// $plan = wc_memberships_get_membership_plan($customer_membership->plan_id);
		?>
		<div style="clear:both;" class="wampum-membership">

			<div class="flex-cols">
				<h3><?php echo esc_html( $customer_membership->get_plan()->get_name() ); ?></h3>
				<!-- wc_memberships_is_user_active_member( $current_user_id, $plan ); -->
			</div>

			<div class="flex-cols flex-col-sm-4-8">

				<div class="col first membership-start-date">
					<h4>Signed Up</h4>
					<?php if ( $start_date = $customer_membership->get_local_start_date( 'timestamp' ) ) : ?>
						<time datetime="<?php echo date( 'Y-m-d', $start_date ); ?>" title="<?php echo esc_attr( date_i18n( wc_date_format(), $start_date ) ); ?>"><?php echo date_i18n( wc_date_format(), $start_date ); ?></time>
					<?php else : ?>
						<?php esc_html_e( 'N/A', 'wampum' ); ?>
					<?php endif; ?>
				</div>

				<div class="col flex-cols flex-col-sm-3">
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

					<div class="membership-actions order-actions">
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
				</div>

			</div>
		</div>
		<?php
	// echo '<pre>';
 //    print_r($plan);
 //    echo '</pre>';
}


// echo '<pre>';
// print_r($memberships);
// echo '</pre>';