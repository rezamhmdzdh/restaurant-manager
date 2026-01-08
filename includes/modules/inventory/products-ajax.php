<?php

defined('ABSPATH') || exit;

add_action('wp_ajax_rm_inventory_get_products', 'rm_inventory_get_products_ajax');

function rm_inventory_get_products_ajax()
{
    check_ajax_referer('RM_UPDATE_INVENTORY', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'دسترسی ممنوع']);
    }

    $grouped_products = rm_inventory_get_grouped_products();
    $args = [
        'sections' => 'top_tabs',
        'cards' => 'style_1',
    ];

    ob_start();
    include __DIR__ . '/templates/main-layout.php';
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}


add_action('wp_ajax_rm_inventory_update_product', 'rm_inventory_update_product_ajax');
function rm_inventory_update_product_ajax()
{

    check_ajax_referer('RM_UPDATE_INVENTORY', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('no_access');
    }

    $product_id = intval($_POST['product_id'] ?? 0);
    $stock      = intval($_POST['stock'] ?? 0);
    $active     = isset($_POST['active']) ? filter_var($_POST['active'], FILTER_VALIDATE_BOOLEAN) : true;

    $updated = rm_inventory_update_product_stock(
        $product_id,
        $stock,
        $active
    );

    if ($updated) {
        wp_send_json_success(['message' => 'ذخیره شد']);
    } else {
        wp_send_json_error(['message' => 'خطا در ذخیره']);
    }
}


add_action('wp_ajax_rm_inventory_mark_outofstock', 'rm_inventory_mark_outofstock_ajax');
function rm_inventory_mark_outofstock_ajax()
{
    check_ajax_referer('RM_UPDATE_INVENTORY', 'nonce');

    $product_id = intval($_POST['product_id']);

    $done = rm_inventory_mark_outofstock($product_id);

    if ($done) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
