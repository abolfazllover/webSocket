import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint : '/custom-broadcast-auth',
    auth: {
        headers: {
            // ارسال پارامتر دلخواه
            'X-Custom-Id': `${userId}`,
            'X-Custom-Token': 'abcd'
        }
    },
    withCredentials: true,
});

window.Echo.channel('test-channel')
    .listen(".App\\Events\\TestEvent", (e) => {
        console.log('Event received:', e);
        alert('یک Event جدید دریافت شد!');
    });

window.Echo.private('pc-amir').listen(".App\\Events\\TestPrivateEvent",(e)=>{
    console.log('Event received:', e);
    alert('یک Event جدید دریافت شد!');
})

