<?php
/**
 * SUPPORT.PHP - CUSTOMER SUPPORT
 * 
 * Customer support center with contact form and troubleshooting resources.
 */

require_once 'config/config.php';
require_once 'config/functions.php';

ensure_session();

$form_success = false;
$form_error = '';

/**
 * SUBMIT CONTACT MESSAGE
 * 
 * Saves a contact/purchase inquiry to the database.
 */
function submit_contact_message($conn, $name, $email, $phone, $category, $message, $user_id = null) {
    $stmt = $conn->prepare("
        INSERT INTO contact_messages (user_id, name, email, phone, category, message, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'new')
    ");
    
    $stmt->bind_param("isssss", $user_id, $name, $email, $phone, $category, $message);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $phone = trim($_POST['order'] ?? ''); // Using order field for reference
    
    // Get user_id if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Validation
    if (empty($name) || empty($email) || empty($category) || empty($message)) {
        $form_error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_error = 'Please enter a valid email address.';
    } else {
        // Submit to database
        if (submit_contact_message($conn, $name, $email, $phone, $category, $message, $user_id)) {
            $form_success = true;
            // Log the activity
            if ($user_id) {
                log_activity($conn, $user_id, 'support_request', 'User submitted support request: ' . $category);
            }
        } else {
            $form_error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Reliwe</title>
    
    <!-- Google Fonts - Orbitron for headings, Montserrat for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
    
    <!-- Page-specific stylesheet -->
    <link rel="stylesheet" href="css/support.css">
</head>
<body>
    <!-- Include reusable header with navigation -->
    <?php include 'includes/header.php'; ?>
    
    <!-- 
        HERO SECTION
        Main headline and introduction to support page
    -->
    <section class="support-hero">
        <div class="support-hero-content">
            <h1>How Can We Help You?</h1>
            <p>Our support team is here to assist you with any questions or issues you may have with your Product</p>
        </div>
    </section>
    
    <!-- 
        CONTACT OPTIONS
        Different ways to reach support team
    -->
    <section class="contact-options">
        <!-- Email support -->
        <div class="contact-card">
            <div class="contact-icon">📧</div>
            <h3>Email Support</h3>
            <p>Get help via email. Response within 24 hours.</p>
            <p class="contact-email">support@reliwe.com</p>
        </div>
        
        <!-- Live chat -->
        <div class="contact-card">
            <div class="contact-icon">💬</div>
            <h3>Live Chat</h3>
            <p>Available during business hours</p>
            <p class="contact-hours">Weekdays: 8:00 - 16:00</p>
            <button class="contact-button" disabled>Currently Offline</button>
        </div>
        
        <!-- Phone support -->
        <div class="contact-card">
            <div class="contact-icon">📞</div>
            <h3>Phone Support</h3>
            <p>Talk directly to our support team</p>
            <p class="contact-phone">+45 12 34 56 78</p>
            <p class="contact-schedule">Weekdays: 8:00 - 16:00</p>
        </div>
    </section>
    
    <!-- 
        FAQ SECTION
        Frequently asked questions and answers
    -->
    <section class="faq-section">
        <h2>Frequently Asked Questions</h2>
        
        <!-- FAQ Item 1 -->
        <div class="faq-item">
            <div class="faq-question">How do I use my device?</div>
            <div class="faq-answer">
                Place the device on your neck, positioning the electrodes on either side of your vagus nerve pathway (just below the jaw). Connect via the mobile app, select your desired program, and adjust the intensity to a comfortable level. Start with 10-15 minute sessions and gradually increase as needed.
            </div>
        </div>
        
        <!-- FAQ Item 2 -->
        <div class="faq-item">
            <div class="faq-question">How often should I use the device?</div>
            <div class="faq-answer">
                For best results, we recommend using Reliwe 1-2 times daily. Many users find benefit from morning sessions for energy and evening sessions for relaxation. Consistency is key - regular daily use typically shows the best outcomes after 4-6 weeks.
            </div>
        </div>
        
        <!-- FAQ Item 3 -->
        <div class="faq-item">
            <div class="faq-question">Is vagus nerve stimulation safe?</div>
            <div class="faq-answer">
                Yes, transcutaneous vagus nerve stimulation (tVNS) is considered safe for most people. The technology has been extensively studied and is non-invasive. However, it's not recommended for people with certain conditions (cardiac pacemakers, epilepsy, pregnancy). Always consult your healthcare provider before starting any new wellness protocol.
            </div>
        </div>
        
        <!-- FAQ Item 4 -->
        <div class="faq-item">
            <div class="faq-question">How do I charge my device?</div>
            <div class="faq-answer">
                Connect the included USB-C cable to the charging port on the bottom of your device. A full charge takes approximately 2 hours and provides 8-12 hours of use. The LED indicator will show red while charging and turn green when fully charged.
            </div>
        </div>
        
        <!-- FAQ Item 5 -->
        <div class="faq-item">
            <div class="faq-question">What if the device isn't working?</div>
            <div class="faq-answer">
                First, ensure your device is fully charged and properly positioned on your neck. Check that the mobile app is connected via Bluetooth. Make sure the electrode contacts are clean. If issues persist, try resetting the device by holding the power button for 10 seconds. Contact support if problems continue.
            </div>
        </div>
        
        <!-- FAQ Item 6 -->
        <div class="faq-item">
            <div class="faq-question">What's your return policy?</div>
            <div class="faq-answer">
                We offer a 30-day money-back guarantee. If you're not completely satisfied with your Reliwe, return it within 30 days of purchase for a full refund. The device must be in good condition with all original packaging and accessories.
            </div>
        </div>
        
        <!-- FAQ Item 7 -->
        <div class="faq-item">
            <div class="faq-question">Does the device work with both iOS and Android?</div>
            <div class="faq-answer">
                Yes! The Neupulse 435i app is available for both iOS (version 13.0+) and Android (version 8.0+). Download it from the App Store or Google Play Store, create an account, and pair your device via Bluetooth.
            </div>
        </div>
        
        <!-- FAQ Item 8 -->
        <div class="faq-item">
            <div class="faq-question">What's covered under warranty?</div>
            <div class="faq-answer">
                Our warranty covers manufacturing defects and hardware malfunctions. Starter packages include 1-year warranty, Professional packages include 2-year warranty, and Clinic packages include 5-year warranty. Warranty does not cover damage from misuse, accidents, or normal wear and tear.
            </div>
        </div>
    </section>
    
    <!-- 
        SUPPORT FORM
        Contact form for submitting support requests
    -->
    <section class="support-form" id="support-form">
        <h2>Submit a Support Request</h2>
        
        <?php if ($form_success): ?>
            <div class="form-success" style="background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                <h3>✅ Request Submitted Successfully!</h3>
                <p>Thank you for contacting us. We'll respond within 24 hours.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($form_error): ?>
            <div class="form-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <?= htmlspecialchars($form_error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$form_success): ?>
        <form method="POST" action="support.php#support-form">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="order">Order Number (if applicable)</label>
                <input type="text" id="order" name="order" value="<?= isset($_POST['order']) ? htmlspecialchars($_POST['order']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Issue Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="device" <?= (isset($_POST['category']) && $_POST['category'] === 'device') ? 'selected' : '' ?>>Device Not Working</option>
                    <option value="app" <?= (isset($_POST['category']) && $_POST['category'] === 'app') ? 'selected' : '' ?>>Mobile App Issue</option>
                    <option value="charging" <?= (isset($_POST['category']) && $_POST['category'] === 'charging') ? 'selected' : '' ?>>Charging Problem</option>
                    <option value="warranty" <?= (isset($_POST['category']) && $_POST['category'] === 'warranty') ? 'selected' : '' ?>>Warranty Claim</option>
                    <option value="return" <?= (isset($_POST['category']) && $_POST['category'] === 'return') ? 'selected' : '' ?>>Return/Refund</option>
                    <option value="other" <?= (isset($_POST['category']) && $_POST['category'] === 'other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message">Describe Your Issue *</label>
                <textarea id="message" name="message" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
            </div>
            
            <button type="submit" class="submit-button">Submit Request</button>
        </form>
        <?php endif; ?>
    </section>
    
    <!-- Include reusable footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Page-specific JavaScript -->
    <script src="js/support.js"></script>
</body>
</html>
