@props(['products' => []])

<!-- Weekly Best Sellers -->
<div class="weekly-best-seller-area py-3">
    <div class="container">
        <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Weekly Best Sellers</h6>
            <a class="btn btn-sm btn-light" href="{{ route('shop.list') }}">
                View all<i class="ms-1 ti ti-arrow-right"></i>
            </a>
        </div>
        <div class="row g-2">
            @forelse($products as $product)
                <!-- Weekly Product Card -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail -->
                                <a class="product-thumbnail d-block" href="{{ route('products.show', $product['id'] ?? 1) }}">
                                    <img src="{{ $product['image'] ?? asset('frontend/img/product/18.png') }}" 
                                         alt="{{ $product['name'] ?? 'Product' }}"
                                         loading="lazy">
                                </a>
                                <!-- Wishlist -->
                                <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title -->
                                <a class="product-title d-block" href="{{ route('products.show', $product['id'] ?? 1) }}">
                                    {{ $product['name'] ?? 'Product Name' }}
                                </a>
                                <!-- Price -->
                                <p class="sale-price">
                                    <i class="ti ti-currency-dollar"></i>${{ number_format($product['sale_price'] ?? 64, 2) }}
                                    @if(isset($product['price']) && $product['price'] > $product['sale_price'])
                                        <span>${{ number_format($product['price'], 2) }}</span>
                                    @endif
                                </p>
                                <!-- Rating -->
                                <div class="product-rating">
                                    <i class="ti ti-star-filled"></i>{{ number_format($product['rating'] ?? 4.88, 2) }}
                                    <span class="ms-1">({{ $product['review_count'] ?? 39 }} review)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Default Weekly Product Cards -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <a class="product-thumbnail d-block" href="#">
                                    <img src="{{ asset('frontend/img/product/18.png') }}" alt="Nescafe Coffee Jar" loading="lazy">
                                </a>
                                <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <a class="product-title d-block" href="#">Nescafe Coffee Jar</a>
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$64<span>$89</span></p>
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.88 <span class="ms-1">(39 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <a class="product-thumbnail d-block" href="#">
                                    <img src="{{ asset('frontend/img/product/7.png') }}" alt="Modern Office Chair" loading="lazy">
                                </a>
                                <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <a class="product-title d-block" href="#">Modern Office Chair</a>
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$99<span>$159</span></p>
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.82 <span class="ms-1">(125 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <a class="product-thumbnail d-block" href="#">
                                    <img src="{{ asset('frontend/img/product/12.png') }}" alt="Beach Sunglasses" loading="lazy">
                                </a>
                                <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <a class="product-title d-block" href="#">Beach Sunglasses</a>
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$24<span>$32</span></p>
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.79 <span class="ms-1">(63 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <a class="product-thumbnail d-block" href="#">
                                    <img src="{{ asset('frontend/img/product/17.png') }}" alt="Meow Mix Cat Food" loading="lazy">
                                </a>
                                <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <a class="product-title d-block" href="#">Meow Mix Cat Food</a>
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$11.49<span>$13</span></p>
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.78 <span class="ms-1">(7 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

