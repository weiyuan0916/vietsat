<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Web\HomeController;
use Illuminate\Http\Request;

/**
 * Home Route
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

// Basic pages from new UI
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/services', function () {
    return view('pages.services');
})->name('services');

Route::get('/projects', function () {
    return view('pages.projects');
})->name('projects');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Contact form POST handler
Route::post('/contact/send', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:191',
        'email' => 'required|email|max:191',
        'message' => 'required|string|max:2000',
    ]);

    // Placeholder: in production send email or store to DB
    // For now flash success and redirect back
    return back()->with('status', 'Cảm ơn! Chúng tôi đã nhận được tin nhắn của bạn.');
})->name('contact.send');

/**
 * Placeholder Routes (to be implemented)
 * These routes are referenced in the Blade templates
 */

// Search
Route::get('/search', function () {
    return view('home'); // Placeholder
})->name('search');

// Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/{id}', function ($id) {
        return redirect()->route('home'); // Placeholder
    })->name('show');
    
    Route::get('/featured', function () {
        return redirect()->route('home'); // Placeholder
    })->name('featured');
    
    Route::get('/flash-sale', function () {
        return redirect()->route('home'); // Placeholder
    })->name('flash-sale');
});

// Shop
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/grid', function () {
        return redirect()->route('home'); // Placeholder
    })->name('grid');
    
    Route::get('/list', function () {
        return redirect()->route('home'); // Placeholder
    })->name('list');
});

// Categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/{slug}', function ($slug) {
        return redirect()->route('home'); // Placeholder
    })->name('show');
});

// Collections
Route::prefix('collections')->name('collections.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
    
    Route::get('/{id}', function ($id) {
        return redirect()->route('home'); // Placeholder
    })->name('show');
});

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
});

// Wishlist
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
    
    Route::get('/grid', function () {
        return redirect()->route('home'); // Placeholder
    })->name('grid');
    
    Route::get('/list', function () {
        return redirect()->route('home'); // Placeholder
    })->name('list');
});

// Profile
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('show');
});

// Messages
Route::prefix('messages')->name('messages.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
});

// Notifications
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
});

// Pages
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
});

// Offline Page
Route::get('/offline', function () {
    return view('pages.offline');
})->name('offline');

// Settings
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home'); // Placeholder
    })->name('index');
});

// Language switching
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, array_keys(config('app.supported_locales', [])))) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Simple test page for localization
Route::get('/test', function () {
    return response()->json([
        'current_locale' => App::getLocale(),
        'hero_title' => __('home.hero.title'),
        'menu_profile' => __('home.menu.my_profile'),
        'supported_locales' => config('app.supported_locales', ['en' => 'English']),
    ]);
})->name('test');

// Simple demo page
Route::get('/simple-demo', function () {
    return view('simple-demo');
})->name('simple-demo');

// Demo page for localization testing
Route::get('/demo', function () {
    // Prepare localized text data for the view
    $localizedTexts = [
        'hero' => [
            'title' => __('home.hero.title'),
            'subtitle' => __('home.hero.subtitle'),
            'description' => __('home.hero.description'),
            'cta_button' => __('home.hero.cta_button'),
        ],
        'nav' => [
            'home' => __('home.nav.home'),
            'features' => __('home.nav.features'),
            'pricing' => __('home.nav.pricing'),
            'blog' => __('home.nav.blog'),
        ],
        'menu' => [
            'my_profile' => __('home.menu.my_profile'),
            'notifications' => __('home.menu.notifications'),
            'settings' => __('home.menu.settings'),
        ],
        'footer' => [
            'brand_name' => __('home.footer.brand_name'),
        ],
        'common' => [
            'loading' => __('home.common.loading'),
            'error' => __('home.common.error'),
        ],
        'current_locale' => App::getLocale(),
        'supported_locales' => config('app.supported_locales', ['en' => 'English']),
    ];

    return view('demo', [
        'texts' => $localizedTexts,
    ]);
})->name('demo');

// Auth Routes (placeholders - will be implemented with Laravel Breeze/Fortify)
Route::get('/login', function () {
    return redirect()->route('home'); // Placeholder
})->name('login');

Route::get('/register', function () {
    return redirect()->route('home'); // Placeholder
})->name('register');

Route::post('/logout', function () {
    return redirect()->route('home'); // Placeholder
})->name('logout');
