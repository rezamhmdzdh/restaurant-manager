<?php
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'rm_inventory_scripts');
require_once __DIR__ . '/products-query.php';
require_once __DIR__ . '/products-edit.php';
require_once __DIR__ . '/ajax.php';

function rm_inventory_scripts() {
    wp_enqueue_script(
        'rm-inventory',
        RM_URL . 'includes/modules/inventory/inventory.js',[], 'RM_VER',true);

//    wp_localize_script('rm-inventory', 'RM', [
//        'ajax'  => admin_url('admin-ajax.php'),
//        'nonce' => wp_create_nonce('rm_nonce')
//    ]);
}