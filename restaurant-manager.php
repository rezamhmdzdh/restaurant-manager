<?php
/**
 * Plugin Name: Restaurant Manager
 * Description: Frontend restaurant management dashboard (Inventory, Orders, Reviews)
 * Version: 1.4.0
 * Author: Reza Mohammadzadeh
 * Text Domain: restaurant-manager
 */

defined('ABSPATH') || exit;


define('RM_PATH', plugin_dir_path(__FILE__));
define('RM_URL', plugin_dir_url(__FILE__));
const RM_VER = '1.4.0';

require_once RM_PATH . 'dashboard/router.php';
// Modules
require_once RM_PATH . 'includes/modules/inventory/inventory.php';


add_action('wp_enqueue_scripts', 'rm_enqueue_assets');
function rm_enqueue_assets()
{
    wp_enqueue_style(
        'rm-dashboard', RM_URL . 'assets/dashboard.css', array(), RM_VER, 'all');
    wp_enqueue_script(
        'rm-dashboard',
        RM_URL . 'assets/dashboard.js', [], RM_VER, true);

//    wp_localize_script('rm-dashboard', 'RM', [
//        'ajax'  => admin_url('admin-ajax.php'),
//        'nonce' => wp_create_nonce('rm_nonce')
//    ]);
}