<?php

if (!defined('ABSPATH')) exit;

/**
 * وقتی سفارش روی on-hold می‌رود، یک ارسال با تاخیر 5 دقیقه‌ای برای مدیر زمان‌بندی کن
 */
add_action('woocommerce_order_status_on-hold', 'rm_schedule_onhold_manager_sms', 10, 2);

/**
 * اکشن اجرایی (Action Scheduler / WP-Cron)
 */
add_action('rm_onhold_manager_sms_after_5m', 'rm_onhold_manager_sms_after_5m', 10, 1);


add_action('woocommerce_order_status_changed', 'rm_cancel_onhold_manager_sms_when_left_onhold', 20, 3);


function rm_schedule_onhold_manager_sms($order_id, $order)
{
    if (!$order_id) return;

    // جلوگیری از تکراری شدن زمان‌بندی
    if (function_exists('as_next_scheduled_action')) {
        $next = as_next_scheduled_action('rm_onhold_manager_sms_after_5m', [$order_id], 'restaurant-manager');
        if ($next) return;

        as_schedule_single_action(
            time() + 1 * 60,
            'rm_onhold_manager_sms_after_5m',
            [$order_id],
            'restaurant-manager'
        );
        return;
    }

    // fallback: WP-Cron (کم‌اعتمادتر ولی کار راه‌انداز)
    if (!wp_next_scheduled('rm_onhold_manager_sms_after_5m', [$order_id])) {
        wp_schedule_single_event(time() + 5 * 60, 'rm_onhold_manager_sms_after_5m', [$order_id]);
    }
}

/**
 * بعد از 5 دقیقه: اگر سفارش هنوز on-hold بود، پیامک مدیر را با PWSMS بفرست
 */
function rm_onhold_manager_sms_after_5m($order_id)
{
    $order_id = intval($order_id);
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    // اگر وضعیت تغییر کرده، هیچ
    if ($order->get_status() !== 'on-hold') return;

    // جلوگیری از ارسال چندباره
    if ($order->get_meta('_rm_onhold_manager_sms_sent') === 'yes') return;

    // افزونه پیامک فعال است؟
    if (!function_exists('PWSMS')) return;

    // آیا ارسال پیامک مدیر در افزونه روشن است؟
    if (!PWSMS()->get_option('enable_super_admin_sms')) return;

    // وضعیت را مثل خود افزونه normalize کن
    $status = PWSMS()->modify_status('on-hold');

    // متن و شماره مدیر از تنظیمات خود افزونه
    $mobile = PWSMS()->get_option('super_admin_phone');
    $message = PWSMS()->get_option('super_admin_sms_body_' . $status);

    if (empty($mobile) || empty($message)) return;

    // متن نهایی با shortcodes خود افزونه
    $final_message = PWSMS()->replace_short_codes($message, $status, $order);

    $data = [
        'post_id' => $order_id,
        'type' => 4, // دقیقا مثل superAdmin در افزونه
        'mobile' => $mobile,
        'message' => $final_message,
    ];

    $result = PWSMS()->send_sms($data);

    if ($result === true) {
        $order->update_meta_data('_rm_onhold_manager_sms_sent', 'yes');
        $order->add_order_note(sprintf('پیامک تاخیری (۵ دقیقه) با موفقیت به مدیر با شماره %s ارسال گردید.', $mobile));
        $order->save();
    } else {
        $order->add_order_note(sprintf('پیامک تاخیری (۵ دقیقه) به مدیر با شماره %s ارسال نشد.<br>پاسخ وبسرویس: %s', $mobile, $result));
        $order->save();
    }
}

function rm_cancel_onhold_manager_sms_when_left_onhold($order_id, $old_status, $new_status) {
    if (!$order_id) return;

    // فقط وقتی از on-hold خارج می‌شود
    if ($old_status !== 'on-hold' || $new_status === 'on-hold') return;

    // Action Scheduler (WooCommerce)
    if (function_exists('as_unschedule_all_actions')) {
        as_unschedule_all_actions(
            'rm_onhold_manager_sms_after_5m',
            [intval($order_id)],
            'restaurant-manager'
        );
        return;
    }

    // fallback WP-Cron
    $timestamp = wp_next_scheduled('rm_onhold_manager_sms_after_5m', [intval($order_id)]);
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'rm_onhold_manager_sms_after_5m', [intval($order_id)]);
    }
}
