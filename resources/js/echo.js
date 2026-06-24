import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Logika Hemat Koneksi (Idle Disconnect)
let idleTimer;
const disconnectTime = 1 * 60 * 1000; // 1 Menit Inaktif = Putus Koneksi

function resetIdleTimer() {
    // Jika sebelumnya terputus, sambungkan kembali
    if (window.Echo.connector.pusher.connection.state === 'disconnected') {
        console.log('User kembali aktif, menyambungkan ke Pusher...');
        window.Echo.connect();
    }

    clearTimeout(idleTimer);
    idleTimer = setTimeout(() => {
        console.log('User tidak aktif selama 5 menit, memutuskan koneksi Pusher untuk menghemat kuota...');
        window.Echo.disconnect();
    }, disconnectTime);
}

// Pantau aktivitas user
['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => {
    window.addEventListener(evt, resetIdleTimer, true);
});

resetIdleTimer();

// Realtime Global Listener
window.Echo.channel('notifications')
    .listen('.new-notification', (e) => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: e.type || 'info',
            title: e.message
        });
    });

