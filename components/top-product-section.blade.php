<div class="top-products-area py-3">
    <div class="container">
        <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Top Products</h6><a class="btn btn-sm btn-light" href="shop-grid.html">View all<i class="ms-1 ti ti-arrow-right"></i></a>
        </div>
        <div class="row g-2">
            @for($i=1 ; $i<=2 ; $i++)
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-warning">Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" style="max-width: 171px; height: 171px" src="{{ "https://scontent.fsgn2-4.fna.fbcdn.net/v/t39.30808-6/543426029_738700995826691_3730733313910458830_n.jpg?stp=cp6_dst-jpg_tt6&_nc_cat=101&ccb=1-7&_nc_sid=aa7b47&_nc_eui2=AeGp_LHB13qFgoyEb1Oz0f9_Nz1K_ZesS743PUr9l6xLvlUfehS5hG8br-LKUImFKNvxuG8xR66J4iPYDIK9s2AG&_nc_ohc=lRejanzIqJgQ7kNvwG9CREy&_nc_oc=AdkUM5D4Izvlxs7lk7ll6uSwIp3PuRmKJi4IRXFNZKb_TRSmKD8qgEmANVxtZmUBnob6jftn33_Yl49cdHyGUfFP&_nc_zt=23&_nc_ht=scontent.fsgn2-4.fna&_nc_gid=0HgTZ45m9W5zBFPSYxRysg&oh=00_AfZnZMc3P3h_uRgrW2gGmCP_CWzdZ53RlXY_PhsdP6sAJA&oe=68C3C5DD" ?? asset('frontend/img/product/11.png') }}" alt="">
                                <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/31 23:59:59">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Beach Cap</a>
                            <!-- Product Price -->
                            <p class="sale-price">$13<span>$42</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-success">Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="{{ asset('frontend/img/product/6.png') }}" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Roof Lamp</a>
                            <!-- Product Price -->
                            <p class="sale-price">$99<span>$113</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-danger">-18%</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="{{ asset('frontend/img/product/9.png') }}" alt="">
                                <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/23 00:21:29">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Sneaker Shoes</a>
                            <!-- Product Price -->
                            <p class="sale-price">$87<span>$92</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-warning">On Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="{{ asset('frontend/img/product/4.png') }}" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Polo Shirts</a>
                            <!-- Product Price -->
                            <p class="sale-price">$38<span>$41</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
            @endfor
            <!-- Product Card -->

        </div>
    </div>
</div>
