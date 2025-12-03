/**
 * GLOBAL.JS - SHARED JAVASCRIPT FOR ALL PAGES
 * 
 * Core validation functions used across login and register pages.
 * Profile page has its own profile.js for specific functionality.
 * 
 * Used on: login.php, register.php
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // ========================================
    // FORMAT DEFINITIONS (REGISTER PAGE)
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
    // ELEMENT REFERENCES
    // ========================================
    
    // Email validation (login & register)
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    
    // Register page - password
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const matchText = document.getElementById('match-text');

    // ========================================
    // EMAIL VALIDATION
    // ========================================
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const validateEmail = () => {
        const email = emailInput.value.trim();
        if (!email) {
            emailError.style.display = 'none';
            return;
        }
        
        if (emailRegex.test(email)) {
            emailError.style.display = 'none';
        } else {
            emailError.textContent = 'Please enter a valid email address';
            emailError.style.display = 'block';
        }
    };

    // ========================================
    // PASSWORD STRENGTH CHECKER
    // Scoring: 5 criteria (length, lowercase, uppercase, numbers, special chars)
    // ========================================
    
    const checkStrength = (passwordEl, barEl, textEl) => {
        const pass = passwordEl.value;
        
        if (!pass) {
            if (barEl) barEl.style.width = '0%';
            if (textEl) textEl.textContent = '';
            return;
        }

        // Calculate strength score (0-5)
        let score = 0;
        if (pass.length >= 8) score++;
        if (/[a-z]/.test(pass)) score++;
        if (/[A-Z]/.test(pass)) score++;
        if (/[0-9]/.test(pass)) score++;
        if (/[^A-Za-z0-9]/.test(pass)) score++;

        const percent = (score / 5) * 100;
        
        const levels = {
            0: { text: 'Weak', color: '#e74c3c' },
            1: { text: 'Weak', color: '#e74c3c' },
            2: { text: 'Weak', color: '#e74c3c' },
            3: { text: 'Medium', color: '#f39c12' },
            4: { text: 'Strong', color: '#2ecc71' },
            5: { text: 'Strong', color: '#2ecc71' }
        };
        
        const level = levels[score];

        if (barEl) {
            barEl.style.width = percent + '%';
            barEl.style.backgroundColor = level.color;
        }
        if (textEl) {
            textEl.textContent = level.text + ' password';
            textEl.style.color = level.color;
        }
    };

    // ========================================
    // PASSWORD MATCH CHECKER
    // ========================================
    
    const checkMatch = (passwordEl, confirmEl, matchEl) => {
        if (!confirmEl?.value || !matchEl) return;
        
        if (passwordEl.value === confirmEl.value) {
            matchEl.textContent = 'Passwords match';
            matchEl.style.color = '#2ecc71';
        } else {
            matchEl.textContent = 'Passwords do not match';
            matchEl.style.color = '#e74c3c';
        }
    };

    // ========================================
    // DYNAMIC FORM PLACEHOLDERS (REGISTER PAGE)
    // ========================================
    
    const updatePhonePlaceholder = () => {
        const phoneCodeSelect = document.getElementById('phone_code');
        const phoneInput = document.getElementById('phone');
        
        if (!phoneCodeSelect || !phoneInput) return;
        
        const placeholder = phoneFormats[phoneCodeSelect.value] || '12 34 56 78';
        phoneInput.placeholder = placeholder;
    };

    const updateZipPlaceholder = () => {
        const countrySelect = document.getElementById('country');
        const zipInput = document.getElementById('zip_code');
        const zipHint = document.getElementById('zip-hint');
        
        if (!countrySelect || !zipInput) return;
        
        const format = zipFormats[countrySelect.value] || zipFormats['Other'];
        
        zipInput.placeholder = format.placeholder;
        if (zipHint) zipHint.textContent = format.hint;
    };

    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    // Email validation (login & register)
    if (emailInput && emailError) {
        emailInput.addEventListener('input', validateEmail);
        emailInput.addEventListener('blur', validateEmail);
    }

    // Register page - password strength & matching
    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', () => {
            checkStrength(passwordInput, strengthBar, strengthText);
            if (confirmInput?.value) checkMatch(passwordInput, confirmInput, matchText);
        });
    }

    if (confirmInput && matchText) {
        confirmInput.addEventListener('input', () => 
            checkMatch(passwordInput, confirmInput, matchText)
        );
    }

    // Register page - phone and zip placeholders
    const phoneCodeSelect = document.getElementById('phone_code');
    const countrySelect = document.getElementById('country');
    
    if (phoneCodeSelect) {
        phoneCodeSelect.addEventListener('change', updatePhonePlaceholder);
        updatePhonePlaceholder();
    }
    
    if (countrySelect) {
        countrySelect.addEventListener('change', updateZipPlaceholder);
        updateZipPlaceholder();
    }
});
