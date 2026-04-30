@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy | Thanh Toán QR Nhanh')
@section('meta_description', 'Thanh toán nhanh tại Tiệm Nhà Duy với mã QR tự động, an toàn và tiện lợi cho đơn hàng nông sản sạch.')
@section('meta_keywords', 'thanh toán tiệm nhà duy, QR chuyển khoản, cà phê robusta chư sê gia lai, nông sản sạch')
@section('og_image', asset('rosta/images/favicon_io/android-chrome-512x512.png'))
@section('canonical_url', route('book-table'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Thanh toán QR nhanh",
    "description": "Thanh toán nhanh tại Tiệm Nhà Duy với mã QR tự động, an toàn và tiện lợi cho đơn hàng nông sản sạch.",
    "url": "{{ route('book-table') }}",
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
            "name": "Thanh toán QR nhanh",
            "item": "{{ route('book-table') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    <div class="page-loading-overlay" id="pageLoadingOverlay" style="display:none;">
        <div class="overlay-loading-box">
            <span class="overlay-loading-icon" aria-hidden="true"></span>
            <p>Vui lòng đợi...</p>
        </div>
    </div>
    <style>
        #MainContent {
            padding-top: 120px;
            padding-bottom: 90px;
        }
        .container.text-only {
            max-width: 800px;
            color: #000;
        }
        .payment-content {
            max-width: 760px;
            margin: 0 auto;
            text-align: center;
        }
        .text-only h1 {
            font-size: 7vw;
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: 400;
            margin-bottom: 20px;
            margin-top: 40px;
            font-family: "Room-205";
            font-size: 50px;
        }
        .qr-result .section-title {
            text-align: center;
        }
        .payment-form-wrap {
            border-top: 1px solid #eee;
            padding-top: 24px;
            margin-top: 18px;
        }
        .input-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
            text-align: left;
        }
        #paymentForm .form-control {
            border: 1px solid #ddd;
            min-height: 56px;
            border-radius: 0;
            padding: 10px 18px;
            font-size: 16px;
        }
        .input-hint {
            margin: 8px 0 0;
            font-size: 13px;
            color: #6f6f6f;
            text-align: left;
        }
        .qr-submit-btn {
            min-width: 190px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
        }
        .btn-loader {
            width: 17px;
            height: 17px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #fff;
            display: none;
            animation: spin 0.8s linear infinite;
        }
        .qr-submit-btn.is-loading {
            opacity: 0.85;
            pointer-events: none;
        }
        .qr-submit-btn.is-loading .btn-loader {
            display: inline-block;
        }
        .page-loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 16, 16, 0.55);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }
        .overlay-loading-box {
            min-width: 220px;
            padding: 18px 22px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.2);
        }
        .overlay-loading-box p {
            margin: 0;
            font-size: 15px;
            font-weight: 500;
            color: #222;
        }
        .overlay-loading-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-top-color: #111;
            display: inline-block;
            animation: spin 0.8s linear infinite;
        }
        .qr-result {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 30px;
            text-align: left;
        }
        .qr-result-card {
            border: 1px solid #ececec;
            border-radius: 18px;
            background: #fff;
            padding: 26px;
            box-shadow: 0 16px 36px rgba(20, 23, 27, 0.08);
            max-width: 560px;
            margin: 0 auto;
        }
        .qr-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }
        .qr-card-title {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            color: #111;
        }
        .qr-card-badge {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f5132;
            background: #d1e7dd;
            padding: 7px 10px;
            border-radius: 999px;
            line-height: 1;
        }
        .qr-layout {
            display: grid;
            grid-template-columns: minmax(220px, 360px) minmax(180px, 1fr);
            gap: 18px;
            align-items: stretch;
            margin-bottom: 10px;
        }
        .qr-box {
            max-width: 360px;
            margin: 0 auto;
            padding: 12px;
            border: 1px solid #ebebeb;
            border-radius: 12px;
            background: #fafafa;
        }
        .qr-box img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .qr-side-meta {
            display: flex;
            flex-direction: column;
            gap: 14px;
            justify-content: center;
        }
        .countdown-wrap {
            border: 1px dashed #d9d9d9;
            border-radius: 12px;
            padding: 14px 16px;
            background: #fbfbfb;
        }
        .countdown-label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .countdown-value {
            font-size: 30px;
            line-height: 1;
            color: #111;
            letter-spacing: 0.02em;
        }
        .btn-copy {
            width: 100%;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .copy-action-wrap {
            position: relative;
            width: 100%;
        }
        .copy-tooltip {
            position: absolute;
            left: 50%;
            bottom: calc(100% + 10px);
            transform: translateX(-50%) translateY(4px);
            background: #198754;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 999px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .copy-tooltip.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .qr-meta-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .qr-meta-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid #f0f0f0;
            padding-top: 10px;
        }
        .meta-label {
            font-size: 14px;
            color: #666;
        }
        .meta-value {
            font-size: 15px;
            color: #111;
            letter-spacing: 0.02em;
        }
        #paymentMessage {
            min-height: 22px;
        }
        #paymentMessage.text-danger {
            color: #dc3545;
        }
        #paymentMessage.text-success {
            color: #198754;
        }
        @media screen and (max-width: 800px) {
            #MainContent {
                padding-top: 84px;
            }
            .text-only h1 {
                font-size: 50px;
            }
            .section-title {
                font-size: 32px;
            }
            .qr-result-card {
                padding: 18px;
                border-radius: 14px;
            }
            .qr-layout {
                grid-template-columns: 1fr;
            }
            .qr-side-meta {
                width: 100%;
            }
            .qr-card-top {
                flex-direction: column;
                align-items: flex-start;
            }
            .qr-card-title {
                font-size: 20px;
            }
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="MainContent" tabindex="-1">
        <main data-header-color="dark">
            <div class="container text-only">
                <div class="payment-content">
                    <h1>Thanh toán</h1>
                    <div class="blurb">
                        <p>Tạo mã QR thanh toán nhanh trong một bước. Mã QR có giá trị 150.000 VND và tự động đổi sau mỗi 10 phút để đảm bảo giao dịch an toàn.</p>
                    </div>
                    <div class="payment-form-wrap">
                        <form id="paymentForm" action="#" method="POST">
                            <div class="form-group mb-4">
                                <label class="input-label" for="facebookLink">Link Facebook</label>
                                <input type="url" name="facebookLink" class="form-control" id="facebookLink" placeholder="https://facebook.com/..." required>
                                <p class="input-hint">Dùng để tạo nội dung chuyển khoản riêng theo từng thiết bị.</p>
                            </div>
                            <button type="submit" class="btn-default btn-highlighted qr-submit-btn" id="generateQrBtn">
                                <span class="btn-text">Tạo mã QR</span>
                                <span class="btn-loader" id="generateQrLoader" aria-hidden="true"></span>
                            </button>
                            <div id="paymentMessage" class="mt-3"></div>
                        </form>
                    </div>
                    <div class="qr-result" id="qrResult" style="display:none;">
                        <h2 class="section-title">Thông tin thanh toán</h2>
                        <div class="qr-result-card">
                            <div class="qr-card-top">
                                <p class="qr-card-title">VietQR - TPBank</p>
                                <span class="qr-card-badge" id="qrStatusBadge">Đang hoạt động</span>
                            </div>
                            <div class="qr-layout">
                                <div class="qr-box">
                                    <img id="vietQrImage" src="" alt="VietQR Payment">
                                </div>
                                <div class="qr-side-meta">
                                    <div class="countdown-wrap">
                                        <span class="countdown-label">Còn hiệu lực</span>
                                        <strong class="countdown-value" id="expiryCountdownValue">10:00</strong>
                                    </div>
                                    <div class="copy-action-wrap">
                                        <button type="button" class="btn-default btn-copy" id="copyTransferCodeBtn">Sao chép nội dung CK</button>
                                        <span class="copy-tooltip" id="copySuccessTooltip">Đã sao chép</span>
                                    </div>
                                </div>
                            </div>
                            <div class="qr-meta-list">
                                <div class="qr-meta-item">
                                    <span class="meta-label">Số tiền</span>
                                    <strong class="meta-value">150.000 VND</strong>
                                </div>
                                <div class="qr-meta-item">
                                    <span class="meta-label">Nội dung CK</span>
                                    <strong class="meta-value" id="transferCodeValue"></strong>
                                </div>
                                <div class="qr-meta-item">
                                    <span class="meta-label">Hết hạn lúc</span>
                                    <strong class="meta-value" id="expiresAtValue"></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        (function () {
            var paymentForm = document.getElementById("paymentForm");
            var facebookInput = document.getElementById("facebookLink");
            var paymentMessage = document.getElementById("paymentMessage");
            var qrResult = document.getElementById("qrResult");
            var qrImage = document.getElementById("vietQrImage");
            var transferCodeValue = document.getElementById("transferCodeValue");
            var expiresAtValue = document.getElementById("expiresAtValue");
            var qrStatusBadge = document.getElementById("qrStatusBadge");
            var generateQrBtn = document.getElementById("generateQrBtn");
            var pageLoadingOverlay = document.getElementById("pageLoadingOverlay");
            var copyTransferCodeBtn = document.getElementById("copyTransferCodeBtn");
            var copySuccessTooltip = document.getElementById("copySuccessTooltip");
            var expiryCountdownValue = document.getElementById("expiryCountdownValue");
            var amount = 150000;
            var refreshTimer = null;
            var countdownTimer = null;
            var copyTooltipTimer = null;
            var lastFacebookLink = "";
            var latestTransferCode = "";
            var currentExpiresAt = null;
            var deviceSignalsPromise = null;

            function pad(num) {
                return num < 10 ? "0" + num : String(num);
            }

            function getTimeBucket() {
                return Math.floor(Date.now() / 600000);
            }

            function getExpiresAtFromBucket(bucket) {
                return new Date((bucket + 1) * 600000);
            }

            function formatDateTime(date) {
                return pad(date.getHours()) + ":" + pad(date.getMinutes()) + ":" + pad(date.getSeconds()) + " " + pad(date.getDate()) + "/" + pad(date.getMonth() + 1) + "/" + date.getFullYear();
            }

            function setMessage(text, isError) {
                paymentMessage.className = isError ? "text-danger" : "text-success";
                paymentMessage.textContent = text;
            }

            function setSubmitLoading(isLoading) {
                if (isLoading) {
                    generateQrBtn.classList.add("is-loading");
                    generateQrBtn.setAttribute("disabled", "disabled");
                    pageLoadingOverlay.style.display = "flex";
                    document.body.style.overflow = "hidden";
                } else {
                    generateQrBtn.classList.remove("is-loading");
                    generateQrBtn.removeAttribute("disabled");
                    pageLoadingOverlay.style.display = "none";
                    document.body.style.overflow = "";
                }
            }

            function showCopyTooltip() {
                if (copyTooltipTimer) {
                    clearTimeout(copyTooltipTimer);
                }
                copySuccessTooltip.classList.add("show");
                copyTooltipTimer = setTimeout(function () {
                    copySuccessTooltip.classList.remove("show");
                }, 1400);
            }

            function formatCountdown(ms) {
                if (ms <= 0) {
                    return "00:00";
                }
                var totalSeconds = Math.floor(ms / 1000);
                var minutes = Math.floor(totalSeconds / 60);
                var seconds = totalSeconds % 60;
                return pad(minutes) + ":" + pad(seconds);
            }

            function startCountdown() {
                if (countdownTimer) {
                    clearInterval(countdownTimer);
                }
                countdownTimer = setInterval(function () {
                    if (!currentExpiresAt) {
                        return;
                    }
                    var remainingMs = currentExpiresAt.getTime() - Date.now();
                    expiryCountdownValue.textContent = formatCountdown(remainingMs);
                    if (remainingMs <= 0) {
                        qrStatusBadge.textContent = "Đang cập nhật";
                    }
                }, 1000);
            }

            async function fetchJson(url) {
                try {
                    var response = await fetch(url, { cache: "no-store" });
                    if (!response.ok) {
                        return null;
                    }
                    return await response.json();
                } catch (error) {
                    return null;
                }
            }

            async function loadDeviceSignals() {
                var browserSignals = {
                    ua: navigator.userAgent || "unknown",
                    lang: navigator.language || "unknown",
                    platform: navigator.platform || "unknown",
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || "unknown",
                    screen: window.screen ? window.screen.width + "x" + window.screen.height : "unknown"
                };
                var ipData = await fetchJson("https://ipapi.co/json/");
                var ip = ipData && ipData.ip ? ipData.ip : "unknown";
                var country = ipData && ipData.country_name ? ipData.country_name : "unknown";
                var latitude = ipData && ipData.latitude ? ipData.latitude : "";
                var longitude = ipData && ipData.longitude ? ipData.longitude : "";
                var weatherText = "unknown";
                if (latitude && longitude) {
                    var weatherData = await fetchJson("https://api.open-meteo.com/v1/forecast?latitude=" + encodeURIComponent(latitude) + "&longitude=" + encodeURIComponent(longitude) + "&current=temperature_2m,weather_code");
                    if (weatherData && weatherData.current) {
                        weatherText = String(weatherData.current.temperature_2m) + "_" + String(weatherData.current.weather_code);
                    }
                }
                return {
                    ip: ip,
                    country: country,
                    weather: weatherText,
                    ua: browserSignals.ua,
                    lang: browserSignals.lang,
                    platform: browserSignals.platform,
                    timezone: browserSignals.timezone,
                    screen: browserSignals.screen
                };
            }

            function getDeviceSignals() {
                if (!deviceSignalsPromise) {
                    deviceSignalsPromise = loadDeviceSignals();
                }
                return deviceSignalsPromise;
            }

            async function createCodeFromSeed(seed) {
                var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                var output = "";
                if (window.crypto && window.crypto.subtle && window.TextEncoder) {
                    var encoded = new TextEncoder().encode(seed);
                    var digest = await window.crypto.subtle.digest("SHA-256", encoded);
                    var bytes = Array.from(new Uint8Array(digest));
                    for (var i = 0; i < 10; i++) {
                        output += alphabet[bytes[i] % alphabet.length];
                    }
                    return output;
                }
                var hash = 0;
                for (var j = 0; j < seed.length; j++) {
                    hash = ((hash << 5) - hash) + seed.charCodeAt(j);
                    hash |= 0;
                }
                var num = Math.abs(hash) + 123456789;
                for (var k = 0; k < 10; k++) {
                    num = (num * 1103515245 + 12345) & 0x7fffffff;
                    output += alphabet[num % alphabet.length];
                }
                return output;
            }

            async function generateTransferCode(facebookLink, bucket) {
                var signals = await getDeviceSignals();
                var seed = [
                    facebookLink.trim(),
                    signals.ip,
                    signals.country,
                    signals.weather,
                    signals.ua,
                    signals.lang,
                    signals.platform,
                    signals.timezone,
                    signals.screen,
                    String(bucket)
                ].join("|");
                return createCodeFromSeed(seed);
            }

            function buildQrUrl(code) {
                var url = new URL("https://img.vietqr.io/image/TPB-03738073001-compact2.png");
                url.searchParams.set("amount", String(amount));
                url.searchParams.set("addInfo", "ORD" + code);
                url.searchParams.set("accountName", "NGUYEN VAN DUY");
                return url.toString();
            }

            async function renderQr(facebookLink, autoRefresh) {
                var bucket = getTimeBucket();
                var code = await generateTransferCode(facebookLink, bucket);
                var expiresAt = getExpiresAtFromBucket(bucket);
                qrImage.src = buildQrUrl(code);
                latestTransferCode = "ORD" + code;
                currentExpiresAt = expiresAt;
                transferCodeValue.textContent = latestTransferCode;
                expiresAtValue.textContent = formatDateTime(expiresAt);
                expiryCountdownValue.textContent = formatCountdown(expiresAt.getTime() - Date.now());
                qrResult.style.display = "block";
                startCountdown();
                if (autoRefresh) {
                    qrStatusBadge.textContent = "Đã làm mới";
                    paymentMessage.className = "";
                    paymentMessage.textContent = "";
                } else {
                    qrStatusBadge.textContent = "Đang hoạt động";
                    paymentMessage.className = "";
                    paymentMessage.textContent = "";
                }
            }

            function scheduleRefresh() {
                if (refreshTimer) {
                    clearTimeout(refreshTimer);
                }
                var now = Date.now();
                var nextTick = (Math.floor(now / 600000) + 1) * 600000 + 300;
                refreshTimer = setTimeout(async function () {
                    if (lastFacebookLink) {
                        await renderQr(lastFacebookLink, true);
                    }
                    scheduleRefresh();
                }, Math.max(1000, nextTick - now));
            }

            paymentForm.addEventListener("submit", async function (event) {
                event.preventDefault();
                var facebookLink = facebookInput.value.trim();
                if (!facebookLink) {
                    setMessage("Vui lòng nhập link Facebook.", true);
                    qrResult.style.display = "none";
                    return;
                }
                var isFacebook = /^https?:\/\/(www\.)?(facebook\.com|fb\.com)\//i.test(facebookLink);
                if (!isFacebook) {
                    setMessage("Link phải bắt đầu bằng facebook.com hoặc fb.com.", true);
                    qrResult.style.display = "none";
                    return;
                }
                try {
                    lastFacebookLink = facebookLink;
                    setSubmitLoading(true);
                    await renderQr(facebookLink, false);
                } catch (error) {
                    setMessage("Không thể tạo mã QR lúc này. Vui lòng thử lại.", true);
                    qrResult.style.display = "none";
                } finally {
                    setSubmitLoading(false);
                }
            });

            copyTransferCodeBtn.addEventListener("click", async function () {
                if (!latestTransferCode) {
                    setMessage("Chưa có nội dung chuyển khoản để sao chép.", true);
                    return;
                }
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        await navigator.clipboard.writeText(latestTransferCode);
                    } else {
                        var hiddenInput = document.createElement("input");
                        hiddenInput.value = latestTransferCode;
                        document.body.appendChild(hiddenInput);
                        hiddenInput.select();
                        document.execCommand("copy");
                        document.body.removeChild(hiddenInput);
                    }
                    paymentMessage.className = "";
                    paymentMessage.textContent = "";
                    showCopyTooltip();
                } catch (error) {
                    setMessage("Không thể sao chép. Vui lòng sao chép thủ công.", true);
                }
            });

            scheduleRefresh();
        })();
    </script>
@endsection
