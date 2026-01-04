<?php

defined('ABSPATH') || exit;
function rm_inventory_update_product_stock($product_id, $stock) {

    $product = wc_get_product($product_id);
    if (!$product) return false;

        $product->set_manage_stock(true);
        $product->set_stock_quantity((int) $stock);
        $product->set_stock_status('instock');

    $product->save();
    return true;
}

function rm_inventory_mark_outofstock($product_id) {

    $product = wc_get_product($product_id);
    if (!$product) return false;

    $product->set_manage_stock(true);
    $product->set_stock_quantity(0);
    $product->save();

    return true;
}
