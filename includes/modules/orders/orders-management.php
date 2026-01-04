<?php
defined('ABSPATH') || exit;

require_once __DIR__ . '/orders-functions.php';
require_once __DIR__ . '/orders-ajax.php';

/**
 * Prepare initial data for orders page
 */
function rm_orders_page_data() {
    return array(
        'orders' => rm_get_orders_list(),
        'nonce'  => wp_create_nonce('rm_orders_nonce'),
    );
}