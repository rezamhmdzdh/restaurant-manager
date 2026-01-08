<div class="rm-orders">

    <?php if (empty($orders)) : ?>
        <p>هیچ سفارشی ثبت نشده است.</p>
    <?php endif; ?>

    <?php foreach ($orders as $order) : ?>

        <div class="rm-order-card status-<?php echo esc_attr($order->get_status()); ?>">

            <!-- Header -->
            <div class="rm-order-header">
                سفارش #<?php echo $order->get_id(); ?>

                <span class="rm-status"><?php echo wc_get_order_status_name($order->get_status()); ?></span>
            </div>

            <!-- Main info -->
            <div class="rm-order-main">
                <p><strong>نام مشتری:</strong> <?php echo esc_html($order->get_formatted_billing_full_name()); ?> </p>

                <p><strong>مبلغ:</strong> <?php echo $order->get_formatted_order_total(); ?></p>
            </div>
            <!-- Accordion -->
            <details class="rm-order-details">
                <summary>مشاهده جزئیات</summary>
                <div class="rm-order-details-inner">

                    <!-- Items -->
                    <div class="rm-order-items">
                        <strong>اقلام:</strong>
                        <ul>
                            <?php foreach ($order->get_items() as $item) : ?>
                                <li>
                                    <?php echo esc_html($item->get_name()); ?>
                                    <span class="rm-order-items-quantity">
                                <?php echo $item->get_quantity(); ?>
                                عدد
                            </span>

                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <p><strong>نام مشتری:</strong> <?php echo esc_html($order->get_formatted_billing_full_name()); ?>
                    </p>

                    <p><strong>تلفن:</strong> <?php echo esc_html($order->get_billing_phone()); ?></p>

                    <p><strong>آدرس صورتحساب:</strong>
                        <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                    </p>
                    <p><strong>یادداشت مشتری:</strong>
                        <?php echo esc_html($order->get_customer_note()); ?>
                    </p>

                    <p><strong>روش تحویل:</strong>
                        <?php echo esc_html($order->get_shipping_method()); ?>
                    </p>
                    <!-- Status update -->
                    <div class="rm-order-status-change">
                        <div class="rm-order-actions" data-order-id="<?php echo esc_attr($order->get_id()); ?>">

                            <?php if ($order->has_status('on-hold')) : ?>
                                <button class="rm-action-btn cancel rm-btn-secondary" data-status="cancelled">
                                    لغو سفارش
                                </button>

                                <button class="rm-action-btn confirm rm-btn-primary" data-status="processing">
                                    تأیید سفارش
                                </button>

                            <?php elseif ($order->has_status('processing')) : ?>

                                <button class="rm-action-btn on-way rm-btn-primary" data-status="on-way">
                                    تحویل به پیک
                                </button>
                            <?php elseif ($order->has_status('on-way')) : ?>
                                <button class="rm-action-btn completed rm-btn-primary" data-status="completed">
                                    تکمیل سفارش
                                </button>
                            <?php endif; ?>

                            <div class="rm-order-status-notice" style="display:none;"></div>
                        </div>
                    </div>

                </div>
            </details>
        </div>

    <?php endforeach; ?>

</div>
