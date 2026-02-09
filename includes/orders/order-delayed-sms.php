<?php

if ( ! defined( 'RM_ONHOLD_DELAY_HOOK' ) ) {
    define( 'RM_ONHOLD_DELAY_HOOK',   'rm_onhold_delay_sms_check' );
}
if ( ! defined( 'RM_ONHOLD_SMS_META_KEY' ) ) {
    define( 'RM_ONHOLD_SMS_META_KEY', '_rm_onhold_delay_sms_sent' );
}

// Schedule the check when order goes on-hold
add_action( 'woocommerce_order_status_on-hold', 'rm_schedule_onhold_sms_check', 10, 1 );

function rm_schedule_onhold_sms_check( $order_id ) {
    as_schedule_single_action( time() + 300, RM_ONHOLD_DELAY_HOOK, [ $order_id ] );
}

// Main action: runs after delay
add_action( RM_ONHOLD_DELAY_HOOK, function ( $order_id ) {

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $status = $order->get_status();
    if ( $status !== 'on-hold' ) {
        return;
    }

    $already_sent = (bool) $order->get_meta( RM_ONHOLD_SMS_META_KEY );
    if ( $already_sent ) {
        return;
    }

    if ( ! function_exists( 'PWSMS' ) || ! is_callable( [ PWSMS(), 'send_sms' ] ) ) {
        $order->add_order_note( 'ارسال پیامک تأخیری انجام نشد: تابع PWSMS پیدا نشد یا ناقص است.' );
        return;
    }

    $data = [
        'post_id' => $order_id,
        'type'    => 4,
        'mobile'  => '09154034946',
        'message' => 'pcode:p8q3trwbrbn8jnp',
    ];

    $result = PWSMS()->send_sms( $data );

    if ( $result === true ) {
        $order->update_meta_data( RM_ONHOLD_SMS_META_KEY, time() );
        $order->save();

        $order->add_order_note( 'پیامک تاخیر ثبت سفارش ارسال شده: ' . $data['mobile'] );
    } else {
        $error_msg = is_string( $result ) ? $result : ( is_array( $result ) ? wp_json_encode( $result ) : 'خطای نامشخص' );
        $order->add_order_note( 'ارسال پیامک تأخیری ناموفق بود. نتیجه: ' . $error_msg );
    }

}, 10, 1 );