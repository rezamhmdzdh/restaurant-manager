document.addEventListener('change', function (e) {

    if (!e.target.classList.contains('rm-order-status')) return;

    const select  = e.target;
    const orderId = select.dataset.orderId;
    const status  = select.value;

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
            if (!res.success) {
                alert(res.data);
            }
        });
});
