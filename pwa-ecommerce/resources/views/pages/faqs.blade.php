@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy | Câu Hỏi Thường Gặp')
@section('meta_description', 'Tổng hợp câu hỏi thường gặp về đặt hàng, vận chuyển, sản phẩm và hỗ trợ khách hàng tại Tiệm Nhà Duy.')
@section('og_image', asset('rosta/images/icon-sub-heading.svg'))
@section('canonical_url', route('faqs'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "name": "Câu hỏi thường gặp",
    "description": "Tổng hợp câu hỏi thường gặp về đặt hàng, vận chuyển, sản phẩm và hỗ trợ khách hàng tại Tiệm Nhà Duy.",
    "url": "{{ route('faqs') }}",
    "inLanguage": "vi-VN"
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Trang chủ",
            "item": "{{ route('home') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Câu hỏi thường gặp",
            "item": "{{ route('faqs') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    <style>
        #MainContent {padding-top: 110px;}
        .container.text-only {max-width: 800px; color: #000; padding-bottom: 90px;}
        .text-only h1 {font-size: 7vw; margin-bottom: 20px;}
        .a-faq {border-top: 1px solid #eee; padding: 18px 0 8px; cursor: pointer;}
        .a-faq h3 {text-transform: uppercase; font-weight: 600; padding-bottom: 8px; position: relative; padding-right: 50px; line-height: 25px; font-size: 20px;}
        .a-faq > h3:before {content: "✕"; position: absolute; right: 30px; top: 0; transform: rotate(45deg); transition: transform 0.2s ease;}
        .a-faq.active > h3:before {transform: rotate(0deg);}
        .a-faq .blurb p {margin-bottom: 10px;}
        .section-title {font-weight: 400; margin-bottom: 20px; margin-top: 40px; font-family: "Room-205"; font-size: 50px;}
        @media screen and (max-width: 800px) {
            #MainContent {padding-top: 80px;}
            .text-only h1 {font-size: 50px;}
            .section-title {font-size: 32px;}
        }
    </style>
    <div id="MainContent" tabindex="-1">
        <main data-header-color="dark">
            <div class="container text-only">
                <div>
                    <h1>FAQs</h1>
                    <div class="blurb">
                        <p>Here are some Frequently Asked Questions that come our way. If you do not see one that helps, please message us and we will try to help soon.</p>
                    </div>
                    <h2 class="section-title">ONLINE ORDERING AND SHIPPING ISSUES</h2>
                    <div class="a-faq">
                        <h3>Why was my card charged twice?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Sometimes there can be an error in processing cards online. This is likely a pending pre-authorization and it should disappear from your credit card transactions within a couple of days.</p>
                            <p>Still experiencing an issue? Please email us at <em><a href="mailto:info@onyxcoffeelab.com">info@onyxcoffeelab.com</a></em> with your name and order number so we can look into it.</p>
                        </div>
                    </div>
                    <div class="a-faq">
                        <h3>Why is my shipment delayed?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Couriers can experience influxes in orders that impact expected shipment timelines. Please use your tracking number and sign up for courier updates.</p>
                            <p>Still experiencing an issue? Email us at <em><a href="mailto:info@onyxcoffeelab.com">info@onyxcoffeelab.com</a></em> with your name and order number and we will help resolve it.</p>
                        </div>
                    </div>
                    <div class="a-faq">
                        <h3>Where is my order confirmation?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Sometimes confirmations land in junk folders, or checkout providers can route notifications by SMS.</p>
                            <p>If you still cannot find it, email <em><a href="mailto:info@onyxcoffeelab.com">info@onyxcoffeelab.com</a></em> with the name on your order and we can send your confirmation details.</p>
                        </div>
                    </div>
                    <h2 class="section-title">COFFEE QUESTIONS</h2>
                    <div class="a-faq">
                        <h3>When do you roast?</h3>
                        <div class="blurb" style="display:none;">
                            <p>We roast Monday through Friday. Most orders are shipped or delivered within 24 hours of roasting.</p>
                        </div>
                    </div>
                    <div class="a-faq">
                        <h3>How do I brew my coffee?</h3>
                        <div class="blurb" style="display:none;">
                            <p>You can check each coffee product page for filter and espresso brew videos and detailed instructions.</p>
                        </div>
                    </div>
                    <h2 class="section-title">CAFE QUESTIONS</h2>
                    <div class="a-faq">
                        <h3>Where are your cafes located?</h3>
                        <div class="blurb" style="display:none;">
                            <p>We currently have 4 cafe locations in Northwest Arkansas. Visit the locations page for full details.</p>
                        </div>
                    </div>
                    <div class="a-faq">
                        <h3>Do you serve food?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Yes, we offer pastries, breakfast items, baguette sandwiches, and grab-and-go options made fresh daily.</p>
                        </div>
                    </div>
                    <h2 class="section-title">SUBSCRIPTIONS</h2>
                    <div class="a-faq">
                        <h3>When will my subscription ship?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Roaster's Choice subscriptions usually ship Mondays and Tuesdays. Other subscriptions process on the same weekday as your initial order.</p>
                        </div>
                    </div>
                    <div class="a-faq">
                        <h3>How do I cancel my subscription?</h3>
                        <div class="blurb" style="display:none;">
                            <p>Log in to your account, open subscription management, select your subscription details, and choose cancel subscription.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        $(".a-faq").click(function () {
            $(this).toggleClass("active");
            $(this).find(".blurb").slideToggle();
        });
    </script>
@endsection
