/**
 * FORM-FORMATS.JS - SHARED FORMAT DEFINITIONS
 * Centralized phone and zip format configurations
 */

// Phone number formats by country code
const phoneFormats = {
    '+45': '12 34 56 78',      // Denmark
    '+46': '70 123 45 67',     // Sweden
    '+47': '12 34 56 78',      // Norway
    '+358': '40 123 4567',     // Finland
    '+49': '1512 3456789',     // Germany
    '+31': '6 12345678',       // Netherlands
    '+32': '470 12 34 56',     // Belgium
    '+33': '6 12 34 56 78',    // France
    '+44': '7700 900123',      // UK
    '+1': '(555) 123-4567',    // US/Canada
    '+61': '0412 345 678'      // Australia
};

// Zip code formats by country
const zipFormats = {
    'Denmark': { pattern: /^\d{4}$/, hint: '4 digits (e.g. 7400)', placeholder: '7400' },
    'Sweden': { pattern: /^\d{3}\s?\d{2}$/, hint: '5 digits (e.g. 123 45)', placeholder: '123 45' },
    'Norway': { pattern: /^\d{4}$/, hint: '4 digits (e.g. 0150)', placeholder: '0150' },
    'Finland': { pattern: /^\d{5}$/, hint: '5 digits (e.g. 00100)', placeholder: '00100' },
    'Germany': { pattern: /^\d{5}$/, hint: '5 digits (e.g. 10115)', placeholder: '10115' },
    'Netherlands': { pattern: /^\d{4}\s?[A-Za-z]{2}$/, hint: '4 digits + 2 letters (e.g. 1012 AB)', placeholder: '1012 AB' },
    'Belgium': { pattern: /^\d{4}$/, hint: '4 digits (e.g. 1000)', placeholder: '1000' },
    'France': { pattern: /^\d{5}$/, hint: '5 digits (e.g. 75001)', placeholder: '75001' },
    'United Kingdom': { pattern: /^[A-Za-z]{1,2}\d[A-Za-z\d]?\s?\d[A-Za-z]{2}$/, hint: 'UK postcode (e.g. SW1A 1AA)', placeholder: 'SW1A 1AA' },
    'United States': { pattern: /^\d{5}(-\d{4})?$/, hint: '5 digits or ZIP+4 (e.g. 10001)', placeholder: '10001' },
    'Canada': { pattern: /^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/, hint: 'Format: A1A 1A1', placeholder: 'M5V 3L9' },
    'Australia': { pattern: /^\d{4}$/, hint: '4 digits (e.g. 2000)', placeholder: '2000' },
    'Other': { pattern: /.*/, hint: '', placeholder: 'Postal code' }
};
