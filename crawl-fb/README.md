# 🔍 FB Group Collector - Safe Mode

A Chrome Extension (Manifest V3) that safely collects Facebook Group posts using auto-scroll with random delays to avoid detection.

---

## ✨ Features

- ✅ **Auto-scroll** with configurable random delays
- ✅ **Safe mode** to avoid Facebook blocking
- ✅ **Real-time progress** tracking (scroll count, posts collected)
- ✅ **Duplicate prevention** (same post won't be collected twice)
- ✅ **JSON export** with structured metadata
- ✅ **Browser notifications** on successful download
- ✅ **Persistent storage** (data survives browser restart)
- ✅ **No image downloads** (only URLs collected)

---

## 📦 What Gets Collected

Each post includes:
```json
{
  "id": "unique_post_id",
  "postLink": "https://www.facebook.com/groups/.../posts/...",
  "authorName": "John Doe",
  "authorProfileLink": "https://www.facebook.com/groups/.../user/...",
  "content": "Post text content (no HTML)",
  "date": "2h ago",
  "images": ["https://...", "https://..."],
  "timestamp": "2025-10-17T02:00:00.000Z",
  "extractedAt": 1729134000000
}
```

---

## 🚀 Installation

### Step 1: Download or Clone
```bash
git clone <your-repo-url>
cd crawl-fb
```

### Step 2: Load in Chrome
1. Open Chrome and navigate to: `chrome://extensions/`
2. Enable **"Developer mode"** (toggle in top-right corner)
3. Click **"Load unpacked"**
4. Select the `crawl-fb` folder
5. Extension should appear with purple-blue icon

### Step 3: Verify
- ✅ Extension appears in toolbar
- ✅ No error messages
- ✅ Icons display correctly

---

## 🎯 How to Use

### 1. Navigate to Facebook Group
```
https://www.facebook.com/groups/YOUR_GROUP_NAME
```
Make sure you're logged in and can see posts.

### 2. Open Extension Popup
Click the extension icon in your Chrome toolbar.

### 3. Configure Settings
- **Min Delay**: 2-5 seconds (minimum wait between scrolls)
- **Max Delay**: 5-10 seconds (maximum wait between scrolls)
- **Max Scrolls**: 10-100 (stop after X scrolls, 0 = unlimited)

**Recommended for safety:**
- Min Delay: `3-5` seconds
- Max Delay: `6-10` seconds
- Max Scrolls: `20-50` for testing

### 4. Start Collecting
1. Click **"▶ Start"** button
2. Watch the page auto-scroll
3. Monitor progress in popup:
   - Status: Running ✓
   - Scroll Count: increasing
   - Posts Collected: increasing

### 5. Stop & Download
1. Click **"⬇ Stop & Download"** button
2. Browser notification appears
3. Save dialog opens
4. File saved as: `facebook_posts_2025-10-17T02-00-00.json`

---

## 📁 File Structure

```
crawl-fb/
├── manifest.json          # Extension configuration
├── background.js          # Service worker (data storage, download)
├── content.js             # Content script (DOM extraction, scrolling)
├── popup.html             # Extension popup UI
├── popup.js               # Popup logic
├── styles.css             # Popup styling
├── icons/
│   ├── icon16.png        # 16x16 toolbar icon
│   ├── icon48.png        # 48x48 management icon
│   └── icon128.png       # 128x128 store icon
├── README.md              # This file
├── CHANGELOG.md           # Version history and updates
├── TESTING_GUIDE.md       # Detailed testing instructions
└── SELECTOR_UPDATE_GUIDE.md  # How to update when Facebook changes
```

---

## 🔧 Technical Details

### Architecture
- **Manifest V3** (latest Chrome extension standard)
- **Service Worker** (background.js) for data management
- **Content Script** (content.js) injected into Facebook pages
- **Popup UI** (popup.html/js) for user interaction
- **Message Passing** between components

### Permissions
```json
{
  "permissions": [
    "activeTab",       // Access current tab
    "scripting",       // Inject content script
    "downloads",       // Download JSON files
    "storage",         // Save data persistently
    "notifications"    // Show download notifications
  ],
  "host_permissions": [
    "https://www.facebook.com/*",
    "https://mbasic.facebook.com/*"
  ]
}
```

### DOM Selectors (Based on Real Facebook HTML)
```javascript
// Post container
'div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z'

// Author name
'span.xdj266r.x14z9mp.xat24cr.x1lziwak.xexx8yu.xyri2b...'

// Profile link
"a[href*='/groups/'][href*='/user/']"

// Post link
"a[href*='/groups/'][href*='/posts/']"

// Date/time
'div.x6s0dn4.x17zd0t2.x78zum5.x1q0g3np.x1a02dak'
```

---

## 🧪 Testing

See [TESTING_GUIDE.md](TESTING_GUIDE.md) for detailed testing instructions.

**Quick Test:**
1. Load extension
2. Go to any Facebook Group
3. Set Max Scrolls to `5`
4. Click Start
5. Wait for 5 scrolls
6. Click Stop & Download
7. Check JSON file

**Expected Console Output:**
```
[FB Collector] Content script loaded
[FB Collector] Starting with settings: {minDelay: 2, maxDelay: 5, maxScrolls: 5}
[FB Collector] Scroll #1
[FB Collector] Found 15 post elements
[FB Collector] Extracted 12 new posts
[Background] Saved 12 new posts. Total: 12
...
[Background] Download started: facebook_posts_2025-10-17T02-00-00.json
```

---

## 🐛 Troubleshooting

### Problem: No posts found
**Symptoms:** Console shows "Found 0 post elements"

**Solutions:**
1. Facebook changed their DOM structure
2. Check [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md)
3. Inspect post elements and update selectors in `content.js`
4. Scroll manually first to load posts

### Problem: Download doesn't work
**Symptoms:** No file downloads when clicking "Stop & Download"

**Solutions:**
1. Check browser's download settings
2. Ensure `downloads` permission is granted
3. Check service worker console: `chrome://extensions/` → "Inspect views: Service Worker"
4. Look for error messages in console

### Problem: Extension won't start
**Symptoms:** Clicking "Start" does nothing

**Solutions:**
1. Make sure you're on a Facebook Group page
2. Refresh the Facebook page
3. Reload the extension (`chrome://extensions/` → Reload)
4. Check console for errors (`F12`)

### Problem: Collected 0 posts after many scrolls
**Symptoms:** Scrolling works but no posts collected

**Solutions:**
1. Selectors are outdated (Facebook changed DOM)
2. Open console and run:
   ```javascript
   document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z').length
   ```
3. If returns `0`, update selectors (see [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md))

---

## ⚠️ Important Notes

### Facebook Terms of Service
- This extension is for **personal use** and **research purposes**
- Respect Facebook's Terms of Service
- Don't use for spam, harassment, or commercial purposes
- Don't abuse the auto-scroll feature

### Rate Limiting
- Facebook may rate-limit or block if scrolling too fast
- Use **longer delays** for safer operation (5-10 seconds)
- Don't run continuously for hours
- Take breaks between collection sessions

### Data Privacy
- All data is stored **locally** in your browser
- No data is sent to external servers
- You control what you collect and when to delete it
- JSON files are saved to your Downloads folder

### Limitations
- Only works on **desktop Facebook** (not mobile site)
- Only collects **visible post data** (no comments, reactions)
- **Images are URLs only** (not downloaded)
- Facebook's DOM changes frequently (selectors need updates)

---

## 🔄 Updating Selectors

Facebook frequently changes their DOM structure. When the extension stops working:

1. **Read:** [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md)
2. **Inspect:** Right-click on a post → "Inspect"
3. **Find:** New CSS selectors for post elements
4. **Test:** In browser console first
5. **Update:** `content.js` with new selectors
6. **Reload:** Extension and test again

---

## 📊 JSON Export Format

```json
{
  "metadata": {
    "totalPosts": 42,
    "exportedAt": "2025-10-17T02:00:00.000Z",
    "version": "1.0.0",
    "source": "FB Group Collector - Safe Mode"
  },
  "posts": [
    {
      "id": "https://www.facebook.com/groups/123/posts/456",
      "postLink": "https://www.facebook.com/groups/123/posts/456",
      "authorName": "John Doe",
      "authorProfileLink": "https://www.facebook.com/groups/123/user/789",
      "content": "This is the post text content...",
      "date": "2h",
      "images": [
        "https://scontent.xx.fbcdn.net/v/t1.0-9/..."
      ],
      "timestamp": "2025-10-17T02:00:00.000Z",
      "extractedAt": 1729134000000
    }
  ]
}
```

---

## 💡 Tips for Best Results

### 1. Start Small
- Test with **5-10 scrolls** first
- Verify data quality before large collection
- Check JSON file format

### 2. Use Safe Delays
- **Minimum 3-5 seconds** between scrolls
- **Maximum 8-12 seconds** for very safe operation
- Randomness helps avoid detection

### 3. Monitor Progress
- Watch the console (`F12`) for errors
- Check "Posts Collected" count increases
- Verify posts in downloaded JSON

### 4. Handle Facebook Updates
- Keep [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md) handy
- Test selectors in console before updating code
- Check multiple posts to ensure universal selectors

---

## 🚧 Future Enhancements

Potential features to add:
- [ ] Comments collection
- [ ] Reactions count
- [ ] Filter by date range
- [ ] Search within collected posts
- [ ] Export to CSV format
- [ ] Scheduled collection
- [ ] Multiple group support
- [ ] Image download option

---

## 📝 Version History

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

**Current Version: 1.0.0**
- Initial release
- Auto-scroll with random delays
- Post extraction with exact selectors
- JSON export with metadata
- Browser notifications

---

## 🤝 Contributing

Contributions welcome! Areas to improve:
- Better selector resilience
- Additional data extraction
- UI/UX enhancements
- Performance optimizations
- Documentation

---

## 📄 License

This extension is provided as-is for educational and personal use.

---

## 🙏 Acknowledgments

- Built with Chrome Extension Manifest V3
- Inspired by Beautiful Soup web scraping
- Thanks to the open-source community

---

## 📞 Support

If you encounter issues:
1. Check [TESTING_GUIDE.md](TESTING_GUIDE.md)
2. Check [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md)
3. Open browser console and check for errors
4. Verify Facebook hasn't changed DOM structure

---

**Happy Collecting! 🚀**

*Remember: Use responsibly and respect Facebook's Terms of Service.*
