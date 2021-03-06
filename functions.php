<?php
/**
 * Marquee Child functions and definitions
 *
 * @package Marquee
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
    $content_width = 620; /* pixels */
}
if ( ! isset( $full_content_width ) ) {
    $full_content_width = 960; /* pixels */
}
function marquee_child_scripts() {
    wp_enqueue_style( 'marquee-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 
        'marquee-child-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        'marquee-parent-style' );
    wp_enqueue_script( 'moment', get_stylesheet_directory_uri() . '/moment.js' );
    wp_enqueue_style( 'pikaday', get_stylesheet_directory_uri() . '/pikaday.css' );
    wp_enqueue_script( 'pikaday', get_stylesheet_directory_uri() . '/pikaday.js' );
    wp_enqueue_script( 'client', 'https://js.braintreegateway.com/web/3.32.1/js/client.min.js' );
    wp_enqueue_script( 'paypal', 'https://js.braintreegateway.com/web/3.32.1/js/paypal.min.js', 'client' );
}
add_action( 'wp_enqueue_scripts', 'marquee_child_scripts', 11 );

// Enable shortcodes in text widgets
add_filter('widget_text','do_shortcode');
