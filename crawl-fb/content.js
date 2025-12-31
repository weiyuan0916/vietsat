// {{CHENGQI:
// Action: Added
// Timestamp: 2025-10-16 14:35:00 UTC+7
// Reason: Content script for Facebook DOM extraction and safe auto-scrolling
// Principles_Applied: SOLID (Single Responsibility - separate extraction and scrolling logic)
// Architecture_Note: Message-driven architecture, defensive DOM extraction
// Security_Note: Safe DOM queries, no eval(), XSS prevention through text content extraction
// Performance_Note: Debounced extraction, Set-based deduplication for O(1) lookups
// }}

(function() {
  'use strict';

  // State Management
  let isRunning = false;
  let scrollCount = 0;
  let settings = {
    minDelay: 2,
    maxDelay: 5,
    maxScrolls: 50
  };
  let collectedPostIds = new Set();
  let scrollInterval = null;

  // Listen for messages from popup
  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === 'start') {
      startCrawler(message.settings);
      sendResponse({ success: true });
    } else if (message.action === 'stop') {
      stopCrawler();
      sendResponse({ success: true });
    }
    return true;
  });

  function startCrawler(newSettings) {
    if (isRunning) {
      console.log('[FB Collector] Already running');
      return;
    }

    settings = { ...settings, ...newSettings };
    isRunning = true;
    scrollCount = 0;

    console.log('[FB Collector] Starting with settings:', settings);
    updateStatus();

    // Start the scroll loop
    scheduleNextScroll();
  }

  function stopCrawler() {
    if (!isRunning) return;

    isRunning = false;
    if (scrollInterval) {
      clearTimeout(scrollInterval);
      scrollInterval = null;
    }

    console.log('[FB Collector] Stopped. Total scrolls:', scrollCount);
    updateStatus();
  }

  function scheduleNextScroll() {
    if (!isRunning) return;

    // Check if max scrolls reached
    if (settings.maxScrolls > 0 && scrollCount >= settings.maxScrolls) {
      console.log('[FB Collector] Max scrolls reached');
      stopCrawler();
      return;
    }

    // Random delay between min and max
    const delay = (settings.minDelay + Math.random() * (settings.maxDelay - settings.minDelay)) * 1000;

    scrollInterval = setTimeout(() => {
      performScroll();
      scheduleNextScroll();
    }, delay);
  }

  function performScroll() {
    // Scroll to bottom smoothly
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: 'smooth'
    });

    scrollCount++;
    console.log(`[FB Collector] Scroll #${scrollCount}`);

    // Wait a bit for content to load, then extract
    setTimeout(() => {
      extractPosts();
      updateStatus();
    }, 1500);
  }

  function extractPosts() {
    const posts = [];
    
    // Remove non-post divs (based on Python code)
    const noiseDivs = document.querySelectorAll('div.xd9ej83.x162z183.xf7dkkf');
    console.log(`[FB Collector] Found ${noiseDivs.length} noise divs to ignore`);
    
    // Find actual Facebook Group posts (exact selector from Python code)
    const postElements = document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z');
    
    console.log(`[FB Collector] Found ${postElements.length} post elements`);

    postElements.forEach(postElement => {
      try {
        const postData = extractPostData(postElement);
        if (postData && postData.id && !collectedPostIds.has(postData.id)) {
          posts.push(postData);
          collectedPostIds.add(postData.id);
        }
      } catch (error) {
        console.error('[FB Collector] Error extracting post:', error);
      }
    });

    if (posts.length > 0) {
      console.log(`[FB Collector] Extracted ${posts.length} new posts`);
      
      // Send to background for storage
      chrome.runtime.sendMessage({
        type: 'save_posts',
        data: posts
      });
    }
  }

  function extractPostData(postElement) {
    const postData = {
      id: null,
      postLink: null,
      authorName: null,
      authorProfileLink: null,
      content: null,
      date: null,
      images: [],
      timestamp: new Date().toISOString(),
      extractedAt: Date.now()
    };

    // --- Extract Author Name (exact selector from Python code) ---
    const authorTag = postElement.querySelector(
      'span.xdj266r.x14z9mp.xat24cr.x1lziwak.xexx8yu.xyri2b.x18d9i69.x1c1uobl.x1hl2dhg.x16tdsg8.x1vvkbs'
    );
    if (authorTag) {
      postData.authorName = authorTag.textContent.trim();
    }

    // --- Extract Profile Link (exact selector from Python code) ---
    const profileTag = postElement.querySelector("a[href*='/groups/'][href*='/user/']");
    if (profileTag) {
      postData.authorProfileLink = profileTag.href;
    }

    // --- Extract Post Link (exact selector from Python code) ---
    const postLinkTag = postElement.querySelector("a[href*='/groups/'][href*='/posts/']");
    if (postLinkTag) {
      postData.postLink = postLinkTag.href;
      // Use post link as unique ID
      postData.id = postData.postLink;
    }

    // --- Extract Date (exact selector from Python code) ---
    const dateTag = postElement.querySelector(
      'div.x6s0dn4.x17zd0t2.x78zum5.x1q0g3np.x1a02dak'
    );
    if (dateTag) {
      postData.date = dateTag.textContent.trim();
    }

    // --- Extract Post Content (text only) ---
    // Try multiple content selectors
    const contentSelectors = [
      'div[data-ad-preview="message"]',
      'div[data-ad-comet-preview="message"]',
      'div[dir="auto"][style*="text-align"]',
      '[data-ad-rendering-role="story_message"]'
    ];

    for (const selector of contentSelectors) {
      const contentElement = postElement.querySelector(selector);
      if (contentElement) {
        postData.content = contentElement.textContent.trim();
        break;
      }
    }

    // Fallback: get all text content if specific selectors fail
    if (!postData.content) {
      const textDivs = postElement.querySelectorAll('div[dir="auto"]');
      let maxText = '';
      textDivs.forEach(div => {
        const text = div.textContent.trim();
        if (text.length > maxText.length && text.length > 20) {
          maxText = text;
        }
      });
      postData.content = maxText;
    }

    // --- Extract Image URLs ---
    const imgElements = postElement.querySelectorAll('img[src]');
    imgElements.forEach(img => {
      const src = img.src;
      // Filter out profile pictures, icons, and tiny images
      if (src && 
          !src.includes('emoji') && 
          !src.includes('static') &&
          !src.includes('/rsrc.php/') &&
          img.naturalWidth > 100) {
        postData.images.push(src);
      }
    });

    // Generate fallback ID if no post link found
    if (!postData.id) {
      postData.id = generatePostId(postElement);
    }

    // Only return post if it has some meaningful content
    if (postData.authorName || postData.content || postData.images.length > 0) {
      return postData;
    }

    return null;
  }

  function generatePostId(element) {
    // Generate a simple hash from text content
    const text = element.textContent.substring(0, 100);
    let hash = 0;
    for (let i = 0; i < text.length; i++) {
      const char = text.charCodeAt(i);
      hash = ((hash << 5) - hash) + char;
      hash = hash & hash; // Convert to 32bit integer
    }
    return `post_${hash}_${Date.now()}`;
  }

  function updateStatus() {
    const status = {
      isRunning: isRunning,
      scrollCount: scrollCount,
      postsCount: collectedPostIds.size
    };

    // Update chrome.storage for popup
    chrome.storage.local.set({ crawlerStatus: status });

    // Send message to popup if open
    chrome.runtime.sendMessage({
      type: 'status_update',
      data: status
    });
  }

  // Initialize
  console.log('[FB Collector] Content script loaded');
})();

