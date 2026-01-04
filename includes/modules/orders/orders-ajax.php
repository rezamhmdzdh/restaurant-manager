<?php
defined('ABSPATH') || exit;

function rm_orders_ajax_handler() {
    // Check nonce for security
    check_ajax_referer('rm_orders_nonce', 'nonce');

    $sub_action = sanitize_text_field($_POST['sub_action'] ?? '');

    switch ($sub_action) {

        case 'get_list':
            $orders = rm_get_orders_list();
            wp_send_json_success($orders);
            break;

        case 'get_details':
            $order_id = intval($_POST['order_id'] ?? 0);
            $details = rm_get_order_details($order_id);
            if ($details) {
                wp_send_json_success($details);
            } else {
                wp_send_json_error('سفارش یافت نشد.');
            }
            break;

        case 'update_order':
            $order_id = intval($_POST['order_id'] ?? 0);
            $order = wc_get_order($order_id);

            if (!$order) {
                wp_send_json_error('سفارش معتبر نیست.');
            }

            // Update status if provided
            $new_status = sanitize_text_field($_POST['status'] ?? '');
            if ($new_status && in_array($new_status, array_keys(wc_get_order_statuses()))) {
                $order->update_status($new_status, 'به‌روزرسانی از پنل مدیریت رستوران');
            }

            // Update customer note
            $notes = sanitize_textarea_field($_POST['notes'] ?? '');
            if ($notes !== $order->get_customer_note()) {
                $order->set_customer_note($notes);
                $order->save();
            }

            wp_send_json_success('سفارش با موفقیت به‌روزرسانی شد.');
            break;

        default:
            wp_send_json_error('عملیات نامعتبر است.');
    }
}

add_action('wp_ajax_rm_orders', 'rm_orders_ajax_handler');