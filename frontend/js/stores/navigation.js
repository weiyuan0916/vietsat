/**
 * Navigation History Store
 * Tracks user navigation to enable proper back functionality
 */

class NavigationStore {
  constructor() {
    this.history = [];
    this.currentIndex = -1;
    this.MAX_HISTORY = 50; // Prevent memory leak
  }

  /**
   * Push a new route to history
   * @param {string} route - Route name (e.g., 'home', 'services', 'service-detail')
   * @param {object} params - Route parameters
   */
  push(route, params = {}) {
    // Remove any forward history when pushing new route
    this.history = this.history.slice(0, this.currentIndex + 1);
    
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
      this.currentIndex--;
      const previousRoute = this.history[this.currentIndex];
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
      window.localStorage.setItem('__nav_history', JSON.stringify({
        history: this.history,
        currentIndex: this.currentIndex
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
      const stored = window.localStorage.getItem('__nav_history');
      if (stored) {
        const data = JSON.parse(stored);
        this.history = data.history || [];
        this.currentIndex = data.currentIndex || -1;
        console.log('Navigation history restored:', this.history.length, 'items');
      }
    } catch (e) {
      console.warn('Failed to restore navigation history:', e);
    }
  }
}

// Create singleton instance
const navigationStore = new NavigationStore();
navigationStore._restore();

// Expose globally
window.NavigationStore = navigationStore;
