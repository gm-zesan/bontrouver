import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const isWebsocketDisabled = import.meta.env.VITE_DISABLE_WEBSOCKETS === 'true';

if (reverbKey && !isWebsocketDisabled && typeof window !== 'undefined') {
    try {
        const configuredHost = import.meta.env.VITE_REVERB_HOST;
        const currentHostname = window.location.hostname;
        
        // Dynamically use current domain if compiled host is localhost but running on a live website
        const isLocalDomain = currentHostname === 'localhost' || currentHostname === '127.0.0.1';
        const wsHost = (!isLocalDomain && (configuredHost === 'localhost' || configuredHost === '127.0.0.1'))
            ? currentHostname
            : (configuredHost || currentHostname);

        const isHttps = window.location.protocol === 'https:' || import.meta.env.VITE_REVERB_SCHEME === 'https';
        const wsPort = isHttps ? (import.meta.env.VITE_REVERB_PORT ?? 443) : (import.meta.env.VITE_REVERB_PORT ?? 8080);
        const wssPort = isHttps ? (import.meta.env.VITE_REVERB_PORT ?? 443) : (import.meta.env.VITE_REVERB_PORT ?? 8080);

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: wsHost,
            wsPort: wsPort,
            wssPort: wssPort,
            forceTLS: isHttps,
            enabledTransports: ['ws', 'wss'],
        });

        // Fail-safe: prevent infinite reconnection loop if WebSocket server is offline or unreachable
        if (window.Echo?.connector?.pusher?.connection) {
            let errorCount = 0;
            const pusherConnection = window.Echo.connector.pusher.connection;

            pusherConnection.bind('error', () => {
                errorCount++;
                if (errorCount >= 2) {
                    pusherConnection.disconnect();
                }
            });

            pusherConnection.bind('unavailable', () => {
                pusherConnection.disconnect();
            });

            pusherConnection.bind('failed', () => {
                pusherConnection.disconnect();
            });
        }
    } catch (e) {
        // Echo initialization gracefully skipped
    }
}

