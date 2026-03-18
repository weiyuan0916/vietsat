/**
 * Navigation History Store
 * Tracks user navigation to enable proper back functionality
 */

class NavigationStore {
  constructor() {
    this.history = [];
    this.currentIndex = -1;
    this.MAX_HISTORY = 50; // Prevent memory leak
    this.storageKey = '__nav_history';
    this.sessionKey = '__nav_session_id';
    this.sessionId = this._getOrCreateSessionId();
  }

  /**
   * Push a new route to history
   * @param {string} route - Route name (e.g., 'home', 'services', 'service-detail')
   * @param {object} params - Route parameters
   */
  push(route, params = {}) {
    this.history = this.history.slice(0, this.currentIndex + 1);

    const current = this.getCurrent();
    if (current && current.route === route) {
      current.params = params;
      current.timestamp = Date.now();
      this._persist();
      console.log('Navigation pushed:', route, 'Index:', this.currentIndex);
      return;
    }

    this.history.push({
      route,
      params,
      timestamp: Date.now()
    });
    
    this.currentIndex = this.history.length - 1;
    
    // Prevent memory bloat
    if (this.history.length > this.MAX_HISTORY) {
      this.history.shift();
      this.currentIndex--;
    }
    
    this._persist();
    console.log('Navigation pushed:', route, 'Index:', this.currentIndex);
  }

  /**
   * Go back to previous route
   * @returns {object} Previous route info or null if at start
   */
  back() {
    if (this.currentIndex > 0) {
      const currentRoute = this.history[this.currentIndex]?.route || null;
      this.currentIndex--;
      while (this.currentIndex > 0 && this.history[this.currentIndex]?.route === currentRoute) {
        this.currentIndex--;
      }
      const previousRoute = this.history[this.currentIndex] || null;
      this._persist();
      console.log('Navigation back to:', previousRoute.route);
      return previousRoute;
    }
    console.log('No previous route in history');
    return null;
  }

  /**
   * Get current route
   */
  getCurrent() {
    if (this.currentIndex >= 0 && this.currentIndex < this.history.length) {
      return this.history[this.currentIndex];
    }
    return null;
  }

  /**
   * Get previous route without changing current position
   */
  getPrevious() {
    if (this.currentIndex > 0) {
      return this.history[this.currentIndex - 1];
    }
    return null;
  }

  /**
   * Clear all history (e.g., on logout)
   */
  clear() {
    this.history = [];
    this.currentIndex = -1;
    this._persist();
  }

  /**
   * Get full history for debugging
   */
  getHistory() {
    return this.history.map((item, index) => ({
      ...item,
      isCurrent: index === this.currentIndex
    }));
  }

  /**
   * Persist to localStorage for recovery
   */
  _persist() {
    try {
      window.localStorage.setItem(this.storageKey, JSON.stringify({
        history: this.history,
        currentIndex: this.currentIndex,
        marker: this._createMarker()
      }));
    } catch (e) {
      console.warn('Failed to persist navigation history:', e);
    }
  }

  /**
   * Restore from localStorage on init
   */
  _restore() {
    try {
      const stored = window.localStorage.getItem(this.storageKey);
      if (stored) {
        const data = JSON.parse(stored);
        const marker = data && data.marker ? data.marker : null;
        if (!this._isMarkerValid(marker)) {
          this.clear();
          return;
        }
        this.history = Array.isArray(data.history) ? data.history : [];
        this.currentIndex = Number.isInteger(data.currentIndex) ? data.currentIndex : -1;
        if (this.currentIndex >= this.history.length) {
          this.currentIndex = this.history.length - 1;
        }
        console.log('Navigation history restored:', this.history.length, 'items');
      }
    } catch (e) {
      console.warn('Failed to restore navigation history:', e);
    }
  }

  _getOrCreateSessionId() {
    try {
      const existing = window.sessionStorage.getItem(this.sessionKey);
      if (existing) return existing;
      const created = String(Date.now()) + '-' + Math.random().toString(36).slice(2, 8);
      window.sessionStorage.setItem(this.sessionKey, created);
      return created;
    } catch (_) {
      return 'no-session';
    }
  }

  _createMarker() {
    const path = (window.location && window.location.pathname) ? window.location.pathname : '';
    return { path, sessionId: this.sessionId };
  }

  _isMarkerValid(marker) {
    if (!marker || typeof marker !== 'object') return false;
    const currentPath = (window.location && window.location.pathname) ? window.location.pathname : '';
    if (marker.path !== currentPath) return false;
    if (marker.sessionId !== this.sessionId) return false;
    return true;
  }
}

// Create singleton instance
const navigationStore = new NavigationStore();
navigationStore._restore();

// Expose globally
window.NavigationStore = navigationStore;
