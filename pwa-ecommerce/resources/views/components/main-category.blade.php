@props(['categories' => []])

<!-- Product Categories -->
<div class="product-catagories-wrapper py-3">
    <div class="container">
        <div class="row g-2 rtl-flex-d-row-r">
            @forelse($categories as $category)
                <!-- Category Card -->
                <div class="col-3">
                    <div class="card catagory-card {{ $category['active'] ?? false ? 'active' : '' }}">
                        <div class="card-body px-2">
                            <a href="{{ route('categories.show', $category['slug'] ?? 1) }}">
                                <img src="{{ $category['icon'] ?? asset('frontend/img/core-img/woman-clothes.png') }}" 
                                     alt="{{ $category['name'] ?? 'Category' }}"
                                     loading="lazy">
                                <span>{{ $category['name'] ?? 'Category' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Default Category Cards -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/woman-clothes.png') }}" alt="Women's Fashion" loading="lazy">
                                <span>Women's Fashion</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/grocery.png') }}" alt="Groceries & Pets" loading="lazy">
                                <span>Groceries &amp; Pets</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/shampoo.png') }}" alt="Health & Beauty" loading="lazy">
                                <span>Health &amp; Beauty</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/rowboat.png') }}" alt="Sports & Outdoors" loading="lazy">
                                <span>Sports &amp; Outdoors</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/tv-table.png') }}" alt="Home Appliance" loading="lazy">
                                <span>Home Appliance</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/beach.png') }}" alt="Tour & Travels" loading="lazy">
                                <span>Tour &amp; Travels</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/baby-products.png') }}" alt="Mother & Baby" loading="lazy">
                                <span>Mother &amp; Baby</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card catagory-card active">
                        <div class="card-body px-2">
                            <a href="#">
                                <img src="{{ asset('frontend/img/core-img/price-tag.png') }}" alt="Clearance Sale" loading="lazy">
                                <span>Clearance Sale</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

