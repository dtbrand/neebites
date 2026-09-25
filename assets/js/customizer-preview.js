/**
 * Customizer Live Preview Handler
 *
 * @package Neebites
 * @version 1.1.0
 */

(function($) {
    'use strict';

    if (!wp || !wp.customize) return;

    // Primary Color
    wp.customize('neebites_theme_options[primary_color]', function(value) {
        value.bind(function(newVal) {
            document.documentElement.style.setProperty('--color-primary', newVal);
        });
    });

    // Accent Color
    wp.customize('neebites_theme_options[accent_color]', function(value) {
        value.bind(function(newVal) {
            document.documentElement.style.setProperty('--color-accent', newVal);
        });
    });

    // Copyright
    wp.customize('neebites_theme_options[footer_copyright]', function(value) {
        value.bind(function(newVal) {
            $('.footer-copyright').html(newVal);
        });
    });
})(window.jQuery || {});
