/**
 * PROFILE.JS - PROFILE PAGE INTERACTIONS
 * 
 * Handles all profile page functionality:
 * - Email validation and update confirmation
 * - Password change with strength validation
 * - Phone/zip placeholder updates based on country
 * - Form submission confirmations
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // ========================================
    // FORMAT DEFINITIONS
    // ========================================
    
    const phoneFormats = {
        '+45': '12 34 56 78',
        '+46': '70 123 45 67',
        '+47': '12 34 56 78',
        '+358': '40 123 4567',
        '+49': '1512 3456789',
        '+31': '6 12345678',
        '+32': '470 12 34 56',
        '+33': '6 12 34 56 78',
        '+44': '7700 900123',
        '+1': '(555) 123-4567',
        '+61': '0412 345 678'
    };

    const zipFormats = {
        'Denmark': { hint: '4 digits (e.g. 7400)', placeholder: '7400' },
        'Sweden': { hint: '5 digits (e.g. 123 45)', placeholder: '123 45' },
        'Norway': { hint: '4 digits (e.g. 0150)', placeholder: '0150' },
        'Finland': { hint: '5 digits (e.g. 00100)', placeholder: '00100' },
        'Germany': { hint: '5 digits (e.g. 10115)', placeholder: '10115' },
        'Netherlands': { hint: '4 digits + 2 letters (e.g. 1012 AB)', placeholder: '1012 AB' },
        'Belgium': { hint: '4 digits (e.g. 1000)', placeholder: '1000' },
        'France': { hint: '5 digits (e.g. 75001)', placeholder: '75001' },
        'United Kingdom': { hint: 'UK postcode (e.g. SW1A 1AA)', placeholder: 'SW1A 1AA' },
        'United States': { hint: '5 digits or ZIP+4 (e.g. 10001)', placeholder: '10001' },
        'Canada': { hint: 'Format: A1A 1A1', placeholder: 'M5V 3L9' },
        'Australia': { hint: '4 digits (e.g. 2000)', placeholder: '2000' },
        'Other': { hint: '', placeholder: 'Postal code' }
    };
    
    // ========================================
    // ELEMENT REFERENCES - PROFILE SPECIFIC
    // ========================================
    
    const elements = {
        // Email update form
        emailInput: document.getElementById('email'),
        emailError: document.getElementById('email-error'),
        emailForm: document.getElementById('emailForm'),
        
        // Password change form
        newPassword: document.getElementById('new_password'),
        confirmPassword: document.getElementById('confirm_password'),
        strengthBar: document.getElementById('strength-bar-new'),
        strengthText: document.getElementById('strength-text-new'),
        matchText: document.getElementById('match-text-new'),
        passwordForm: document.getElementById('passwordForm'),
        
        // Profile info form
        phoneCode: document.getElementById('phone_code'),
        phone: document.getElementById('phone'),
        country: document.getElementById('country'),
        zipCode: document.getElementById('zip_code'),
        zipHint: document.getElementById('zip-hint')
    };

    // ========================================
    // EMAIL VALIDATION
    // ========================================
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    const validateEmail = () => {
        const email = elements.emailInput.value.trim();
        if (!email) {
            elements.emailError.style.display = 'none';
            return;
        }
        
        const isValid = emailRegex.test(email);
        elements.emailError.style.display = isValid ? 'none' : 'block';
        if (!isValid) {
            elements.emailError.textContent = 'Please enter a valid email address';
        }
    };

    // ========================================
    // PASSWORD STRENGTH CHECKER
    // ========================================
    
    const checkPasswordStrength = () => {
        const pass = elements.newPassword.value;
        
        if (!pass) {
            elements.strengthBar.style.width = '0%';
            elements.strengthText.textContent = '';
            return;
        }

        // Calculate strength (5 criteria)
        let score = 0;
        if (pass.length >= 8) score++;
        if (/[a-z]/.test(pass)) score++;
        if (/[A-Z]/.test(pass)) score++;
        if (/[0-9]/.test(pass)) score++;
        if (/[^A-Za-z0-9]/.test(pass)) score++;

        const levels = [
            { text: 'Weak', color: '#e74c3c' },
            { text: 'Weak', color: '#e74c3c' },
            { text: 'Weak', color: '#e74c3c' },
            { text: 'Medium', color: '#f39c12' },
            { text: 'Strong', color: '#2ecc71' },
            { text: 'Strong', color: '#2ecc71' }
        ];
        
        const level = levels[score];
        elements.strengthBar.style.width = (score / 5) * 100 + '%';
        elements.strengthBar.style.backgroundColor = level.color;
        elements.strengthText.textContent = level.text + ' password';
        elements.strengthText.style.color = level.color;
    };

    // ========================================
    // PASSWORD MATCH CHECKER
    // ========================================
    
    const checkPasswordMatch = () => {
        const confirmValue = elements.confirmPassword.value;
        if (!confirmValue) {
            elements.matchText.textContent = '';
            return;
        }
        
        const matches = elements.newPassword.value === confirmValue;
        elements.matchText.textContent = matches ? 'Passwords match' : 'Passwords do not match';
        elements.matchText.style.color = matches ? '#2ecc71' : '#e74c3c';
    };

    // ========================================
    // DYNAMIC FORM PLACEHOLDERS
    // ========================================
    
    const updatePhonePlaceholder = () => {
        if (!elements.phoneCode || !elements.phone) return;
        const format = phoneFormats[elements.phoneCode.value] || '12 34 56 78';
        elements.phone.placeholder = format;
    };

    const updateZipPlaceholder = () => {
        if (!elements.country || !elements.zipCode) return;
        
        const format = zipFormats[elements.country.value] || zipFormats['Other'];
        
        elements.zipCode.placeholder = format.placeholder;
        if (elements.zipHint) elements.zipHint.textContent = format.hint;
    };

    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    // Email validation
    if (elements.emailInput && elements.emailError) {
        elements.emailInput.addEventListener('input', validateEmail);
        elements.emailInput.addEventListener('blur', validateEmail);
    }
    
    // Password strength and matching
    if (elements.newPassword && elements.strengthBar) {
        elements.newPassword.addEventListener('input', () => {
            checkPasswordStrength();
            if (elements.confirmPassword.value) checkPasswordMatch();
        });
    }
    
    if (elements.confirmPassword && elements.matchText) {
        elements.confirmPassword.addEventListener('input', checkPasswordMatch);
    }
    
    // Phone code changes
    if (elements.phoneCode) {
        elements.phoneCode.addEventListener('change', updatePhonePlaceholder);
        updatePhonePlaceholder();
    }
    
    // Country changes
    if (elements.country) {
        elements.country.addEventListener('change', updateZipPlaceholder);
        updateZipPlaceholder();
    }
    
    // Form submission confirmations
    elements.emailForm?.addEventListener('submit', (e) => {
        if (!confirm('Are you sure you want to update your email address?')) {
            e.preventDefault();
        }
    });
    
    elements.passwordForm?.addEventListener('submit', (e) => {
        if (!confirm('Are you sure you want to change your password?')) {
            e.preventDefault();
        }
    });
});
