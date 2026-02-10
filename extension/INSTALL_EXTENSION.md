Chrome extension — load & test

1) Open Chrome (or Chromium-based browser) and go to `chrome://extensions`.
2) Enable "Developer mode" (top-right).
3) Click "Load unpacked" and select the extension folder:

   `/Users/adward/Herd/neurapen.framer.website/next-tnd/extension`

4) The extension named `Facebook UID Opener` will appear. Click its icon to open the popup.
5) In the popup paste a Facebook profile URL, for example:

   `https://www.facebook.com/100005438413536/`

6) Click Submit. The extension will attempt to fetch the profile HTML, extract the UID from a meta tag like:

   `<meta property="al:android:url" content="fb://profile/100005438413536" />`

   If found, it will open:

   `https://www.facebook.com/groups/782860725537921/user/100005438413536/`

   in a new tab and automatically close that tab after 5 minutes.

Notes & troubleshooting:
- If a profile is private or requires login, the extension may not find the UID. Ensure you're logged into Facebook in the same browser so fetch() can include cookies.
- If the fetch is blocked or you see CORS/login issues, consider using a server-side fetch (not included) and modify `background.js` to call your server to resolve the UID.


