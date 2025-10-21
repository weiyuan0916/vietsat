# Offline Page Architecture

## 📐 Component Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Browser Request                           │
│                    GET /offline                              │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Router                            │
│                    routes/web.php                            │
│                    Route::get('/offline')                    │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    View Resolution                           │
│                    pages/offline.blade.php                   │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 Layout: layouts/simple.blade.php             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │  <html>                                                 │ │
│ │  <head>                                                 │ │
│ │    - SEO Meta Tags                                      │ │
│ │    - Social Media Tags (OG, Twitter)                    │ │
│ │    - PWA Manifest                                       │ │
│ │    - CSS Assets                                         │ │
│ │  </head>                                                │ │
│ │  <body>                                                 │ │
│ └─────────────────────────────────────────────────────────┘ │
│           │                                                   │
│           ├─> <x-preloader />                                │
│           │   └─> components/preloader.blade.php             │
│           │                                                   │
│           ├─> <x-simple-header />                            │
│           │   ├─> components/simple-header.blade.php         │
│           │   └─> app/View/Components/SimpleHeader.php       │
│           │       ├─ Props: title, backUrl                   │
│           │       ├─ Props: showBackButton                   │
│           │       └─ Props: showNavbarToggler                │
│           │                                                   │
│           ├─> <x-menu />                                     │
│           │   └─> components/menu.blade.php                  │
│           │       ├─ Offcanvas navigation                    │
│           │       └─ User profile section                    │
│           │                                                   │
│           ├─> <x-p-w-alert />                                │
│           │   └─> components/p-w-alert.blade.php             │
│           │                                                   │
│           ├─> @yield('content')                              │
│           │   └─────────────────────┐                        │
│           │                         │                        │
│           │   ┌─────────────────────┴──────────────────┐    │
│           │   │  Offline Page Content                  │    │
│           │   │  ┌──────────────────────────────────┐  │    │
│           │   │  │ <div class="container">          │  │    │
│           │   │  │   <img no-internet.png>          │  │    │
│           │   │  │   <h5>No Internet Connection!</h5>│  │    │
│           │   │  │   <p>Offline message...</p>       │  │    │
│           │   │  │   <a href="home">Back Home</a>    │  │    │
│           │   │  │ </div>                            │  │    │
│           │   │  └──────────────────────────────────┘  │    │
│           │   └────────────────────────────────────────┘    │
│           │                                                   │
│           ├─> <div id="internetStatus"></div>                │
│           │   └─> Internet connection indicator              │
│           │                                                   │
│           ├─> <x-footer-nav />                               │
│           │   └─> components/footer-nav.blade.php            │
│           │       └─ Bottom navigation bar                   │
│           │                                                   │
│           ├─> JavaScript Assets                              │
│           │   ├─ Bootstrap                                   │
│           │   ├─ jQuery                                      │
│           │   ├─ PWA scripts                                 │
│           │   └─ Custom active.js                            │
│           │                                                   │
│           └─> @stack('scripts')                              │
│               └─ Page-specific scripts                       │
│                                                               │
│  </body>                                                      │
│  </html>                                                      │
└───────────────────────────────────────────────────────────────┘
```

---

## 🔄 Component Flow Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                   SimpleHeader Component                      │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  PHP Class: app/View/Components/SimpleHeader.php             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  Properties:                                        │    │
│  │  - public string $title                             │    │
│  │  - public ?string $backUrl                          │    │
│  │  - public bool $showBackButton                      │    │
│  │  - public bool $showNavbarToggler                   │    │
│  │                                                      │    │
│  │  Constructor:                                        │    │
│  │  - Receives props from Blade component call         │    │
│  │  - Sets default values                              │    │
│  │  - Passes data to view                              │    │
│  │                                                      │    │
│  │  render():                                           │    │
│  │  - Returns view('components.simple-header')         │    │
│  └─────────────────────────────────────────────────────┘    │
│                           │                                   │
│                           ▼                                   │
│  Blade Template: resources/views/components/                 │
│                  simple-header.blade.php                      │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  <div class="header-area">                          │    │
│  │    @if($showBackButton)                             │    │
│  │      <a href="{{ $backUrl }}">                      │    │
│  │        <i class="ti ti-arrow-left"></i>             │    │
│  │      </a>                                            │    │
│  │    @endif                                            │    │
│  │                                                      │    │
│  │    <h6>{{ $title }}</h6>                            │    │
│  │                                                      │    │
│  │    @if($showNavbarToggler)                          │    │
│  │      <div class="toggler">...</div>                 │    │
│  │    @endif                                            │    │
│  │  </div>                                              │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

---

## 🎨 Layout Comparison

```
┌─────────────────────────────────────────────────────────────┐
│                   layouts/app.blade.php                      │
├─────────────────────────────────────────────────────────────┤
│  Use For: Home, Shop, Products, Vendors                     │
│                                                              │
│  Header: <x-page-header />                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  ┌─────┐     ┌──────────────┐     ┌────┐ ┌────┐   │   │
│  │  │ 🏠  │     │ Site Logo    │     │ 🛒 │ │ 👤 │   │   │
│  │  └─────┘     └──────────────┘     └────┘ └────┘   │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                              │
│  Used By:                                                    │
│  - Home page                                                 │
│  - Shop pages (grid/list)                                   │
│  - Product details                                           │
│  - Vendor pages                                              │
│  - Category pages                                            │
└──────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                 layouts/simple.blade.php                     │
├─────────────────────────────────────────────────────────────┤
│  Use For: Settings, Profile, Offline, Privacy               │
│                                                              │
│  Header: <x-simple-header />                                │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  ┌───┐                                        ┌───┐ │   │
│  │  │ ← │     Page Title                         │ ☰ │ │   │
│  │  └───┘                                        └───┘ │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                              │
│  Used By:                                                    │
│  - Offline page                                              │
│  - Settings pages                                            │
│  - Profile pages                                             │
│  - Privacy policy                                            │
│  - Terms & conditions                                        │
│  - About us                                                  │
│  - Contact page                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## 📊 Data Flow

```
┌──────────────────────────────────────────────────────────────┐
│                      User Action                              │
│             (Clicks link or loses internet)                   │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                   Route Resolution                            │
│        route('offline') → GET /offline                        │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                  View Compilation                             │
│  1. Load layouts/simple.blade.php                             │
│  2. Pass layout variables:                                    │
│     - pageTitle: 'Offline Detected'                           │
│     - backUrl: route('home')                                  │
│     - showBackButton: true                                    │
│     - showNavbarToggler: true                                 │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                Component Resolution                           │
│  <x-simple-header />                                          │
│  └─> Instantiate SimpleHeader class                           │
│      └─> Pass props to constructor                            │
│          └─> Render components/simple-header.blade.php        │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                   Asset Resolution                            │
│  asset('frontend/img/bg-img/no-internet.png')                │
│  └─> /frontend/img/bg-img/no-internet.png                    │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                    HTML Response                              │
│  200 OK - Fully rendered HTML with:                           │
│  - SEO meta tags                                              │
│  - Optimized assets                                           │
│  - Lazy-loaded images                                         │
│  - PWA manifest                                               │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                   Browser Render                              │
│  - Parse HTML                                                 │
│  - Load CSS                                                   │
│  - Execute JavaScript                                         │
│  - Render components                                          │
│  - Display to user                                            │
└──────────────────────────────────────────────────────────────┘
```

---

## 🔧 Component Props Flow

```
┌──────────────────────────────────────────────────────────────┐
│         Blade Component Call (in Layout)                      │
│                                                               │
│  <x-simple-header                                             │
│    :title="$pageTitle ?? 'Page'"                              │
│    :back-url="$backUrl ?? route('home')"                      │
│    :show-back-button="$showBackButton ?? true"                │
│    :show-navbar-toggler="$showNavbarToggler ?? true"          │
│  />                                                            │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│         SimpleHeader Constructor                              │
│                                                               │
│  public function __construct(                                 │
│    string $title = 'Page',                    ──┐             │
│    ?string $backUrl = null,                     │ Defaults    │
│    bool $showBackButton = true,                 │ Applied     │
│    bool $showNavbarToggler = true               │             │
│  )                                            ──┘             │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│         Component Properties Set                              │
│                                                               │
│  $this->title = 'Offline Detected'                            │
│  $this->backUrl = 'http://site.com/'                          │
│  $this->showBackButton = true                                 │
│  $this->showNavbarToggler = true                              │
└───────────────────────────┬──────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│         Blade Template Access                                 │
│                                                               │
│  {{ $title }}              → 'Offline Detected'               │
│  {{ $backUrl }}            → 'http://site.com/'               │
│  @if($showBackButton)      → true (shows back button)         │
│  @if($showNavbarToggler)   → true (shows menu toggle)         │
└──────────────────────────────────────────────────────────────┘
```

---

## 🏗️ File Dependency Graph

```
pages/offline.blade.php
    │
    ├─── extends: layouts/simple.blade.php
    │        │
    │        ├─── uses: components/preloader.blade.php
    │        │
    │        ├─── uses: components/simple-header.blade.php
    │        │        └─── class: app/View/Components/SimpleHeader.php
    │        │
    │        ├─── uses: components/menu.blade.php
    │        │
    │        ├─── uses: components/p-w-alert.blade.php
    │        │
    │        └─── uses: components/footer-nav.blade.php
    │
    └─── includes: asset files
             ├─── frontend/css/*.css
             ├─── frontend/js/*.js
             └─── frontend/img/bg-img/no-internet.png
```

---

## 🎯 Benefits of This Architecture

### 1. **Separation of Concerns**
- Layout handles structure
- Components handle reusable UI
- Pages handle content
- PHP classes handle logic

### 2. **Reusability**
- SimpleHeader used across multiple pages
- Simple layout used for all internal pages
- Components shared across layouts

### 3. **Maintainability**
- Change header once, affects all pages
- Update layout, all pages benefit
- Centralized asset management

### 4. **Scalability**
- Easy to add new pages
- Simple to create new components
- Flexible layout system

### 5. **Type Safety**
- PHP class with strict types
- Props validated at runtime
- IDE autocomplete support

---

## 📝 Example Usage Patterns

### Pattern 1: Simple Page with Back Button
```blade
@extends('layouts.simple', [
    'pageTitle' => 'Settings',
    'backUrl' => route('home')
])
```

### Pattern 2: Page without Back Button
```blade
@extends('layouts.simple', [
    'pageTitle' => 'About Us',
    'showBackButton' => false
])
```

### Pattern 3: Standalone Header Component
```blade
<x-simple-header 
    title="Custom Page" 
    :back-url="route('custom.back')" 
/>
```

---

**Architecture Version**: 1.0  
**Last Updated**: 2025-10-14  
**Framework**: Laravel 12

