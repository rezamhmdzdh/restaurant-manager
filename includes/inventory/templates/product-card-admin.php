<?php
defined('ABSPATH') || exit;
/** @var WC_Product $product */
?>
<div class="reyhoon-products-list__item">
    <div class="reyhoon-product rm-product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">

        <div class="food-info">
            <div class="reyhoon-product__content">
                <h2 class="reyhoon-product__title"><?php echo esc_html($product->get_name()); ?></h2>
                <div class="reyhoon-product__actions-price">
                    <?php echo $product->get_price_html(); ?>
                </div>
            </div>

            <div class="reyhoon-product__hero">
                <?php echo $product->get_image('medium'); ?>
            </div>
        </div>
        <!-- manage stock for admin -->
        <div class="rm-product-actions">
            <div class="rm-field">
                <span>موجودی:</span>
                <input type="number" class="rm-stock-input" value="<?php echo esc_attr($stock_qty); ?>" min="0">
            </div>

            <div class="rm-product-actions-buttons">
                <button class="rm-save-product">ذخیره</button>
                <?php if ($stock_qty > 0): ?>
                    <button class="rm-mark-outofstock">اتمام موجودی</button>
                <?php else: ?>
                    <div class="product-is-outofstock">ناموجود است</div>
                <?php endif; ?>
            </div>
            <div class="rm-message"></div>
        </div>
    </div>
</div>