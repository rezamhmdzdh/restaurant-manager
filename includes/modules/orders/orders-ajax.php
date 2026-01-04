<?php
if (!defined('ABSPATH')) exit;

function rm_orders_ajax_handler() {
    check_ajax_referer('rm_orders_nonce', 'nonce');

    $action = sanitize_text_field($_POST['sub_action']);

    switch ($action) {
        case 'get_list':
            $orders = rm_get_orders_list(['pending', 'processing', 'on-hold']);
            wp_send_json_success($orders);
            break;

        case 'get_details':
            $order_id = intval($_POST['order_id']);
            $details = rm_get_order_details($order_id);
            if ($details) {
                wp_send_json_success($details);
            } else {
                wp_send_json_error('سفارش یافت نشد');
            }
            break;

        case 'update_order':
            $order_id = intval($_POST['order_id']);
            $order = wc_get_order($order_id);

            if (!$order) {
                wp_send_json_error('سفارش معتبر نیست');
            }

            // به‌روزرسانی وضعیت
            if (!empty($_POST['status'])) {
                $order->update_status(sanitize_text_field($_POST['status']), 'به‌روزرسانی از پنل رستوران');
            }

            // یادداشت مشتری
            if (!empty($_POST['notes'])) {
                $order->set_customer_note(sanitize_textarea_field($_POST['notes']));
                $order->save();
            }

            wp_send_json_success('سفارش با موفقیت به‌روزرسانی شد');
            break;

        default:
            wp_send_json_error('اقدام نامعتبر');
    }
}

add_action('wp_ajax_rm_orders', 'rm_orders_ajax_handler');