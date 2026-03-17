/**
 * Service Store - State management for service data
 * Implements observer pattern for reactive updates
 */

(function() {
  'use strict';

  class ServiceStore {
    constructor() {
      this._services = [];
      this._service = null; // Default service
      this._loading = false;
      this._error = null;
      this._meta = {};
      this._links = {};
      this._subscribers = [];
    }

    /**
     * Get all services
     * @returns {Array}
     */
    get services() {
      return this._services;
    }

    /**
     * Get current (default) service
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
      return (this._service !== null || this._services.length > 0) && !this._loading;
    }

    /**
     * Get pagination meta
     * @returns {Object}
     */
    get meta() {
      return this._meta;
    }

    /**
     * Get pagination links
     * @returns {Object}
     */
    get links() {
      return this._links;
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
        services: this._services,
        service: this._service,
        loading: this._loading,
        error: this._error,
        isLoaded: this.isLoaded,
        meta: this._meta,
        links: this._links
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
     * Load all services from API with pagination
     * @param {number} page - Page number
     * @param {number} perPage - Items per page
     */
    async loadServices(page = 1, perPage = 10) {
      if (this._loading) {
        return;
      }

      this._loading = true;
      this._error = null;
      this._notify();

      try {
        console.log('ServiceStore: Loading services...');
        const result = await ServiceApi.getAll(page, perPage);
        console.log('ServiceStore: Got services:', result);

        this._services = result.items || [];
        this._meta = result.meta || {};
        this._links = result.links || {};

        // Set first active service as default
        if (this._services.length > 0 && !this._service) {
          this._service = this._services[0];
        }

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
     * Load default service from API
     */
    async loadService() {
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
     * Set active service by ID
     * @param {number} id - Service ID
     */
    setActiveService(id) {
      const found = this._services.find(s => s.id === id);
      if (found) {
        this._service = found;
        this._notify();
      }
    }

    /**
     * Reset store to initial state
     */
    reset() {
      this._services = [];
      this._service = null;
      this._loading = false;
      this._error = null;
      this._meta = {};
      this._links = {};
      this._notify();
    }

    /**
     * Get service display data with formatting
     * @param {Object|null} service - Service object (optional, uses default if not provided)
     * @returns {Object|null}
     */
    getDisplayData(service = null) {
      const svc = service || this._service;

      if (!svc) {
        return null;
      }

      return {
        id: svc.id,
        name: svc.name,
        duration: svc.duration_days,
        durationFormatted: svc.duration_days + ' ngày',
        durationMonths: Math.round(svc.duration_days / 30),
        price: svc.price,
        priceFormatted: svc.price.toLocaleString('vi-VN') + ' VND',
        priceShort: (svc.price / 1000).toFixed(0) + 'k',
        pricePerMonth: Math.round(svc.price / (svc.duration_days / 30))
      };
    }

    /**
     * Get all services display data
     * @returns {Array}
     */
    getAllDisplayData() {
      return this._services.map(svc => this.getDisplayData(svc));
    }
  }

  // Create singleton instance
  const serviceStore = new ServiceStore();

  // Export globally
  window.ServiceStore = serviceStore;
})();
