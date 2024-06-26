<?php

/**
 * Plugin Name: Recent Restocked Products Carousel
 * Description: An Elementor widget to display recently restocked products in a carousel.
 * Version: 1.0.0
 * Author: Mirailit Limited
 * Author URI: https://mirailit.com/
 * Text Domain: recent-restocked-products-carousel
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue necessary styles and scripts
function rrp_enqueue_scripts()
{
    // Enqueue main plugin stylesheet
    wp_enqueue_style('recent-restocked-products-carousel', plugins_url('recent-restocked-products-carousel.css', __FILE__));

    // Enqueue Slick CSS
    wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), '1.8.1');

    // Enqueue Slick JavaScript
    wp_enqueue_script('slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), '1.8.1', true);

    // Enqueue custom JavaScript for initializing the carousel
    wp_enqueue_script('recent-restocked-products-carousel-js', plugins_url('recent-restocked-products-carousel.js', __FILE__), array('jquery', 'slick-js'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'rrp_enqueue_scripts');

// Register the custom widget
function rrp_register_recent_restocked_products_widget($widgets_manager)
{
    require_once(__DIR__ . '/widgets/recent-restocked-products-carousel-widget.php');
    $widgets_manager->register_widget_type(new \Elementor\Widget_Recent_Restocked_Products_Carousel());
}
add_action('elementor/widgets/widgets_registered', 'rrp_register_recent_restocked_products_widget');
