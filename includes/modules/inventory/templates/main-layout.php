<?php
/**
 * Main inventory layout
 * Similar to the "Reyhoon" menu from main.php file
 */
defined('ABSPATH') || exit;

/** @var array $grouped_products */
/** @var array $args */
?>
<div class="reyhoon-main <?php echo esc_attr(apply_filters('reyhoon_main_class', '', $args)); ?>">
    <?php //do_action('reyhoon_main_before_sections', $args, $grouped_products); ?>
    <?php if (!empty($grouped_products)): ?>
        <?php

        if (!empty($grouped_products)):
            ?>
            <div class="reyhoon-main__tabs-wrapper">
                <?php do_action('reyhoon_before_tabs'); ?>
                <ul class="reyhoon-main__tabs">
                    <?php $i = 0;
                    foreach ($grouped_products as $group):
                        $category = $group['category'];
                        $thumbnail_id = $category['thumbnail_id'] ?? get_term_meta($category['id'], 'thumbnail_id', true);
                        $image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                        $classes = ($i === 0) ? ['reyhoon-tabs__item--active'] : [];
                        ?>
                        <li class="reyhoon-tabs__item <?php echo esc_attr(implode(' ', $classes)); ?>]" role="tab">
                            <a href="#category-<?php echo esc_attr($category['id']); ?>" class="reyhoon-tabs__link">
                                <?php echo esc_html($category['name']); ?>
                            </a>
                        </li>
                        <?php $i++; endforeach; ?>
                </ul>
                <div class="fix-sticky"></div>
            </div>
        <?php endif; ?>


        <div class="reyhoon-main__sections">
            <?php foreach ($grouped_products as $group):
                $category = $group['category'];
                $products = $group['products'];
                ?>
                <div id="category-<?php echo esc_attr($category['id']); ?>" class="reyhoon-main__group">

                    <h3 class="reyhoon-tab-title">
                        <span>
                            <?php echo esc_html($category['name']); ?>
                        </span>
                    </h3>

                    <div class="reyhoon-products-list reyhoon-products-list--grid">
                        <?php foreach ($products as $product):
                            $stock_qty = $product->get_stock_quantity() ?? 0;
                            ?>
                            <?php include __DIR__ . '/product-card-admin.php'; ?>
                        <?php endforeach; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>هیچ محصولی یافت نشد.</p>
    <?php endif; ?>

    <?php do_action('reyhoon_main_after_sections', $args, $grouped_products); ?>
</div>