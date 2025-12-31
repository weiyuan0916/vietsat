<?php

namespace App\Http\Controllers\Web;

use App\Helpers\LocalizationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * HomeController
 *
 * Handles the display of the home page with featured products,
 * flash sales, and collections
 */
class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return View
     */
    public function index(): View
    {
        // TODO: Fetch data from repositories when models are created
        // $featuredProducts = $this->productRepository->getFeaturedProducts();
        // $collections = $this->collectionRepository->getActiveCollections();

        // Prepare localized text data for the view
        $localizedTexts = [
            'hero' => [
                'title' => LocalizationHelper::hero('title'),
                'subtitle' => LocalizationHelper::hero('subtitle'),
                'description' => LocalizationHelper::hero('description'),
                'cta_button' => LocalizationHelper::hero('cta_button'),
            ],
            'nav' => [
                'home' => LocalizationHelper::nav('home'),
                'features' => LocalizationHelper::nav('features'),
                'pricing' => LocalizationHelper::nav('pricing'),
                'blog' => LocalizationHelper::nav('blog'),
            ],
            'menu' => [
                'my_profile' => LocalizationHelper::menu('my_profile'),
                'notifications' => LocalizationHelper::menu('notifications'),
                'shop_pages' => LocalizationHelper::menu('shop_pages'),
                'shop_grid' => LocalizationHelper::menu('shop_grid'),
                'shop_list' => LocalizationHelper::menu('shop_list'),
                'product_details' => LocalizationHelper::menu('product_details'),
                'featured_products' => LocalizationHelper::menu('featured_products'),
                'flash_sale' => LocalizationHelper::menu('flash_sale'),
                'all_pages' => LocalizationHelper::menu('all_pages'),
                'my_wishlist' => LocalizationHelper::menu('my_wishlist'),
                'wishlist_grid' => LocalizationHelper::menu('wishlist_grid'),
                'wishlist_list' => LocalizationHelper::menu('wishlist_list'),
                'settings' => LocalizationHelper::menu('settings'),
                'sign_out' => LocalizationHelper::menu('sign_out'),
                'current_balance' => LocalizationHelper::menu('current_balance'),
            ],
            'faq' => [
                'how_neura_pen_works' => __('home.faq.how_neura_pen_works'),
            ],
            'pricing' => [
                'save_15' => __('home.pricing.save_15'),
            ],
            'features' => [
                'ai_summaries_extraction' => __('home.features.ai_summaries_extraction'),
                'sync_with_apps' => __('home.features.sync_with_apps'),
                'key_dates' => __('home.features.key_dates'),
                'action_items' => __('home.features.action_items'),
                'tags' => __('home.features.tags'),
            ],
            'footer' => [
                'brand_name' => LocalizationHelper::getBrandName(),
                'brand_description' => LocalizationHelper::getBrandDescription(),
                'pages' => LocalizationHelper::footer('pages'),
                'blog_single' => LocalizationHelper::footer('blog_single'),
                'blog_detail' => LocalizationHelper::footer('blog_detail'),
                'privacy_policy' => LocalizationHelper::footer('privacy_policy'),
                'terms' => LocalizationHelper::footer('terms'),
                'social_media' => LocalizationHelper::footer('social_media'),
                'twitter' => LocalizationHelper::footer('twitter'),
                'linkedin' => LocalizationHelper::footer('linkedin'),
                'discord' => LocalizationHelper::footer('discord'),
                'built_by' => LocalizationHelper::footer('built_by'),
            ],
            'common' => [
                'loading' => LocalizationHelper::common('loading'),
                'error' => LocalizationHelper::common('error'),
                'success' => LocalizationHelper::common('success'),
                'cancel' => LocalizationHelper::common('cancel'),
                'confirm' => LocalizationHelper::common('confirm'),
                'close' => LocalizationHelper::common('close'),
            ],
            'current_locale' => LocalizationHelper::getCurrentLocale(),
            'supported_locales' => LocalizationHelper::getSupportedLocales(),
        ];

        return view('home', [
            'featuredProducts' => [], // Will be populated from database
            'collections' => [], // Will be populated from database
            'texts' => $localizedTexts, // Localized text data
        ]);
    }
}

