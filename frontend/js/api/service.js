/**
 * Service API - Handles service-related API calls
 * Uses base ApiService for HTTP requests
 */

(function() {
  'use strict';

  // Create service API instance
  const ServiceApi = {
    /**
     * Fetch default service from API
     * @returns {Promise<Object>} Service data with id, name, duration_days, price
     */
    async getDefault() {
      try {
        console.log('Fetching default service from:', window.AppConfig.apiBaseUrl + '/services/default');
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const response = await api.get('/services/default');
        console.log('Service API response:', response);
        
        // API returns: { status, message, data: { id, name, duration_days, price, ... } }
        // We need to extract the data object
        if (response && response.status && response.data) {
          return response.data;
        }
        
        // Fallback if response format is different
        return response;
      } catch (error) {
        console.error('Error fetching default service:', error);
        throw error;
      }
    },

    /**
     * Fetch service by ID
     * @param {number} id - Service ID
     * @returns {Promise<Object>} Service data
     */
    async getById(id) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const response = await api.get(`/services/${id}`);
        
        // API returns: { status, message, data: { ... } }
        if (response && response.status && response.data) {
          return response.data;
        }
        
        // Fallback if response format is different
        return response;
      } catch (error) {
        console.error(`Error fetching service ${id}:`, error);
        throw error;
      }
    },

    /**
     * Fetch all active services with pagination
     * @param {number} page - Page number (default: 1)
     * @param {number} perPage - Items per page (default: 10)
     * @returns {Promise<Object>} Response with items, meta, and links
     */
    async getAll(page = 1, perPage = 10) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const response = await api.get(`/services?page=${page}&per_page=${perPage}`);

        // Transform response to match expected format
        // API returns: { status, message, data: { items, meta, links } }
        if (response && response.data) {
          return {
            items: response.data.items || [],
            meta: response.data.meta || {},
            links: response.data.links || {}
          };
        }

        // Fallback for old format
        return {
          items: Array.isArray(response) ? response : [],
          meta: {},
          links: {}
        };
      } catch (error) {
        console.error('Error fetching services:', error);
        throw error;
      }
    },

    /**
     * Get list of services (simplified, returns items array)
     * @returns {Promise<Array>} Array of service objects
     */
    async getList() {
      const result = await this.getAll();
      return result.items;
    }
  };

  // Export globally
  window.ServiceApi = ServiceApi;
})();
