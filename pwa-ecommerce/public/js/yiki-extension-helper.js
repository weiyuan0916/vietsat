/**
 * Yiki to Extension Integration Helper
 *
 * File này cung cấp hàm để Yiki PWA notify extension khi đã trích xuất UID thành công
 *
 * Cách sử dụng trong Yiki:
 *
 * 1. Include file này trong Yiki:
 *    <script src="/path/to/yiki-extension-helper.js"></script>
 *
 * 2. Sau khi gọi API extract-uid thành công:
 *
 *    const response = await fetch('/api/v1/facebook-profiles/extract-uid', {
 *      method: 'POST',
 *      headers: { 'Content-Type': 'application/json' },
 *      body: JSON.stringify({ facebook_profile_link: userInputUrl })
 *    });
 *    const result = await response.json();
 *
 *    if (result.success) {
 *      // Notify extension
 *      YikiExtension.notify(result.data.uid, result.data.original_url);
 *    }
 */

(function(global) {
  'use strict';

  const YikiExtension = {
    /**
     * Extension ID (thay bằng ID thực của extension sau khi publish)
     * Để tìm ID: mở chrome://extensions, bật Developer mode, xem ID của extension
     */
    EXTENSION_ID: 'YOUR_EXTENSION_ID_HERE',

    /**
     * Notify extension đã extract được UID
     *
     * @param {string} uid - Facebook UID đã trích xuất
     * @param {string} profileUrl - Profile URL đầy đủ (optional, sẽ generate nếu không có)
     * @param {function} callback - Callback nhận kết quả (optional)
     */
    notify: function(uid, profileUrl, callback) {
      if (!uid) {
        console.warn('[YikiExtension] UID is required');
        if (callback) callback({ success: false, error: 'UID is required' });
        return Promise.reject(new Error('UID is required'));
      }

      const url = profileUrl || `https://www.facebook.com/${uid}`;

      // Method 1: Try direct message first
      if (typeof chrome !== 'undefined' &&
          chrome.runtime &&
          chrome.runtime.sendMessage) {

        chrome.runtime.sendMessage(
          this.EXTENSION_ID,
          {
            action: 'yiki_uid_extracted',
            uid: uid,
            profileUrl: url,
            source: 'yiki_pwa',
            timestamp: Date.now()
          },
          (response) => {
            if (chrome.runtime.lastError) {
              console.log('[YikiExtension] Direct message failed:', chrome.runtime.lastError.message);
              // Fall through to storage method
              this.notifyViaStorage(uid, url, callback);
            } else if (response && response.success) {
              console.log('[YikiExtension] Notification sent successfully');
              if (callback) callback({ success: true, method: 'message', response });
            }
          }
        );
      } else {
        // Chrome runtime not available, try storage
        this.notifyViaStorage(uid, url, callback);
      }

      // Return promise for modern usage
      return new Promise((resolve) => {
        this.notifyViaStorage(uid, url, (result) => {
          if (callback) callback(result);
          resolve(result);
        });
      });
    },

    /**
     * Notify via Chrome storage (fallback method)
     * Extension sẽ listen storage changes và auto-fill
     */
    notifyViaStorage: function(uid, profileUrl, callback) {
      if (typeof chrome === 'undefined' || !chrome.storage) {
        console.warn('[YikiExtension] Chrome storage not available');
        if (callback) callback({ success: false, error: 'Storage not available' });
        return;
      }

      chrome.storage.local.set({
        yiki_uid: uid,
        yiki_profile_url: profileUrl,
        yiki_source: 'yiki_pwa',
        yiki_timestamp: Date.now()
      }, () => {
        console.log('[YikiExtension] Data saved to storage:', { uid, profileUrl });
        if (callback) callback({ success: true, method: 'storage' });
      });
    },

    /**
     * Ping extension để kiểm tra xem có available không
     */
    ping: function(callback) {
      if (typeof chrome !== 'undefined' &&
          chrome.runtime &&
          chrome.runtime.sendMessage) {

        chrome.runtime.sendMessage(
          this.EXTENSION_ID,
          { action: 'yiki_ping' },
          (response) => {
            if (chrome.runtime.lastError) {
              console.log('[YikiExtension] Extension not available:', chrome.runtime.lastError.message);
              if (callback) callback({ available: false, error: chrome.runtime.lastError.message });
            } else if (response && response.success) {
              console.log('[YikiExtension] Extension available:', response);
              if (callback) callback({ available: true, info: response });
            }
          }
        );
      } else {
        console.warn('[YikiExtension] Chrome runtime not available');
        if (callback) callback({ available: false, error: 'Chrome runtime not available' });
      }
    },

    /**
     * Clear stored data (gọi khi không cần nữa)
     */
    clear: function(callback) {
      if (typeof chrome !== 'undefined' && chrome.storage) {
        chrome.storage.local.remove(
          ['yiki_uid', 'yiki_profile_url', 'yiki_timestamp', 'yiki_source'],
          () => {
            console.log('[YikiExtension] Storage cleared');
            if (callback) callback({ success: true });
          }
        );
      }
    }
  };

  // Export to global scope
  global.YikiExtension = YikiExtension;

  // Also support require/module exports
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = YikiExtension;
  }

})(typeof window !== 'undefined' ? window : this);


