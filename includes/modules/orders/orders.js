document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('rm-orders-list');

    // Stop if container does not exist
    if (!container) {
        console.warn('RM Orders: container not found');
        return;
    }

    /**
     * Load orders from server via AJAX
     */
    function loadOrders() {

        fetch(RM_Orders.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'rm_get_orders',
                nonce: RM_Orders.nonce
            })
        })
            .then(response => response.json())
            .then(json => {

                console.log('RM Orders Response:', json);

                // Validate response structure
                if (
                    !json ||
                    json.success !== true ||
                    !json.data ||
                    !Array.isArray(json.data.orders)
                ) {
                    console.warn('RM Orders: invalid response format');
                    return;
                }

                renderOrders(json.data.orders);
            })
            .catch(error => {
                console.error('RM Orders AJAX error:', error);
            });
    }

    /**
     * Render orders list
     *
     * @param {Array} orders
     */
    function renderOrders(orders) {

        // Clear previous orders
        container.innerHTML = '';

        // Handle empty orders
        if (orders.length === 0) {
            container.innerHTML = '<p>No orders found.</p>';
            return;
        }

        orders.forEach(function (order) {
            container.insertAdjacentHTML(
                'beforeend',
                renderOrder(order)
            );
        });
    }

    /**
     * Render single order card
     *
     * @param {Object} order
     * @returns {string}
     */
    function renderOrder(order) {
        return `
        <div class="rm-order-card" data-order-id="${order.id}">
            <div class="rm-order-header">
                <strong>${order.customer}</strong>
                <span class="rm-order-status status-${order.status}">
                    ${order.status}
                </span>
            </div>

            <ul class="rm-order-items-preview">
                ${order.items.map(item => `
                    <li>${item.name} × ${item.qty}</li>
                `).join('')}
            </ul>

            <div class="rm-order-footer">
                <span class="rm-order-total">${order.total}</span>
                <small>${order.time}</small>
                <button class="rm-order-view-btn" data-id="${order.id}">
                    جزئیات
                </button>
            </div>
        </div>
    `;
    }


    // Initial load
    loadOrders();
});

/**
 * Handle order card action click
 */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.rm-order-view-btn');
    if (!btn) return;

    const orderId = btn.dataset.id;
    openOrderEditModal(orderId);
});

function openOrderEditModal(orderId) {
    fetch(RM_Orders.ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'rm_get_single_order',
            nonce: RM_Orders.nonce,
            order_id: orderId
        })
    })
        .then(res => res.json())
        .then(response => {
            if (!response.success) return;

            fillOrderModal(response.data);
            document.getElementById('orderEditModal').classList.add('active');
        });
}


function fillOrderModal(order) {

    // Order number
    document.getElementById('orderNumber').textContent = order.id;

    // Status fields
    document.querySelector('#orderEditForm select[name="status"]').value = order.status;
    document.querySelector('#orderEditForm select[name="payment_status"]').value = order.payment_status;

    // Order items
    const tbody = document.getElementById('orderItemsBody');
    tbody.innerHTML = '';

    order.items.forEach(item => {
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>${item.price}</td>
                <td>${item.total}</td>
                <td>-</td>
            </tr>
        `);
    });

    // Summary
    document.getElementById('orderSubtotal').textContent = order.subtotal;
    document.getElementById('orderShipping').textContent = order.shipping;
    document.getElementById('orderTax').textContent = order.tax;
    document.getElementById('orderTotal').textContent = order.total;

    // Notes
    document.querySelector('#orderEditForm textarea[name="notes"]').value = order.notes || '';

    // Store order id on form
    document.getElementById('orderEditForm').dataset.orderId = order.id;
}

function closeOrderEditModal() {
    document.getElementById('orderEditModal').classList.remove('active');
}

document.getElementById('orderEditForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const orderId = this.dataset.orderId;
    const formData = new FormData(this);

    fetch(RM_Orders.ajaxUrl, {
        method: 'POST',
        body: new URLSearchParams({
            action: 'rm_update_order',
            nonce: RM_Orders.nonce,
            order_id: orderId,
            status: formData.get('status'),
            payment_status: formData.get('payment_status'),
            notes: formData.get('notes')
        })
    })
        .then(res => res.json())
        .then(response => {
            if (!response.success) return;

            closeOrderEditModal();
            loadOrders(); // refresh cards
        });
});



// Handle form submit
document.getElementById('orderEditForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const orderId = this.dataset.orderId;
    const formData = new FormData(this);

    fetch(RM_Orders.ajaxUrl, {
        method: 'POST',
        body: new URLSearchParams({
            action: 'rm_update_order',
            nonce: RM_Orders.nonce,
            order_id: orderId,
            status: formData.get('status'),
            payment_status: formData.get('payment_status'),
            notes: formData.get('notes')
        })
    })
        .then(res => res.json())
        .then(response => {
            if (!response.success) {
                alert(response.data?.message || 'Error updating order');
                return;
            }

            alert('Order updated successfully!');
            closeOrderEditModal();
            loadOrders(); // refresh order list
        });
});
