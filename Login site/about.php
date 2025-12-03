<?php
/**
 * ABOUT.PHP - COMPANY INFORMATION
 * 
 * Provides information about Reliwe company, mission, and values.
 */

require_once 'config/functions.php';
ensure_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding and viewport settings -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Page title for browser tab -->
    <title>About Us | Reliwe</title>
    
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
    
    <!-- Page-specific stylesheet -->
    <link rel="stylesheet" href="css/about.css">
</head>

<body>
    <!-- Include shared header navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- 
        HERO SECTION
        Bold mission statement with background image
    -->
    <section class="about-hero">
        <img src="Images/Scientists.jpeg" alt="Reliwe research team">
        <div class="about-hero-content">
            <h1>TRANSFORMING MEDICINE THROUGH INNOVATION</h1>
            <p>At Reliwe, we're pioneering the future of wellness technology, combining cutting-edge neuroscience with accessible, user-friendly devices.</p>
        </div>
    </section>

    <!-- 
        MISSION SECTION
        Company mission with video
    -->
    <section class="mission-section">
        <div class="mission-content">
            <h2>Our Mission</h2>
            <p>We believe in empowering individuals to take control of their health through scientifically-backed, non-invasive technology.</p>
            <p>Reliwe represents years of research in vagus nerve stimulation, distilled into an elegant device that anyone can use at home.</p>
            <p>Our goal is simple: make advanced wellness technology accessible to everyone, everywhere.</p>
        </div>
        
        <div class="mission-video">
            <video autoplay loop muted playsinline>
                <source src="Images/Plant.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </section>

    <!-- 
        VALUES SECTION
        Core company values
    -->
    <section class="values-section">
        <h2>Our Values</h2>
        <div class="values-grid">
            <div class="value-item">
                <div class="value-icon">🔬</div>
                <h3>Science-Based</h3>
                <p>Every feature is backed by peer-reviewed research and clinical studies.</p>
            </div>
            
            <div class="value-item">
                <div class="value-icon">💎</div>
                <h3>Quality First</h3>
                <p>Premium materials, rigorous testing, and uncompromising standards.</p>
            </div>
            
            <div class="value-item">
                <div class="value-icon">🌍</div>
                <h3>Accessible</h3>
                <p>Making advanced wellness technology available to people worldwide.</p>
            </div>
        </div>
    </section>

    <!-- 
        CALL TO ACTION
        Final conversion push
    -->
    <section class="about-cta">
        <h2>Ready to Transform Your Wellness?</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="start-app">GO TO DASHBOARD</a>
        <?php else: ?>
            <a href="purchase.php" class="start-app">START YOUR JOURNEY</a>
        <?php endif; ?>
    </section>

    <!-- Include shared footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
