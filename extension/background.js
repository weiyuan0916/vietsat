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
        const onUpdatedCheck = (updatedTabId, changeInfo) => {
          if (updatedTabId === tabId && changeInfo && changeInfo.status === 'complete') {
            chrome.tabs.onUpdated.removeListener(onUpdatedCheck);
            // Ensure the tab still exists before executing script
            chrome.tabs.get(tabId, (tabInfo) => {
              if (chrome.runtime.lastError) {
                // Tab no longer exists
                console.warn('Tab missing when attempting to check page text:', chrome.runtime.lastError.message);
                return;
              }
              try {
                // Wait 7 seconds after opening the tab, then run a compact, promise-based sequence
                setTimeout(() => {
                  (async () => {
                    // Helper: execute a function in the page context and return its result (or null on failure)
                    function execScriptPromise(fn) {
                      return new Promise((resolve) => {
                        try {
                          // Ensure the tab still exists before injecting script to avoid "No tab with id" runtime errors
                          chrome.tabs.get(tabId, (tabInfo) => {
                            if (chrome.runtime.lastError || !tabInfo) {
                              return resolve(null);
                            }
                            try {
                              chrome.scripting.executeScript({ target: { tabId }, func: fn }, (res) => {
                                try {
                                  const r = res && res[0] && res[0].result;
                                  resolve(r === undefined ? null : r);
                                } catch (e) {
                                  resolve(null);
                                }
                              });
                            } catch (e) {
                              resolve(null);
                            }
                          });
                        } catch (e) {
                          resolve(null);
                        }
                      });
                    }

                    try {
                      // Attempt to click the profile settings menu (best-effort)
                      await execScriptPromise(() => {
                        const selector = '[aria-expanded=\"false\"][aria-haspopup=\"menu\"][aria-label=\"Xem thêm lựa chọn trong phần cài đặt trang cá nhân\"]';
                        let el = document.querySelector(selector) || document.querySelector('[aria-label=\"Xem thêm lựa chọn trong phần cài đặt trang cá nhân\"]');
                        if (el) {
                          try { el.click(); return { clicked: true }; } catch (e) { return { clicked: false, error: String(e) }; }
                        }
                        return { clicked: false };
                      });

                        // Guard tab exists before running any further checks to avoid races
                      chrome.tabs.get(tabId, async (tabCheck) => {
                          if (chrome.runtime.lastError) {
                            console.warn('Tab missing before post-click checks:', chrome.runtime.lastError.message);
                            return;
                          }

                        try {
                          // Check for the specific span text "Bỏ cấm thành viên"
                          const spanCheck = await execScriptPromise(() => {
                            try {
                                    const spans = Array.from(document.querySelectorAll('span'));
                                    const found = spans.find(s => s.innerText && s.innerText.trim() === 'Bỏ cấm thành viên');
                                    if (found) return { found: true, text: found.innerText.trim() };
                                    return { found: false };
                            } catch (e) { return { found: false, error: String(e) }; }
                          });
                          if (spanCheck && spanCheck.found) {
                            console.log('Found \"Bỏ cấm thành viên\":', spanCheck.text);
                          } else if (spanCheck && spanCheck.error) {
                            console.warn('Error checking for \"Bỏ cấm thành viên\":', spanCheck.error);
                                } else {
                            console.log('Did not find \"Bỏ cấm thành viên\"');
                          }

                          // Extract posts count like "202 bài viết"
                          const postCount = await execScriptPromise(() => {
                                  try {
                                    const spans = Array.from(document.querySelectorAll('span'));
                                    const regex = /(\\d+)\\s*bài\\s*viết/i;
                                    for (const s of spans) {
                                      const txt = s.innerText && s.innerText.trim();
                                      if (!txt) continue;
                                      const m = txt.match(regex);
                                      if (m) return { found: true, text: txt, count: Number(m[1]) };
                                    }
                                    const divs = Array.from(document.querySelectorAll('div'));
                                    for (const d of divs) {
                                      const txt = d.innerText && d.innerText.trim();
                                      if (!txt) continue;
                                      const m = txt.match(regex);
                                      if (m) return { found: true, text: txt, count: Number(m[1]) };
                                    }
                                    return { found: false, count: 0 };
                            } catch (e) { return { found: false, error: String(e), count: 0 }; }
                          });
                          if (postCount && postCount.found) {
                            console.log(`Found posts count: ${postCount.count} bài viết (raw: ${postCount.text})`);
                          } else if (postCount && postCount.error) {
                            console.warn('Error extracting posts count:', postCount.error);
                                } else {
                                  console.log('Found posts count: 0 bài viết');
                          }

                          // Wait 3 seconds after clicking (or attempting to click), then read page text
                          setTimeout(async () => {
                            try {
                              const pageText = (await execScriptPromise(() => (document.body ? document.body.innerText : ''))) || '';
                                  const needle = 'không phải là thành viên';
                              if (typeof pageText === 'string' && pageText.toLowerCase().includes(needle)) {
                                    const logEntry = { time: Date.now(), uid, groupUrl, reason: 'not_member' };
                                    chrome.storage.local.get({ fb_uid_opener_logs: [] }, (data) => {
                                      const logs = data.fb_uid_opener_logs || [];
                                      logs.push(logEntry);
                                      chrome.storage.local.set({ fb_uid_opener_logs: logs }, () => {
                                        if (chrome.runtime.lastError) {
                                          console.warn('storage.set error:', chrome.runtime.lastError.message);
                                        } else {
                                          console.log('fb_uid_opener_logs updated', logEntry);
                                        }
                                      });
                                    });
                                    try {
                                      safeNotify('FB UID Opener', `Detected \"không phải là thành viên\" for UID ${uid}`);
                                    } catch (e) {
                                      console.log('Notification skipped:', e && e.message ? e.message : e);
                                    }
                                  }

                                // Detect and log one of the three specific cases if present
                                try {
                                  const cases = [
                                    { key: 'no_restriction', text: 'Hiện không có hạn chế nào áp dụng với' },
                                    { key: 'approval_on', text: 'Đang bật phê duyệt bài viết' },
                                    { key: 'blocked', text: 'Đã chặn' }
                                  ];
                                  const matched = cases.filter(c => pageText.includes(c.text));
                                  if (matched.length) {
                                    matched.forEach(m => console.log(`Detected case: ${m.key} (${m.text}) for UID ${uid}`));
                                  } else {
                                    console.log('No special case matched (no_restriction/approval_on/blocked) for UID', uid);
                                  }
                                } catch (e) {
                                  console.warn('Error while detecting special cases:', e && e.message ? e.message : e);
                                }

                                // If approval_on detected, try to open the action menu and click "Tắt phê duyệt bài viết của ..."
                                try {
                                  if (pageText.includes('Đang bật phê duyệt bài viết')) {
                                  // set a small delay, click the action menu, then wait 3s before searching for the approval option
                                  setTimeout(() => {
                                    (async () => {
                                      try {
                                        await execScriptPromise(() => {
                                          const selector = 'div[aria-expanded=\"true\"][aria-haspopup=\"menu\"][aria-label=\"Hành động với bài viết này\"]';
                                          let el = document.querySelector(selector) || document.querySelector('div[aria-label=\"Hành động với bài viết này\"]');
                                          if (el) {
                                            try { el.click(); return { clickedMenu: true }; } catch (e) { return { clickedMenu: false, error: String(e) }; }
                                          }
                                          return { clickedMenu: false };
                                        });
                                      } catch (e) {
                                        // ignore click failures
                                      }
                                      // sleep 3s to allow menu to render
                                      await new Promise(res => setTimeout(res, 3000));
                                      // After pause, search for the disable-approval text and click it
                                      try {
                                        const resOption = await execScriptPromise(() => {
                                          const logs = [];
                                          try {
                                            // 1) Prefer scanning menu items to match both icon and the approval text together
                                            const menuItems = Array.from(document.querySelectorAll('div[role=\"menuitem\"], div.x1i10hfl'));
                                            logs.push(`menuItems_count: ${menuItems.length}`);
                                            let targetMenu = null;
                                            for (const mi of menuItems) {
                                              try {
                                                const icon = mi.querySelector('i[data-visualcompletion=\"css-img\"]');
                                                const iconCls = (icon && icon.className) || '';
                                                const iconStyle = (icon && icon.getAttribute('style')) || '';
                                                const span = mi.querySelector('span');
                                                const txt = (span && span.innerText && span.innerText.trim()) || '';
                                                logs.push(`menuItem txt="${txt.slice(0,60)}" iconCls="${iconCls}" iconStyle="${iconStyle.slice(0,80)}"`);
                                                // match when text contains approval phrase OR icon matches expected classes/style
                                                const textMatches = txt.indexOf('Tắt phê duyệt') !== -1 || txt.indexOf('Tắt phê duyệt bài viết') !== -1;
                                                const iconMatches = icon && iconCls.indexOf('x1b0d499') !== -1 && iconCls.indexOf('xep6ejk') !== -1 && iconStyle.indexOf('-424') !== -1;
                                                if (textMatches || iconMatches) {
                                                  logs.push(`menuItem matched by text:${textMatches} icon:${iconMatches}`);
                                                  targetMenu = mi;
                                                  break;
                                                }
                                              } catch (e) {
                                                // ignore single item errors
                                              }
                                            }
                                            logs.push(`targetMenu: ${targetMenu ? 'yes' : 'no'}`);
                                            if (targetMenu) {
                                              try {
                                                targetMenu.click();
                                                logs.push('clicked targetMenu');
                                                return { clickedOption: true, by: 'menuItem_match', outer: (targetMenu.outerHTML || '').slice(0,800), logs };
                                              } catch (e) {
                                                logs.push('targetMenu click error: ' + String(e));
                                              }
                                              // fallback: try nearest clickable ancestor of the matched icon/span
                                              const fallbackClickable = targetMenu && (targetMenu.closest && (targetMenu.closest('button, a, div[role=\"menuitem\"], div.xu06os2'))) || null;
                                              if (fallbackClickable) {
                                                try {
                                                  fallbackClickable.click();
                                                  logs.push('clicked fallbackClickable for targetMenu');
                                                  return { clickedOption: true, by: 'menuItem_fallback', outer: (fallbackClickable.outerHTML || '').slice(0,800), logs };
                                                } catch (e) {
                                                  logs.push('fallbackClickable click error: ' + String(e));
                                                }
                                              }
                                            }

                                            // 2) Fallback: text-based search (existing)
                                                    const needle = 'Tắt phê duyệt bài viết';
                                            logs.push(`needle: ${needle}`);
                                            const divSelector = 'div.x1i10hfl.xjbqb8w.x1ejq31n.x18oe1m7.x1sy0etr.xstzfhl.x972fbf.x10w94by.x1qhh985.x14e42zd.x3ct3a4.x1hl2dhg.xggy1nq.x1fmog5m.xu25z0z.x140muxe.xo1y3bh.x87ps6o.x1lku1pv.x1a2a7pz.xjyslct.x9f619.x1ypdohk.x78zum5.x1q0g3np.x2lah0s.x1i6fsjq.xfvfia3.x8e7100.x1a16bkn.x1n2onr6.x16tdsg8.x1ja2u2z.x6s0dn4.x1y1aw1k.xwib8y2.x1qpxxdj.xa6wxux[role=\"menuitem\"][tabindex=\"-1\"]';
                                            logs.push(`divSelector: ${divSelector}`);
                                            const preferredDiv = document.querySelector(divSelector);
                                            logs.push(`preferredDiv found: ${preferredDiv ? 'yes' : 'no'}`);
                                            const preferred = preferredDiv ? preferredDiv.querySelector('span.x193iq5w.xeuugli.x13faqbe.x1vvkbs.xlh3980.xvmahel.x1n0sxbx.x1lliihq.x1s928wv.xhkezso.x1gmr53x.x1cpjm7i.x1fgarty.x1943h6x.x4zkp8e.x3x7a5m.x6prxxf.xvq8zen.xk50ysn.xzsf02u.x1yc453h[dir=\"auto\"]') : null;
                                            logs.push(`preferred span found: ${preferred ? 'yes' : 'no'}`);
                                            const preferredText = preferred && preferred.innerText ? preferred.innerText.trim() : null;
                                            logs.push(`preferredText: ${preferredText || '<none>'}`);
                                            if (preferredText && preferredText.includes(needle)) {
                                              logs.push('preferredText includes needle');
                                                      function doClickElement(el) {
                                                        try {
                                                          el.click();
                                                  logs.push('clicked element');
                                                  return { clickedOption: true, text: (el.innerText && el.innerText.trim()), outer: (el.outerHTML || '').slice(0,800), logs };
                                                        } catch (e) {
                                                  logs.push('click error: ' + String(e));
                                                  return { clickedOption: false, error: String(e), text: (el.innerText && el.innerText.trim()), logs };
                                                        }
                                                      }
                                                      try {
                                                        const parentDiv = (preferred.closest && (preferred.closest('div.html-div') || preferred.closest('div.xu06os2') || preferred.closest('div.x78zum5'))) || preferred.parentElement;
                                                logs.push(`parentDiv found: ${parentDiv ? 'yes' : 'no'}`);
                                                        if (parentDiv) {
                                                          const res = doClickElement(parentDiv);
                                                          if (res && res.clickedOption) return Object.assign(res, { by: 'preferred_parent' });
                                                        }
                                                      } catch (e) {
                                                logs.push('parentDiv click exception: ' + String(e));
                                                      }
                                                      const resSpan = doClickElement(preferred);
                                                      if (resSpan && resSpan.clickedOption) return Object.assign(resSpan, { by: 'preferred' });
                                              return { clickedOption: false, error: 'click_failed_on_preferred', text: preferredText, logs };
                                                    }
                                            logs.push('preferredText does not include needle; searching candidates');
                                                    const candidates = Array.from(document.querySelectorAll('button,span,div,a'));
                                                    for (const c of candidates) {
                                                      const txt = c.innerText && c.innerText.trim();
                                                      if (!txt) continue;
                                                      if (txt.includes(needle)) {
                                                logs.push('candidate matched: ' + txt);
                                                        try {
                                                          c.click();
                                                  logs.push('clicked candidate');
                                                  return { clickedOption: true, text: txt, by: 'fallback', outer: (c.outerHTML || '').slice(0,800), logs };
                                                        } catch (e) {
                                                  logs.push('candidate click error: ' + String(e));
                                                  return { clickedOption: false, error: String(e), text: txt, by: 'fallback', logs };
                                                }
                                              }
                                            }
                                            logs.push('no candidates matched');
                                            return { clickedOption: false, logs };
                                          } catch (e) {
                                            return { clickedOption: false, error: String(e), logs: ['exception: ' + String(e)] };
                                          }
                                        });
                                        if (resOption && resOption.logs) {
                                          resOption.logs.forEach(l => console.log('approval-click-log:', l));
                                        }
                                        if (resOption && resOption.clickedOption) {
                                          console.log('Clicked \"Tắt phê duyệt bài viết của ...\" option:', resOption.text);
                                        } else if (resOption && resOption.error) {
                                          console.warn('Error clicking option:', resOption.error, resOption.text || '');
                                        } else {
                                          console.log('Did not find \"Tắt phê duyệt bài viết của ...\" option to click.');
                                        }
                                        // After selecting the option, wait 2s then try to click the confirmation "Xác nhận"
                                        setTimeout(async () => {
                                          try {
                                            const confirmRes = await execScriptPromise(() => {
                                              const logs = [];
                                              try {
                                                const needle = 'Xác nhận';
                                                logs.push(`confirm needle: ${needle}`);
                                                // search spans for exact trimmed text first
                                                const spans = Array.from(document.querySelectorAll('span'));
                                                for (const s of spans) {
                                                  const txt = s.innerText && s.innerText.trim();
                                                  if (!txt) continue;
                                                  if (txt === needle || txt.indexOf(needle) !== -1) {
                                                    logs.push(`found confirm span text="${txt.slice(0,80)}"`);
                                                    // try clicking nearest clickable ancestor first
                                                    const btn = s.closest('button, div[role=\"button\"], a') || s.parentElement;
                                                    if (btn) {
                                                      try {
                                                        btn.click();
                                                        logs.push('clicked confirm via ancestor');
                                                        return { clicked: true, text: txt, logs };
                                                      } catch (e) {
                                                        logs.push('ancestor click error: ' + String(e));
                                                      }
                                                    }
                                                    try {
                                                      s.click();
                                                      logs.push('clicked confirm span directly');
                                                      return { clicked: true, text: txt, logs };
                                                  } catch (e) {
                                                      logs.push('span click error: ' + String(e));
                                                      return { clicked: false, error: String(e), text: txt, logs };
                                                    }
                                                  }
                                                }
                                                logs.push('no confirm span matched');
                                                return { clicked: false, logs };
                                              } catch (e) {
                                                return { clicked: false, error: String(e), logs: ['exception:' + String(e)] };
                                              }
                                            });
                                            if (confirmRes && confirmRes.logs) {
                                              confirmRes.logs.forEach(l => console.log('approval-confirm-log:', l));
                                            }
                                            if (confirmRes && confirmRes.clicked) {
                                              console.log('Clicked confirmation (Xác nhận).');
                                            } else {
                                              console.log('Did not click confirmation (Xác nhận).');
                                            }
                                          } catch (e) {
                                            console.warn('Exception during confirm click:', e && e.message ? e.message : e);
                                          }
                                        }, 2000);
                                      } catch (e) {
                                        console.warn('Exception during searching/clicking option:', e && e.message ? e.message : e);
                                      }
                                    })();
                                  }, 0);
                                  }
                                } catch (e) {
                                  console.warn('Error handling approval_on case:', e && e.message ? e.message : e);
                              }
                          } catch (e) {
                            console.warn('executeScript exception during read:', e && e.message ? e.message : e);
                          }
                        }, 3000);
                        } catch (e) {
                          console.warn('post-click checks failed:', e && e.message ? e.message : e);
                      }
                      });
                  } catch (e) {
                      console.warn('executeScript exception during click sequence:', e && e.message ? e.message : e);
                  }
                  })();
                }, 7000);
              } catch (e) {
                console.warn('executeScript exception:', e && e.message ? e.message : e);
              }
            });
          }
        };
        chrome.tabs.onUpdated.addListener(onUpdatedCheck);

        try {
          safeNotify('FB UID Opener', `Opened group link for UID ${uid}. This tab will close automatically in 5 minutes.`);
        } catch (e) {
          console.warn('Notification skipped:', e && e.message ? e.message : e);
        }

        sendResponse({ success: true, uid, groupUrl });
      });
    } catch (err) {
      sendResponse({ success: false, error: String(err && err.message ? err.message : err) });
    }
  })();

  // Keep the message channel open for async sendResponse
  return true;
});

// Alarm handler to close tabs
chrome.alarms.onAlarm.addListener((alarm) => {
  if (!alarm || !alarm.name) return;
  if (!alarm.name.startsWith('close-')) return;
  const parts = alarm.name.split('-');
  const tabId = Number(parts[1]);
  if (isNaN(tabId)) return;
  chrome.tabs.remove(tabId, () => {
    // ignore errors (tab might already be closed)
  });
});

/**
 * ============================================
 * YIKI INTEGRATION - Receive UID from Yiki PWA
 * ============================================
 * Listen for messages from Yiki PWA and storage changes
 * When Yiki extracts UID, extension will auto-fill the profile URL input
 */

// Listen for direct messages from Yiki PWA
chrome.runtime.onMessageExternal.addListener((message, sender, sendResponse) => {
  console.log('[Yiki] Received external message:', message, 'from:', sender?.url);

  // Handle Yiki UID extraction result
  if (message.action === 'yiki_uid_extracted' && message.uid) {
    console.log('[Yiki] UID extracted:', message.uid, 'URL:', message.profileUrl);

    // Store the UID and profile URL for popup to access
    chrome.storage.local.set({
      yiki_uid: message.uid,
      yiki_profile_url: message.profileUrl || `https://www.facebook.com/${message.uid}`,
      yiki_timestamp: Date.now(),
      yiki_source: message.source || 'yiki_pwa'
    }, () => {
      console.log('[Yiki] UID stored in extension storage');

      // Send notification to user
      safeNotify('Yiki Integration', `Đã nhận UID: ${message.uid}`);

      sendResponse({ success: true, uid: message.uid, stored: true });
    });

    // Return true to indicate async response
    return true;
  }

  // Handle ping to check if extension is available
  if (message.action === 'yiki_ping') {
    sendResponse({
      success: true,
      extension: 'Facebook UID Opener',
      version: '1.0',
      ready: true
    });
    return false;
  }

  sendResponse({ success: false, error: 'Unknown message type' });
  return false;
});

// Listen for storage changes (alternative communication method)
chrome.storage.onChanged.addListener((changes, namespace) => {
  if (namespace === 'local') {
    // Check if Yiki wrote new data
    if (changes.yiki_uid || changes.yiki_profile_url) {
      const uid = changes.yiki_uid?.newValue;
      const profileUrl = changes.yiki_profile_url?.newValue;

      console.log('[Yiki Storage] Changed:', { uid, profileUrl });

      if (uid) {
        // Notify popup if it's open
        notifyPopupOfYikiData(uid, profileUrl);
      }
    }
  }
});

// Helper function to notify popup of new Yiki data
function notifyPopupOfYikiData(uid, profileUrl) {
  // Try to send message to popup
  try {
    chrome.runtime.sendMessage({
      action: 'yiki_uid_received',
      uid: uid,
      profileUrl: profileUrl || `https://www.facebook.com/${uid}`,
      timestamp: Date.now()
    }, (response) => {
      if (chrome.runtime.lastError) {
        console.log('[Yiki] Popup not open or error:', chrome.runtime.lastError.message);
      } else {
        console.log('[Yiki] Popup notified:', response);
      }
    });
  } catch (e) {
    console.log('[Yiki] Could not notify popup:', e.message);
  }
}

/**
 * Helper function for Yiki to call from their PWA
 * Call this from Yiki's JavaScript:
 *   chrome.runtime.sendMessage(EXTENSION_ID, {
 *     action: 'yiki_uid_extracted',
 *     uid: '100014343376569',
 *     profileUrl: 'https://www.facebook.com/100014343376569'
 *   });
 */
function yikiNotifyExtension(extensionId, uid, profileUrl) {
  if (typeof chrome !== 'undefined' && chrome.runtime && chrome.runtime.sendMessage) {
    chrome.runtime.sendMessage(extensionId, {
      action: 'yiki_uid_extracted',
      uid: uid,
      profileUrl: profileUrl,
      source: 'yiki_pwa'
    }, (response) => {
      if (chrome.runtime.lastError) {
        console.error('Extension message error:', chrome.runtime.lastError);
        return false;
      }
      return response?.success;
    });
  }
  return false;
}

// Expose helper for debugging
if (typeof window !== 'undefined') {
  window.yikiNotifyExtension = yikiNotifyExtension;
}


