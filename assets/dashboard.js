/**
 * Restaurant Manager Dashboard JavaScript
 * Handles tab navigation, orders management, polling, modal interactions
 */

jQuery(document).ready(function ($) {

    // 1. Tab Navigation

    $('.nav-item').on('click', function (e) {
        e.preventDefault();

        const targetPage = $(this).data('page');

        // Update active state in sidebar
        $('.nav-item').removeClass('active');
        $(this).addClass('active');

        // Hide all content pages
        $('.content').addClass('hidden');

        // Show target page
        if ($(`#${targetPage}-page`).length) {
            $(`#${targetPage}-page`).removeClass('hidden');
        }
    });
});


let rmAudioUnlocked = false;
let rmNotified = false;

document.getElementById('rm-enable-notification')?.addEventListener('click', () => {
    const audio = document.getElementById('rm-new-order-sound');

    if (!audio) {
        console.warn('[RM Audio] Audio element not found');
        return;
    }

    audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;

        rmAudioUnlocked = true;

        console.log('[RM Audio] Notifications enabled');

        // تغییر متن دکمه
        const btn = document.getElementById('rm-enable-notification');
        if (btn) {
            btn.textContent = '✅ اعلان فعال شد';
            btn.disabled = true;
            btn.classList.add('rm-enabled');
        }

    }).catch(err => {
        console.error('[RM Audio] Enable notification failed:', err.name, err.message);
        alert('مرورگر اجازه فعال‌سازی صدا را نداد');
    });
});


// new order notif
function rmNotifyNewOrder() {

    if (rmNotified) return;
    rmNotified = true;

    const modal = document.getElementById('rm-new-order-modal');
    if (modal) modal.style.display = 'flex';

    const audio = document.getElementById('rm-new-order-sound');
    if (audio && rmAudioUnlocked) {
        audio.currentTime = 0;
        audio.play().catch(err => {
            console.warn('[RM Audio] Play blocked:', err.message);
        });
    }

    if ('vibrate' in navigator) {
        navigator.vibrate([500, 200, 500, 200, 500]);
    }
}

document.addEventListener('click', function (e) {
    if (e.target.id === 'rm-refresh-orders') {
        location.reload();
    }

    if (e.target.id === 'close-modal') {
        const modal = document.getElementById('rm-new-order-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

});

