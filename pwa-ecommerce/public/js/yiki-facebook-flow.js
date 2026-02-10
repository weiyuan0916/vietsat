/**
 * ============================================
 * YIKI FRONTEND - Facebook Profile Extract Flow
 * ============================================
 *
 * Khi người dùng nhập Facebook URL và nhấn "Tiếp tục":
 * 1. Gọi API extract-uid để lấy UID thực từ Facebook
 * 2. Nếu thành công, notify extension để auto-fill
 * 3. Redirect sang trang thanh toán
 *
 * Cách sử dụng:
 * - Include yiki-extension-helper.js trong view
 * - Import module này hoặc sử dụng YikiExtension global
 */

// Cách 1: Sử dụng module import (nếu có ES modules)
// import { extractFacebookUid, continueToPayment } from './utils/yiki-facebook-flow';

// Cách 2: Sử dụng global YikiExtension
// <script src="/js/yiki-extension-helper.js"></script>

/**
 * Main function: Xử lý flow khi user nhấn "Tiếp tục"
 *
 * @param {string} facebookUrl - URL Facebook profile từ input
 * @returns {Promise<object>} - Kết quả xử lý
 */
async function handleFacebookContinue(facebookUrl) {
  console.log('[Yiki] Processing Facebook URL:', facebookUrl);

  // Validate URL trước
  if (!isValidFacebookUrl(facebookUrl)) {
    return {
      success: false,
      error: 'URL Facebook không hợp lệ. Vui lòng nhập đúng định dạng.'
    };
  }

  try {
    // Bước 1: Gọi API extract-uid để lấy UID thực
    const extractResult = await extractFacebookUid(facebookUrl);

    if (!extractResult.success) {
      // Vẫn tiếp tục với URL gốc nếu extract thất bại
      console.warn('[Yiki] UID extract failed, using URL:', extractResult.error);

      // Vẫn notify extension với URL gốc
      await notifyExtensionWithUrl(facebookUrl);

      return {
        success: true,
        data: {
          uid: null,
          url: facebookUrl,
          note: 'Không trích xuất được UID, sử dụng URL gốc'
        }
      };
    }

    const { uid, original_url, normalized_url } = extractResult.data;

    console.log('[Yiki] UID extracted successfully:', uid);

    // Bước 2: Notify extension
    const extensionNotified = await notifyExtension(uid, original_url);

    // Bước 3: Tiếp tục sang thanh toán
    const paymentResult = await continueToPayment({
      facebook_profile_link: original_url,
      uid: uid, // Gửi UID về backend nếu cần
      normalized_url: normalized_url
    });

    return {
      success: true,
      data: {
        uid: uid,
        url: original_url,
        extensionNotified: extensionNotified,
        payment: paymentResult
      }
    };

  } catch (error) {
    console.error('[Yiki] Error in Facebook flow:', error);
    return {
      success: false,
      error: error.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
    };
  }
}

/**
 * Gọi API extract-uid để trích xuất UID từ Facebook
 *
 * @param {string} url - Facebook profile URL
 * @returns {Promise<object>} - Kết quả từ API
 */
async function extractFacebookUid(url) {
  try {
    const response = await fetch('/api/v1/facebook-profiles/extract-uid', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        facebook_profile_link: url
      })
    });

    const result = await response.json();

    if (!response.ok) {
      return {
        success: false,
        error: result.message || 'Lỗi khi trích xuất UID',
        status: response.status
      };
    }

    return {
      success: result.success,
      data: result.data,
      message: result.message
    };

  } catch (error) {
    console.error('[Yiki] API error:', error);
    return {
      success: false,
      error: 'Không thể kết nối server: ' + error.message
    };
  }
}

/**
 * Notify extension về UID đã trích xuất
 *
 * @param {string} uid - Facebook UID
 * @param {string} profileUrl - Profile URL đầy đủ
 * @returns {Promise<object>} - Kết quả notify
 */
async function notifyExtension(uid, profileUrl) {
  // Kiểm tra xem extension có available không
  if (typeof YikiExtension === 'undefined') {
    console.warn('[Yiki] YikiExtension not loaded, skipping notify');
    return { success: false, error: 'Extension helper not loaded' };
  }

  try {
    // Ping để kiểm tra extension
    const pingResult = await new Promise((resolve) => {
      YikiExtension.ping((result) => resolve(result));
    });

    console.log('[Yiki] Extension ping result:', pingResult);

    if (!pingResult.available) {
      console.log('[Yiki] Extension not available, skipping');
      return { success: false, error: 'Extension not available' };
    }

    // Notify extension
    const notifyResult = await YikiExtension.notify(uid, profileUrl);
    console.log('[Yiki] Extension notified successfully:', notifyResult);

    return { success: true, method: notifyResult.method };

  } catch (error) {
    console.error('[Yiki] Extension notify error:', error);
    // Không throw error vì đây chỉ là optional feature
    return { success: false, error: error.message };
  }
}

/**
 * Notify extension với URL gốc (fallback khi extract UID thất bại)
 *
 * @param {string} url - Facebook profile URL
 * @returns {Promise<object>}
 */
async function notifyExtensionWithUrl(url) {
  if (typeof YikiExtension === 'undefined') {
    return { success: false, error: 'Extension helper not loaded' };
  }

  // Try extract UID từ URL
  const uidMatch = url.match(/facebook\.com\/(profile\.php\?id=)?(\d+)/i);
  const uid = uidMatch ? uidMatch[2] : null;

  if (uid) {
    return await notifyExtension(uid, url);
  }

  // Nếu không parse được UID, vẫn notify với URL
  return await YikiExtension.notify('unknown', url);
}

/**
 * Tiếp tục sang trang thanh toán
 *
 * @param {object} data - Dữ liệu Facebook profile
 * @returns {Promise<object>} - Kết quả redirect
 */
async function continueToPayment(data) {
  try {
    // Lưu vào sessionStorage để trang thanh toán sử dụng
    sessionStorage.setItem('yiki_facebook_data', JSON.stringify({
      ...data,
      timestamp: Date.now()
    }));

    // Redirect sang trang thanh toán
    window.location.href = `/payment?facebook_url=${encodeURIComponent(data.facebook_profile_link)}`;

    return { success: true, redirected: true };

  } catch (error) {
    console.error('[Yiki] Redirect error:', error);
    return { success: false, error: error.message };
  }
}

/**
 * Validate Facebook profile URL
 *
 * @param {string} url - URL to validate
 * @returns {boolean}
 */
function isValidFacebookUrl(url) {
  if (!url || !url.trim()) return false;

  const urlLower = url.toLowerCase().trim();

  // Check for facebook.com
  if (!urlLower.includes('facebook.com')) return false;

  // Basic format validation
  const facebookPatterns = [
    /^https?:\/\/(www\.|m\.|mbasic\.)?facebook\.com\/[\w.]+\/?$/i,
    /^https?:\/\/(www\.|m\.|mbasic\.)?facebook\.com\/profile\.php\?id=\d+&?.*$/i,
    /^https?:\/\/(www\.|m\.|mbasic\.)?facebook\.com\/groups\/.*$/i
  ];

  return facebookPatterns.some(pattern => pattern.test(url));
}

/**
 * Khôi phục dữ liệu từ sessionStorage (gọi ở trang thanh toán)
 *
 * @returns {object|null}
 */
function getStoredFacebookData() {
  try {
    const data = sessionStorage.getItem('yiki_facebook_data');
    if (!data) return null;

    const parsed = JSON.parse(data);

    // Check if data is still valid (less than 24 hours)
    const maxAge = 24 * 60 * 60 * 1000;
    if (Date.now() - parsed.timestamp > maxAge) {
      sessionStorage.removeItem('yiki_facebook_data');
      return null;
    }

    return parsed;
  } catch (error) {
    console.error('[Yiki] Error reading stored data:', error);
    return null;
  }
}

// Export functions if using ES modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    handleFacebookContinue,
    extractFacebookUid,
    notifyExtension,
    notifyExtensionWithUrl,
    continueToPayment,
    isValidFacebookUrl,
    getStoredFacebookData
  };
}

// Also expose to global scope
if (typeof window !== 'undefined') {
  window.YikiFacebookFlow = {
    handleFacebookContinue,
    extractFacebookUid,
    notifyExtension,
    notifyExtensionWithUrl,
    continueToPayment,
    isValidFacebookUrl,
    getStoredFacebookData
  };
}


