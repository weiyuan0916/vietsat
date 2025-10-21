<?php

namespace App\Http\Controllers\Web;

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
        
        return view('home', [
            'featuredProducts' => [], // Will be populated from database
            'collections' => [], // Will be populated from database
        ]);
    }
}

