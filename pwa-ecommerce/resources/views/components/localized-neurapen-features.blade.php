{{-- Localized NeuraPen Features Section --}}
<section class="features-section" data-framer-name="neura bento" id="features">
    <div class="container">
        {{-- Section Header --}}
        <div class="section-header text-center mb-5">
            <h2 class="section-title" data-framer-name="Tiệm Nhà Duy—Beyond">
                {{ __('home.footer.brand_name') }}—Beyond Handwriting
            </h2>
            <p class="section-subtitle">
                Discover the powerful features that make digital note-taking effortless
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="features-grid">
            {{-- Feature 1: AI-Powered Recognition --}}
            <div class="feature-card" data-framer-name="Handwriting Recognition">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">AI-Powered Recognition</h3>
                    <p class="feature-description">
                        Advanced artificial intelligence converts your handwritten notes into accurate, searchable digital text with remarkable precision.
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>

            {{-- Feature 2: Smart Organization --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Smart Organization</h3>
                    <p class="feature-description">
                        Automatically categorize and tag your notes, making it easy to find exactly what you need when you need it.
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>

            {{-- Feature 3: Multi-Platform Sync --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Seamless Sync</h3>
                    <p class="feature-description">
                        {{ $texts['features']['sync_with_apps'] }}
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>

            {{-- Feature 4: Advanced Search --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Advanced Search</h3>
                    <p class="feature-description">
                        Powerful search capabilities help you find notes instantly using keywords, dates, or even handwriting patterns.
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>

            {{-- Feature 5: Real-time Processing --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Real-time Processing</h3>
                    <p class="feature-description">
                        Watch your handwritten notes transform into digital text in real-time as you write, with instant synchronization.
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>

            {{-- Feature 6: Privacy First --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Privacy First</h3>
                    <p class="feature-description">
                        Your notes are encrypted and processed locally whenever possible, ensuring your privacy and data security.
                    </p>
                </div>
                <div class="feature-decoration">
                    <div class="decoration-line"></div>
                </div>
            </div>
        </div>

        {{-- Feature Highlights --}}
        <div class="feature-highlights mt-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="highlight-content">
                            <h4>{{ $texts['features']['ai_summaries_extraction'] }}</h4>
                            <p>Automatically generate summaries and extract key action items from your meeting notes and lectures.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="highlight-content">
                            <h4>Save Hours Daily</h4>
                            <p>Transform hours of manual note-taking and organization into minutes with intelligent automation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Call to Action --}}
        <div class="features-cta text-center mt-5">
            <h3>Ready to Transform Your Note-Taking?</h3>
            <p>Join thousands of students and professionals who have revolutionized their workflow</p>
            <a href="#pricing" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket me-2"></i>
                Get Started Today
            </a>
        </div>
    </div>
</section>

<style>
.features-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    position: relative;
    overflow: hidden;
}

.features-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23000000" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="%23000000" opacity="0.03"/><circle cx="50" cy="10" r="0.5" fill="%23000000" opacity="0.02"/><circle cx="10" cy="50" r="0.5" fill="%23000000" opacity="0.02"/><circle cx="90" cy="30" r="0.5" fill="%23000000" opacity="0.02"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.section-header {
    margin-bottom: 4rem;
}

.section-title {
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.feature-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 2;
}

.feature-content {
    position: relative;
    z-index: 2;
}

.feature-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 1rem;
}

.feature-description {
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.feature-decoration {
    position: absolute;
    bottom: 20px;
    right: 20px;
    opacity: 0.1;
}

.decoration-line {
    width: 80px;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 1px;
}

.feature-highlights {
    margin-top: 4rem;
}

.highlight-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.highlight-card:hover {
    transform: translateX(10px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.highlight-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.highlight-content h4 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.highlight-content p {
    color: #666;
    margin: 0;
    line-height: 1.5;
}

.features-cta {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    box-shadow: 0 15px 50px rgba(102, 126, 234, 0.3);
}

.features-cta h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.features-cta p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.features-cta .btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.features-cta .btn:hover {
    background: white;
    color: #667eea;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .features-section {
        padding: 60px 0;
    }

    .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .feature-card {
        padding: 2rem;
    }

    .highlight-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .features-cta {
        padding: 2rem 1.5rem;
    }

    .features-cta h3 {
        font-size: 1.5rem;
    }
}
</style>
