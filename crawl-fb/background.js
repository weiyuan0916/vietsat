// {{CHENGQI:
// Action: Added
// Timestamp: 2025-10-16 14:40:00 UTC+7
// Reason: Service worker for data aggregation and JSON export
// Principles_Applied: Single Responsibility (data storage and export only), DRY
// Architecture_Note: Event-driven service worker, persistent data storage
// Security_Note: Validated message handling, safe JSON serialization
// Performance_Note: Efficient Map-based storage, batch updates, memory management
// }}

// Service Worker - Background Script
'use strict';

// Storage for collected posts (keyed by post ID to prevent duplicates)
let postsMap = new Map();
let totalPostsCollected = 0;

// Listen for messages
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === 'save_posts') {
    savePosts(message.data);
    sendResponse({ success: true, total: postsMap.size });
  } else if (message.action === 'download') {
    downloadJSON();
    sendResponse({ success: true });
  }
  return true;
});

/**
 * Save posts to storage
 * @param {Array} posts - Array of post objects
 */
function savePosts(posts) {
  if (!Array.isArray(posts)) {
    console.error('[Background] Invalid posts data');
    return;
  }

  let newPostsCount = 0;

  posts.forEach(post => {
    if (post.id && !postsMap.has(post.id)) {
      postsMap.set(post.id, post);
      newPostsCount++;
    }
  });

  totalPostsCollected = postsMap.size;

  console.log(`[Background] Saved ${newPostsCount} new posts. Total: ${totalPostsCollected}`);

  // Update storage for persistence
  persistPosts();
  
  // Notify popup of update
  notifyPopup();
}

/**
 * Persist posts to chrome.storage
 */
function persistPosts() {
  const postsArray = Array.from(postsMap.values());
  
  chrome.storage.local.set({
    collectedPosts: postsArray,
    lastUpdate: new Date().toISOString()
  }, () => {
    if (chrome.runtime.lastError) {
      console.error('[Background] Storage error:', chrome.runtime.lastError);
    }
  });
}

/**
 * Load posts from chrome.storage on startup
 */
function loadPosts() {
  chrome.storage.local.get(['collectedPosts'], (result) => {
    if (result.collectedPosts && Array.isArray(result.collectedPosts)) {
      postsMap.clear();
      result.collectedPosts.forEach(post => {
        if (post.id) {
          postsMap.set(post.id, post);
        }
      });
      totalPostsCollected = postsMap.size;
      console.log(`[Background] Loaded ${totalPostsCollected} posts from storage`);
    }
  });
}

/**
 * Download collected posts as JSON file
 */
function downloadJSON() {
  const postsArray = Array.from(postsMap.values());
  
  if (postsArray.length === 0) {
    console.warn('[Background] No posts to download');
    // Show notification
    chrome.notifications.create({
      type: 'basic',
      iconUrl: 'icons/icon48.png',
      title: 'FB Group Collector',
      message: 'No posts collected yet!'
    });
    return;
  }

  // Create JSON content
  const jsonContent = JSON.stringify({
    metadata: {
      totalPosts: postsArray.length,
      exportedAt: new Date().toISOString(),
      version: '1.0.0',
      source: 'FB Group Collector - Safe Mode'
    },
    posts: postsArray
  }, null, 2);

  // Create data URL (works in service workers, unlike blob URLs)
  const dataUrl = 'data:application/json;charset=utf-8,' + encodeURIComponent(jsonContent);
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
  const filename = `facebook_posts_${timestamp}.json`;

  chrome.downloads.download({
    url: dataUrl,
    filename: filename,
    saveAs: true
  }, (downloadId) => {
    if (chrome.runtime.lastError) {
      console.error('[Background] Download error:', chrome.runtime.lastError);
    } else {
      console.log(`[Background] Download started: ${filename}`);
      
      // Show success notification
      chrome.notifications.create({
        type: 'basic',
        iconUrl: 'icons/icon48.png',
        title: 'FB Group Collector',
        message: `Downloaded ${postsArray.length} posts successfully! 🎉`
      });

      // Clear collected posts after successful download
      clearPosts();
    }
  });
}

/**
 * Clear collected posts
 */
function clearPosts() {
  postsMap.clear();
  totalPostsCollected = 0;
  
  chrome.storage.local.set({
    collectedPosts: [],
    crawlerStatus: {
      isRunning: false,
      scrollCount: 0,
      postsCount: 0
    }
  });

  console.log('[Background] Cleared all collected posts');
  notifyPopup();
}

/**
 * Notify popup of status changes
 */
function notifyPopup() {
  chrome.runtime.sendMessage({
    type: 'status_update',
    data: {
      postsCount: postsMap.size
    }
  }).catch(() => {
    // Popup might not be open, ignore error
  });
}

// Initialize on service worker startup
loadPosts();

// Listen for extension installation/update
chrome.runtime.onInstalled.addListener((details) => {
  console.log('[Background] Extension installed/updated:', details.reason);
  
  if (details.reason === 'install') {
    // First time installation
    chrome.storage.local.set({
      crawlerSettings: {
        minDelay: 2,
        maxDelay: 5,
        maxScrolls: 50
      },
      crawlerStatus: {
        isRunning: false,
        scrollCount: 0,
        postsCount: 0
      },
      collectedPosts: []
    });
  }
});

console.log('[Background] Service worker initialized');

