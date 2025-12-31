# 🔧 Troubleshooting Guide

Quick solutions to common issues with FB Group Collector.

---

## 🚨 Common Errors

### 1. ❌ `URL.createObjectURL is not a function`

**Error Message:**
```
Error in event handler: TypeError: URL.createObjectURL is not a function
at downloadJSON (chrome-extension://xxx/background.js:125:19)
```

**Cause:**
- Blob URLs don't work in Manifest V3 service workers
- This was in version 1.0.0

**Solution:**
✅ **FIXED in version 1.0.1**

**Steps to Fix:**
1. Make sure you have version 1.0.1 or later
2. Check `manifest.json` → should say `"version": "1.0.1"`
3. If not, pull latest code
4. Reload extension: `chrome://extensions/` → Click reload (↻)

**Technical Details:**
```javascript
// OLD (broken in service workers):
const url = URL.createObjectURL(blob);

// NEW (works in MV3):
const dataUrl = 'data:application/json;charset=utf-8,' 
              + encodeURIComponent(jsonContent);
```

---

### 2. ❌ `Found 0 post elements`

**Console Message:**
```
[FB Collector] Found 0 post elements
[FB Collector] Extracted 0 new posts
```

**Cause:**
- Facebook changed their DOM structure
- CSS selectors no longer match
- You're on the wrong page (not a Group page)

**Solutions:**

#### A. Check You're on a Facebook Group
1. URL should contain: `facebook.com/groups/`
2. Navigate to: **About**, **Discussion**, or **Posts** tab
3. NOT on: Home feed, Profile, or other pages

#### B. Update Selectors (if Facebook changed)
See **SELECTOR_UPDATE_GUIDE.md** for detailed instructions.

**Quick Check:**
```javascript
// In browser console (F12) on Facebook Group page:
document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z').length
// Should return > 0
```

If returns 0, Facebook changed their classes. Update selectors.

---

### 3. ❌ Download Doesn't Trigger

**Symptoms:**
- Click "Stop & Download" button
- Nothing happens
- No file dialog appears

**Solutions:**

#### A. Check Permissions
1. Go to: `chrome://extensions/`
2. Find: "FB Group Collector - Safe Mode"
3. Click: "Details"
4. Verify permissions:
   - ✅ Read and change data on facebook.com
   - ✅ Manage your downloads

#### B. Check Console for Errors
1. Press F12 on Facebook page
2. Click "Console" tab
3. Click "Stop & Download"
4. Look for errors in red

#### C. Allow Popups/Downloads
1. Chrome may block automatic downloads
2. Check address bar for blocked popup icon
3. Click → Allow downloads from this site

---

### 4. ❌ Extension Won't Load

**Error Message:**
```
Failed to load extension
Could not load manifest
```

**Solutions:**

#### A. Check manifest.json Syntax
```bash
# In terminal, run:
cd /Users/adward/Herd/vietsat/crawl-fb
python3 -m json.tool manifest.json
```

If error → fix JSON syntax in manifest.json

#### B. Check File Permissions
```bash
# Make sure files are readable:
ls -la *.js *.json *.html
```

#### C. Verify All Files Present
Required files:
- ✅ manifest.json
- ✅ background.js
- ✅ content.js
- ✅ popup.html
- ✅ popup.js
- ✅ styles.css
- ✅ icons/icon16.png
- ✅ icons/icon48.png
- ✅ icons/icon128.png

---

### 5. ❌ No Real-time Progress Updates

**Symptoms:**
- Extension starts
- Page scrolls
- But popup shows: "Scrolls: 0, Posts: 0"

**Solutions:**

#### A. Keep Popup Open
- Progress updates only show when popup is open
- Don't close popup during collection
- Click extension icon to reopen if closed

#### B. Check Service Worker
1. Go to: `chrome://extensions/`
2. Find extension
3. Click: "Inspect views: service worker"
4. Check console for errors

---

### 6. ❌ Posts Have Missing Data

**Symptoms:**
- JSON downloads successfully
- But posts missing: author, link, or date

**Solutions:**

#### A. Check DOM Structure
Facebook may have changed. In console (F12):

```javascript
// Check post container:
document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z')[0]

// Check author:
document.querySelector('span.xdj266r.x14z9mp...')

// Check links:
document.querySelector("a[href*='/groups/'][href*='/posts/']")
```

#### B. Update Selectors
See **SELECTOR_UPDATE_GUIDE.md**

---

### 7. ❌ Browser Notifications Don't Show

**Symptoms:**
- Download works
- But no notification appears

**Solutions:**

#### A. Check Chrome Notification Settings
1. Go to: `chrome://settings/content/notifications`
2. Make sure notifications are allowed
3. Check extension isn't blocked

#### B. Check Permission in Manifest
```json
"permissions": [
  "notifications"  // ← Must be present
]
```

---

## 🔍 Debugging Tips

### Enable Detailed Logging

**Content Script (F12 on Facebook page):**
```javascript
// Watch scroll events:
console.log('[FB Collector] Scroll #' + scrollCount);

// Check post extraction:
console.log('[FB Collector] Found X post elements');
console.log('[FB Collector] Extracted X new posts');
```

**Service Worker (chrome://extensions/ → Inspect views):**
```javascript
// Watch data saving:
console.log('[Background] Saved X new posts. Total: Y');

// Watch download:
console.log('[Background] Download started: filename.json');
```

### Check Storage
```javascript
// In console (F12):
chrome.storage.local.get(null, (data) => console.log(data));
```

### Clear Storage
```javascript
// In console (F12):
chrome.storage.local.clear(() => console.log('Cleared'));
```

---

## 📊 Expected Behavior

### ✅ When Working Correctly:

**Console Output:**
```
[FB Collector] Content script loaded
[FB Collector] Starting with settings: {minDelay: 2, maxDelay: 5, maxScrolls: 10}
[FB Collector] Scroll #1
[FB Collector] Found 15 post elements  ← > 0
[FB Collector] Extracted 12 new posts  ← > 0
[Background] Saved 12 new posts. Total: 12
[FB Collector] Scroll #2
[FB Collector] Found 18 post elements
[FB Collector] Extracted 5 new posts (7 duplicates)
[Background] Saved 5 new posts. Total: 17
...
[FB Collector] Completed after 10 scrolls
[Background] Download started: facebook_posts_2025-10-17T02-00-00.json
```

**Popup Display:**
```
Status: Running / Stopped
Scrolls: 10 / 10
Posts Collected: 42
```

**Downloaded JSON:**
```json
{
  "metadata": {
    "totalPosts": 42,
    "exportedAt": "2025-10-17T02:00:00.000Z",
    "version": "1.0.1"
  },
  "posts": [
    {
      "id": "https://www.facebook.com/groups/.../posts/123",
      "postLink": "https://...",
      "authorName": "John Doe",
      "authorProfileLink": "https://...",
      "content": "Post text...",
      "date": "2h",
      "images": ["https://..."]
    }
  ]
}
```

---

## 🆘 Still Having Issues?

### Diagnostic Checklist:

1. **Version Check:**
   - [ ] Using version 1.0.1 or later?
   - [ ] Check `manifest.json` → `"version": "1.0.1"`

2. **Location Check:**
   - [ ] On a Facebook Group page?
   - [ ] URL contains: `facebook.com/groups/`?

3. **Permission Check:**
   - [ ] All permissions granted in `chrome://extensions/`?
   - [ ] Downloads permission enabled?

4. **Console Check:**
   - [ ] Any red errors in console (F12)?
   - [ ] "Found X post elements" > 0?

5. **File Check:**
   - [ ] All 9 required files present?
   - [ ] No syntax errors in manifest.json?

### Complete Reset:

If nothing works, try a clean reinstall:

```bash
1. Remove extension from Chrome
2. Close Chrome completely
3. Reload extension:
   - chrome://extensions/
   - Developer mode ON
   - Load unpacked
   - Select folder
4. Test on Facebook Group
```

---

## 📞 Quick Reference

**Key Files:**
- `content.js` → DOM extraction logic
- `background.js` → Download handler
- `manifest.json` → Permissions & config

**Key Selectors (in content.js):**
- Post container: `div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z`
- Author name: `span.xdj266r.x14z9mp...`
- Post link: `a[href*='/groups/'][href*='/posts/']`

**Testing Commands:**
```javascript
// In console (F12) on Facebook Group:
document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z').length
```

---

## 📚 Related Guides

- **SELECTOR_UPDATE_GUIDE.md** → Fix when Facebook changes DOM
- **TESTING_GUIDE.md** → Complete testing procedures
- **QUICKSTART.md** → Basic setup instructions
- **CHANGELOG.md** → Version history and fixes

---

**Last Updated:** Version 1.0.1 - 2025-10-17

