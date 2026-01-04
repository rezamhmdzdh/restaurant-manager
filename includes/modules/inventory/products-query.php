<?php

defined('ABSPATH') || exit;
function rm_inventory_get_grouped_products()
{
    if (!class_exists('WooCommerce')) {
        return [];
    }
    $all_products = wc_get_products([
        'limit' => -1,
        'status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ]);


    $parent_categories = get_terms([
        'taxonomy' => 'product_cat',
        'parent' => 0,
        'hide_empty' => true,
        'orderby' => 'term_order'
    ]);

    $grouped = [];

    foreach ($parent_categories as $parent_cat) {


        $cat_products = array_filter($all_products, function ($product) use ($parent_cat) {
            $terms = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'ids']);
            if (is_wp_error($terms) || empty($terms)) return false;

            $children = get_term_children($parent_cat->term_id, 'product_cat');
            $target_ids = array_merge([$parent_cat->term_id], $children);

            return !empty(array_intersect($terms, $target_ids));
        });

        if (empty($cat_products)) {
            continue;
        }

        $grouped[] = [
            'category' => [
                'id' => $parent_cat->term_id,
                'name' => $parent_cat->name,
            ],
            'products' => array_values($cat_products), // reset keys
        ];
    }

    return $grouped;
}
