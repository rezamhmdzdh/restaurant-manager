<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_rm_change_order_status', 'rm_change_order_status');

function rm_change_order_status() {

    check_ajax_referer('RM_UPDATE_ORDER_STATUS', 'nonce');

//    if ( ! current_user_can('manage_woocommerce') ) {
//        wp_send_json_error('دسترسی غیرمجاز');
//    }

    $order_id = intval($_POST['order_id'] ?? 0);
    $status   = sanitize_text_field($_POST['status'] ?? '');

    if ( ! $order_id || ! $status ) {
        wp_send_json_error('اطلاعات ناقص است');
    }

    $order = wc_get_order($order_id);

    if ( ! $order ) {
        wp_send_json_error('سفارش یافت نشد');
    }

    $order->update_status(
        $status,
        'تغییر وضعیت از پنل رستوران',
        false
    );

    wp_send_json_success([
        'order_id' => $order_id,
        'status'   => $status,
    ]);
}
