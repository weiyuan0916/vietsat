# 🔧 Selector Update Guide

## When Facebook Changes Their DOM

Facebook frequently updates their DOM structure and CSS class names. When the extension stops finding posts, follow this guide to update the selectors.

---

## 🔍 Step 1: Inspect a Facebook Post

1. **Open a Facebook Group** in Chrome
2. **Right-click on a post** → "Inspect" (or press `F12`)
3. **Find the post container** in the Elements tab
4. **Look for the outermost div** that contains the entire post

### Example:
```html
<div class="x1yztbdb x1n2onr6 xh8yej3 x1ja2u2z">
  <!-- This is the post container -->
  <div class="...">
    <!-- Author info -->
    <span class="xdj266r x14z9mp xat24cr...">John Doe</span>
  </div>
  <div class="...">
    <!-- Post content -->
  </div>
</div>
```

---

## 📝 Step 2: Copy New Selectors

### 1. Post Container
**Location:** The outermost div of each post

**How to find:**
- Right-click post → Inspect
- Look for the main container div
- It usually has many class names

**Current selector:**
```javascript
'div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z'
```

**Test in console:**
```javascript
document.querySelectorAll('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z').length
// Should return number of visible posts (e.g., 10-20)
```

---

### 2. Author Name
**Location:** A `<span>` tag inside the post header

**How to find:**
- Hover over the author name in the post
- Right-click → Inspect
- Look for the `<span>` element

**Current selector:**
```javascript
'span.xdj266r.x14z9mp.xat24cr.x1lziwak.xexx8yu.xyri2b.x18d9i69.x1c1uobl.x1hl2dhg.x16tdsg8.x1vvkbs'
```

**Test in console:**
```javascript
const post = document.querySelector('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z');
const author = post.querySelector('span.xdj266r.x14z9mp.xat24cr.x1lziwak.xexx8yu.xyri2b.x18d9i69.x1c1uobl.x1hl2dhg.x16tdsg8.x1vvkbs');
console.log(author.textContent); // Should show author name
```

---

### 3. Profile Link
**Location:** An `<a>` tag that links to the user's profile

**How to find:**
- Right-click on author name → Inspect
- Look for `<a href="...">` tag
- URL should contain `/groups/` and `/user/`

**Current selector:**
```javascript
"a[href*='/groups/'][href*='/user/']"
```

**Test in console:**
```javascript
const post = document.querySelector('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z');
const profileLink = post.querySelector("a[href*='/groups/'][href*='/user/']");
console.log(profileLink.href); // Should show profile URL
```

---

### 4. Post Link
**Location:** An `<a>` tag that links to the specific post

**How to find:**
- Right-click on post timestamp → Inspect
- Look for `<a href="...">` tag
- URL should contain `/groups/` and `/posts/`

**Current selector:**
```javascript
"a[href*='/groups/'][href*='/posts/']"
```

**Test in console:**
```javascript
const post = document.querySelector('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z');
const postLink = post.querySelector("a[href*='/groups/'][href*='/posts/']");
console.log(postLink.href); // Should show post URL
```

---

### 5. Date/Time
**Location:** A `<div>` near the author name showing when post was created

**How to find:**
- Right-click on "2h ago" or timestamp → Inspect
- Look for the parent `<div>` container
- It usually contains the timestamp text

**Current selector:**
```javascript
'div.x6s0dn4.x17zd0t2.x78zum5.x1q0g3np.x1a02dak'
```

**Test in console:**
```javascript
const post = document.querySelector('div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z');
const date = post.querySelector('div.x6s0dn4.x17zd0t2.x78zum5.x1q0g3np.x1a02dak');
console.log(date.textContent); // Should show "2h", "Yesterday", etc.
```

---

## ✏️ Step 3: Update content.js

Open `content.js` and update the selectors:

### Location 1: Line 111 - Post Container
```javascript
// Find actual Facebook Group posts
const postElements = document.querySelectorAll(
  'div.YOUR_NEW_POST_CONTAINER_CLASSES_HERE'
);
```

### Location 2: Line 151-157 - Author Name
```javascript
// --- Extract Author Name ---
const authorTag = postElement.querySelector(
  'span.YOUR_NEW_AUTHOR_SPAN_CLASSES_HERE'
);
if (authorTag) {
  postData.authorName = authorTag.textContent.trim();
}
```

### Location 3: Line 159-163 - Profile Link
```javascript
// --- Extract Profile Link ---
const profileTag = postElement.querySelector(
  "a[href*='/groups/'][href*='/user/']" // Usually stays the same
);
if (profileTag) {
  postData.authorProfileLink = profileTag.href;
}
```

### Location 4: Line 165-171 - Post Link
```javascript
// --- Extract Post Link ---
const postLinkTag = postElement.querySelector(
  "a[href*='/groups/'][href*='/posts/']" // Usually stays the same
);
if (postLinkTag) {
  postData.postLink = postLinkTag.href;
  postData.id = postData.postLink;
}
```

### Location 5: Line 173-179 - Date
```javascript
// --- Extract Date ---
const dateTag = postElement.querySelector(
  'div.YOUR_NEW_DATE_DIV_CLASSES_HERE'
);
if (dateTag) {
  postData.date = dateTag.textContent.trim();
}
```

---

## 🧪 Step 4: Test in Browser Console

Before updating the extension, test your new selectors directly in the Facebook page console:

```javascript
// Test post container
const posts = document.querySelectorAll('div.YOUR_NEW_SELECTOR');
console.log(`Found ${posts.length} posts`);

// Test extraction on first post
const firstPost = posts[0];

const author = firstPost.querySelector('span.YOUR_AUTHOR_SELECTOR');
console.log('Author:', author?.textContent);

const profileLink = firstPost.querySelector("a[href*='/groups/'][href*='/user/']");
console.log('Profile:', profileLink?.href);

const postLink = firstPost.querySelector("a[href*='/groups/'][href*='/posts/']");
console.log('Post Link:', postLink?.href);

const date = firstPost.querySelector('div.YOUR_DATE_SELECTOR');
console.log('Date:', date?.textContent);

// If all values show correctly, update content.js!
```

---

## 🔄 Step 5: Reload Extension

1. Go to `chrome://extensions/`
2. Find **"FB Group Collector - Safe Mode"**
3. Click **🔄 Reload** button
4. Test on Facebook Group again

---

## 💡 Pro Tips

### Tip 1: Use Attribute Selectors
If CSS classes change too often, use attribute selectors:

```javascript
// Instead of classes:
'div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z'

// Use attributes (more stable):
'div[role="article"]'
'div[data-pagelet^="FeedUnit_"]'
```

### Tip 2: Combine Selectors
Use multiple selectors as fallbacks:

```javascript
const postElements = 
  document.querySelectorAll('div.x1yztbdb.x1n2onr6') ||
  document.querySelectorAll('[role="article"]') ||
  document.querySelectorAll('div[data-pagelet^="FeedUnit_"]');
```

### Tip 3: Use CSS Selector Inspector
Chrome DevTools has a built-in CSS selector copier:

1. Right-click element → Inspect
2. Right-click the element in Elements tab
3. Copy → Copy selector
4. Paste and test in console

### Tip 4: Check Multiple Posts
Always test on **multiple posts** to ensure selector works universally:

```javascript
document.querySelectorAll('div.YOUR_SELECTOR').forEach((post, i) => {
  const author = post.querySelector('span.AUTHOR_SELECTOR');
  console.log(`Post ${i + 1}: ${author?.textContent || 'NO AUTHOR'}`);
});
```

---

## 🐛 Common Issues

### Issue 1: Selector returns 0 elements
**Solution:** 
- Facebook changed the container class
- Scroll down to load posts first
- Try alternative selectors

### Issue 2: Selector returns too many elements
**Solution:**
- Add more specific classes
- Use `:not()` to exclude certain elements
- Add attribute selectors

### Issue 3: Author name is null
**Solution:**
- Inspect multiple posts
- Author span might have different structure
- Use a more generic selector like `h4 span` or `strong`

### Issue 4: Links not found
**Solution:**
- URLs might have different structure
- Try `a[href*="/permalink/"]` instead
- Check if Facebook is using `data-href` attribute

---

## 📦 Python Helper Script

You can use this Python script to quickly test selectors on saved HTML:

```python
from bs4 import BeautifulSoup

# Save Facebook page as HTML (Ctrl+S)
with open("facebook_group.html", "r", encoding="utf-8") as f:
    html = f.read()

soup = BeautifulSoup(html, "html.parser")

# Test post container
posts = soup.select("div.x1yztbdb.x1n2onr6.xh8yej3.x1ja2u2z")
print(f"Found {len(posts)} posts")

if posts:
    first_post = posts[0]
    
    # Test author
    author = first_post.select_one("span.xdj266r.x14z9mp.xat24cr...")
    print(f"Author: {author.get_text(strip=True) if author else 'NOT FOUND'}")
    
    # Test profile link
    profile = first_post.select_one("a[href*='/groups/'][href*='/user/']")
    print(f"Profile: {profile['href'] if profile else 'NOT FOUND'}")
    
    # Test post link
    post_link = first_post.select_one("a[href*='/groups/'][href*='/posts/']")
    print(f"Post: {post_link['href'] if post_link else 'NOT FOUND'}")
    
    # Test date
    date = first_post.select_one("div.x6s0dn4.x17zd0t2.x78zum5...")
    print(f"Date: {date.get_text(strip=True) if date else 'NOT FOUND'}")
```

---

## 📞 Need Help?

If selectors are completely broken:

1. **Save the Facebook page** (Ctrl+S) as HTML
2. **Open in text editor** and search for post content
3. **Find the parent div** structure
4. **Identify unique classes** or attributes
5. **Update selectors** accordingly

Remember: Facebook changes their DOM frequently, so periodic updates are normal!

---

**Good luck! 🚀**

