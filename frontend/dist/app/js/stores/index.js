/**
 * Stores Module Index
 * Entry point for all state management stores
 */

(function() {
  'use strict';

  // Export stores to window
  // ServiceStore is in service.js
  // OrderStore is in order.js

  // Convenience check for store readiness
  window.StoresReady = {
    check() {
      return !!(window.ServiceStore && window.OrderStore);
    },
    
    status() {
      return {
        serviceStore: !!window.ServiceStore,
        orderStore: !!window.OrderStore,
        ready: this.check()
      };
    }
  };

  console.log('Stores loaded:', window.StoresReady.status());
})();
