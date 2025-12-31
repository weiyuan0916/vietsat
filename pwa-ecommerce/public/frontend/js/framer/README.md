# Framer Local Files Setup

## Overview
This directory contains local copies of Framer website scripts and chunks that were originally loaded from external URLs.

## Files
- `script_main.MWUL3U4K.js` - Main Framer script
- `chunk-*.js` - Various Framer code chunks
- `_fPwoKBrHccY6YNLGYEDnoXN13KQJ93krMtcAMkyIEE.IOQM4BMF.js` - Special chunk file

## Usage
Use the `neurapen-script-local.blade.php` component instead of the original `neurapen-script.blade.php` to load these local files.

## Important Notes
1. **Placeholder Content**: The current files contain placeholder content. You need to manually download the actual content from the original Framer URLs.

2. **Download Instructions**:
   - Visit each original URL and save the content
   - Replace the placeholder content in each file
   - Make sure to preserve the `.js` extension (changed from `.mjs`)

3. **Original URLs**: The original URLs are commented in each file for reference.

4. **Module Loading**: These files use ES6 modules, so ensure your web server serves them with the correct MIME type (`application/javascript`).

## Laravel Integration
The local component uses `{{ asset('js/framer/filename.js') }}` to generate proper URLs for the assets.
