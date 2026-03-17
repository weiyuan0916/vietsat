/**
 * API Module Index
 * Entry point for all API services
 * 
 * Usage:
 * - ServiceApi.getDefault()
 * - OrderApi.create(facebookLink)
 * - OrderApi.getStatus(orderCode)
 */

(function() {
  'use strict';

  // Export base ApiService
  window.ApiService = window.ApiService || null;

  // Service API is already exported as window.ServiceApi in service.js
  // Order API is already exported as window.OrderApi in order.js

  // Convenience method to check if API is ready
  window.ApiReady = {
    /**
     * Check if all API modules are loaded
     * @returns {boolean} True if all modules are ready
     */
    check() {
      return !!(
        window.AppConfig && 
        window.AppConfig.apiBaseUrl && 
        window.ApiService && 
        window.ServiceApi && 
        window.OrderApi
      );
    },

    /**
     * Get API status info
     * @returns {Object} Status information
     */
    status() {
      return {
        config: !!(window.AppConfig && window.AppConfig.apiBaseUrl),
        baseService: !!window.ApiService,
        serviceApi: !!window.ServiceApi,
        orderApi: !!window.OrderApi,
        ready: this.check()
      };
    }
  };

  console.log('API Modules loaded:', window.ApiReady.status());
  console.log('AppConfig:', window.AppConfig);
})();
