/**
 * Laravel Reverb WebSocket Client
 *
 * Kết nối WebSocket để nhận thông báo thanh toán real-time
 *
 * @package App\Http\Controllers\Api
 * @version 1.0.0
 */

// WebSocket connection configuration
const WEBSOCKET_CONFIG = {
    // WebSocket URL - sẽ được thay thế tự động dựa trên môi trường
    url: '{{WEBSOCKET_URL}}',

    // Reverb app key từ .env
    appKey: '{{REVERB_APP_KEY}}',

    // Các kênh cần lắng nghe
    channels: {
        order: (orderCode) => `order.${orderCode}`,
    },

    // Các sự kiện
    events: {
        paymentPending: 'payment.pending',
        paymentSuccess: 'payment.success',
        paymentExpired: 'payment.expired',
    },
};

/**
 * WebSocket Service Class
 * Quản lý kết nối và lắng nghe sự kiện
 */
class WebSocketService {
    constructor(config = {}) {
        this.config = { ...WEBSOCKET_CONFIG, ...config };
        this.socket = null;
        this.channels = new Map();
        this.listeners = new Map();
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
    }

    /**
     * Khởi tạo kết nối WebSocket
     */
    connect() {
        return new Promise((resolve, reject) => {
            try {
                // Xây dựng URL từ config
                const wsUrl = this._buildWebSocketUrl();

                console.log('[WebSocket] Connecting to:', wsUrl);

                // Tạo kết nối WebSocket
                this.socket = new WebSocket(wsUrl);

                // Xử lý sự kiện mở kết nối
                this.socket.onopen = (event) => {
                    console.log('[WebSocket] Connected successfully');
                    this.isConnected = true;
                    this.reconnectAttempts = 0;
                    this._authenticate();
                    resolve(event);
                };

                // Xử lý sự kiện nhận message
                this.socket.onmessage = (event) => {
                    this._handleMessage(JSON.parse(event.data));
                };

                // Xử lý lỗi
                this.socket.onerror = (event) => {
                    console.error('[WebSocket] Error:', event);
                    reject(event);
                };

                // Xử lý ngắt kết nối
                this.socket.onclose = (event) => {
                    console.log('[WebSocket] Disconnected:', event.code, event.reason);
                    this.isConnected = false;
                    this._handleDisconnect();
                };
            } catch (error) {
                console.error('[WebSocket] Connection error:', error);
                reject(error);
            }
        });
    }

    /**
     * Xây dựng WebSocket URL
     */
    _buildWebSocketUrl() {
        const appKey = this.config.appKey;
        const host = window.location.hostname;
        const port = '{{REVERB_PORT}}' || 8080;
        const scheme = window.location.protocol === 'https:' ? 'wss:' : 'ws:';

        // Sử dụng config url nếu được cung cấp
        if (this.config.url && this.config.url !== '{{WEBSOCKET_URL}}') {
            return this.config.url;
        }

        return `${scheme}//${host}:${port}?app_key=${appKey}`;
    }

    /**
     * Xác thực với Reverb server
     */
    _authenticate() {
        // Gửi Pusher-style authentication message
        const authMessage = {
            event: 'pusher:subscribe',
            data: {
                auth: '',
                channel_data: JSON.stringify({
                    user_id: null,
                    user_info: {},
                }),
            },
        };

        if (this.socket && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(authMessage));
        }
    }

    /**
     * Đăng ký kênh
     */
    subscribe(channelName) {
        if (!this.isConnected) {
            console.warn('[WebSocket] Not connected. Call connect() first.');
            return;
        }

        const subscribeMessage = {
            event: 'pusher:subscribe',
            data: {
                channel: channelName,
            },
        };

        this.socket.send(JSON.stringify(subscribeMessage));
        this.channels.set(channelName, true);
        console.log(`[WebSocket] Subscribed to channel: ${channelName}`);
    }

    /**
     * Hủy đăng ký kênh
     */
    unsubscribe(channelName) {
        if (!this.isConnected) return;

        const unsubscribeMessage = {
            event: 'pusher:unsubscribe',
            data: {
                channel: channelName,
            },
        };

        this.socket.send(JSON.stringify(unsubscribeMessage));
        this.channels.delete(channelName);
        console.log(`[WebSocket] Unsubscribed from channel: ${channelName}`);
    }

    /**
     * Lắng nghe sự kiện từ kênh
     */
    listen(channelName, eventName, callback) {
        const listenerKey = `${channelName}:${eventName}`;

        if (!this.listeners.has(listenerKey)) {
            this.listeners.set(listenerKey, []);
        }

        this.listeners.get(listenerKey).push(callback);

        // Nếu chưa đăng ký kênh, đăng ký ngay
        if (!this.channels.has(channelName)) {
            this.subscribe(channelName);
        }
    }

    /**
     * Lắng nghe cập nhật thanh toán đơn hàng
     */
    onOrderPaymentUpdate(orderCode, callback) {
        const channelName = this.config.channels.order(orderCode);

        // Lắng nghe các sự kiện thanh toán
        this.listen(channelName, this.config.events.paymentPending, callback);
        this.listen(channelName, this.config.events.paymentSuccess, callback);
        this.listen(channelName, this.config.events.paymentExpired, callback);
    }

    /**
     * Xử lý tin nhắn nhận được
     */
    _handleMessage(message) {
        const event = message.event || '';
        const channel = message.channel || '';
        const data = message.data || {};

        console.log(`[WebSocket] Received event: ${event} on channel: ${channel}`);

        // Tìm và gọi các listener phù hợp
        const listenerKey = `${channel}:${event}`;
        if (this.listeners.has(listenerKey)) {
            this.listeners.get(listenerKey).forEach((callback) => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`[WebSocket] Error in listener:`, error);
                }
            });
        }

        // Xử lý Pusher internal events
        if (event === 'pusher:subscription_succeeded') {
            console.log(`[WebSocket] Subscription succeeded: ${channel}`);
        }

        if (event === 'pusher:subscription_error') {
            console.error(`[WebSocket] Subscription error:`, data);
        }
    }

    /**
     * Xử lý ngắt kết nối
     */
    _handleDisconnect() {
        // Xóa tất cả kênh đã đăng ký
        this.channels.clear();

        // Thử kết nối lại
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1);

            console.log(`[WebSocket] Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);

            setTimeout(() => {
                this.connect().catch((error) => {
                    console.error('[WebSocket] Reconnection failed:', error);
                });
            }, delay);
        } else {
            console.error('[WebSocket] Max reconnection attempts reached');
        }
    }

    /**
     * Ngắt kết nối
     */
    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
        }
        this.isConnected = false;
        this.channels.clear();
        this.listeners.clear();
        console.log('[WebSocket] Disconnected manually');
    }

    /**
     * Kiểm tra trạng thái kết nối
     */
    get isOpen() {
        return this.isConnected;
    }
}

/**
 * PaymentStatusService
 * Service riêng để quản lý trạng thái thanh toán
 */
class PaymentStatusService {
    constructor() {
        this.websocket = new WebSocketService();
        this.currentOrderCode = null;
        this.onStatusChange = null;
    }

    /**
     * Bắt đầu lắng nghe cập nhật thanh toán
     */
    startListening(orderCode, onStatusChange) {
        this.currentOrderCode = orderCode;
        this.onStatusChange = onStatusChange;

        return new Promise((resolve, reject) => {
            this.websocket
                .connect()
                .then(() => {
                    // Đăng ký kênh đơn hàng
                    this.websocket.onOrderPaymentUpdate(orderCode, (data) => {
                        console.log('[PaymentStatus] Status update:', data);

                        if (this.onStatusChange) {
                            this.onStatusChange({
                                orderCode: data.order_code || orderCode,
                                status: data.status,
                                paidAt: data.paid_at,
                                message: data.message,
                            });
                        }
                    });

                    resolve();
                })
                .catch(reject);
        });
    }

    /**
     * Dừng lắng nghe
     */
    stopListening() {
        if (this.currentOrderCode) {
            const channelName = this.websocket.config.channels.order(this.currentOrderCode);
            this.websocket.unsubscribe(channelName);
        }
        this.currentOrderCode = null;
        this.onStatusChange = null;
    }

    /**
     * Ngắt kết nối hoàn toàn
     */
    disconnect() {
        this.stopListening();
        this.websocket.disconnect();
    }
}

// Export cho sử dụng trong Framework7
if (typeof window !== 'undefined') {
    window.WebSocketService = WebSocketService;
    window.PaymentStatusService = PaymentStatusService;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { WebSocketService, PaymentStatusService };
}

