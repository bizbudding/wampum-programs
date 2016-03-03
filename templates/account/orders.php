<?php
/**
 * My Programs tab of Wampum
 */

// Enqueue Wampum default styling
wp_enqueue_style('wampum');

/**
 * Change order url to our custom location to keep it all Wampum ;)
 *
 * @since  1.0.0
 *
 * @param  array   $actions  The order actions
 * @param  object  $order    The order object
 *
 * @return  string The order URL
 */
add_filter( 'woocommerce_my_account_my_orders_actions', function( $actions, $order ) {
	// global $wampum_account_page;
	// Current URL with query args
	// $current_url = $wampum_account_page->get_current_url_with_args();
	$current_url = home_url( add_query_arg( null, null ) );
	$actions['view']['url'] = esc_url_raw( add_query_arg( 'order', $order->post->ID, $current_url ) );
	// $actions['view']['url'] = esc_url_raw( add_query_arg( 'order', $order->post->ID, $current_url ) );
	return $actions;
}, 10, 2 );

// If we're viewing a single order
if ( isset($_GET['order']) ) {

	$order_id	= absint($_GET['order']);
	$user_id	= get_current_user_id();
	$order		= wc_get_order( $order_id );

	if ( ! current_user_can( 'view_order', $order_id ) ) {
		$account_page = get_option('wampum_settings')['account_page'];
		echo '<div class="woocommerce-error">' . __( 'Invalid order.', 'wampum' ) . ' <a href="' . get_permalink( $account_page ) . '" class="wc-forward">' . __( 'Back to', 'wampum' ) . ' ' . get_the_title( $account_page ) . '</a></div>';
		return;
	}

	// Back to all orders notice
	global $wampum_account_page;
	$message = '<a href="' . $wampum_account_page->get_menu_item_url('orders') . '">' . __( 'Back to all orders', 'wampum' ) . '</a>';
	wc_print_notice( $message, 'notice' );

	wc_get_template( 'myaccount/view-order.php', array(
        'order'     => $order,
        'order_id'  => $order_id
    ) );

    // WooCommerce thing. Do we want/need this? If so, where?
	// do_action( 'woocommerce_view_order', $order->post->ID );

} else {

	// Get logged in users orders
	// https://github.com/woothemes/woocommerce/blob/60e8432474fe4bbf7ef48052c0ca9fe86e08feac/templates/myaccount/my-orders.php#L34
	$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
		'numberposts' => 12,
		'meta_key'    => '_customer_user',
		'meta_value'  => get_current_user_id(),
		'post_type'   => wc_get_order_types( 'view-orders' ),
		'post_status' => array_keys( wc_get_order_statuses() )
	) ) );

	if ( $customer_orders ) {

	    foreach ( $customer_orders as $customer_order ) {

		    $order = wc_get_order( $customer_order );

		    wc_get_template( 'myaccount/my-orders.php', array(
		        'current_user'  => get_user_by( 'id', get_current_user_id() ),
		        'order_count'   => -1,
		    ) );

	    }
	    // WooCommerce thing. Do we want/need this? If so, where?
		// do_action( 'woocommerce_view_order', $order->post->ID );
	}
	wp_reset_postdata();

}

do_action('wampum_account_after_orders');
