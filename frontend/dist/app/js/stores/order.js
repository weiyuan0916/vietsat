/**
 * Order Store - State management for order data
 * Implements observer pattern for reactive updates
 */

(function() {
  'use strict';

  class OrderStore {
    constructor() {
      this._currentOrder = null;
      this._orders = [];
      this._loading = false;
      this._error = null;
      this._subscribers = [];
    }

    /**
     * Get current order
     * @returns {Object|null}
     */
    get currentOrder() {
      return this._currentOrder;
    }

    /**
     * Get all orders
     * @returns {Array}
     */
    get orders() {
      return this._orders;
    }

    /**
     * Get loading state
     * @returns {boolean}
     */
    get loading() {
      return this._loading;
    }

    /**
     * Get error state
     * @returns {Error|null}
     */
    get error() {
      return this._error;
    }

    /**
     * Check if there's an active order
     * @returns {boolean}
     */
    get hasActiveOrder() {
      return this._currentOrder !== null;
    }

    /**
     * Get current order status
     * @returns {string|null}
     */
    get status() {
      return this._currentOrder?.status || null;
    }

    /**
     * Check if order is paid
     * @returns {boolean}
     */
    get isPaid() {
      return this._currentOrder?.status === 'paid';
    }

    /**
     * Check if order is pending
     * @returns {boolean}
     */
    get isPending() {
      return this._currentOrder?.status === 'pending';
    }

    /**
     * Check if order is expired
     * @returns {boolean}
     */
    get isExpired() {
      return this._currentOrder?.status === 'expired';
    }

    /**
     * Subscribe to store changes
     * @param {Function} callback - Function to call on state changes
     * @returns {Function} Unsubscribe function
     */
    subscribe(callback) {
      this._subscribers.push(callback);
      
      return () => {
        this._subscribers = this._subscribers.filter(cb => cb !== callback);
      };
    }

    /**
     * Notify all subscribers of state change
     */
    _notify() {
      const state = {
        currentOrder: this._currentOrder,
        orders: this._orders,
        loading: this._loading,
        error: this._error,
        hasActiveOrder: this.hasActiveOrder,
        status: this.status,
        isPaid: this.isPaid,
        isPending: this.isPending,
        isExpired: this.isExpired
      };
      
      this._subscribers.forEach(callback => {
        try {
          callback(state);
        } catch (err) {
          console.error('Store subscriber error:', err);
        }
      });
    }

    /**
     * Create a new order
     * @param {string} facebookProfileLink - Facebook profile URL
     * @returns {Promise<Object>} Created order data
     */
    async createOrder(facebookProfileLink) {
      this._loading = true;
      this._error = null;
      this._notify();

      try {
        const order = await OrderApi.create(facebookProfileLink);
        this._currentOrder = order;
        this._loading = false;
        this._notify();
        return order;
      } catch (error) {
        this._error = error;
        this._loading = false;
        this._notify();
        throw error;
      }
    }

    /**
     * Refresh current order status
     * @returns {Promise<Object>} Updated order data
     */
    async refreshStatus() {
      if (!this._currentOrder?.order_code) {
        return null;
      }

      try {
        const order = await OrderApi.getStatus(this._currentOrder.order_code);
        this._currentOrder = order;
        this._notify();
        return order;
      } catch (error) {
        console.error('Error refreshing order status:', error);
        throw error;
      }
    }

    /**
     * Load user's orders
     * @param {Object} params - Query parameters
     * @returns {Promise<Array>} List of orders
     */
    async loadOrders(params = {}) {
      this._loading = true;
      this._error = null;
      this._notify();

      try {
        const result = await OrderApi.list(params);
        this._orders = Array.isArray(result) ? result : (result.data || []);
        this._loading = false;
        this._notify();
        return this._orders;
      } catch (error) {
        this._error = error;
        this._loading = false;
        this._notify();
        throw error;
      }
    }

    /**
     * Clear current order
     */
    clearCurrentOrder() {
      this._currentOrder = null;
      this._notify();
    }

    /**
     * Reset store to initial state
     */
    reset() {
      this._currentOrder = null;
      this._orders = [];
      this._loading = false;
      this._error = null;
      this._notify();
    }

    /**
     * Get current order display data
     * @returns {Object|null}
     */
    getDisplayData() {
      if (!this._currentOrder) {
        return null;
      }

      return {
        orderCode: this._currentOrder.order_code,
        amount: this._currentOrder.amount,
        amountFormatted: this._currentOrder.amount.toLocaleString('vi-VN') + ' VND',
        status: this._currentOrder.status,
        statusText: this._getStatusText(this._currentOrder.status),
        expiresAt: this._currentOrder.expires_at,
        qrContent: this._currentOrder.qr_content,
        paidAt: this._currentOrder.paid_at,
        bankInfo: this._currentOrder.bank_info
      };
    }

    /**
     * Get status display text
     * @param {string} status - Order status
     * @returns {string}
     */
    _getStatusText(status) {
      const statusMap = {
        'pending': 'Chờ thanh toán',
        'paid': 'Đã thanh toán',
        'expired': 'Đã hết hạn',
        'cancelled': 'Đã hủy'
      };
      return statusMap[status] || status;
    }
  }

  // Create singleton instance
  const orderStore = new OrderStore();

  // Export globally
  window.OrderStore = orderStore;
})();
