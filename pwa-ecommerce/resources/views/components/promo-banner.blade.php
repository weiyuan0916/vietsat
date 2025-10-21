@props(['title' => "20% discount on women's care items.", 'buttonText' => 'Grab this offer', 'link' => '#', 'image' => null])

<!-- CTA Area -->
<div class="container">
    <div class="cta-text dir-rtl p-4 p-lg-5">
        <div class="row">
            <div class="col-9">
                <h5 class="text-white">{{ $title }}</h5>
                <a class="btn btn-primary" href="{{ $link }}">{{ $buttonText }}</a>
            </div>
        </div>
        <img src="{{ $image ?? asset('frontend/img/bg-img/make-up.png') }}" alt="Promo Banner">
    </div>
</div>

