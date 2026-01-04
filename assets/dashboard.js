// dashboard script
// Navigation with Submenu
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        // Remove active class from all items
        document.querySelectorAll('.nav-item').forEach(nav => {
            nav.classList.remove('active');
        });

        // Add active class to clicked item
        item.classList.add('active');

        // Hide all content pages
        document.querySelectorAll('.content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected content page
        const page = item.getAttribute('data-page');
        document.getElementById(`${page}-page`)?.classList.remove('hidden');
    });
});


// Modal Functions
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}


// Order Modal Functions
window.showOrderEditModal = (orderId) => {
    document.getElementById('orderNumber').textContent = orderId;
    showModal('orderEditModal');
};
window.closeOrderEditModal = () => closeModal('orderEditModal');

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (e.target === modal) {
            closeModal(modal.id);
        }
    });
});

// Sample Data
const sampleOrders = [
    {
        id: 'ORD-001',
        customer: 'John Doe',
        products: [
            {name: 'T-Shirt', quantity: 2, price: 29.99}
        ],
        total: 59.98,
        date: '2024-02-20',
        status: 'pending'
    }
];

document.getElementById('orderEditForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    // Update order logic here
    closeOrderEditModal();
});