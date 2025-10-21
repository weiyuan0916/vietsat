@props(['products' => [], 'endDate' => '2025/12/31 14:21:59'])

<!-- Flash Sale Slide -->
<div class="flash-sale-wrapper">
    <div class="container">
        <div class="section-heading d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <h6 class="d-flex align-items-center rtl-flex-d-row-r">
                <i class="ti ti-bolt-lightning me-1 text-danger lni-flashing-effect"></i>Cyclone Offer
            </h6>
            <!-- Please use event time this format: YYYY/MM/DD hh:mm:ss -->
            <ul class="sales-end-timer ps-0 d-flex align-items-center rtl-flex-d-row-r" data-countdown="{{ $endDate }}">
                <li><span class="days">0</span>d</li>
                <li><span class="hours">0</span>h</li>
                <li><span class="minutes">0</span>m</li>
                <li><span class="seconds">0</span>s</li>
            </ul>
        </div>
        <!-- Flash Sale Slide -->
        <div class="flash-sale-slide owl-carousel">
            @forelse($products as $product)
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body">
                        <a href="{{ route('products.show', $product['id'] ?? 1) }}">
                            <img src="{{ $product['image'] ?? asset('frontend/img/product/1.png') }}" 
                                 alt="{{ $product['name'] ?? 'Product' }}"
                                 loading="lazy">
                            <span class="product-title">{{ $product['name'] ?? 'Product Name' }}</span>
                            <p class="sale-price">
                                ${{ number_format($product['sale_price'] ?? 7.99, 2) }}
                                <span>${{ number_format($product['price'] ?? 15, 2) }}</span>
                            </p>
                            <span class="progress-title">{{ $product['sold_percentage'] ?? 33 }}% Sold</span>
                            <!-- Progress Bar -->
                            <div class="progress">
                                <div class="progress-bar {{ ($product['sold_percentage'] ?? 33) >= 99 ? 'bg-danger' : '' }}" 
                                     role="progressbar" 
                                     style="width: {{ $product['sold_percentage'] ?? 33 }}%" 
                                     aria-valuenow="{{ $product['sold_percentage'] ?? 33 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @empty
                <!-- Default Flash Sale Cards -->
                <div class="card flash-sale-card">
                    <div class="card-body">
                        <a href="#">
                            <img src="{{ asset('frontend/img/product/1.png') }}" alt="Black Table Lamp" loading="lazy">
                            <span class="product-title">Black Table Lamp</span>
                            <p class="sale-price">$7.99<span>$15</span></p>
                            <span class="progress-title">33% Sold</span>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card flash-sale-card">
                    <div class="card-body">
                        <a href="#">
                            <img src="{{ asset('frontend/img/product/2.png') }}" alt="Modern Sofa" loading="lazy">
                            <span class="product-title">Modern Sofa</span>
                            <p class="sale-price">$14<span>$21</span></p>
                            <span class="progress-title">57% Sold</span>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 57%" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card flash-sale-card">
                    <div class="card-body">
                        <a href="#">
                            <img src="{{ asset('frontend/img/product/3.png') }}" alt="Classic Garden Chair" loading="lazy">
                            <span class="product-title">Classic Garden Chair</span>
                            <p class="sale-price">$36<span>$49</span></p>
                            <span class="progress-title">99% Sold</span>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

