<div class="rm-orders">

    <?php if (empty($orders)) : ?>
        <p>هیچ سفارشی ثبت نشده است.</p>
    <?php endif; ?>

    <?php foreach ($orders as $order) : ?>
        <?php
        $order_id = $order->get_id();
        $status_key = $order->get_status();
        ?>

        <div class="rm-order-card status-<?php echo esc_attr($status_key); ?>"
             data-order-id="<?php echo esc_attr($order_id); ?>"
             role="button"
             tabindex="0"
             aria-label="<?php echo esc_attr('جزئیات سفارش #' . $order_id); ?>">

            <!-- Header -->
            <div class="rm-order-header">
                سفارش #<?php echo esc_html($order_id); ?>
                <span class="rm-status"><?php echo esc_html(wc_get_order_status_name($status_key)); ?></span>
            </div>

            <!-- Main info -->
            <div class="rm-order-main">
                <p><strong>نام مشتری:</strong> <?php echo esc_html($order->get_formatted_billing_full_name()); ?></p>
                <p><strong>مبلغ:</strong> <?php echo wp_kses_post($order->get_formatted_order_total()); ?></p>
            </div>

            <!-- Details template -->
            <div class="rm-order-details-template" hidden>
                <div class="rm-order-details-inner">

                    <!-- Items -->
                    <div class="rm-order-items">
                        <strong>اقلام:</strong>
                        <ul>
                            <?php foreach ($order->get_items() as $item) : ?>
                                <li>
                                    <?php echo esc_html($item->get_name()); ?>
                                    <span class="rm-order-items-quantity">
                                      <?php echo (int)$item->get_quantity(); ?> عدد
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="rm-order-details-inner-grid">

                        <div class="detail-item">
                            <label>نام مشتری:</label>
                            <span data-copy> <?php echo esc_html($order->get_formatted_billing_full_name()); ?> </span>
                        </div>

                        <div class="detail-item">
                            <label>شماره همراه:</label>
                            <?php echo esc_html($order->get_billing_phone()); ?>
                        </div>

                        <div class="detail-item">
                            <label>نوع پرداخت:</label>
                            <?php echo esc_html($order->get_payment_method_title()); ?>
                        </div>

                        <div class="detail-item">
                            <label>روش تحویل:</label>
                            <?php echo esc_html($order->get_shipping_method()); ?>
                        </div>

                        <div class="detail-item">
                            <label>آدرس:</label>
                            <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                        </div>
                        <div class="detail-item">
                            <label>آدرس:</label>
                            <div data-copy>
                                <?php
                                echo esc_html(trim($order->get_billing_address_1() . ' ' . $order->get_billing_address_2()));
                                ?>
                            </div>
                        </div>


                        <?php if ($order->get_customer_note()) : ?>
                            <div class="detail-item">
                                <label>یادداشت مشتری:</label>
                                <?php echo esc_html($order->get_customer_note()); ?>
                            </div>
                        <?php endif; ?>



                    </div>

                    <!-- Status update actions (keep as-is) -->
                    <div class="rm-order-status-change">
                        <div class="rm-order-actions" data-order-id="<?php echo esc_attr($order_id); ?>">

                            <?php if ($order->has_status('on-hold')) : ?>
                                <button type="button" class="rm-action-btn cancel rm-btn-secondary"
                                        data-status="cancelled">
                                    لغو سفارش
                                </button>
                                <button type="button" class="rm-action-btn confirm rm-btn-primary"
                                        data-status="processing">
                                    تأیید سفارش
                                </button>

                            <?php elseif ($order->has_status('processing')) : ?>
                                <button type="button" class="rm-action-btn completed rm-btn-primary"
                                        data-status="completed">
                                    تکمیل سفارش
                                </button>

                            <?php elseif ($order->has_status('completed')) : ?>
                                سفارش ارسال شده است
                            <?php endif; ?>

                            <div class="rm-order-status-notice" style="display:none;"></div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Details template -->


        </div>

    <?php endforeach; ?>

</div>



<div class="detail-item">
    <label>آدرس:</label>

    <span class="rm-copy-text" data-copy>
    <?php echo esc_html(implode('، ', $addr)); ?>
  </span>

    <button type="button" class="rm-copy-btn" aria-label="Copy address">📋</button>
</div>
