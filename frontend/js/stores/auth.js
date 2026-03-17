/**
 * Auth Store - token/user state in localStorage
 */

(function() {
  'use strict';

  class AuthStore {
    constructor() {
      this._user = null;
      this._token = null;
      this._subscribers = [];
      this._loadFromStorage();
    }

    _loadFromStorage() {
      try {
        if (window.localStorage) {
          this._token = window.localStorage.getItem('token') || window.localStorage.getItem('auth_token');
        } else {
          this._token = null;
        }
        const rawUser = window.localStorage ? window.localStorage.getItem('auth_user') : null;
        this._user = rawUser ? JSON.parse(rawUser) : null;
      } catch (_) {
        this._token = null;
        this._user = null;
      }
    }

    _saveToStorage() {
      try {
        if (!window.localStorage) return;
        if (this._token) window.localStorage.setItem('token', this._token);
        else window.localStorage.removeItem('token');
        window.localStorage.removeItem('auth_token');
        if (this._user) window.localStorage.setItem('auth_user', JSON.stringify(this._user));
        else window.localStorage.removeItem('auth_user');
      } catch (_) {}
    }

    subscribe(cb) {
      this._subscribers.push(cb);
      return () => {
        this._subscribers = this._subscribers.filter(x => x !== cb);
      };
    }

    _notify() {
      const state = { user: this._user, token: this._token, isAuthenticated: this.isAuthenticated };
      this._subscribers.forEach(cb => {
        try { cb(state); } catch (_) {}
      });
    }

    get user() { return this._user; }
    get token() { return this._token; }
    get isAuthenticated() { return !!this._token; }

    setAuth(user, token) {
      this._user = user || null;
      this._token = token || null;
      this._saveToStorage();
      this._notify();
    }

    clear() {
      this.setAuth(null, null);
    }

    async refreshProfile() {
      const res = await window.AuthApi.profile();
      const user = res?.data?.user || res?.data || null;
      if (user) {
        this._user = user;
        this._saveToStorage();
        this._notify();
      }
      return user;
    }
  }

  window.AuthStore = new AuthStore();
})();

