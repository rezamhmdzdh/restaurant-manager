<?php
defined('ABSPATH') || exit;

/**
 * Get latest restaurant orders
 *
 * @return array
 */
function rm_get_orders_list()
{

    if (!class_exists('WooCommerce')) {
        return [];
    }

    $orders = wc_get_orders([
        'limit' => 15,
//        'status'  => ['pending', 'processing'],
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
//    $orders = wc_get_orders([]);


    $data = [];


    foreach ($orders as $order) {
        /** @var WC_Order $order */

        $items = [];

        foreach ($order->get_items() as $item) {
            $items[] = [
                'name' => $item->get_name(),
                'qty' => $item->get_quantity(),
            ];
        }

        $data[] = [
            'id' => $order->get_id(),
            'customer' => trim(
                $order->get_billing_first_name() . ' ' .
                $order->get_billing_last_name()
            ),
            'status' => wc_get_order_status_name($order->get_status()),
            'items' => $items,
            'total' => $order->get_total(),
            'time' => $order->get_date_created()
                ? $order->get_date_created()->date('H:i')
                : '',
        ];
    }

    return $data;

}