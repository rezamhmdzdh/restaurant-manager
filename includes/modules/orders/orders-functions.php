<?php
defined('ABSPATH') || exit;

/**
 * Get list of orders for dashboard table
 * Only shows relevant statuses for restaurant: pending, processing, on-hold
 */
function rm_get_orders_list() {
    $args = array(
        'limit'         => 50,
        'status'        => array('pending', 'processing', 'on-hold'),
        'orderby'       => 'date',
        'order'         => 'DESC',
    );

    $orders = wc_get_orders($args);

    $list = [];
    foreach ($orders as $order) {
        $list[] = array(
            'id'         => $order->get_id(),
            'customer'   => $order->get_formatted_billing_full_name() ?: 'مهمان',
            'status'     => wc_get_order_status_name($order->get_status()),
            'status_key' => $order->get_status(), // برای کلاس CSS
            'date'       => $order->get_date_created()->date_i18n('Y/m/d H:i'),
            'total'      => $order->get_formatted_order_total(),
        );
    }

    return $list;
}

/**
 * Get full details of a single order for modal
 */
function rm_get_order_details($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        return false;
    }

    $items = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $items[] = array(
            'name'     => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'price'    => wc_price($item->get_subtotal() / $item->get_quantity()),
            'total'    => wc_price($item->get_total()),
            'image'    => $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : '',
        );
    }

    return array(
        'id'             => $order->get_id(),
        'status'         => $order->get_status(),
        'status_name'    => wc_get_order_status_name($order->get_status()),
        'subtotal'       => wc_price($order->get_subtotal()),
        'shipping'       => wc_price($order->get_shipping_total()),
        'tax'            => wc_price($order->get_total_tax()),
        'total'          => $order->get_formatted_order_total(),
        'notes'          => $order->get_customer_note(),
        'items'          => $items,
    );
}