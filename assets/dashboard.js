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

function rmUnlockAudio() {

    if (rmAudioUnlocked) return;
    rmAudioUnlocked = true;

    const audio = document.getElementById('rm-new-order-sound');
    if (!audio) return;

    audio.muted = true;
    audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;
        audio.muted = false;
    }).catch(() => {

    });
}

['click', 'keydown', 'touchstart', 'wheel'].forEach(event => {
    document.addEventListener(event, rmUnlockAudio, {once: true});
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
        audio.play().catch(() => {
        });
    }

    if ('vibrate' in navigator) {
        navigator.vibrate([200, 100, 200]);
    }
}

document.addEventListener('click', function (e) {
    if (e.target.id === 'rm-refresh-orders') {
        location.reload();
    }

    if (e.target.id === 'cloce-modal') {
        const modal = document.getElementById('rm-new-order-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

});

