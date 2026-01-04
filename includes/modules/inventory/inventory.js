document.getElementById('rm-load-products')?.addEventListener('click', () => {

    fetch(RM.ajax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_get_products',
            nonce: RM.nonce
        })
    })
        .then(r => r.json())
        .then(r => {
            document.getElementById('rm-products').innerHTML = r.data.html;
            if (r.success) {
                document.getElementById('rm-products').innerHTML = r.data.html;
            }
        });


});

document.addEventListener('click', function (e) {

    if (!e.target.classList.contains('rm-save-product')) return;

    const card = e.target.closest('.rm-product-card');

    const productId = card.dataset.productId;
    const stock = card.querySelector('.rm-stock-input').value;
    // const active    = card.querySelector('.rm-status-toggle').checked;
    const message = card.querySelector('.rm-message');

    message.innerHTML = 'در حال ذخیره...';

    fetch(RM.ajax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_update_product',
            nonce: RM.nonce,
            product_id: productId,
            stock: stock,
        })
    })
        .then(r => r.json())
        .then(r => {
            if (r.success) {
                message.innerHTML = '✔ ذخیره شد';
            } else {
                message.innerHTML = '✖ خطا';
            }
        });
});


document.addEventListener('click', function (e) {

    if (!e.target.classList.contains('rm-mark-outofstock')) return;

    const card = e.target.closest('.rm-product-card');
    const productId = card.dataset.productId;
    const stockInput = card.querySelector('.rm-stock-input');
    const message = card.querySelector('.rm-message');

    message.innerHTML = 'در حال ناموجود کردن...';

    fetch(RM.ajax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_mark_outofstock',
            nonce: RM.nonce,
            product_id: productId
        })
    })
        .then(r => r.json())
        .then(r => {
            if (r.success) {
                stockInput.value = 0;
                message.innerHTML = '✔ محصول ناموجود شد';
            } else {
                message.innerHTML = '✖ خطا';
            }
        });

});