@props(['products' => []])

<!-- Top Products -->
<div class="top-products-area py-3">
    <div class="container">
        <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Top Products</h6>
            <a class="btn btn-sm btn-light" href="{{ route('shop.grid') }}">
                View all<i class="ms-1 ti ti-arrow-right"></i>
            </a>
        </div>
        <div class="row g-2">
            @forelse($products as $product)
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge -->
                            @if(isset($product['badge']))
                                <span class="badge rounded-pill badge-{{ $product['badge_color'] ?? 'warning' }}">
                                    {{ $product['badge'] }}
                                </span>
                            @endif
                            
                            <!-- Wishlist Button -->
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            
                            <!-- Thumbnail -->
                            <a class="product-thumbnail d-block" href="{{ route('products.show', $product['id'] ?? 1) }}">
                                <img class="mb-2" 
                                     src="{{ $product['image'] ?? asset('frontend/img/product/11.png') }}" 
                                     alt="{{ $product['name'] ?? 'Product' }}"
                                     loading="lazy">
                                
                                @if(isset($product['countdown']))
                                    <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                                    <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="{{ $product['countdown'] }}">
                                        <li><span class="days">0</span>d</li>
                                        <li><span class="hours">0</span>h</li>
                                        <li><span class="minutes">0</span>m</li>
                                        <li><span class="seconds">0</span>s</li>
                                    </ul>
                                @endif
                            </a>
                            
                            <!-- Product Title -->
                            <a class="product-title" href="{{ route('products.show', $product['id'] ?? 1) }}">
                                {{ $product['name'] ?? 'Product Name' }}
                            </a>
                            
                            <!-- Product Price -->
                            <p class="sale-price">
                                ${{ number_format($product['sale_price'] ?? 13, 2) }}
                                @if(isset($product['price']) && $product['price'] > $product['sale_price'])
                                    <span>${{ number_format($product['price'], 2) }}</span>
                                @endif
                            </p>
                            
                            <!-- Rating -->
                            <div class="product-rating">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="ti ti-star-filled"></i>
                                @endfor
                            </div>
                            
                            <!-- Add to Cart -->
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Default Product Cards (placeholder) -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-warning">Sale</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/11.png') }}" alt="Beach Cap" loading="lazy">
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/31 23:59:59">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul>
                            </a>
                            <a class="product-title" href="#">Beach Cap</a>
                            <p class="sale-price">$13<span>$42</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-success">New</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/5.png') }}" alt="Wooden Sofa" loading="lazy">
                            </a>
                            <a class="product-title" href="#">Wooden Sofa</a>
                            <p class="sale-price">$74<span>$99</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-success">Sale</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/6.png') }}" alt="Roof Lamp" loading="lazy">
                            </a>
                            <a class="product-title" href="#">Roof Lamp</a>
                            <p class="sale-price">$99<span>$113</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-danger">-18%</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/9.png') }}" alt="Sneaker Shoes" loading="lazy">
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/23 00:21:29">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul>
                            </a>
                            <a class="product-title" href="#">Sneaker Shoes</a>
                            <p class="sale-price">$87<span>$92</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-danger">-11%</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/8.png') }}" alt="Wooden Chair" loading="lazy">
                            </a>
                            <a class="product-title" href="#">Wooden Chair</a>
                            <p class="sale-price">$21<span>$25</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-warning">On Sale</span>
                            <a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <a class="product-thumbnail d-block" href="#">
                                <img class="mb-2" src="{{ asset('frontend/img/product/4.png') }}" alt="Polo Shirts" loading="lazy">
                            </a>
                            <a class="product-title" href="#">Polo Shirts</a>
                            <p class="sale-price">$38<span>$41</span></p>
                            <div class="product-rating">
                                <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                            </div>
                            <a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

