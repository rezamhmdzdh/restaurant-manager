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


let rmNewOrderNotified = false;

function rmNotifyNewOrder() {

    if (rmNewOrderNotified) return;
    rmNewOrderNotified = true;

    const modal = document.getElementById('rm-new-order-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
    const audio = document.getElementById('rm-new-order-sound');
    if (audio) {
        audio.currentTime = 0;
        audio.play().catch(() => {
            console.warn('Audio play blocked');
        });
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

