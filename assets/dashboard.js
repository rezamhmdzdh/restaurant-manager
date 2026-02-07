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


let rmAudioUnlocked = false;
let rmNotified = false;

document.getElementById('rm-enable-notification')?.addEventListener('click', () => {
    const audio = document.getElementById('rm-new-order-sound');

    if (!audio) return;

    audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;

        rmAudioUnlocked = true;
        console.log('Notifications enabled');

        const btn = document.getElementById('rm-enable-notification');
        if (btn) {
            btn.textContent = '✅ اعلان فعال شد';
            btn.disabled = true;
            btn.classList.add('rm-enabled');
        }

    }).catch(err => {
        console.error('Enable notification failed:', err.name, err.message);
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
        });
    }

    if ('vibrate' in navigator) {
        navigator.vibrate([500, 200, 500, 200, 500]);
    }
}

// filter orders

document.querySelectorAll('.rm-tab').forEach(tab => {

    tab.addEventListener('click', function () {

        const status = this.dataset.status;

        document.querySelectorAll('.rm-tab')
            .forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        document.querySelectorAll('.rm-order-card').forEach(card => {

            if (!status) {
                card.style.display = '';
                return;
            }

            card.style.display = card.classList.contains('status-' + status)
                ? ''
                : 'none';
        });
    });
});
// ===========================
// Order modal (open/close)
// ===========================

// Helper: lock/unlock page scroll when modal is open
function rmSetBodyScrollLocked(locked) {
    document.body.style.overflow = locked ? 'hidden' : '';
}

// Open modal when clicking on an order card
document.addEventListener('click', function (e) {
    // Do nothing if click is inside the modal itself
    if (e.target.closest('#rm-order-modal')) return;

    const card = e.target.closest('.rm-order-card');
    if (!card) return;

    // Don't open modal when clicking on action buttons inside the card
    if (e.target.closest('.rm-action-btn')) return;

    const tpl = card.querySelector('.rm-order-details-template');
    if (!tpl) return;

    const overlay = document.getElementById('rm-modal-overlay');
    const modal   = document.getElementById('rm-order-modal');
    const body    = document.getElementById('rm-order-modal__body');
    const title   = document.getElementById('rm-order-modal__title');

    // Safety checks
    if (!overlay || !modal || !body || !title) return;

    const orderId = card.dataset.orderId || '';

    title.textContent = orderId ? `جزئیات سفارش #${orderId}` : 'جزئیات سفارش';
    body.innerHTML = tpl.innerHTML;

    overlay.style.display = 'block';
    modal.style.display = 'block';

    rmSetBodyScrollLocked(true);
});

// Close modal
function rmCloseOrderModal() {
    const overlay = document.getElementById('rm-modal-overlay');
    const modal   = document.getElementById('rm-order-modal');
    const body    = document.getElementById('rm-order-modal__body');

    if (modal) modal.style.display = 'none';
    if (overlay) overlay.style.display = 'none';
    if (body) body.innerHTML = '';

    rmSetBodyScrollLocked(false);
}

// Close modal when clicking overlay or close button
document.addEventListener('click', function (e) {
    if (e.target.id === 'rm-modal-overlay' || e.target.id === 'rm-order-modal-close') {
        rmCloseOrderModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') rmCloseOrderModal();
});

// Accessibility: open modal with Enter/Space when focusing the card
document.addEventListener('keydown', function (e) {
    const card = e.target.closest('.rm-order-card');
    if (!card) return;

    if (e.key === 'Enter' || e.key === ' ') {
        // Prevent page scroll on Space
        e.preventDefault();
        card.click();
    }
});

// Copy text from any element with [data-copy]
document.addEventListener('click', async (e) => {
    const el = e.target.closest('[data-copy]');
    if (!el) return;

    const text = (el.textContent || '').trim();
    if (!text) return;

    try {
        await navigator.clipboard.writeText(text);
        el.classList.add('copied');
        setTimeout(() => el.classList.remove('copied'), 700);
    } catch {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
    }
});
