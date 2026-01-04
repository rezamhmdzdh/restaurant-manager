/**
 * Restaurant Manager Dashboard JavaScript
 * Handles tab navigation, orders management, polling, modal interactions
 */

jQuery(document).ready(function ($) {

    // ==================================================================
    // 1. Tab Navigation
    // ==================================================================

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

        if (targetPage === 'orders') {
            fetchOrdersList();
        }
    });

    // ==================================================================
    // 2. Orders Management Functions
    // ==================================================================

    function fetchOrdersList() {
        $('#refreshIndicator').show();

        $.ajax({
            url: RM.ajaxUrl,
            method: 'POST',
            data: {
                action: 'rm_orders',
                sub_action: 'get_list',
                nonce: RM.nonce
            },
            success: function (response) {
                if (response.success) {
                    renderOrdersTable(response.data);
                } else {
                    alert('خطا در دریافت سفارشات: ' + (response.data || 'نامشخص'));
                }
            },
            error: function () {
                alert('خطا در ارتباط با سرور. لطفاً صفحه را رفرش کنید.');
            },
            complete: function () {
                $('#refreshIndicator').hide();
            }
        });
    }

    // order table rendering
    function renderOrdersTable(orders) {
        const $tbody = $('#orderTableBody');
        $tbody.empty();

        if (orders.length === 0) {
            $tbody.append('<tr><td colspan="6" style="text-align:center; padding:20px;">سفارشی یافت نشد.</td></tr>');
            return;
        }

        orders.forEach(function (order) {
            const row = `
                <tr class="order-row" data-id="${order.id}">
                    <td data-label="شماره">#${order.id}</td>
                    <td data-label="مشتری">${order.customer}</td>
                    <td data-label="وضعیت">
                        <span class="status-badge status-${order.status_key}">
                            ${order.status}
                        </span>
                    </td>
                    <td data-label="تاریخ">${order.date}</td>
                    <td data-label="مبلغ">${order.total}</td>
                    <td data-label="اقدامات">
                        <button class="btn btn-small btn-primary view-details" data-id="${order.id}">
                            جزئیات
                        </button>
                    </td>
                </tr>`;
            $tbody.append(row);
        });
    }

    // ==================================================================
    // 3. Order Details Modal
    // ==================================================================

    $(document).on('click', '.view-details', function () {
        const orderId = $(this).data('id') || $(this).closest('tr').data('id');
        loadOrderDetails(orderId);
    });

    // modal details
    function loadOrderDetails(orderId) {
        $.ajax({
            url: RM.ajaxUrl,
            method: 'POST',
            data: {
                action: 'rm_orders',
                sub_action: 'get_details',
                order_id: orderId,
                nonce: RM.nonce
            },
            success: function (response) {
                if (response.success) {
                    populateOrderModal(response.data);
                    $('#orderNumber').text(orderId);
                    $('#currentOrderId').val(orderId);
                    $('#orderEditModal').show();
                } else {
                    alert('خطا: ' + response.data);
                }
            },
            error: function () {
                alert('خطا در دریافت جزئیات سفارش.');
            }
        });
    }

    // پر کردن مودال با اطلاعات سفارش
    function populateOrderModal(data) {

        $('#statusSelect').val(data.status);

        $('#orderNotes').val(data.notes || '');

        const $itemsBody = $('#orderItemsBody');
        $itemsBody.empty();

        data.items.forEach(function (item) {
            const itemRow = `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                    <td>${item.total}</td>
                </tr>`;
            $itemsBody.append(itemRow);
        });

        $('#orderSubtotal').html(data.subtotal);
        $('#orderShipping').html(data.shipping);
        $('#orderTax').html(data.tax);
        $('#orderTotal').html(data.total);
    }

    // ==================================================================
    // 4. Update Order (Submit Form)
    // ==================================================================

    $('#orderEditForm').on('submit', function (e) {
        e.preventDefault();

        const orderId = $('#orderNumber').text();
        const status = $('#statusSelect').val();
        const notes = $('#orderNotes').val();

        $.ajax({
            url: RM.ajaxUrl,
            method: 'POST',
            data: {
                action: 'rm_orders',
                sub_action: 'update_order',
                order_id: orderId,
                status: status,
                notes: notes,
                nonce: RM.nonce
            },
            success: function (response) {
                if (response.success) {
                    alert('سفارش با موفقیت به‌روزرسانی شد.');
                    closeOrderEditModal();
                    fetchOrdersList(); // بروزرسانی جدول
                } else {
                    alert('خطا: ' + response.data);
                }
            },
            error: function () {
                alert('خطا در ارسال اطلاعات.');
            }
        });
    });

    // ==================================================================
    // 5. Polling: هر 60 ثانیه سفارشات جدید چک شود
    // ==================================================================

    let ordersPollingInterval = null;

    function startOrdersPolling() {
        // فقط وقتی تب سفارشات فعال است، polling فعال باشد
        if ($('#orders-page').is(':visible')) {
            fetchOrdersList(); // اولین بار فوری

            ordersPollingInterval = setInterval(function () {
                if ($('#orders-page').is(':visible')) {
                    fetchOrdersList();
                }
            }, 60000); // هر 60 ثانیه
        }
    }

    function stopOrdersPolling() {
        if (ordersPollingInterval) {
            clearInterval(ordersPollingInterval);
            ordersPollingInterval = null;
        }
    }

    // وقتی وارد تب سفارشات می‌شیم
    $('.nav-item[data-page="orders"]').on('click', function () {
        startOrdersPolling();
    });

    // وقتی از تب سفارشات خارج می‌شیم
    $('.nav-item').not('[data-page="orders"]').on('click', function () {
        stopOrdersPolling();
    });

    // اگر صفحه اول لود شد و تب سفارشات فعال بود
    if ($('#orders-page').is(':visible')) {
        startOrdersPolling();
    }

    // ==================================================================
    // 6. Close Modal on Outside Click or Button
    // ==================================================================

    window.closeOrderEditModal = function () {
        $('#orderEditModal').hide();
    };

    // کلیک بیرون از مودال
    $(window).on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            closeOrderEditModal();
        }
    });

    // دکمه بستن
    $('.close-btn').on('click', closeOrderEditModal);

});