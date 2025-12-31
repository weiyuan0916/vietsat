# ⚡ Quick Start Guide

## 🚀 Get Started in 3 Minutes

### ✅ Step 1: Install (30 seconds)
1. Open Chrome → Go to `chrome://extensions/`
2. Enable **"Developer mode"** (top-right toggle)
3. Click **"Load unpacked"**
4. Select this folder: `/Users/adward/Herd/vietsat/crawl-fb`
5. ✓ Extension appears with purple-blue icon

### ✅ Step 2: Navigate to Facebook (30 seconds)
1. Go to any Facebook Group you're a member of
2. Example: `https://www.facebook.com/groups/YOUR_GROUP_ID`
3. Make sure posts are visible on the page
4. Scroll down manually once to load initial posts

### ✅ Step 3: Start Collecting (2 minutes)
1. **Click extension icon** in toolbar
2. **Set parameters:**
   ```
   Min Delay: 2 seconds
   Max Delay: 5 seconds
   Max Scrolls: 10
   ```
3. **Click "▶ Start"** button
4. **Watch it work:**
   - Page scrolls automatically
   - Status shows "Running"
   - Scroll count increases
   - Posts collected increases

5. **Click "⬇ Stop & Download"**
6. **Save JSON file** when dialog appears

---

## 📋 First-Time Checklist

- [ ] Extension loaded without errors
- [ ] Icons visible in toolbar
- [ ] Logged into Facebook
- [ ] On a Facebook Group page
- [ ] Posts visible on page
- [ ] Extension popup opens
- [ ] Settings can be changed
- [ ] Start button works
- [ ] Page auto-scrolls
- [ ] Console shows posts found (F12)
- [ ] Stop & Download works
- [ ] JSON file downloads
- [ ] JSON file contains valid data

---

## 🎯 What Success Looks Like

### Console Output (Press F12):
```
[FB Collector] Content script loaded
[FB Collector] Starting with settings: {minDelay: 2, maxDelay: 5, maxScrolls: 10}
[FB Collector] Scroll #1
[FB Collector] Found 15 post elements
[FB Collector] Extracted 12 new posts
[FB Collector] Scroll #2
[FB Collector] Found 18 post elements
[FB Collector] Extracted 5 new posts
...
[FB Collector] Stopped. Total scrolls: 10
[Background] Saved 5 new posts. Total: 17
[Background] Download started: facebook_posts_2025-10-17T02-00-00.json
```

### Extension Popup:
```
Status: ● Running (green dot)
Scroll Count: 10
Posts Collected: 42 NEW
```

### Downloaded JSON:
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
      "id": "https://...",
      "postLink": "https://...",
      "authorName": "John Doe",
      "authorProfileLink": "https://...",
      "content": "Post text...",
      "date": "2h",
      "images": ["https://..."],
      "timestamp": "2025-10-17T02:00:00.000Z",
      "extractedAt": 1729134000000
    }
  ]
}
```

---

## 🐛 Common First-Time Issues

### Issue: "⚠️ Please navigate to a Facebook Group page first!"
**Fix:** You're not on a Facebook page. Go to `https://www.facebook.com/groups/...`

### Issue: "Found 0 post elements"
**Fix:** 
- Scroll down manually first to load posts
- Facebook might have changed their DOM (see SELECTOR_UPDATE_GUIDE.md)
- Refresh the page and try again

### Issue: Extension icon doesn't appear
**Fix:**
- Check `chrome://extensions/` for errors
- Make sure all files are present (manifest.json, background.js, etc.)
- Reload the extension

### Issue: Download doesn't work
**Fix:**
- Check browser's download settings
- Make sure extension has `downloads` permission
- Try clicking Stop & Download again

---

## 🎓 Next Steps

### For Testing:
1. ✅ Read [TESTING_GUIDE.md](TESTING_GUIDE.md)
2. ✅ Test with small Max Scrolls (5-10)
3. ✅ Verify JSON data quality

### For Production Use:
1. ✅ Use safe delays (3-10 seconds)
2. ✅ Don't run for hours continuously
3. ✅ Respect Facebook's Terms of Service

### When Facebook Changes:
1. ✅ Read [SELECTOR_UPDATE_GUIDE.md](SELECTOR_UPDATE_GUIDE.md)
2. ✅ Test new selectors in console
3. ✅ Update `content.js`

---

## 📊 Recommended Settings

### For Quick Testing (safe):
```
Min Delay: 2 seconds
Max Delay: 5 seconds
Max Scrolls: 10
⏱️ Duration: ~1 minute
📦 Expected: 20-50 posts
```

### For Normal Use (safer):
```
Min Delay: 3 seconds
Max Delay: 8 seconds
Max Scrolls: 30
⏱️ Duration: ~3-5 minutes
📦 Expected: 100-200 posts
```

### For Large Collection (safest):
```
Min Delay: 5 seconds
Max Delay: 10 seconds
Max Scrolls: 50
⏱️ Duration: ~6-10 minutes
📦 Expected: 200-400 posts
```

---

## ✨ Pro Tips

1. **Start small:** Test with 5-10 scrolls first
2. **Check quality:** Verify JSON data before large runs
3. **Monitor console:** Keep F12 open to see progress
4. **Use safe delays:** Longer is safer
5. **Take breaks:** Don't run continuously for hours

---

## 🎉 You're Ready!

If you can:
- ✅ Load the extension without errors
- ✅ See the popup with settings
- ✅ Click Start and see the page scroll
- ✅ See "Posts Collected" count increase
- ✅ Download JSON with valid data

**Congratulations! Your extension is working perfectly! 🚀**

---

## 📚 Full Documentation

- **README.md** - Complete overview and features
- **TESTING_GUIDE.md** - Detailed testing instructions
- **CHANGELOG.md** - Version history and updates
- **SELECTOR_UPDATE_GUIDE.md** - How to update when Facebook changes

---

**Need help?** Open an issue or check the troubleshooting sections in the guides above.

**Happy Collecting! 🎯**

