<?php
/**
 * INDEX.PHP - MAIN LANDING PAGE
 * 
 * Public-facing homepage for Reliwe product.
 * Showcases features, benefits, and drives conversions.
 */

require_once 'config/functions.php';
ensure_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding and viewport for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Page title for browser tab and SEO -->
    <title>Reliwe - Advanced Stress Relief Technology</title>
    
    <!-- Google Fonts - Orbitron for headers, Montserrat for body text -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
    
    <!-- Page-specific stylesheet -->
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <!-- Include reusable header navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- 
        HERO SECTION
        Main landing area with clear value proposition
    -->
    <section class="index-hero">
        <img src="Images/Pulsetto_Inv.webp" alt="Neupulse 435i">
        <div class="index-hero-content">
            <h1>STRESS RELIEF<br>REIMAGINED</h1>
            <p class="hero-subtitle">Experience the future of wellness with Reliwe.<br>Scientifically proven vagus nerve stimulation technology.</p>
            <div class="hero-cta-group">
                <button class="hero-btn-primary">SHOP NOW</button>
                <button class="hero-btn-secondary">LEARN MORE</button>
            </div>
        </div>
    </section>

    <!-- 
        FEATURES STRIP
        Quick highlight of key features
    -->
    <section class="features-strip">
        <div class="feature-item">
            <span>✓</span>
            <span>FDA-Cleared Technology</span>
        </div>
        <div class="feature-item">
            <span>✓</span>
            <span>Clinically Proven Results</span>
        </div>
        <div class="feature-item">
            <span>✓</span>
            <span>30-Day Money Back</span>
        </div>
    </section>

    <!-- 
        HOW IT WORKS
        Simple 3-step process
    -->
    <section class="how-it-works">
        <h2>How It Works</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <h3>Place on Neck</h3>
                <p>Position the device comfortably on your neck to target the vagus nerve pathway.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">02</div>
                <h3>Choose Program</h3>
                <p>Select from our range of scientifically designed programs via the mobile app.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">03</div>
                <h3>Feel Relief</h3>
                <p>Experience reduced stress and improved wellness in just 15 minutes per day.</p>
            </div>
        </div>
    </section>

    <!-- 
        SOCIAL PROOF
        Statistics and credibility
    -->
    <section class="social-proof">
        <h2>Trusted by Thousands</h2>
        <p class="social-proof-subtitle">Join our growing community of wellness enthusiasts</p>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">47%</div>
                <div class="stat-label">Stress Reduction</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">4.8★</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>
    </section>

    <!-- 
        BENEFITS SECTION
        Key product benefits
    -->
    <section class="benefits-section">
        <h2>Why Choose Reliwe?</h2>
        <p class="section-subtitle">Backed by science, designed for life. Experience transformative wellness through advanced vagus nerve technology.</p>
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">🧘</div>
                <h3>Stress Relief</h3>
                <p>Activate your body's natural relaxation response and reduce stress levels significantly through clinically proven vagus nerve stimulation.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">💤</div>
                <h3>Better Sleep</h3>
                <p>Improve sleep quality and fall asleep faster by naturally regulating your nervous system for deeper, more restorative rest.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">🧠</div>
                <h3>Mental Clarity</h3>
                <p>Enhance focus, concentration, and cognitive performance while reducing brain fog through consistent daily use.</p>
            </div>
        </div>
        <div class="benefits-cta">
            <a href="technology.php" class="benefits-btn">EXPLORE THE SCIENCE</a>
        </div>
    </section>

    <!-- 
        FINAL CTA
        Conversion-focused call to action
    -->
    <section class="final-cta">
        <h2>Ready to Transform Your Life?</h2>
        <p>Join thousands of satisfied customers and experience the science-backed power of vagus nerve stimulation.</p>
        <button class="cta-btn">GET STARTED TODAY</button>
    </section>

    <!-- Include reusable footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Page-specific JavaScript -->
    <script src="js/index.js"></script>
</body>
</html>
