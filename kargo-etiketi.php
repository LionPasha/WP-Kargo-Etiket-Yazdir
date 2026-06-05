<?php
/**
 * Plugin Name:       Kargo Etiketi
 * Plugin URI:        https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir
 * Description:       WooCommerce siparisleri icin tek tikla profesyonel kargo etiketi olusturur, yazdirir ve siparis durumunu otomatik gunceller.
 * Version:           1.1.0
 * Author:            Ahmet YURUK
 * Author URI:        https://wpwix.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kargo-etiketi
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * WC requires at least: 6.0
 * WC tested up to:   9.0
 */

defined( 'ABSPATH' ) || exit;

define( 'KE_VERSION',     '1.1.0' );
define( 'KE_PLUGIN_FILE', __FILE__ );
define( 'KE_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'KE_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

/**
 * WooCommerce HPOS (Custom Order Tables) compatibility.
 */
add_action( 'before_woocommerce_init', function () {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
} );

/**
 * WooCommerce missing notice.
 */
function ke_woocommerce_missing_notice() {
    echo '<div class="notice notice-error"><p>'
        . esc_html__( 'Kargo Etiketi eklentisi icin WooCommerce gereklidir.', 'kargo-etiketi' )
        . '</p></div>';
}

/**
 * Boot the plugin.
 */
function ke_init() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'ke_woocommerce_missing_notice' );
        return;
    }

    require_once KE_PLUGIN_DIR . 'includes/class-ke-settings.php';
    require_once KE_PLUGIN_DIR . 'includes/class-ke-label.php';
    require_once KE_PLUGIN_DIR . 'includes/class-ke-admin.php';

    KE_Settings::init();
    KE_Label::init();
    KE_Admin::init();
}
add_action( 'plugins_loaded', 'ke_init' );

/**
 * Plugin list – add "Ayarlar" action link.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
    $settings = '<a href="' . esc_url( admin_url( 'admin.php?page=kargo-etiketi-settings' ) ) . '">'
        . esc_html__( 'Ayarlar', 'kargo-etiketi' )
        . '</a>';
    array_unshift( $links, $settings );
    return $links;
} );

/**
 * Plugin list – row meta links.
 */
add_filter( 'plugin_row_meta', function ( $links, $file ) {
    if ( plugin_basename( __FILE__ ) !== $file ) {
        return $links;
    }
    $links[] = '<a href="https://wpwix.com" target="_blank">' . esc_html__( 'Gelistirici', 'kargo-etiketi' ) . '</a>';
    $links[] = '<a href="https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir" target="_blank">GitHub</a>';

    // "Gerekli: WooCommerce" satırı
    $wc_url  = admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );
    $links[] = sprintf(
        '%s: <a href="%s" target="_blank">WooCommerce</a>',
        esc_html__( 'Gerekli', 'kargo-etiketi' ),
        esc_url( $wc_url )
    );

    return $links;
}, 10, 2 );

/**
 * Activation hook – load defaults from WooCommerce store settings.
 */
register_activation_hook( __FILE__, function () {

    $wc_address  = get_option( 'woocommerce_store_address', '' );
    $wc_address2 = get_option( 'woocommerce_store_address_2', '' );
    $wc_city     = get_option( 'woocommerce_store_city', '' );
    $wc_postcode = get_option( 'woocommerce_store_postcode', '' );
    $wc_country  = get_option( 'woocommerce_default_country', '' );

    $full_address = trim( $wc_address . ( $wc_address2 ? "\n" . $wc_address2 : '' ) );

    $city_parts   = array_filter( array( $wc_city, $wc_postcode ) );
    $country_code = strstr( $wc_country, ':', true ) ?: $wc_country;
    if ( $country_code && 'TR' !== strtoupper( $country_code ) ) {
        $city_parts[] = $country_code;
    }
    $city_line = implode( ' / ', $city_parts );

    $defaults = array(
        'ke_sender_name'         => get_bloginfo( 'name' ),
        'ke_sender_address'      => $full_address,
        'ke_sender_city'         => $city_line,
        'ke_sender_phone'        => get_option( 'woocommerce_store_phone', '' ),
        'ke_payment_mode'        => 'auto',
        'ke_payment_manual_text' => 'Gonderici Odemeli',
        'ke_show_products'       => 0,
        'ke_show_order_note'     => 0,
        'ke_label_size'          => 'a6',
        'ke_logo_url'            => '',
        'ke_logo_show'           => 0,
        'ke_logo_position'       => 'left',
        'ke_logo_max_height'     => 60,
        'ke_auto_status'         => 0,
        'ke_auto_status_value'   => 'wc-completed',
        'ke_show_tracking'       => 0,
    );

    foreach ( $defaults as $key => $value ) {
        if ( false === get_option( $key ) ) {
            update_option( $key, $value );
        }
    }
} );
