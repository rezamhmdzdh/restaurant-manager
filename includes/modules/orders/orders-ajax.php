<?php
defined('ABSPATH') || exit;

/**
 * Ajax handler: Get orders list
 */
add_action('wp_ajax_rm_get_orders', 'rm_ajax_get_orders');
function rm_ajax_get_orders() {

    check_ajax_referer('rm_nonce', 'nonce');

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $orders = rm_get_orders_list();

    wp_send_json_success([
        'orders' => $orders
    ]);
}

/**
 * Get single order details (for modal)
 */
add_action('wp_ajax_rm_get_single_order', 'rm_get_single_order_ajax');
function rm_get_single_order_ajax() {

    check_ajax_referer('rm_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

    if (!$order_id) {
        wp_send_json_error(['message' => 'Invalid order ID']);
    }

    $order = wc_get_order($order_id);

    if (!$order) {
        wp_send_json_error(['message' => 'Order not found']);
    }

    $items = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $items[] = [
            'name'  => $item->get_name(),
            'qty'   => $item->get_quantity(),
            'price' => $product ? wc_price($product->get_price()) : '0',
            'total' => wc_price($item->get_total())
        ];
    }

    $subtotal = $order->get_subtotal();
    $shipping = $order->get_shipping_total();
    $tax      = $order->get_total_tax();
    $total    = $order->get_total();

    wp_send_json_success([
        'id'              => $order->get_id(),
        'customer'        => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
        'phone'           => $order->get_billing_phone(),
        'address'         => $order->get_formatted_billing_address(),
        'status'          => $order->get_status(), // raw status for select field
        'payment_status'  => $order->get_payment_method() ? 'paid' : 'pending', // simple example
        'items'           => $items,
        'subtotal'        => wc_price($subtotal),
        'shipping'        => wc_price($shipping),
        'tax'             => wc_price($tax),
        'total'           => wc_price($total),
        'notes'           => $order->get_customer_note(),
    ]);
}

/**
 * Update order status and notes
 */
add_action('wp_ajax_rm_update_order', 'rm_update_order_ajax');
function rm_update_order_ajax() {

    check_ajax_referer('rm_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status   = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    $payment  = isset($_POST['payment_status']) ? sanitize_text_field($_POST['payment_status']) : '';
    $notes    = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';

    if (!$order_id) {
        wp_send_json_error(['message' => 'Invalid order ID']);
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error(['message' => 'Order not found']);
    }

    // Update order status
    if ($status && in_array($status, ['pending', 'processing', 'completed', 'cancelled'])) {
        $order->update_status($status, 'Updated via Restaurant Manager dashboard');
    }

    // Update notes
    if ($notes !== '') {
        $order->set_customer_note($notes);
    }

    $order->save();

    // Optionally update payment status (simple example)
    // TODO: integrate with actual payment gateway if needed

    wp_send_json_success(['message' => 'Order updated successfully']);
}
