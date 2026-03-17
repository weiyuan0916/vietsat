/**
 * Service Store - State management for service data
 * Implements observer pattern for reactive updates
 */

(function() {
  'use strict';

  class ServiceStore {
    constructor() {
      this._service = null;
      this._loading = false;
      this._error = null;
      this._subscribers = [];
    }

    /**
     * Get current service data
     * @returns {Object|null}
     */
    get service() {
      return this._service;
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
     * Check if service is loaded
     * @returns {boolean}
     */
    get isLoaded() {
      return this._service !== null && !this._loading;
    }

    /**
     * Subscribe to store changes
     * @param {Function} callback - Function to call on state changes
     * @returns {Function} Unsubscribe function
     */
    subscribe(callback) {
      this._subscribers.push(callback);
      
      // Return unsubscribe function
      return () => {
        this._subscribers = this._subscribers.filter(cb => cb !== callback);
      };
    }

    /**
     * Notify all subscribers of state change
     */
    _notify() {
      const state = {
        service: this._service,
        loading: this._loading,
        error: this._error,
        isLoaded: this.isLoaded
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
     * Load default service from API
     */
    async loadService() {
      // Don't reload if already loading
      if (this._loading) {
        return;
      }

      this._loading = true;
      this._error = null;
      this._notify();

      try {
        const service = await ServiceApi.getDefault();
        this._service = service;
        this._loading = false;
        this._notify();
      } catch (error) {
        this._error = error;
        this._loading = false;
        this._notify();
        throw error;
      }
    }

    /**
     * Reset store to initial state
     */
    reset() {
      this._service = null;
      this._loading = false;
      this._error = null;
      this._notify();
    }

    /**
     * Get service display data with formatting
     * @returns {Object|null}
     */
    getDisplayData() {
      if (!this._service) {
        return null;
      }

      return {
        name: this._service.name,
        duration: this._service.duration_days,
        durationFormatted: this._service.duration_days + ' ngày',
        price: this._service.price,
        priceFormatted: this._service.price.toLocaleString('vi-VN') + ' VND',
        priceShort: (this._service.price / 1000).toFixed(0) + 'k'
      };
    }
  }

  // Create singleton instance
  const serviceStore = new ServiceStore();

  // Export globally
  window.ServiceStore = serviceStore;
})();
