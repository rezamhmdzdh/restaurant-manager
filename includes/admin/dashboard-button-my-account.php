<?php

add_action('wp_enqueue_scripts', function () {
    if (!is_account_page() || !is_user_logged_in()) return;

    if ( ! current_user_can('manage_woocommerce') ) {
        return;
    }

    wp_add_inline_script('jquery', '
        document.addEventListener("DOMContentLoaded", function () {
            var wrapper = document.querySelector(".nav-container .nav-wrapper");
            if (!wrapper) return;

            var a = document.createElement("a");
            a.href = ' . json_encode(site_url('/restaurant-dashboard/')) . ';
            a.classList.add("link-to-rm-dashboaed");
            a.textContent = "داشبورد رستوران";

            wrapper.insertBefore(a, wrapper.firstChild);
        });
    ');
});
