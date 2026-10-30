const notifications = document.getElementById('realtime-notifications');

const updateConnectionStatus = (state) => {
    const connected = state === 'connected';
    const label = connected ? 'Notifikasi tersambung' : 'Notifikasi tidak tersambung';

    document.querySelectorAll('[data-realtime-status]').forEach((status) => {
        const text = status.querySelector('[data-realtime-status-text]');

        if (text) {
            text.textContent = label;
        }

        status.classList.toggle('is-connected', connected);
        status.classList.toggle('is-disconnected', !connected);
    });
};

if (notifications && window.Echo) {
    const connection = window.Echo.connector.pusher.connection;
    connection.bind('state_change', ({ current }) => updateConnectionStatus(current));
    updateConnectionStatus(connection.state);

    window.Echo.private('orders').listen('.order.placed', (event) => {
        const notification = document.createElement('div');
        const title = document.createElement('strong');
        const message = document.createElement('div');
        const invoice = document.createElement('a');

        notification.setAttribute('role', 'status');
        notification.style.cssText = 'background:#166534;color:#fff;padding:14px 18px;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,.2);max-width:360px;display:grid;gap:6px;';
        title.textContent = 'Transaksi baru';
        message.textContent = `${event.receipt_number} · ${event.cashier_name} · ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(event.total)}`;
        invoice.href = `/orders/${encodeURIComponent(event.order_id)}/invoice`;
        invoice.target = '_blank';
        invoice.rel = 'noopener';
        invoice.textContent = 'Buka invoice PDF';
        invoice.style.cssText = 'width:max-content;color:#fff;text-decoration:underline;font-size:12px;';
        notification.append(title, message, invoice);
        notifications.append(notification);

        window.setTimeout(() => notification.remove(), 8000);
    });
} else {
    updateConnectionStatus('failed');
}
