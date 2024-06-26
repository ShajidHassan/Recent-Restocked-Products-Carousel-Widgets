<?php

namespace Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Widget_Recent_Restocked_Products_Carousel extends Widget_Base
{

    public function get_name()
    {
        return 'recent-restocked-products-carousel';
    }

    public function get_title()
    {
        return __('Recent Restocked Carousel', 'recent-restocked-products-carousel');
    }

    public function get_icon()
    {
        return 'eicon-post-slider';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'recent-restocked-products-carousel'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'number_of_products',
            [
                'label' => __('Number of Products', 'recent-restocked-products-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Retrieve number of products to display
        $number_of_products = !empty($settings['number_of_products']) ? $settings['number_of_products'] : 6;

        global $wpdb;

        // Query to fetch recently restocked products
        $query = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID 
                FROM {$wpdb->prefix}posts AS p
                INNER JOIN {$wpdb->prefix}stock_change_history AS sch ON p.ID = sch.product_id
                WHERE p.post_type = 'product'
                AND p.post_status = 'publish'
                AND sch.new_stock_status = 'instock'
                AND sch.change_date = (
                    SELECT MAX(inner_sch.change_date)
                    FROM {$wpdb->prefix}stock_change_history AS inner_sch
                    WHERE inner_sch.product_id = sch.product_id
                )
                ORDER BY sch.change_date DESC
                LIMIT %d",
                $number_of_products
            )
        );

        // Check if there are products retrieved
        if ($query) {
            echo '<div class="recent-restocked-products-carousel slick-carousel products slider-wrapper slick-slider">';

            foreach ($query as $product_id) {
                $product = wc_get_product($product_id->ID);

                if ($product && !is_wp_error($product)) {
                    echo '<div class="product">';
                    echo '<div class="product-wrapper product-type-' . $product->get_type() . '">';

                    // Thumbnail wrapper
                    echo '<div class="thumbnail-wrapper">';
                    echo '<a href="' . esc_url(get_permalink($product_id->ID)) . '">';

                    // Product image
                    $product_id = $product->get_id();
                    $image_html = get_the_post_thumbnail($product_id, 'full', array('loading' => false));
                    echo $image_html;

                    echo '</a>';

                    // Product buttons wrapper
                    echo '<div class="product-buttons">';

                    // Quick view button
                    echo '<a href="' . esc_url(get_permalink($product_id->ID)) . '" class="detail-bnt quick-view-button" aria-label="Quick View">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">';
                    echo '<path d="M128 32V0H16C7.163 0 0 7.163 0 16v112h32V54.56L180.64 203.2l22.56-22.56L54.56 32H128zM496 0H384v32h73.44L308.8 180.64l22.56 22.56L480 54.56V128h32V16c0-8.837-7.163-16-16-16zM480 457.44L331.36 308.8l-22.56 22.56L457.44 480H384v32h112c8.837 0 16-7.163 16-16V384h-32v73.44zM180.64 308.64L32 457.44V384H0v112c0 8.837 7.163 16 16 16h112v-32H54.56L203.2 331.36l-22.56-22.72z"></path>';
                    echo '</svg>';
                    echo '</a>';

                    // Wishlist button
                    echo do_shortcode('[ti_wishlists_addtowishlist]');

                    echo '</div>'; // .product-buttons

                    echo '</div>'; // .thumbnail-wrapper

                    // Content wrapper
                    echo '<div class="content-wrapper">';

                    // Product title
                    echo '<h3 class="product-title">';
                    echo '<a href="' . esc_url(get_permalink($product_id->ID)) . '" title="' . esc_attr($product->get_name()) . '">' . esc_html($product->get_name()) . '</a>';
                    echo '</h3>';

                    // Product meta (e.g., weight and stock status)
                    echo '<div class="product-meta">';
                    echo '<div class="product-unit">' . wc_format_weight($product->get_weight()) . '</div>';
                    echo '<div class="product-available ' . ($product->is_in_stock() ? 'in-stock' : 'out-of-stock') . '">' . ($product->is_in_stock() ? 'In Stock' : 'Out of Stock') . '</div>';
                    echo '</div>';

                    // Product price
                    echo '<span class="price">';
                    echo '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span>' . $product->get_price() . '</bdi></span>';
                    echo '<span class="price-after-text"> (With Tax)</span>';
                    echo '</span>';

                    // Add to cart button
                    echo '<div class="product-button-group">';
                    echo '<a href="' . esc_url($product->add_to_cart_url()) . '" class="button-primary xsmall rounded wide button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product_id) . '" data-product_sku="' . esc_attr($product->get_sku()) . '" aria-label="' . esc_attr__('Add to cart', 'woocommerce') . '" rel="nofollow">';
                    echo __('Add to cart', 'woocommerce');
                    echo '</a>';

                    echo '</div>'; // .product-button-group

                    echo '</div>'; // .content-wrapper

                    echo '</div>'; // .product-wrapper
                    echo '</div>'; // .product
                }
            }

            echo '</div>'; // .recent-restocked-products-carousel
        } else {
            echo '<p>No newly restocked products found.</p>';
        }

        // Reset global post data
        wp_reset_postdata();
    }


    protected function _content_template()
    {
        // Optional: Define the content template for Elementor. This can be left empty if not needed for now.
    }
}
