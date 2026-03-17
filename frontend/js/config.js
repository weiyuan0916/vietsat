/**
 * Application Configuration
 * Centralized configuration for the frontend application
 * 
 * API Base URL should be set via environment variable during build
 * Fallback to window.APP_CONFIG if available (set by backend)
 */

(function() {
  'use strict';

  // Try to get config from window.APP_CONFIG (set by Laravel backend)
  const backendConfig = window.APP_CONFIG || {};

  // Determine API base URL based on environment
  // Production API: https://tiemnhaduy.com/api/v1
  // Fallback to window.__API_BASE_URL__ or backend config
  let apiBaseUrl = backendConfig.apiBaseUrl || window.__API_BASE_URL__;
  
  // If no valid URL is set, use production URL
  if (!apiBaseUrl || apiBaseUrl.includes('pwa-ecommerce.test')) {
    apiBaseUrl = 'https://tiemnhaduy.com/api/v1';
  }

  // Configuration object
  // __API_BASE_URL__ is replaced by Vite at build time
  window.AppConfig = {
    // API Configuration
    apiBaseUrl: apiBaseUrl,
    
    // App Info
    appName: 'TiemNhaDuy',
    appVersion: '1.0.0',
    
    // Payment Settings
    paymentExpiryMinutes: 5,
    
    // Realtime Settings
    realtimeEnabled: true,
    
    // Feature Flags
    features: {
      realtimePayment: true,
      offlineSupport: false,
      analytics: false,
      ...backendConfig.features
    }
  };

  // Expose config globally for legacy code compatibility
  window.API_BASE_URL = window.AppConfig.apiBaseUrl;
  
  // Debug: Log config in development
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('App Config:', window.AppConfig);
  }
})();
