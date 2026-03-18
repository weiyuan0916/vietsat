/**
 * Order API - Handles order-related API calls
 * Uses base ApiService for HTTP requests
 */

(function() {
  'use strict';

  // Create order API instance
  const OrderApi = {
    /**
     * Create a new order
     * @param {string} facebookProfileLink - Facebook profile URL
     * @returns {Promise<Object>} Order data with order_code, amount, expires_at, qr_content
     */
    async create(facebookProfileLink) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        return await api.post('/orders', { 
          facebook_profile_link: facebookProfileLink 
        });
      } catch (error) {
        console.error('Error creating order:', error);
        throw error;
      }
    },

    /**
     * Check order status
     * @param {string} orderCode - Order code
     * @returns {Promise<Object>} Order data with status, amount, etc.
     */
    async getStatus(orderCode) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        return await api.get(`/orders/${orderCode}`);
      } catch (error) {
        console.error(`Error checking order ${orderCode}:`, error);
        throw error;
      }
    },

    /**
     * Get order details
     * @param {string} orderCode - Order code
     * @returns {Promise<Object>} Full order details
     */
    async getByCode(orderCode) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        return await api.get(`/orders/${orderCode}`);
      } catch (error) {
        console.error(`Error fetching order ${orderCode}:`, error);
        throw error;
      }
    },

    /**
     * List user's orders
     * @param {Object} params - Query parameters (status, page, limit)
     * @returns {Promise<Object>} List of orders
     */
    async list(params = {}) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const queryString = new URLSearchParams(params).toString();
        const endpoint = queryString ? `/orders?${queryString}` : '/orders';
        return await api.get(endpoint);
      } catch (error) {
        console.error('Error listing orders:', error);
        throw error;
      }
    }
  };

  // Export globally
  window.OrderApi = OrderApi;
})();
