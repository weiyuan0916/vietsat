<div class="flash-sale-wrapper">
    <div class="container">
        <div class="section-heading d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <h6 class="d-flex align-items-center rtl-flex-d-row-r"><i class="ti ti-bolt-lightning me-1 text-danger lni-flashing-effect"></i>Cyclone Offer</h6>
            <!-- Please use event time this format: YYYY/MM/DD hh:mm:ss -->
            <ul class="sales-end-timer ps-0 d-flex align-items-center rtl-flex-d-row-r" data-countdown="2025/12/31 14:21:59">
                <li><span class="days">0</span>d</li>
                <li><span class="hours">0</span>h</li>
                <li><span class="minutes">0</span>m</li>
                <li><span class="seconds">0</span>s</li>
            </ul>
        </div>
        <!-- Flash Sale Slide-->
        <div class="flash-sale-slide owl-carousel">
            @for($i=1; $i<=8; $i++)
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img style="max-width: 127px; max-height: 95px" src="{{ "https://scontent.fsgn2-4.fna.fbcdn.net/v/t39.30808-6/543426029_738700995826691_3730733313910458830_n.jpg?stp=cp6_dst-jpg_tt6&_nc_cat=101&ccb=1-7&_nc_sid=aa7b47&_nc_eui2=AeGp_LHB13qFgoyEb1Oz0f9_Nz1K_ZesS743PUr9l6xLvlUfehS5hG8br-LKUImFKNvxuG8xR66J4iPYDIK9s2AG&_nc_ohc=lRejanzIqJgQ7kNvwG9CREy&_nc_oc=AdkUM5D4Izvlxs7lk7ll6uSwIp3PuRmKJi4IRXFNZKb_TRSmKD8qgEmANVxtZmUBnob6jftn33_Yl49cdHyGUfFP&_nc_zt=23&_nc_ht=scontent.fsgn2-4.fna&_nc_gid=0HgTZ45m9W5zBFPSYxRysg&oh=00_AfZnZMc3P3h_uRgrW2gGmCP_CWzdZ53RlXY_PhsdP6sAJA&oe=68C3C5DD" ?? asset('frontend/img/product/1.png') }}" alt=""><span class="product-title">Black Table Lamp</span>
                            <p class="sale-price">$7.99<span class="real-price">$15</span></p><span class="progress-title">33% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>

                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="{{ asset('frontend/img/product/3.png') }}" alt=""><span class="product-title">Classic Garden Chair</span>
                            <p class="sale-price">$36<span class="real-price">$49</span></p><span class="progress-title">99% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>

            @endfor
        </div>
    </div>
</div>
