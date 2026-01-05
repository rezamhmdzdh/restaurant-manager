document.getElementById('rm-load-products')?.addEventListener('click', () => {

    fetch(RM_Inventory.ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_get_products',
            nonce: RM_Inventory.nonce
        })
    })

        .then(r => r.json())
        .then(r => {
            if (!r.success) {
                console.error('Inventory AJAX Error:', r);
                document.getElementById('rm-products').innerHTML =
                    '<p class="rm-error">خطا در دریافت محصولات</p>';
                return;
            }
            if (r.data?.html) {
                document.getElementById('rm-products').innerHTML = r.data.html;
            }
        });
});

document.addEventListener('click', function (e) {

    if (!e.target.classList.contains('rm-save-product')) return;

    const card = e.target.closest('.rm-product-card');

    const productId = card.dataset.productId;
    const stock = card.querySelector('.rm-stock-input').value;
    const message = card.querySelector('.rm-message');

    message.innerHTML = 'در حال ذخیره...';

    fetch(RM_Inventory.ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_update_product',
            nonce: RM_Inventory.nonce,
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

    fetch(RM_Inventory.ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_inventory_mark_outofstock',
            nonce: RM_Inventory.nonce,
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