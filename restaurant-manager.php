<?php
/**
 * Plugin Name: Restaurant Manager
 * Plugin URI:  https://example.com/restaurant-manager
 * Description: A lightweight frontend dashboard for restaurant owners to manage inventory, orders, and reviews without accessing the WordPress admin.
 * Version:     1.5.7
 * Author:      Reza Mohammadzadeh
 * License:     GPL-2.0+
 * Text Domain: restaurant-manager
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('RM_PATH', plugin_dir_path(__FILE__));
define('RM_URL',  plugin_dir_url(__FILE__));
define('RM_VER',  '1.5.7');

/**
 * Register the dashboard shortcode.
 */
add_shortcode('restaurant_dashboard', 'rm_dashboard_shortcode');

function rm_dashboard_shortcode() {
    if (!current_user_can('manage_woocommerce')) {
        return '<p style="color:red; text-align:center;">شما دسترسی لازم برای مشاهده این صفحه را ندارید.</p>';
    }
    rm_enqueue_assets();
    rm_enqueue_order_scripts();
    rm_inventory_scripts();

    ob_start();
    include RM_PATH . 'dashboard/dashboard.php';
    return ob_get_clean();
}
/**
 * Load all module management files.
 */
require_once RM_PATH . 'includes/modules/inventory/inventory.php';
require_once RM_PATH . 'includes/modules/orders/orders-management.php';

/**
 * Enqueue dashboard assets.
 */

//add_action('wp_enqueue_scripts', 'rm_enqueue_assets');
function rm_enqueue_assets() {
    wp_enqueue_style(
        'rm-dashboard-css',
        RM_URL . 'assets/dashboard.css',
        [],
        RM_VER
    );

    wp_enqueue_script(
        'rm-dashboard-js',
        RM_URL . 'assets/dashboard.js',
        ['jquery'],
        RM_VER,
        true
    );
}

function rm_enqueue_order_scripts() {

    wp_enqueue_script(
        'rm-orders-js',
        RM_URL . 'includes/modules/orders/orders.js',
        ['jquery'],RM_VER,
        true
    );

    wp_localize_script('rm-orders-js', 'rm_orders_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('RM_UPDATE_ORDER_STATUS'),
        'page_loaded_at' => time(),
    ]);


}
function rm_inventory_scripts()
{
    wp_enqueue_script(
        'rm-inventory',
        RM_URL . 'includes/modules/inventory/inventory.js', [], RM_VER, true);

    wp_localize_script('rm-inventory', 'rm_inventory_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('RM_UPDATE_INVENTORY'),
    ]);
}

function rm_hide_admin_bar_on_dashboard($show) {
    if (is_page('restaurant-dashboard')) {
        return false;
    }
    return $show;
}
add_filter('show_admin_bar', 'rm_hide_admin_bar_on_dashboard');