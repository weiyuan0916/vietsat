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
        return await api.get('/service/default');
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
        return await api.get(`/services/${id}`);
      } catch (error) {
        console.error(`Error fetching service ${id}:`, error);
        throw error;
      }
    },

    /**
     * Fetch all active services
     * @returns {Promise<Array>} List of active services
     */
    async getAll() {
      try {
        const api = new window.ApiService(window.AppConfig.apiBaseUrl);
        return await api.get('/services');
      } catch (error) {
        console.error('Error fetching services:', error);
        throw error;
      }
    }
  };

  // Export globally
  window.ServiceApi = ServiceApi;
})();
