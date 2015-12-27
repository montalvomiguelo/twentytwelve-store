<?php

/**
 * Enqueue the parent and child theme stylesheets
 *
 */
function twentytwelve_store_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}
add_action( 'wp_enqueue_scripts', 'twentytwelve_store_enqueue_styles' );

/**
 * Woocommerce Tweaks
 *
 */

// Store column count for displaying the grid
function twentytwelve_store_store_columns( $columns ) {
    return $columns = 3;
}
add_filter( 'loop_shop_columns', 'twentytwelve_store_store_columns' );

// Related products
function twentytwelve_store_related_products( $args = array() ) {
    $args['posts_per_page'] = 3;
    $args['columns'] = 3;
    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'twentytwelve_store_related_products' );

// Up-sells
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

function twentytwelve_store_upsell_display() {
    $args = array(
        'posts_per_page' => 3,
        'orderby' => 'rand',
        'columns' => 3
    );
    wc_get_template( 'single-product/up-sells.php', $args );
}
add_action( 'woocommerce_after_single_product_summary', 'twentytwelve_store_upsell_display', 15 );

// Breadcrumbs
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 9 );

/**
 * Header
 *
 */

// Display product search
function twentytwelve_store_product_search() {
    if ( is_woocommerce_activated() ) {
    ?>
        <div class="site-search">
            <?php the_widget( 'WC_Widget_Product_Search' ); ?>
        </div>
    <?php
    }
}
add_action( 'twentytwelve_store_header', 'twentytwelve_store_product_search', 10 );

// Cart link
function twentytwelve_store_cart_link() {
?>
    <a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
        <?php echo sprintf (_n( '%d item', '%d items', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ); ?> - <?php echo WC()->cart->get_cart_total(); ?>
    </a>
<?php
}

// Display header cart
function twentytwelve_store_header_cart() {
    if ( is_woocommerce_activated() ) {
    ?>
        <div class="site-header-cart">
            <?php twentytwelve_store_cart_link(); ?>
            <?php the_widget( 'WC_Widget_Cart' ); ?>
        </div>
    <?php
    }
}
add_action( 'twentytwelve_store_header', 'twentytwelve_store_header_cart', 20 );

/**
 * Cart fragments
 * Ensure cart contents update when products are added to the cart via AJAX
 * @param array $fragments Fragments to refresh via AJAX
 * @return array Fragments to refresh via AJAX
 *
 */
function twentytwelve_store_cart_link_fragment( $fragments ) {
    ob_start();
    twentytwelve_store_cart_link();
    $fragments['a.cart-contents'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'twentytwelve_store_cart_link_fragment' );

// Display product categories
function twentytwelve_store_product_categories() {
    if ( is_woocommerce_activated() ) {
        $args = apply_filters( 'twentytwelve_store_product_categories_args', array(
            'limit' => 3,
            'columns' => 3,
            'child_categories' => 0,
            'orderby' => 'name',
            'title' => __( 'Product Categories', 'twentytwelve-store' ),
        ) );
        ?>
        <article class="entry-meta">
            <div class="entry-header">
                <h2 class="entry-title"><?php echo wp_kses_post( $args['title'] ); ?></h2>
            </div>
            <div class="entry-content">
                <?php echo twentytwelve_store_do_shortcode( 'product_categories', array(
                    'number' => intval( $args['limit'] ),
                    'columns' => intval( $args['columns'] ),
                    'orderby' => esc_attr( $args['orderby'] ),
                    'parent' => esc_attr( $args['child_categories'] ),
                ) ); ?>
            </div>
        </article>
        <?php
    }
}
add_action( 'twentytwelve_store_homepage', 'twentytwelve_store_product_categories', 10 );

// Display recent products
function twentytwelve_store_recent_products() {
    if ( is_woocommerce_activated() ) {
        $args = apply_filters( 'twentytwelve_store_recent_products_args', array(
            'limit' => 3,
            'columns' => 3,
            'title' => __( 'Recent Products', 'twentytwelve-store'),
        ) );
        ?>
        <article class="entry-meta">
            <div class="entry-header">
                <h2 class="entry-title"><?php echo wp_kses_post( $args['title'] ); ?></h2>
            </div>
            <div class="entry-content">
                <?php echo twentytwelve_store_do_shortcode( 'recent_products', array(
                    'per_page' => intval( $args['limit'] ),
                    'columns' => intval( $args['columns'] ),
                ) ); ?>
            </div>
        </article>
        <?php
    }
}
add_action( 'twentytwelve_store_homepage', 'twentytwelve_store_recent_products', 20 );

// Display featured products
function twentytwelve_store_featured_products() {
    if ( is_woocommerce_activated() ) {
        $args = apply_filters( 'twentytwelve_store_featured_products_args', array(
            'limit' => 3,
            'columns' => 3,
            'orderby' => 'date',
            'order' => 'desc',
            'title' => __( 'Featured Products', 'twentytwelve-store' ),
        ) );
        ?>
        <article class="entry-meta">
            <div class="entry-header">
                <h2 class="entry-title"><?php echo wp_kses_post( $args['title'] ); ?></h2>
            </div>
            <div class="entry-content">
                <?php echo twentytwelve_store_do_shortcode( 'featured_products', array(
                    'per_page' => intval( $args['limit'] ),
                    'columns' => intval( $args['columns'] ),
                    'orderby' => esc_attr( $args['orderby'] ),
                    'order' => esc_attr( $args['order'] ),
                ) ); ?>
            </div>
        </article>
        <?php
    }
}
add_action( 'twentytwelve_store_homepage', 'twentytwelve_store_featured_products', 30 );

// Display on sale products
function twentytwelve_store_on_sale_products() {
    if ( is_woocommerce_activated() ) {
        $args = apply_filters( 'twentytwelve_store_on_sale_products_args', array(
            'limit' => 3,
            'columns' => 3,
            'title' => __( 'On Sale', 'twentytwelve-store' ),
        ) );
        ?>
        <article class="entry-meta">
            <div class="entry-header">
                <h2 class="entry-title"><?php echo wp_kses_post( $args['title'] ); ?></h2>
            </div>
            <div class="entry-content">
                <?php echo twentytwelve_store_do_shortcode( 'sale_products', array(
                    'per_page' => intval( $args['limit'] ),
                    'columns' => intval( $args['columns'] ),
                ) ); ?>
            </div>
        </article>
        <?php
    }
}
add_action( 'twentytwelve_store_homepage', 'twentytwelve_store_on_sale_products', 40 );

// Body class
function twentytwelve_store_body_class( $classes = array() ) {
    if ( is_woocommerce_activated() ) {
        $classes[] = 'woocommerce-activated';
    }
    return $classes;
}
add_filter( 'body_class', 'twentytwelve_store_body_class' );

/**
 * Helpers
 *
 */

// Query WooCommerce activation
function is_woocommerce_activated() {
    return class_exists( 'woocommerce' ) ? true : false;
}

// Call a shortcode function by tag name
function twentytwelve_store_do_shortcode( $tag, $atts = array(), $content = null ) {
    global $shortcode_tags;
    if ( ! isset( $shortcode_tags[ $tag] ) ) {
        return false;
    }
    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}
