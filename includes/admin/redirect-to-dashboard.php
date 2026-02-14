<?php

/**
 * redirect shop manager role to restaurant dashboard after login
 */

add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {

    if (is_wp_error($user) || ! $user) {
        return $redirect_to;
    }

    if (in_array('shop_manager', (array) $user->roles, true)) {
        return site_url('/restaurant-dashboard/');
    }

    return $redirect_to;

}, 10, 3);
