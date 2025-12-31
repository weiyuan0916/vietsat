{{-- Localized Hero Component - Example of how to use localization --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    {{-- Main Title --}}
                    <h1 class="hero-title">
                        {{ $texts['hero']['title'] ?? 'The Smartest Way to Digitize' }}
                    </h1>

                    {{-- Subtitle with animation --}}
                    <div class="hero-subtitle">
                        <h2 class="animated-text">
                            {{ $texts['hero']['subtitle'] ?? 'Handwritten Notes' }}
                        </h2>
                    </div>

                    {{-- Description --}}
                    <p class="hero-description">
                        {{ $texts['hero']['description'] ?? 'Transform your handwritten notes into digital, searchable, and organized text. Designed for students, professionals, and creatives.' }}
                    </p>

                    {{-- CTA Button --}}
                    <a href="#features" class="btn btn-primary btn-lg hero-cta">
                        {{ $texts['hero']['cta_button'] ?? 'Buy Template' }}
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-image">
                    {{-- Placeholder for hero image --}}
                    <img src="{{ asset('img/hero-image.png') }}" alt="Hero Image" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hero-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-subtitle h2 {
    font-size: 2.5rem;
    font-weight: 600;
    margin-bottom: 2rem;
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-description {
    font-size: 1.2rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-cta {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.hero-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle h2 {
        font-size: 2rem;
    }

    .hero-section {
        padding: 50px 0;
        text-align: center;
    }
}
</style>
