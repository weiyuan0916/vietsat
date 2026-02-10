document.addEventListener('DOMContentLoaded', () => {
  const profileInput = document.getElementById('profileUrl');
  const submitBtn = document.getElementById('submitBtn');
  const statusEl = document.getElementById('status');
  const resultEl = document.getElementById('result');
  const yikiStatusEl = document.getElementById('yikiStatus') || createYikiStatusElement();

  function setStatus(msg) {
    statusEl.textContent = msg;
  }

  function setResult(msg) {
    resultEl.textContent = msg;
  }

  function setYikiStatus(msg, type = 'info') {
    yikiStatusEl.textContent = msg;
    yikiStatusEl.className = `yiki-status yiki-${type}`;
    yikiStatusEl.style.display = 'block';
  }

  function createYikiStatusElement() {
    const el = document.createElement('div');
    el.className = 'yiki-status';
    el.style.cssText = 'display:none; padding:8px; margin-top:10px; border-radius:4px; font-size:12px;';
    document.querySelector('.card').appendChild(el);
    return el;
  }

  /**
   * ============================================
   * YIKI INTEGRATION
   * Listen for UID from Yiki PWA and auto-fill
   * ============================================
   */

  // Listen for messages from background script
  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    console.log('[Popup] Received message:', message);

    if (message.action === 'yiki_uid_received' && message.uid) {
      console.log('[Popup] Yiki UID received:', message.uid);

      // Auto-fill the profile URL input
      const profileUrl = message.profileUrl || `https://www.facebook.com/${message.uid}`;
      profileInput.value = profileUrl;

      // Visual feedback
      setYikiStatus(`✓ Đã nhận từ Yiki: ${message.uid}`, 'success');

      // Highlight the input
      profileInput.style.borderColor = '#4CAF50';
      profileInput.style.backgroundColor = '#E8F5E9';
      setTimeout(() => {
        profileInput.style.borderColor = '';
        profileInput.style.backgroundColor = '';
      }, 3000);

      sendResponse({ success: true, filled: true });
    }

    return true;
  });

  // Check storage on load for any pending Yiki data
  chrome.storage.local.get(['yiki_uid', 'yiki_profile_url', 'yiki_timestamp'], (data) => {
    if (data.yiki_uid && data.yiki_profile_url) {
      const age = Date.now() - (data.yiki_timestamp || 0);
      // Only use data less than 5 minutes old
      if (age < 5 * 60 * 1000) {
        console.log('[Popup] Found Yiki data in storage:', data);
        profileInput.value = data.yiki_profile_url;
        setYikiStatus(`📥 Đã khôi phục từ Yiki: ${data.yiki_uid}`, 'info');
      } else {
        // Clear old data
        chrome.storage.local.remove(['yiki_uid', 'yiki_profile_url', 'yiki_timestamp']);
      }
    }
  });

  // Normalize URL when user types (convert mobile URLs, etc.)
  profileInput.addEventListener('input', () => {
    let url = profileInput.value.trim();
    if (url) {
      // Convert mobile URLs to desktop
      url = url.replace(/m\.facebook\.com/i, 'www.facebook.com');
      url = url.replace(/mbasic\.facebook\.com/i, 'www.facebook.com');
    }
  });

  submitBtn.addEventListener('click', () => {
    const url = profileInput.value && profileInput.value.trim();
    if (!url) {
      setStatus('Vui lòng nhập URL hồ sơ');
      return;
    }
    setStatus('Đang tìm UID…');
    setResult('');

    chrome.runtime.sendMessage({ action: 'fetchUid', url }, response => {
      if (!response) {
        setStatus('Không nhận được phản hồi từ background');
        return;
      }
      if (response.success) {
        setStatus('Đã tìm UID: ' + response.uid);
        setResult(response.groupUrl);
      } else {
        setStatus('Lỗi: ' + (response.error || 'Lỗi không xác định'));
      }
    });
  });
});


/**
 * ============================================
 * YIKI INTEGRATION - Helper for external PWA
 * ============================================
 *
 * Yiki PWA có thể gọi hàm này để notify extension:
 *
 * // Trong Yiki PWA JavaScript:
 * if (typeof window.yikiNotifyExtension === 'function') {
 *   window.yikiNotifyExtension(
 *     'cgeconocdkgepngjnakjlpmxibjbcncb', // Extension ID
 *     extractedUid,
 *     profileUrl
 *   );
 * }
 *
 * Hoặc sử dụng storage approach:
 * chrome.storage.local.set({
 *   yiki_uid: '100014343376569',
 *   yiki_profile_url: 'https://www.facebook.com/100014343376569'
 * });
 *
 * Extension sẽ tự động:
 * 1. Nhận message
 * 2. Lưu vào storage
 * 3. Thông báo cho popup
 * 4. Auto-fill input field
 */
