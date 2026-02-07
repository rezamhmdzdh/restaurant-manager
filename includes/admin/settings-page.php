<?php
if (!defined('ABSPATH')) exit;

function rm_register_settings_page()
{
    add_submenu_page(
        'woocommerce',
        'Restaurant Manager Settings',
        'Restaurant Manager',
        'manage_woocommerce',
        'rm-settings',
        'rm_render_settings_page'
    );
}

add_action('admin_init', 'rm_register_settings');

function rm_register_settings()
{
    register_setting(
        'rm_settings_group',
        'rm_settings',
        [
            'type' => 'array',
            'sanitize_callback' => 'rm_sanitize_settings',
            'default' => rm_default_settings(),
        ]
    );

    add_settings_section(
        'rm_notifications_section',
        'اعلان',
        '__return_false',
        'rm-settings'
    );

    add_settings_field(
        'new_order_sound_url',
        'انتخاب اعلان سفارش جدید',
        'rm_field_new_order_sound_url',
        'rm-settings',
        'rm_notifications_section'
    );
}

function rm_default_settings()
{
    $default = 'https://beirutelebanon.com/wp-content/plugins/reyhoon-pro/inc/modules/live-view-pro/assets/audio/ding.wav';

    return [
        'new_order_sound_url' => $default,
    ];
}

function rm_get_settings()
{
    $defaults = rm_default_settings();
    $saved = get_option('rm_settings', []);
    if (!is_array($saved)) $saved = [];
    return wp_parse_args($saved, $defaults);
}

function rm_sanitize_settings($input)
{
    $defaults = rm_default_settings();
    $out = [];

    $url = $input['new_order_sound_url'] ?? '';
    $url = is_string($url) ? trim($url) : '';

    $url = esc_url_raw($url);

    $out['new_order_sound_url'] = $url !== '' ? $url : $defaults['new_order_sound_url'];

    return $out;
}

function rm_field_new_order_sound_url()
{
    $s = rm_get_settings();
    ?>
    <input
            type="url"
            class="regular-text"
            name="rm_settings[new_order_sound_url]"
            value="<?php echo esc_attr($s['new_order_sound_url']); ?>"
            placeholder="https://example.com/sound.mp3"
    />
    <p class="description">
        URL فایل صوتی برای اعلان سفارش جدید (wav / ogg).
    </p>
    <?php
}

function rm_render_settings_page()
{
    if (!current_user_can('manage_woocommerce')) {
        wp_die('You do not have permission to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>تنظیمات داشبورد مدیریت رستوران </h1>

        <form method="post" action="options.php">
            <?php
            settings_fields('rm_settings_group');
            do_settings_sections('rm-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}