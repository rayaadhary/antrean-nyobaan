import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo:  any;
    }
}

// Inisialisasi Pusher
window.Pusher = Pusher;

// Konfigurasi Echo untuk Laravel-Websockets
const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'qwerty',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1', 
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    debug: true,
    enabledTransports: ['ws', 'wss'],
});

console.log('Echo initialized');

export default echo;