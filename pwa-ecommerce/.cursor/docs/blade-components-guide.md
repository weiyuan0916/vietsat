# Blade Components & Layouts Quick Reference

## 📋 Table of Contents
1. [Layouts](#layouts)
2. [Header Components](#header-components)
3. [Common Components](#common-components)
4. [Creating New Pages](#creating-new-pages)
5. [Best Practices](#best-practices)

---

## 🎨 Layouts

### Main Layout (`layouts.app`)
**Use for**: Homepage, shop, products, vendor pages

```blade
@extends('layouts.app')

@section('title', 'Page Title')
@section('meta_description', 'Page description for SEO')

@section('content')
    <!-- Your content here -->
@endsection
```

**Features**:
- Page header with logo, cart, profile icons
- Full navigation menu
- Footer navigation
- PWA support

---

### Simple Layout (`layouts.simple`)
**Use for**: Settings, profile, offline, terms, privacy pages

```blade
@extends('layouts.simple', [
    'pageTitle' => 'Page Title',
    'backUrl' => route('home'),
    'showBackButton' => true,
    'showNavbarToggler' => true
])

@section('title', 'Full Page Title')
@section('meta_description', 'SEO description')

@section('content')
    <!-- Your content here -->
@endsection
```

**Parameters**:
- `pageTitle`: Header title text
- `backUrl`: URL for back button (optional)
- `showBackButton`: Show/hide back button (default: true)
- `showNavbarToggler`: Show/hide menu toggle (default: true)

---

## 🧩 Header Components

### Page Header (`<x-page-header />`)
**Main header with logo, cart, and profile**

```blade
<x-page-header 
    :cart-count="0" 
    :user="auth()->user()" 
/>
```

### Simple Header (`<x-simple-header />`)
**Header with back button and title**

```blade
{{-- Basic --}}
<x-simple-header title="Settings" />

{{-- Custom back URL --}}
<x-simple-header 
    title="Privacy Policy" 
    :back-url="route('home')" 
/>

{{-- No back button --}}
<x-simple-header 
    title="About" 
    :show-back-button="false" 
/>

{{-- No menu toggle --}}
<x-simple-header 
    title="Terms" 
    :show-navbar-toggler="false" 
/>
```

**Props**:
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | 'Page' | Header title text |
| `backUrl` | string\|null | previous URL | Back button destination |
| `showBackButton` | bool | true | Show back button |
| `showNavbarToggler` | bool | true | Show menu toggle |

---

## 🎯 Common Components

### Preloader
```blade
<x-preloader />
```

### Offcanvas Menu
```blade
<x-menu :user="auth()->user()" :notification-count="3" />
```

### Footer Navigation
```blade
<x-footer-nav />
```

### PWA Install Alert
```blade
<x-p-w-alert />
```

### Search Form
```blade
<x-search-form />
```

### Main Slider
```blade
<x-main-slider />
```

### Categories
```blade
<x-main-category />
```

### Flash Sale
```blade
<x-flash-sale />
```

### Top Products
```blade
<x-top-product-section />
```

### Promo Banner
```blade
<x-promo-banner />
```

### Best Sellers
```blade
<x-best-seller-list />
```

---

## 📄 Creating New Pages

### Example 1: Settings Page
```blade
@extends('layouts.simple', [
    'pageTitle' => 'Settings',
    'backUrl' => route('home')
])

@section('title', 'Settings - ' . config('app.name'))
@section('meta_description', 'Manage your account settings')

@section('content')
<div class="container">
    <div class="py-3">
        <h5>Account Settings</h5>
        <!-- Settings content -->
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom scripts
</script>
@endpush
```

### Example 2: Product Page
```blade
@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))
@section('meta_description', $product->description)

@section('content')
<div class="container">
    <!-- Product details -->
</div>
@endsection
```

### Example 3: Privacy Policy
```blade
@extends('layouts.simple', [
    'pageTitle' => 'Privacy Policy',
    'backUrl' => route('home')
])

@section('title', 'Privacy Policy')
@section('meta_description', 'Read our privacy policy and data protection information')

@section('content')
<div class="container">
    <div class="py-3">
        <h5>Privacy Policy</h5>
        <p>Last updated: {{ now()->format('F d, Y') }}</p>
        <!-- Policy content -->
    </div>
</div>
@endsection
```

---

## ✅ Best Practices

### 1. SEO Optimization
```blade
{{-- Always provide meta tags --}}
@section('title', 'Specific Page Title - ' . config('app.name'))
@section('meta_description', 'Unique description for this page')
@section('meta_keywords', 'keyword1, keyword2, keyword3')

{{-- Social sharing --}}
@section('og_title', 'Social Media Title')
@section('og_description', 'Description for social sharing')
@section('og_image', asset('images/share-image.jpg'))
```

### 2. Asset Loading
```blade
{{-- Always use asset() helper --}}
<img src="{{ asset('frontend/img/product.jpg') }}" alt="Product">
<link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}">

{{-- Use lazy loading for images --}}
<img src="{{ asset('img/product.jpg') }}" alt="Product" loading="lazy">
```

### 3. Routing
```blade
{{-- Always use named routes --}}
<a href="{{ route('products.show', $product->id) }}">View Product</a>

{{-- Never hardcode URLs --}}
{{-- ❌ BAD: <a href="/products/{{ $product->id }}"> --}}
{{-- ✅ GOOD: <a href="{{ route('products.show', $product) }}"> --}}
```

### 4. Conditional Rendering
```blade
{{-- Check authentication --}}
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
@else
    <a href="{{ route('login') }}">Login</a>
@endauth

{{-- Check data existence --}}
@if($products->count() > 0)
    @foreach($products as $product)
        <!-- Product card -->
    @endforeach
@else
    <p>No products found</p>
@endif

{{-- Use optional chaining for safety --}}
<p>{{ $user?->name ?? 'Guest' }}</p>
```

### 5. Component Organization
```blade
{{-- Stack scripts at the end --}}
@push('scripts')
<script>
    // Page-specific scripts
</script>
@endpush

{{-- Stack styles in head --}}
@push('styles')
<style>
    /* Page-specific styles */
</style>
@endpush
```

### 6. Accessibility
```blade
{{-- Always provide alt text --}}
<img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">

{{-- Use semantic HTML --}}
<nav aria-label="Main navigation">
    <!-- Navigation items -->
</nav>

<article>
    <h1>Article Title</h1>
    <!-- Article content -->
</article>
```

### 7. Performance
```blade
{{-- Lazy load images below the fold --}}
<img src="{{ asset('img/product.jpg') }}" alt="Product" loading="lazy">

{{-- Preload critical assets --}}
@push('styles')
<link rel="preload" href="{{ asset('frontend/fonts/custom.woff2') }}" as="font" type="font/woff2" crossorigin>
@endpush

{{-- Use CDN for external resources --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
```

---

## 🚀 Quick Commands

### Create New Blade Component
```bash
php artisan make:component ComponentName
```

### Create New View Component with Class
```bash
php artisan make:component ComponentName --view
```

### Clear View Cache
```bash
php artisan view:clear
```

### Test Routes
```bash
php artisan route:list --name=offline
```

---

## 📚 Additional Resources

- [Laravel Blade Documentation](https://laravel.com/docs/12.x/blade)
- [Laravel Components](https://laravel.com/docs/12.x/blade#components)
- [SEO Best Practices](https://developers.google.com/search/docs)
- [PWA Guidelines](https://web.dev/progressive-web-apps/)

---

**Last Updated**: 2025-10-14  
**Laravel Version**: 12.x  
**Template**: Suha 3.3.0

