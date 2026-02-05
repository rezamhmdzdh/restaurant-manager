<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_rm_change_order_status', 'rm_change_order_status');

function rm_change_order_status() {

    check_ajax_referer('RM_UPDATE_ORDER_STATUS', 'nonce');

    if ( ! current_user_can('manage_woocommerce') ) {
        wp_send_json_error('دسترسی ندارید');
    }

    $order_id = intval($_POST['order_id'] ?? 0);
    $new_status = sanitize_text_field($_POST['status'] ?? '');

    $order = wc_get_order($order_id);
    if ( ! $order ) {
        wp_send_json_error('سفارش یافت نشد');
    }

    $current_status = $order->get_status();

    $allowed_transitions = [
        'on-hold' => ['processing', 'cancelled'],
        'processing' => ['on-way'],
        'on-way' => ['completed'],
    ];

    if (
        ! isset($allowed_transitions[$current_status]) ||
        ! in_array($new_status, $allowed_transitions[$current_status], true)
    ) {
        wp_send_json_error('تغییر وضعیت مجاز نیست');
    }

    $order->update_status(
        $new_status,
        'تغییر وضعیت از پنل رستوران',
        false
    );

    wp_send_json_success([
        'order_id' => $order_id,
        'status'   => $new_status,
    ]);
}

//check new order
add_action('wp_ajax_rm_has_new_order', 'rm_has_new_order');

function rm_has_new_order() {

    check_ajax_referer('RM_UPDATE_ORDER_STATUS', 'nonce');

    $page_loaded_at = intval($_POST['page_loaded_at'] ?? 0);

    $query = new WC_Order_Query([
        'limit'   => 1,
        'orderby' => 'date',
        'order'   => 'DESC',
        'return'  => 'objects',
        'status' => ['on-hold']
    ]);

    $orders = $query->get_orders();

    if ( empty($orders) ) {
        wp_send_json_success(false);
    }

    $order = $orders[0];
    $created = $order->get_date_created();

    if ( $created && $created->getTimestamp() > $page_loaded_at ) {
        wp_send_json_success(true);
    }

    wp_send_json_success(false);
}
