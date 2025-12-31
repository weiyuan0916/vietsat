{{-- Localized NeuraPen Hero Section --}}
<section class="hero-section" data-framer-name="hero">
    <div class="container-fluid px-0">
        <div class="hero-background">
            {{-- Animated background elements --}}
            <div class="hero-bg-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>

        <div class="hero-content-wrapper">
            <div class="container">
                <div class="row align-items-center min-vh-100">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            {{-- Main heading with gradient --}}
                            <h1 class="hero-title" data-framer-name="The Smartest Way to Digitize">
                                {{ $texts['hero']['title'] }}
                            </h1>

                            {{-- Subtitle with animation --}}
                            <div class="hero-subtitle-wrapper">
                                <h2 class="hero-subtitle" data-framer-name="Handwritten Notes">
                                    {{ $texts['hero']['subtitle'] }}
                                </h2>
                                <div class="subtitle-decoration"></div>
                            </div>

                            {{-- Description --}}
                            <p class="hero-description" data-framer-name="Hero Description">
                                {{ $texts['hero']['description'] }}
                            </p>

                            {{-- CTA Buttons --}}
                            <div class="hero-cta-buttons">
                                <a href="#features" class="btn btn-primary btn-lg hero-cta-main">
                                    <span>{{ $texts['hero']['cta_button'] }}</span>
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                                <a href="#demo" class="btn btn-outline-light btn-lg hero-cta-secondary">
                                    <i class="fas fa-play-circle me-2"></i>
                                    <span>Watch Demo</span>
                                </a>
                            </div>

                            {{-- Trust indicators --}}
                            <div class="hero-trust">
                                <div class="trust-users">
                                    <div class="user-avatars">
                                        <img src="https://via.placeholder.com/32x32/007bff/ffffff?text=U1" alt="User 1" class="avatar">
                                        <img src="https://via.placeholder.com/32x32/28a745/ffffff?text=U2" alt="User 2" class="avatar">
                                        <img src="https://via.placeholder.com/32x32/dc3545/ffffff?text=U3" alt="User 3" class="avatar">
                                        <span class="avatar-more">+2.5k</span>
                                    </div>
                                    <div class="trust-text">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span>Loved by 2,500+ users</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="hero-visual">
                            {{-- Main visual element --}}
                            <div class="hero-device-mockup">
                                <div class="device-screen">
                                    <div class="screen-content">
                                        {{-- Animated writing effect --}}
                                        <div class="writing-animation">
                                            <svg class="writing-svg" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M50,150 Q100,100 150,150 T250,150 T350,150" stroke="#007bff" stroke-width="3" fill="none" stroke-dasharray="1000" stroke-dashoffset="1000" class="writing-path">
                                                    <animate attributeName="stroke-dashoffset" from="1000" to="0" dur="3s" repeatCount="indefinite"/>
                                                </path>
                                            </svg>
                                        </div>

                                        {{-- Digital text conversion effect --}}
                                        <div class="digital-text">
                                            <div class="text-line line-1">Handwritten Notes</div>
                                            <div class="text-line line-2">→</div>
                                            <div class="text-line line-3">Digital Searchable Text</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="device-frame"></div>
                            </div>

                            {{-- Floating elements --}}
                            <div class="floating-elements">
                                <div class="floating-card card-1">
                                    <i class="fas fa-brain"></i>
                                    <span>AI-Powered</span>
                                </div>
                                <div class="floating-card card-2">
                                    <i class="fas fa-sync"></i>
                                    <span>Auto-Sync</span>
                                </div>
                                <div class="floating-card card-3">
                                    <i class="fas fa-search"></i>
                                    <span>Searchable</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hero-section {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: hidden;
    color: white;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.hero-bg-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s ease-in-out infinite;
}

.shape-1 {
    width: 300px;
    height: 300px;
    top: 10%;
    right: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 200px;
    height: 200px;
    top: 60%;
    left: 10%;
    animation-delay: 2s;
}

.shape-3 {
    width: 150px;
    height: 150px;
    bottom: 20%;
    right: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.hero-content-wrapper {
    position: relative;
    z-index: 2;
    padding: 100px 0;
}

.hero-content {
    max-width: 600px;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 1s ease-out;
}

.hero-subtitle-wrapper {
    position: relative;
    margin-bottom: 2rem;
}

.hero-subtitle {
    font-size: clamp(1.5rem, 3vw, 2.2rem);
    font-weight: 600;
    color: #f0f8ff;
    margin-bottom: 1rem;
    animation: fadeInUp 1s ease-out 0.2s both;
}

.subtitle-decoration {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #ff6b6b, #ee5a24);
    border-radius: 2px;
    animation: expandWidth 1s ease-out 0.4s both;
}

.hero-description {
    font-size: 1.25rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2.5rem;
    animation: fadeInUp 1s ease-out 0.4s both;
}

.hero-cta-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
    animation: fadeInUp 1s ease-out 0.6s both;
    flex-wrap: wrap;
}

.hero-cta-main {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    border: none;
    padding: 16px 32px;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
}

.hero-cta-main:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
}

.hero-cta-secondary {
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 14px 30px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.hero-cta-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

.hero-trust {
    animation: fadeInUp 1s ease-out 0.8s both;
}

.trust-users {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatars {
    display: flex;
    align-items: center;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.2);
    margin-left: -10px;
}

.avatar:first-child {
    margin-left: 0;
}

.avatar-more {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 3px solid rgba(255, 255, 255, 0.2);
    margin-left: -10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.trust-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stars {
    color: #ffd700;
}

.stars i {
    margin-right: 2px;
}

.trust-text span {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.hero-visual {
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-device-mockup {
    position: relative;
    width: 300px;
    height: 600px;
    animation: deviceFloat 4s ease-in-out infinite;
}

.device-screen {
    position: absolute;
    top: 20px;
    left: 15px;
    right: 15px;
    bottom: 80px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.5);
}

.screen-content {
    padding: 30px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.writing-animation {
    margin-bottom: 2rem;
}

.writing-svg {
    width: 200px;
    height: 150px;
}

.writing-path {
    filter: drop-shadow(0 0 10px rgba(0, 123, 255, 0.5));
}

.digital-text {
    text-align: center;
    font-family: 'Courier New', monospace;
}

.text-line {
    font-size: 1.1rem;
    margin: 0.5rem 0;
    color: #00d4ff;
    font-weight: 500;
}

.text-line.line-2 {
    color: #ff6b6b;
    font-size: 1.5rem;
    margin: 1rem 0;
}

.text-line.line-3 {
    color: #00ff88;
}

.device-frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 8px solid #333;
    border-radius: 30px;
    pointer-events: none;
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.floating-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    animation: cardFloat 3s ease-in-out infinite;
}

.floating-card i {
    color: #007bff;
}

.card-1 {
    top: 10%;
    right: -20px;
    animation-delay: 0s;
}

.card-2 {
    top: 40%;
    left: -20px;
    animation-delay: 1s;
}

.card-3 {
    bottom: 20%;
    right: -20px;
    animation-delay: 2s;
}

@keyframes deviceFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes cardFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-5px) rotate(1deg); }
    66% { transform: translateY(5px) rotate(-1deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes expandWidth {
    from { width: 0; }
    to { width: 60px; }
}

@media (max-width: 768px) {
    .hero-section {
        text-align: center;
        padding: 50px 0;
    }

    .hero-content {
        max-width: 100%;
        margin-bottom: 3rem;
    }

    .hero-cta-buttons {
        justify-content: center;
    }

    .hero-visual {
        display: none;
    }

    .trust-users {
        justify-content: center;
    }
}
</style>
