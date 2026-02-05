document.addEventListener('click', function (e) {

    if (!e.target.classList.contains('rm-action-btn')) return;

    const button = e.target;
    const wrapper = button.closest('.rm-order-actions');
    const orderId = wrapper.dataset.orderId;
    const status  = button.dataset.status;
    const notice  = wrapper.querySelector('.rm-order-status-notice');

    notice.style.display = 'block';
    notice.style.color = '#555';
    notice.textContent = 'در حال بروزرسانی...';

    fetch(rm_orders_ajax.ajax_url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_change_order_status',
            order_id: orderId,
            status: status,
            nonce: rm_orders_ajax.nonce
        })
    })
        .then(res => res.json())
        .then(res => {

            if (res.success) {
                notice.style.color = 'green';
                notice.textContent = 'وضعیت سفارش بروزرسانی شد';

                // setTimeout(() => location.reload(), 600);

            } else {
                notice.style.color = 'red';
                notice.textContent = res.data || 'خطا در تغییر وضعیت';
            }

        })
        .catch(() => {
            notice.style.color = 'red';
            notice.textContent = 'خطای ارتباط با سرور';
        });
});


const pageLoadedAt = rm_orders_ajax.page_loaded_at;

setInterval(checkNewOrder, 30000);

function checkNewOrder() {

    fetch(rm_orders_ajax.ajax_url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_has_new_order',
            nonce: rm_orders_ajax.nonce,
            page_loaded_at: pageLoadedAt
        })
    })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data === true) {
                rmNotifyNewOrder();
            }
        });
}