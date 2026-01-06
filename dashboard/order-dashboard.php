<?php
defined('ABSPATH') || exit;

/**
 * Get orders
 */
$orders = wc_get_orders([
    'limit' => 20,
    'orderby' => 'date',
    'order' => 'DESC',
]);
?>

<div class="rm-orders">

    <?php if (empty($orders)) : ?>
        <p>هیچ سفارشی ثبت نشده است.</p>
    <?php endif; ?>

    <?php foreach ($orders as $order) : ?>

        <div class="rm-order-card status-<?php echo esc_attr($order->get_status()); ?>">

            <!-- Header -->
            <div class="rm-order-header">
                <strong>سفارش #<?php echo $order->get_id(); ?></strong>
                <span class="rm-status"><?php echo wc_get_order_status_name($order->get_status()); ?></span>
            </div>

            <!-- Main info -->
            <div class="rm-order-main">
                <p><strong>مشتری:</strong> <?php echo esc_html($order->get_formatted_billing_full_name()); ?></p>
                <p><strong>مبلغ:</strong> <?php echo $order->get_formatted_order_total(); ?></p>
            </div>

            <!-- Items -->
            <div class="rm-order-items">
                <strong>اقلام سفارش:</strong>
                <ul>
                    <?php foreach ($order->get_items() as $item) : ?>
                        <li>
                            <?php echo esc_html($item->get_name()); ?>
                            × <?php echo $item->get_quantity(); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Accordion -->
            <details class="rm-order-details">
                <summary>مشاهده جزئیات</summary>

                <p><strong>تلفن:</strong> <?php echo esc_html($order->get_billing_phone()); ?></p>
                <p><strong>آدرس:</strong> <?php echo wp_kses_post($order->get_formatted_billing_address()); ?></p>
                <p><strong>یادداشت مشتری:</strong> <?php echo esc_html($order->get_customer_note()); ?></p>

                <!-- Status update -->

                <label>وضعیت سفارش:</label>
                <select class="rm-order-status" data-order-id="<?php echo esc_attr($order->get_id()); ?>">
                    <?php foreach (wc_get_order_statuses() as $key => $label) : ?>
                        <?php $status = str_replace('wc-', '', $key); ?>
                        <option value="<?php echo esc_attr($status); ?>"
                            <?php selected($order->get_status(), $status); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="rm-order-status-notice" style="display:none;"></div>
            </details>
        </div>

    <?php endforeach; ?>

</div>
