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

        const itemsHtml = Array.isArray(order.items)
            ? order.items.map(item => `
            <li>
                ${item.name}
                <span>× ${item.qty}</span>
            </li>
        `).join('')
            : '';

        return `
        <div class="rm-order-card" data-order-id="${order.id}">

            <div class="rm-order-header">
                <strong class="rm-order-customer">${order.customer}</strong>
                <span class="rm-order-status">${order.status}</span>
            </div>

            <ul class="rm-order-items">
                ${itemsHtml}
            </ul>

            <div class="rm-order-footer">
                <div class="rm-order-meta">
                    <span class="rm-order-total">${order.total} تومان</span>
                    <span class="rm-order-time">${order.time}</span>
                </div>

                <button
                    class="rm-order-action"
                    data-order-id="${order.id}">
                    مشاهده سفارش
                </button>
            </div>

        </div>
    `;
    }


    // function renderOrder(order) {
    //
    //     // Fallback values for safety
    //     const id       = order.id || '-';
    //     const customer = order.customer || '—';
    //     const total    = order.total || '0';
    //     const status   = order.status || '';
    //     const time     = order.time || '';
    //
    //     return `
    //         <div class="rm-order-card" data-order-id="${id}">
    //             <strong>#${id}</strong>
    //             <div class="rm-order-customer">${customer}</div>
    //             <div class="rm-order-total">${total} تومان</div>
    //             <small class="rm-order-meta">${status} ${time ? ' - ' + time : ''}</small>
    //         </div>
    //     `;
    // }

    // Initial load
    loadOrders();
});
