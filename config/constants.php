<?php
/**
 * CONSTANTS.PHP - SHARED CONSTANTS
 * 
 * Centralized constants used across multiple files.
 */

// Phone codes with country flags
define('PHONE_CODES', [
    '+45' => '🇩🇰 +45',
    '+46' => '🇸🇪 +46',
    '+47' => '🇳🇴 +47',
    '+358' => '🇫🇮 +358',
    '+49' => '🇩🇪 +49',
    '+31' => '🇳🇱 +31',
    '+32' => '🇧🇪 +32',
    '+33' => '🇫🇷 +33',
    '+44' => '🇬🇧 +44',
    '+1' => '🇺🇸/🇨🇦 +1',
    '+61' => '🇦🇺 +61'
]);

// Supported countries
define('COUNTRIES', [
    'Denmark', 'Sweden', 'Norway', 'Finland', 'Germany',
    'Netherlands', 'Belgium', 'France', 'United Kingdom',
    'United States', 'Canada', 'Australia', 'Other'
]);
