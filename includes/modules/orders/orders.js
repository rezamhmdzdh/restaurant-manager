document.addEventListener('change', function (e) {

    if (!e.target.classList.contains('rm-order-status')) return;

    const select = e.target;
    const orderId = select.dataset.orderId;
    const status = select.value;
    const notice = select.nextElementSibling;

    // حالت لودینگ ساده
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
                notice.textContent = 'تغییر وضعیت انجام شد';
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

setInterval(checkNewOrder, 10000);

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
                showNewOrderModal();
            }
        });
}
