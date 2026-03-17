/**
 * API Service - Base class for all API calls
 * Provides common HTTP methods with error handling
 */

class ApiService {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
    this.defaultHeaders = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
  }

  /**
   * Make a GET request
   * @param {string} endpoint - API endpoint
   * @param {Object} options - Fetch options
   * @returns {Promise<Object>} Response data
   */
  async get(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      method: 'GET',
      headers: { ...this.defaultHeaders, ...options.headers },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      return await this._handleResponse(response);
    } catch (error) {
      console.error(`API GET Error (${endpoint}):`, error);
      throw error;
    }
  }

  /**
   * Make a POST request
   * @param {string} endpoint - API endpoint
   * @param {Object} data - Request body data
   * @param {Object} options - Fetch options
   * @returns {Promise<Object>} Response data
   */
  async post(endpoint, data, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      method: 'POST',
      headers: { ...this.defaultHeaders, ...options.headers },
      body: JSON.stringify(data),
      ...options,
    };

    try {
      const response = await fetch(url, config);
      return await this._handleResponse(response);
    } catch (error) {
      console.error(`API POST Error (${endpoint}):`, error);
      throw error;
    }
  }

  /**
   * Make a PUT request
   * @param {string} endpoint - API endpoint
   * @param {Object} data - Request body data
   * @param {Object} options - Fetch options
   * @returns {Promise<Object>} Response data
   */
  async put(endpoint, data, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      method: 'PUT',
      headers: { ...this.defaultHeaders, ...options.headers },
      body: JSON.stringify(data),
      ...options,
    };

    try {
      const response = await fetch(url, config);
      return await this._handleResponse(response);
    } catch (error) {
      console.error(`API PUT Error (${endpoint}):`, error);
      throw error;
    }
  }

  /**
   * Make a DELETE request
   * @param {string} endpoint - API endpoint
   * @param {Object} options - Fetch options
   * @returns {Promise<Object>} Response data
   */
  async delete(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      method: 'DELETE',
      headers: { ...this.defaultHeaders, ...options.headers },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      return await this._handleResponse(response);
    } catch (error) {
      console.error(`API DELETE Error (${endpoint}):`, error);
      throw error;
    }
  }

  /**
   * Handle HTTP response
   * @param {Response} response - Fetch response object
   * @returns {Promise<Object>} Parsed response data
   * @throws {Error} If response is not ok
   */
  async _handleResponse(response) {
    const data = await response.json().catch(() => null);

    if (!response.ok) {
      const error = new Error(data?.message || `HTTP Error ${response.status}`);
      error.status = response.status;
      error.data = data;
      throw error;
    }

    return data;
  }
}

// Export for use
window.ApiService = ApiService;
