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
