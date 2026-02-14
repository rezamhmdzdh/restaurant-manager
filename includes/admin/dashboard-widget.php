<?php

add_action('wp_dashboard_setup', function () {


    if ( ! current_user_can('shop_manager') ) {
        return;
    }

    wp_add_dashboard_widget(
        'restaurant_dashboard_widget',
        'ورود به داشبورد مدیرت رستوران',
        'render_restaurant_dashboard_widget'
    );

    global $wp_meta_boxes;

    $widget = $wp_meta_boxes['dashboard']['normal']['core']['restaurant_dashboard_widget'] ?? null;
    if (!$widget) return;

    unset($wp_meta_boxes['dashboard']['normal']['core']['restaurant_dashboard_widget']);

    $wp_meta_boxes['dashboard']['normal']['high']['core']['restaurant_dashboard_widget'] = $widget;
});

function render_restaurant_dashboard_widget() {

    $url = site_url('/restaurant-dashboard/');

    ?>
    <p>برای مدیریت سفارش‌ها و وضعیت رستوران از اینجا وارد شوید.</p>

    <p>
        <a class="button button-primary button-hero" href="<?php echo esc_url($url); ?>">
            ورود به داشبورد مدیریت رستوران
        </a>
    </p>

    <p style="margin-top:10px; color:#666;">
        نکته: این بخش فقط برای مدیر فروشگاه نمایش داده می‌شود.
    </p>
    <?php
}