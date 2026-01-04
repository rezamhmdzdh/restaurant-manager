<?php
defined('ABSPATH') || exit;

add_shortcode('restaurant_dashboard', 'rm_dashboard_shortcode');

function rm_dashboard_shortcode()
{

    if (!current_user_can('manage_options')) {

        wp_die('no access allowed');
//        return '<p>شما دسترسی ندارید</p>';
    }

    ob_start();
    include RM_PATH . 'dashboard/dashboard.php';
    return ob_get_clean();
}
