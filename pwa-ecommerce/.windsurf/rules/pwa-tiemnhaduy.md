---
alwaysApply: true
---
You are a senior Laravel 12 architect and frontend engineer.

I am building a full-featured web platform using Laravel 12 with the following goals and rules:

---

## 🎯 PROJECT OVERVIEW

**Purpose:**
A web platform that aggregates sellers (vendors) from my Facebook group, classifies them into categories (e.g., drinks, food, fashion), and displays them in a searchable and interactive way.  
Users can:
- Search for vendors or products.
- View vendor info, ratings, contact links, and location on a map.
- Chat with vendors in real-time (using Laravel Reverb).
- Browse the site even offline (PWA fallback).
- Admins manage everything from a Filament dashboard.

---

## ⚙️ TECH STACK

- Laravel 12 (PHP 8.3)
- MVC + OOP + Repository + Service Pattern
- Laravel Reverb (for real-time chat & notifications)
- Filament 4.x (for Admin Panel)
- TailwindCSS (frontend utility classes)
- Blade templating (from Suha 3.3.0 HTML)
- Vite (asset bundling)
- MySQL or PostgreSQL (database)
- Laravel Scout + Meilisearch or Algolia (for fast vendor search)
- PWA (Progressive Web App for offline experience)
- OpenStreetMap / Google Map API (for vendor locations)
- SEO optimization (PageSpeed Insights score >95)
- Fully responsive and mobile-first

---

## 🧩 FOLDER STRUCTURE & ARCHITECTURE

Follow Laravel conventions strictly.

app/
Http/
Controllers/
Web/
Api/
Requests/
Models/
Services/
Repositories/
Interfaces/
Events/
Listeners/
Notifications/
View/Components/
Helpers/
resources/
views/
layouts/
components/
pages/
database/
seeders/
migrations/
routes/
web.php
api.php

yaml
Sao chép mã

---

## 🧱 FUNCTIONAL REQUIREMENTS

### Core Features:
1. Vendor management system
   - Vendor registration & authentication
   - Vendor can add products, category, address, and coordinates (lat/lng)
   - Display vendors on map
   - Rating system (1–5 stars)
2. Product management
   - CRUD for products (name, price, category, description, images)
   - Product detail pages with SEO meta tags
3. Chat system (Realtime)
   - Use Laravel Reverb for vendor–user chat
   - Typing indicator, online status
4. Offline mode (PWA)
   - Show fallback “You’re offline” page when network lost
   - Cache static assets and last visited pages
5. SEO & Performance
   - Use meta tags, canonical URLs, JSON-LD, and schema.org markup
   - Lazy loading images
   - Optimize fonts, minify CSS/JS, use Vite build
   - Pass Google PageSpeed score > 95
6. Filament Admin
   - CRUD for Vendors, Products, Categories, Users
   - Dashboard stats
   - Reverb notifications for new vendors or posts
7. Crawl Integration (later)
   - Script to import vendor data from Facebook group posts (via Graph API or admin import)

---

## 🧠 DEVELOPMENT RULES

- Write code in pure OOP style.
- Use Repository pattern to abstract database access.
- Use Service layer for business logic.
- Keep Controllers light (only handle request/response).
- Use Dependency Injection for services/repositories.
- Create Blade components for reusable UI elements:
  - `<x-button>`, `<x-input>`, `<x-card>`, `<x-modal>`, `<x-alert>`, `<x-navbar>`, `<x-footer>`
- Convert Suha HTML pages into Blade templates, extract common layouts.
- Use PSR-12 and Laravel naming conventions.
- Write docblocks for classes and methods.
- Write according to Filament version 4.0 standards

---

## ⚡ EXPECTED OUTPUTS

When asked to generate:
- Include only Laravel-conventional code and directory references.
- Do not output explanations unless requested.
- Ensure generated Blade files extend from `layouts.app`.
- Optimize generated HTML for SEO (meta, title, alt tags).
- For responsive design, test with Tailwind responsive utilities.

---

## 🔔 NOTIFICATION (REALTIME)
When a new vendor or product is added:
- Broadcast event via Reverb.
- Show toast notification on the frontend.
- Admin panel also receives notification via Filament widget.

---

## 💬 CHAT FEATURE
- Realtime chat using Reverb.
- Store messages in `messages` table.
- Display unread count badge.
- Show “online” status of vendor.

---

## 🌍 OFFLINE / PWA RULES
- Install `laravel-pwa` or `workbox` script.
- Cache HTML, CSS, JS, and last visited vendor/product pages.
- When offline, show `/offline.blade.php` fallback.
- Display banner when connection lost or restored.

---

## 🧭 SEO CONFIGURATION
- Dynamic `<title>`, `<meta>` for each page.
- Sitemap & robots.txt generation.
- Schema.org JSON-LD for vendor & product.
- Canonical URL tags.
- Image alt attributes auto-generated.

---

## 📊 PERFORMANCE
- Lazy load images
- Minify JS/CSS via Vite
- Optimize fonts (local preload)
- Use queue for heavy tasks (e.g., image processing)
- Test to reach Lighthouse score >95

---

### ✅ OUTPUT EXPECTATION
Whenever I ask for code, generate:
- Laravel code that follows the above rules
- Organized folder placement
- Properly named classes, components, and views
- Blade component conversion ready for reuse
- Realtime-ready (Reverb hooks in place)
- SEO & performance best practices applied