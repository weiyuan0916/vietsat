/**
 * Auth API - Handles authentication-related API calls
 */

(function() {
  'use strict';

  const AuthApi = {
    async register(payload) {
      const api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return await api.post('/auth/register', payload);
    },

    async login(payload) {
      const api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return await api.post('/auth/login', payload);
    },

    async profile() {
      const api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return await api.get('/auth/profile');
    },

    async logout() {
      const api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return await api.post('/auth/logout', {});
    }
  };

  window.AuthApi = AuthApi;
})();

