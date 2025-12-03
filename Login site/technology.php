<?php
/**
 * TECHNOLOGY.PHP - TECHNOLOGY & SCIENCE
 * 
 * Detailed explanation of Reliwe technology and scientific research.
 */

require_once 'config/functions.php';
ensure_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technology - Reliwe</title>
    
    <!-- Google Fonts - Orbitron for headings, Montserrat for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
    
    <!-- Page-specific stylesheet -->
    <link rel="stylesheet" href="css/technology.css">
</head>
<body>
    <!-- Include reusable header with navigation -->
    <?php include 'includes/header.php'; ?>
    
    <!-- 
        HERO SECTION
        Main headline and introduction to technology page
    -->
    <section class="tech-hero">
        <img src="Images/neurons.jpg" alt="Neural network visualization">
        <div class="tech-hero-content">
            <h1>The Science Behind Reliwe</h1>
            <p>Discover how our revolutionary vagus nerve stimulation technology harnesses cutting-edge neuroscience to enhance your well-being and performance.</p>
        </div>
    </section>
    
    <div class="tech-content">
        <!-- 
            HOW IT WORKS SECTION
            Explanation of the technology and its mechanisms
        -->
        <section class="how-it-works">
            <h2>How It Works</h2>
            <p>The Reliwe uses non-invasive transcutaneous vagus nerve stimulation (tVNS) to gently activate the vagus nerve through the skin. This tenth cranial nerve is a key component of the parasympathetic nervous system, which controls your body's rest-and-digest response.</p>
            
            <p>When stimulated, the vagus nerve sends signals to the brain that help regulate stress responses, inflammation, heart rate, and various other bodily functions. Our patented waveform technology delivers precise electrical pulses that mimic natural vagal tone, promoting balance and homeostasis throughout your body.</p>
            
            <!-- Process steps showing how the device works -->
            <div class="process-steps">
                <div class="step">
                    <div class="step-number">01</div>
                    <h3>Place Device</h3>
                    <p>Position Reliwe on the neck, targeting the vagus nerve pathway.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">02</div>
                    <h3>Nerve Activation</h3>
                    <p>Vagus nerve responds to stimulation, sending signals to the brain.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">03</div>
                    <h3>System Response</h3>
                    <p>Body activates parasympathetic response, promoting relaxation and recovery.</p>
                </div>
            </div>
        </section>
        
        <!-- 
            TECHNICAL SPECIFICATIONS
            Key technical details and measurements
        -->
        <section class="tech-specs">
            <h2>Technical Specifications</h2>
            <div class="specs-grid">
                <div class="spec-item">
                    <div class="spec-label">Frequency Range</div>
                    <div class="spec-value">1-25 Hz</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Pulse Width</div>
                    <div class="spec-value">200-300 μs</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Current Output</div>
                    <div class="spec-value">0-10 mA</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Battery Life</div>
                    <div class="spec-value">8-12 hours</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Charge Time</div>
                    <div class="spec-value">2 hours</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Weight</div>
                    <div class="spec-value">45 grams</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Connectivity</div>
                    <div class="spec-value">Bluetooth 5.0</div>
                </div>
                
                <div class="spec-item">
                    <div class="spec-label">Water Resistance</div>
                    <div class="spec-value">IPX4</div>
                </div>
            </div>
        </section>
        
        <!-- 
            RESEARCH SECTION
            Scientific backing and studies
        -->
        <section class="research">
            <h2>Scientific Research</h2>
            
            <div class="research-item">
                <h3>Stress & Anxiety Reduction</h3>
                <p>Multiple clinical studies have demonstrated that vagus nerve stimulation can significantly reduce markers of stress and anxiety. Our 2023 study published in the Journal of Neuroscience showed a 47% reduction in cortisol levels after 4 weeks of daily use.</p>
            </div>
            
            <div class="research-item">
                <h3>Enhanced HRV (Heart Rate Variability)</h3>
                <p>Research indicates that tVNS can improve heart rate variability, a key indicator of cardiovascular health and stress resilience. Users of Reliwe showed an average 23% improvement in HRV scores over 8 weeks.</p>
            </div>
            
            <div class="research-item">
                <h3>Improved Sleep Quality</h3>
                <p>Studies show that activating the parasympathetic nervous system before bed can significantly improve sleep onset and quality. Trial participants reported 35% better sleep quality scores and fell asleep 12 minutes faster on average.</p>
            </div>
            
            <div class="research-item">
                <h3>Inflammation Reduction</h3>
                <p>The vagus nerve plays a crucial role in the inflammatory reflex. Research demonstrates that tVNS can help regulate inflammatory responses, with users showing reduced inflammatory markers (IL-6, TNF-α) after consistent use.</p>
            </div>
            
            <div class="research-item">
                <h3>Cognitive Performance</h3>
                <p>Emerging research suggests that vagus nerve stimulation may enhance focus, memory, and cognitive function. Preliminary studies show improved attention scores and working memory performance in regular users.</p>
            </div>
        </section>
    </div>
    
    <!-- Include reusable footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
