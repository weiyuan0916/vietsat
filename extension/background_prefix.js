// background service worker for the extension
'use strict';

// Utility: extract UID from Facebook profile HTML using several common patterns.
function extractFbUidFromHtml(html) {
  if (!html) return null;
  // 1) meta tag for Android deep link: <meta property="al:android:url" content="fb://profile/100014343376569" />
  let m = html.match(/<meta[^>]*property=["']al:android:url["'][^>]*content=["']fb:\/\/profile\/(\d+)["'][^>]*>/i);
  if (m && m[1]) return m[1];

  // 2) any occurrence of fb://profile/<digits>
  m = html.match(/fb:\/\/profile\/(\d+)/i);
  if (m && m[1]) return m[1];

  // 3) JSON snippets sometimes include "profile_id":"<digits>"
  m = html.match(/["']profile_id["']\s*[:]\s*["']?(\d+)["']?/i);
  if (m && m[1]) return m[1];

  // 4) other common keys
  m = html.match(/["']entity_id["']\s*[:]\s*["']?(\d+)["']?/i);
  if (m && m[1]) return m[1];

  return null;
}

// Safe notification helper: logs to console and attempts to create a notification,
// but never throws — falls back to console on error.
function safeNotify(title, message) {
  console.log('Notify:', title, message);
  try {
    chrome.notifications.create('', {
      type: 'basic',
      title: title,
      message: message
    }, (nid) => {
      if (chrome.runtime.lastError) {
        console.warn('Notification error:', chrome.runtime.lastError.message);
      }
    });
  } catch (e) {
    console.warn('Notification create threw:', e && e.message ? e.message : e);
  }
}

// Handle messages from popup
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (!message || message.action !== 'fetchUid' || !message.url) {
    return;
  }

  (async () => {
    try {
      // Try unauthenticated fetch first (public view) which may include meta tag
      async function tryPublicFetch(url) {
        try {
          const resp = await fetch(url, { credentials: 'omit' });
          const html = await resp.text();
          const uid = extractFbUidFromHtml(html);
          return uid ? { uid, html } : null;
        } catch (e) {
          return null;
        }
      }

      // Execute script in a tab to get page HTML
      function getHtmlFromTab(tabId) {
        return new Promise((resolve, reject) => {
          try {
            chrome.scripting.executeScript(
              { target: { tabId }, func: () => document.documentElement.outerHTML },
              (results) => {
                if (chrome.runtime.lastError) return reject(chrome.runtime.lastError);
                if (!results || !results[0] || typeof results[0].result !== 'string') return resolve('');
                resolve(results[0].result);
              }
            );
          } catch (err) {
            reject(err);
          }
        });
      }

      // Wait for tab to reach 'complete' status (or timeout)
      function waitForTabComplete(tabId, timeoutMs = 15000) {
        return new Promise((resolve) => {
          let finished = false;
          const onUpdated = (updatedTabId, changeInfo) => {
            if (updatedTabId === tabId && changeInfo && changeInfo.status === 'complete') {
              finished = true;
              chrome.tabs.onUpdated.removeListener(onUpdated);
              resolve(true);
            }
          };
          chrome.tabs.onUpdated.addListener(onUpdated);
          setTimeout(() => {
            if (!finished) {
              chrome.tabs.onUpdated.removeListener(onUpdated);
              resolve(false);
            }
          }, timeoutMs);
        });
      }

      // Try public fetch
      let result = await tryPublicFetch(message.url);

      // If public fetch didn't find UID, try opening an incognito window and read its HTML (requires extension allowed in incognito)
      let usedIncognitoWindowId = null;
      if (!result) {
        try {
          const win = await new Promise((resolve, reject) => {
            chrome.windows.create({ url: message.url, incognito: true, focused: false }, (w) => {
              if (chrome.runtime.lastError) return reject(chrome.runtime.lastError);
              resolve(w);
            });
          });
          usedIncognitoWindowId = win && win.id;
          const tab = win && win.tabs && win.tabs[0];
          const tabId = tab && tab.id;
          if (tabId != null) {
            await waitForTabComplete(tabId, 15000);
            try {
              const html = await getHtmlFromTab(tabId);
              const uid = extractFbUidFromHtml(html);
              if (uid) {
                result = { uid, html };
              }
            } catch (e) {
              // scripting may fail in incognito if extension not allowed — ignore and fallback
              console.warn('scripting.executeScript error in incognito:', e && e.message ? e.message : e);
            }
          }
        } catch (e) {
          // Could not open incognito window (user may not have enabled extension in incognito) — ignore
          console.warn('incognito window error:', e && e.message ? e.message : e);
        } finally {
          // close incognito window if we opened it
          if (usedIncognitoWindowId != null) {
            chrome.windows.remove(usedIncognitoWindowId, () => {});
          }
        }
      }

      // If still not found, try authenticated fetch (include cookies) and look for userID/profile_id/entity_id
      if (!result) {
        try {
          const resp = await fetch(message.url, { credentials: 'include' });
          const html = await resp.text();
          const uid = extractFbUidFromHtml(html);
          if (uid) result = { uid, html };
        } catch (e) {
          // ignore
        }
      }

      if (!result || !result.uid) {
        sendResponse({ success: false, error: 'UID not found. Profile may be private or blocked.' });
        return;
      }

      const uid = result.uid;
      const groupUrl = `https://www.facebook.com/groups/782860725537921/user/${uid}/`;

      // Open the constructed group-user URL in a new tab (regular)
      chrome.tabs.create({ url: groupUrl }, (tab) => {
        if (chrome.runtime.lastError || !tab) {
          const errMsg = (chrome.runtime.lastError && chrome.runtime.lastError.message) || 'Failed to create tab';
          safeNotify('FB UID Opener', 'Error opening tab: ' + errMsg);
          sendResponse({ success: false, error: errMsg });
          return;
        }

        const tabId = tab.id;
        const alarmName = 'close-' + tabId;
        // schedule an alarm to close the tab after 5 minutes
        chrome.alarms.create(alarmName, { when: Date.now() + 5 * 60 * 1000 });

        // After opening the tab, check the page content for the Vietnamese phrase
        // "không phải là thành viên" (not a member). If found, persist a log entry.
        // Handler omitted in this prefix-only copy.

        sendResponse({ success: true, uid, groupUrl });
      });
    } catch (err) {
      sendResponse({ success: false, error: String(err && err.message ? err.message : err) });
    }
  })();

  // Keep the message channel open for async sendResponse
  return true;
});


