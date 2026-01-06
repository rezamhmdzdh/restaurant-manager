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


function showNewOrderModal() {

    const modal = document.getElementById('rm-new-order-modal');
    if (!modal) return;

    modal.style.display = 'block';

    document.getElementById('rm-refresh-orders')?.addEventListener('click', () => {
        location.reload();
    });
}


function rmPlayNewOrderSound() {
    const audio = document.getElementById('rm-new-order-sound');
    if (!audio) return;

    audio.currentTime = 0;
    audio.play().catch(() => {
        console.warn('Audio play blocked');
    });
}
