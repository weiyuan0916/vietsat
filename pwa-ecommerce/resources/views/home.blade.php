@extends('layouts.app')

@section('title', 'Home - ' . config('app.name'))
@section('meta_description', 'Discover amazing vendors and products on our platform. Shop from local sellers in various categories.')
@section('meta_keywords', 'vendors, products, shop, ecommerce, local sellers, online shopping')

@section('content')
    <!-- Search Form -->
    <x-search-form />
    
    <!-- Hero Wrapper / Main Slider -->
    <x-main-slider />
    
    <!-- Product Categories -->
    <x-main-category />
    
    <!-- Flash Sale Slide -->
    <x-flash-sale />
    
    <!-- Dark Mode Toggle -->
    <div class="container">
        <div class="dark-mode-wrapper mt-3 bg-img p-4 p-lg-5">
            <p class="text-white">You can change your display to a dark background using a dark mode.</p>
            <div class="form-check form-switch mb-0">
                <label class="form-check-label text-white h6 mb-0" for="darkSwitch">Switch to Dark Mode</label>
                <input class="form-check-input" id="darkSwitch" type="checkbox" role="switch">
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    <x-top-product-section />
    
    <!-- CTA Area / Promo Banner -->
    <x-promo-banner />
    
    <!-- Weekly Best Sellers -->
    <x-best-seller-list />
    
    <!-- Featured Products -->
    <div class="featured-products-wrapper py-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Featured Products</h6>
                <a class="btn btn-sm btn-light" href="{{ route('products.featured') }}">
                    View all<i class="ms-1 ti ti-arrow-right"></i>
                </a>
            </div>
            <div class="row g-2">
                @foreach($featuredProducts ?? [] as $product)
                    <!-- Featured Product Card -->
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <!-- Badge -->
                                <span class="badge badge-warning custom-badge">
                                    <i class="ti ti-star-filled"></i>
                                </span>
                                <div class="product-thumbnail-side">
                                    <!-- Thumbnail -->
                                    <a class="product-thumbnail d-block" href="{{ route('products.show', $product->id ?? 1) }}">
                                        <img src="{{ $product->image ?? asset('frontend/img/product/14.png') }}" 
                                             alt="{{ $product->name ?? 'Product' }}" 
                                             loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <!-- Product Title -->
                                    <a class="product-title d-block" href="{{ route('products.show', $product->id ?? 1) }}">
                                        {{ $product->name ?? 'Blue Skateboard' }}
                                    </a>
                                    <!-- Price -->
                                    <p class="sale-price">
                                        ${{ number_format($product->sale_price ?? 39, 2) }}
                                        @if(isset($product->price) && $product->price > $product->sale_price)
                                            <span>${{ number_format($product->price ?? 89, 2) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if(empty($featuredProducts) || count($featuredProducts) == 0)
                    <!-- Placeholder Featured Product Cards -->
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/14.png') }}" alt="Blue Skateboard" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">Blue Skateboard</a>
                                    <p class="sale-price">$39<span>$89</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/15.png') }}" alt="Travel Bag" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">Travel Bag</a>
                                    <p class="sale-price">$14.7<span>$21</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/16.png') }}" alt="Cotton T-shirts" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">Cotton T-shirts</a>
                                    <p class="sale-price">$3.69<span>$5</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/21.png') }}" alt="ECG Rice Cooker" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">ECG Rice Cooker</a>
                                    <p class="sale-price">$9.33<span>$13</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/20.png') }}" alt="Beauty Cosmetics" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">Beauty Cosmetics</a>
                                    <p class="sale-price">$5.99<span>$8</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <a class="product-thumbnail d-block" href="#">
                                        <img src="{{ asset('frontend/img/product/19.png') }}" alt="Basketball" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-description">
                                    <a class="product-title d-block" href="#">Basketball</a>
                                    <p class="sale-price">$16<span>$20</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Collections -->
    <div class="pb-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Collections</h6>
                <a class="btn btn-sm btn-light" href="{{ route('collections.index') }}">
                    View all<i class="ms-1 ti ti-arrow-right"></i>
                </a>
            </div>
            <!-- Collection Slide -->
            <div class="collection-slide owl-carousel">
                @foreach($collections ?? [] as $collection)
                    <!-- Collection Card -->
                    <div class="card collection-card">
                        <a href="{{ route('collections.show', $collection->id ?? 1) }}">
                            <img src="{{ $collection->image ?? asset('frontend/img/product/17.jpg') }}" 
                                 alt="{{ $collection->name ?? 'Collection' }}" 
                                 loading="lazy">
                        </a>
                        <div class="collection-title">
                            <span>{{ $collection->name ?? 'Women' }}</span>
                            <span class="badge bg-danger">{{ $collection->products_count ?? 9 }}</span>
                        </div>
                    </div>
                @endforeach
                
                @if(empty($collections) || count($collections) == 0)
                    <!-- Placeholder Collection Cards -->
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/17.jpg') }}" alt="Women Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Women</span><span class="badge bg-danger">9</span>
                        </div>
                    </div>
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/19.jpg') }}" alt="Men Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Men</span><span class="badge bg-danger">29</span>
                        </div>
                    </div>
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/21.jpg') }}" alt="Kids Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Kids</span><span class="badge bg-danger">4</span>
                        </div>
                    </div>
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/22.jpg') }}" alt="Gadget Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Gadget</span><span class="badge bg-danger">11</span>
                        </div>
                    </div>
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/23.jpg') }}" alt="Foods Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Foods</span><span class="badge bg-danger">2</span>
                        </div>
                    </div>
                    <div class="card collection-card">
                        <a href="#"><img src="{{ asset('frontend/img/product/24.jpg') }}" alt="Sports Collection" loading="lazy"></a>
                        <div class="collection-title">
                            <span>Sports</span><span class="badge bg-danger">5</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

