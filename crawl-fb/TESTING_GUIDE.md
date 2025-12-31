# 🧪 Testing Guide for FB Group Collector Extension

## ✅ What Was Updated

Based on your Beautiful Soup code, I've updated the extension to use **exact DOM selectors** that match Facebook Group posts:

### Updated Selectors in `content.js`:

1. **Post Container**: `div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z`
2. **Author Name**: `span.xdj266r.x14z9mp.xat24cr.x1lziwak.xexx8yu.xyri2b.x18d9i69.x1c1uobl.x1hl2dhg.x16tdsg8.x1vvkbs`
3. **Profile Link**: `a[href*='/groups/'][href*='/user/']`
4. **Post Link**: `a[href*='/groups/'][href*='/posts/']`
5. **Date**: `div.x6s0dn4.x17zd0t2.x78zum5.x1q0g3np.x1a02dak`

### New Data Structure:
```json
{
  "id": "post_link_or_hash",
  "postLink": "https://www.facebook.com/groups/.../posts/...",
  "authorName": "John Doe",
  "authorProfileLink": "https://www.facebook.com/groups/.../user/...",
  "content": "Post text content...",
  "date": "2h ago",
  "images": ["url1", "url2"],
  "timestamp": "2025-10-17T02:00:00.000Z",
  "extractedAt": 1729134000000
}
```

---

## 🚀 Step-by-Step Testing Instructions

### Step 1: Reload the Extension
1. Open Chrome → `chrome://extensions/`
2. Find **"FB Group Collector - Safe Mode"**
3. Click the **🔄 Reload** button
4. Verify no errors appear

### Step 2: Navigate to a Facebook Group
1. Go to any **Facebook Group** you're a member of
2. Example: `https://www.facebook.com/groups/YOUR_GROUP_NAME`
3. Make sure you're logged in to Facebook
4. Scroll down a bit to ensure posts are visible

### Step 3: Open the Extension Popup
1. Click the **extension icon** in your Chrome toolbar
2. You should see:
   - ✅ Status: **Stopped**
   - ✅ Scroll Count: **0**
   - ✅ Posts Collected: **0**

### Step 4: Configure Settings
Set your preferences:
- **Min Delay**: `2` seconds (safer = 3-5 seconds)
- **Max Delay**: `5` seconds (safer = 6-10 seconds)
- **Max Scrolls**: `10` (for testing, use a small number)

### Step 5: Start Collecting
1. Click **"▶ Start"** button
2. Watch the Facebook page:
   - ✅ Page should auto-scroll smoothly
   - ✅ Status should change to **"Running"**
   - ✅ Scroll count should increase
   - ✅ Posts count should increase

3. Check the browser console (`F12` → Console tab):
   ```
   [FB Collector] Starting with settings: {minDelay: 2, maxDelay: 5, maxScrolls: 10}
   [FB Collector] Scroll #1
   [FB Collector] Found 15 post elements
   [FB Collector] Extracted 12 new posts
   ```

### Step 6: Stop & Download
1. Click **"⬇ Stop & Download"** button
2. You should see:
   - ✅ A browser notification: "Downloaded X posts successfully! 🎉"
   - ✅ A save dialog appears
   - ✅ Default filename: `facebook_posts_2025-10-17T02-00-00.json`

### Step 7: Verify JSON File
Open the downloaded JSON file:

```json
{
  "metadata": {
    "totalPosts": 12,
    "exportedAt": "2025-10-17T02:00:00.000Z",
    "version": "1.0.0",
    "source": "FB Group Collector - Safe Mode"
  },
  "posts": [
    {
      "id": "https://www.facebook.com/groups/.../posts/123...",
      "postLink": "https://www.facebook.com/groups/.../posts/123...",
      "authorName": "John Doe",
      "authorProfileLink": "https://www.facebook.com/groups/.../user/456...",
      "content": "This is a sample post content...",
      "date": "2h",
      "images": [
        "https://scontent.xx.fbcdn.net/v/..."
      ],
      "timestamp": "2025-10-17T02:00:00.000Z",
      "extractedAt": 1729134000000
    }
  ]
}
```

---

## 🔍 Troubleshooting

### Problem: No posts found
**Solution**:
1. Open browser console (`F12`)
2. Check for error messages
3. Facebook may have changed their DOM structure
4. Try scrolling manually first to load posts
5. Look at the DOM using "Inspect Element" on a post
6. Update selectors in `content.js` if needed

### Problem: Download doesn't work
**Solution**:
1. Check background service worker console:
   - Go to `chrome://extensions/`
   - Click "Inspect views: Service Worker"
2. Look for error messages
3. Verify `downloads` permission is granted

### Problem: Extension shows error on load
**Solution**:
1. Clear extension storage:
   ```javascript
   chrome.storage.local.clear()
   ```
2. Reload extension
3. Check manifest.json for syntax errors

### Problem: Selectors don't match posts
**Solution**:
1. Facebook's DOM changes frequently
2. Use your browser's "Inspect Element" tool
3. Find the new class names for:
   - Post container
   - Author name
   - Links
4. Update selectors in `content.js`

---

## 🎯 Expected Results

### ✅ What Should Work:
- Auto-scrolling with random delays
- Real-time status updates in popup
- Post extraction with correct data
- Duplicate prevention (same post won't be collected twice)
- JSON download with structured data
- Browser notification on download

### ⚠️ Known Limitations:
- Facebook's DOM structure changes frequently (selectors may need updates)
- Some posts may not be extracted if they have a different structure
- Images are links only (not downloaded)
- Only works on desktop Facebook (not mobile site)

---

## 🐛 Debugging Tips

### Check Content Script Console:
```javascript
// In Facebook page console
[FB Collector] Starting with settings: {...}
[FB Collector] Scroll #1
[FB Collector] Found 15 post elements
[FB Collector] Extracted 12 new posts
```

### Check Background Service Worker:
```javascript
// In extension service worker console
[Background] Saved 12 new posts. Total: 12
[Background] Download started: facebook_posts_2025-10-17T02-00-00.json
```

### Manual DOM Inspection:
```javascript
// Test selectors in Facebook console
document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z').length
// Should return number of posts visible on page
```

---

## 📊 Performance Tips

### For Large Groups:
- Use **longer delays** (5-10 seconds) to avoid detection
- Start with **small max scrolls** (10-20) to test
- Facebook may rate-limit or block if scrolling too fast

### For Best Results:
- Scroll manually first to load initial posts
- Use the extension during off-peak hours
- Don't run continuously for hours
- Take breaks between collection sessions

---

## 🎉 Success Criteria

Your extension is working correctly if:
- ✅ Auto-scroll works smoothly
- ✅ Console shows "Found X post elements" > 0
- ✅ Console shows "Extracted X new posts" > 0
- ✅ Status updates in real-time
- ✅ JSON file downloads successfully
- ✅ JSON file contains valid post data with all fields

---

## 📝 Next Steps

If everything works:
1. 🎉 Congratulations! Your extension is ready!
2. Use it responsibly and respect Facebook's Terms of Service
3. Don't abuse the auto-scroll feature
4. Consider adding more features (filters, search, etc.)

If something doesn't work:
1. Check the troubleshooting section above
2. Inspect the DOM to verify selectors
3. Check browser console for errors
4. Update selectors if Facebook changed their structure

---

**Happy Collecting! 🚀**

