<?php

defined('ABSPATH') || exit;

/**
 * Load order management module files
 */
require_once __DIR__ . '/orders-functions.php';
require_once __DIR__ . '/orders-ajax.php';

/**
 * Enqueue orders scripts
 */
add_action('wp_enqueue_scripts', 'rm_enqueue_orders_assets');
function rm_enqueue_orders_assets() {

    wp_enqueue_script(
        'rm-orders',
        RM_URL . 'includes/modules/orders/orders.js',
        ['jquery'],
        RM_VER,
        true
    );

    wp_localize_script('rm-orders', 'RM_Orders', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('rm_nonce')
    ]);
}