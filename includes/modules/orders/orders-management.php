<?php
defined('ABSPATH') || exit;


add_action('wp_enqueue_scripts', 'rm_enqueue_order_scripts');
require_once __DIR__ . '/orders-ajax.php';

/**
 * Get full order data for dashboard
 *
 * @return array
 */
function rm_get_orders_for_dashboard() {

    if ( ! class_exists('WooCommerce') ) {
        return [];
    }

    $query = new WC_Order_Query([
        'limit'   => 10,
        'orderby' => 'date',
        'order'   => 'DESC',
        'return'  => 'objects',
    ]);

    $orders = $query->get_orders();
    $data   = [];

    foreach ( $orders as $order ) {

        // Order items
        $items = [];
        foreach ( $order->get_items() as $item ) {
            $items[] = [
                'name' => $item->get_name(),
                'qty'  => $item->get_quantity(),
                'total'=> wc_price( $item->get_total() ),
            ];
        }

        $data[] = [
            'id'       => $order->get_id(),
            'status'   => wc_get_order_status_name( $order->get_status() ),
            'status_key' => $order->get_status(),

            'customer' => trim(
                $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
            ),

            'total'    => wc_price( $order->get_total() ),
            'date'     => $order->get_date_created()
                ? $order->get_date_created()->date('Y/m/d H:i')
                : '',

            'items'    => $items,

            'phone'    => $order->get_billing_phone(),
            'address'  => $order->get_formatted_billing_address(),
            'payment'  => $order->get_payment_method_title(),
            'note'     => $order->get_customer_note(),
        ];
    }

    return $data;
}
function rm_enqueue_order_scripts() {

     wp_enqueue_script(
            'rm-orders-js',
            plugin_dir_url(__FILE__) . 'orders.js',
             ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('rm-orders-js', 'rm_orders_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('RM_UPDATE_ORDER_STATUS')
        ]);

}
