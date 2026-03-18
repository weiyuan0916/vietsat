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
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const response = await api.get('/services/default');
        return response?.data ?? response;
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
        return response?.data ?? response;
      } catch (error) {
        console.error(`Error fetching service ${id}:`, error);
        throw error;
      }
    },

    /**
     * Fetch all active services
     * @returns {Promise<Array>} List of active services
     */
    async getAll(page = 1, perPage = 10) {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        const response = await api.get(`/services?page=${page}&per_page=${perPage}`);

        if (response?.data) {
          return {
            items: response.data.items || [],
            meta: response.data.meta || {},
            links: response.data.links || {},
          };
        }

        return {
          items: Array.isArray(response) ? response : [],
          meta: {},
          links: {},
        };
      } catch (error) {
        console.error('Error fetching services:', error);
        throw error;
      }
    },

    async getList() {
      const result = await this.getAll();
      return result.items;
    }
  };

  // Export globally
  window.ServiceApi = ServiceApi;
})();
