<?php
/**
 * Plugin Name: Restaurant Manager
 * Plugin URI:  https://example.com/restaurant-manager
 * Description: A lightweight frontend dashboard for restaurant owners to manage inventory, orders, and reviews without accessing the WordPress admin.
 * Version:     1.5.3
 * Author:      Reza Mohammadzadeh
 * License:     GPL-2.0+
 * Text Domain: restaurant-manager
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('RM_PATH', plugin_dir_path(__FILE__));
define('RM_URL',  plugin_dir_url(__FILE__));
define('RM_VER',  '1.5.4');

/**
 * Register the dashboard shortcode.
 */
add_shortcode('restaurant_dashboard', 'rm_dashboard_shortcode');

function rm_dashboard_shortcode() {
    if (!current_user_can('manage_options')) {
        return '<p style="color:red; text-align:center;">شما دسترسی لازم برای مشاهده این صفحه را ندارید.</p>';
    }

    ob_start();
    include RM_PATH . 'dashboard/dashboard.php';
    return ob_get_clean();
}
/**
 * Load all module management files.
 * Since all tabs load on the same page, we include modules upfront for simplicity and performance.
 */
require_once RM_PATH . 'includes/modules/inventory/inventory.php';
require_once RM_PATH . 'includes/modules/orders/orders-management.php';

/**
 * Enqueue dashboard assets.
 * Assets are only loaded when the shortcode is present (via wp_enqueue_scripts priority).
 */
add_action('wp_enqueue_scripts', 'rm_enqueue_assets');
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