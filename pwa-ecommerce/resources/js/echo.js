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
    
    // Disable CSRF for API-only frontend (Framework7)
    // Authentication is done via order code validation in channel callback
    csrfToken: null,
    
    // Custom authorizer for API-based authentication
    authorizer: (channel) => {
        return {
            authorize: (socketId, callback) => {
                // Extract order code from channel name
                // Channel format: private-order.ORD-XXXXXXXXXX
                const channelName = channel.name;
                const orderCode = channelName.replace('private-order.', '');
                
                // Make API request to authorize
                fetch('/api/v1/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channelName,
                        order_code: orderCode,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.authorized) {
                        callback(null, { auth: data.auth });
                    } else {
                        callback(new Error(data.error || 'Unauthorized'), null);
                    }
                })
                .catch(error => {
                    callback(error, null);
                });
            },
        };
    },
    
    // Reconnect configuration
    reconnectDelay: 5000,
    reconnectAttempts: 10,
    
    // Activity timeout
    activityTimeout: 30000,
});
