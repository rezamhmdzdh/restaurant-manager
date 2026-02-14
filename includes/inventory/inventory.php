<?php
defined('ABSPATH') || exit;

if (!is_admin()) {

    if (is_page(4589)) {
        require_once __DIR__ . '/products-query.php';
        require_once __DIR__ . '/products-edit.php';
        require_once __DIR__ . '/products-ajax.php';
    }

}