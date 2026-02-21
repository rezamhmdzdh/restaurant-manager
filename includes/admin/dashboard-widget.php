<?php

add_action('wp_dashboard_setup', function () {


    if ( ! current_user_can('manage_woocommerce') ) {
        return;
    }

    wp_add_dashboard_widget(
        'restaurant_dashboard_widget',
        'ورود به داشبورد مدیریت رستوران',
        'render_restaurant_dashboard_widget'
    );
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
    <?php
}