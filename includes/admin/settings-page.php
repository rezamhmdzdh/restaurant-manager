<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * Admin Menu
 * =========================
 */
add_action('admin_menu', 'rm_register_settings_page');
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

/**
 * =========================
 * Settings Register
 * =========================
 */
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
        'new_order_sound_id',
        'انتخاب اعلان سفارش جدید',
        'rm_field_new_order_sound_media',
        'rm-settings',
        'rm_notifications_section'
    );

    add_settings_field(
        'sms_admin_mobiles',
        'شماره موبایل دریافت پیامک',
        'rm_field_sms_admin_mobiles',
        'rm-settings',
        'rm_notifications_section'
    );
}

function rm_default_settings()
{
    return [
        'new_order_sound_id' => 0, // attachment ID
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
    $out = [];

    $out['new_order_sound_id'] = isset($input['new_order_sound_id'])
        ? absint($input['new_order_sound_id'])
        : 0;

    $out['sms_admin_mobiles'] = isset($input['sms_admin_mobiles'])
        ? sanitize_text_field($input['sms_admin_mobiles'])
        : '';

    return $out;
}

/**
 * Helper: Get final URL from saved attachment ID
 */
function rm_get_new_order_sound_url()
{
    $s = rm_get_settings();
    $id = absint($s['new_order_sound_id']);
    if (!$id) return '';
    $url = wp_get_attachment_url($id);
    return $url ? $url : '';
}

/**
 * =========================
 * Field Renderer (Media Picker)
 * =========================
 */
function rm_field_new_order_sound_media()
{
    $s = rm_get_settings();
    $id = absint($s['new_order_sound_id']);
    $url = $id ? wp_get_attachment_url($id) : '';
    ?>
    <div class="rm-media-field">
        <input
                type="hidden"
                id="rm_new_order_sound_id"
                name="rm_settings[new_order_sound_id]"
                value="<?php echo esc_attr($id); ?>"
        />

        <input
                type="text"
                id="rm_new_order_sound_url_preview"
                class="regular-text"
                value="<?php echo esc_attr($url); ?>"
                readonly
                placeholder="هنوز فایلی انتخاب نشده"
        />

        <button type="button" class="button" id="rm_pick_new_order_sound">
            انتخاب از رسانه
        </button>

        <button type="button" class="button" id="rm_remove_new_order_sound">
            حذف انتخاب
        </button>

        <?php if ($url): ?>
            <audio controls style="display:block; margin-top:10px; max-width: 420px;">
                <source src="<?php echo esc_url($url); ?>">
                مرورگر شما از پخش صوت پشتیبانی نمی‌کند.
            </audio>
        <?php endif; ?>

        <p class="description">
            فایل صوتی اعلان سفارش جدید را از کتابخانه رسانه انتخاب کنید (wav / ogg / mp3).
        </p>
    </div>
    <?php
}

/**
 * =========================
 * Enqueue Media Uploader only on this settings page
 * =========================
 */
add_action('admin_enqueue_scripts', 'rm_admin_enqueue_media');
function rm_admin_enqueue_media($hook)
{
    // woocommerce_page_rm-settings
    if ($hook !== 'woocommerce_page_rm-settings') return;

    wp_enqueue_media();
    wp_add_inline_script('jquery-core', rm_admin_media_js());
}

function rm_admin_media_js()
{
    return <<<JS
jQuery(function($){
    var frame;

    $('#rm_pick_new_order_sound').on('click', function(e){
        e.preventDefault();

        if(frame){
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'انتخاب فایل صوتی اعلان',
            button: { text: 'انتخاب' },
            multiple: false,
            library: { type: 'audio' }
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#rm_new_order_sound_id').val(attachment.id);
            $('#rm_new_order_sound_url_preview').val(attachment.url);
        });

        frame.open();
    });

    $('#rm_remove_new_order_sound').on('click', function(e){
        e.preventDefault();
        $('#rm_new_order_sound_id').val('0');
        $('#rm_new_order_sound_url_preview').val('');
    });
});
JS;
}

/**
 * =========================
 * Settings Page Renderer
 * =========================
 */
function rm_render_settings_page()
{
    if (!current_user_can('manage_woocommerce')) {
        wp_die('You do not have permission to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>تنظیمات داشبورد مدیریت رستوران</h1>

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



function rm_field_sms_admin_mobiles()
{
    $s = rm_get_settings();
    $val = isset($s['sms_admin_mobiles']) ? (string)$s['sms_admin_mobiles'] : '';
    ?>
    <input
            type="text"
            class="regular-text"
            name="rm_settings[sms_admin_mobiles]"
            value="<?php echo esc_attr($val); ?>"
            placeholder="0912xxxx,0915xxxx"
            dir="ltr"
    />
    <p class="description">
        شماره‌ها را با کاما جدا کنید. مثال: 0912xxxx,0915xxxx
    </p>
    <?php
}