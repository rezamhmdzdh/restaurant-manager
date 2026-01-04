<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/orders-functions.php';
require_once __DIR__ . '/orders-ajax.php';

/**
 * دریافت داده‌های اولیه برای صفحه سفارشات
 */
function rm_orders_page_data() {
    return array(
        'orders' => rm_get_orders_list(['pending', 'processing', 'on-hold'], 50),
        'nonce'  => wp_create_nonce('rm_orders_nonce'),
    );
}