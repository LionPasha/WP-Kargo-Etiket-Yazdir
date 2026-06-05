<?php
defined( 'ABSPATH' ) || exit;

/**
 * Gönderici bilgileri ve etiket ayar sayfası.
 */
class KE_Settings {

    public static function init() {
        add_action( 'admin_menu',           array( __CLASS__, 'add_menu' ) );
        add_action( 'admin_init',           array( __CLASS__, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_settings_assets' ) );
    }

    public static function add_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Kargo Etiketi Ayarları', 'kargo-etiketi' ),
            __( 'Kargo Etiketi', 'kargo-etiketi' ),
            'manage_woocommerce',
            'kargo-etiketi-settings',
            array( __CLASS__, 'render_page' )
        );
    }

    public static function enqueue_settings_assets( $hook ) {
        if ( 'woocommerce_page_kargo-etiketi-settings' !== $hook ) {
            return;
        }
        wp_enqueue_media(); // WordPress medya kütüphanesi
        wp_enqueue_style( 'ke-admin',     KE_PLUGIN_URL . 'assets/css/admin.css',    array(), KE_VERSION );
        wp_enqueue_style( 'ke-label',     KE_PLUGIN_URL . 'assets/css/label.css',    array(), KE_VERSION );
        wp_enqueue_script( 'ke-settings', KE_PLUGIN_URL . 'assets/js/settings.js',   array( 'jquery', 'media-upload' ), KE_VERSION, true );
        wp_localize_script( 'ke-settings', 'keSettings', array(
            'mediaTitle'  => __( 'Firma Logosu Seç', 'kargo-etiketi' ),
            'mediaButton' => __( 'Bu Logoyu Kullan', 'kargo-etiketi' ),
        ) );
    }

    public static function register_settings() {

        /* ---- BÖLÜM 1: Gönderici Bilgileri ---- */
        add_settings_section( 'ke_sender_section', __( 'Gönderici Bilgileri', 'kargo-etiketi' ), null, 'kargo-etiketi-settings' );

        $sender_fields = array(
            'ke_sender_name'    => array( 'label' => __( 'Firma / Gönderici Adı', 'kargo-etiketi' ), 'type' => 'text',     'preview' => 'sender-name' ),
            'ke_sender_address' => array( 'label' => __( 'Adres',                  'kargo-etiketi' ), 'type' => 'textarea', 'preview' => 'sender-address' ),
            'ke_sender_city'    => array( 'label' => __( 'İlçe / İl',              'kargo-etiketi' ), 'type' => 'text',     'preview' => 'sender-city' ),
            'ke_sender_phone'   => array( 'label' => __( 'Telefon',                'kargo-etiketi' ), 'type' => 'text',     'preview' => 'sender-phone' ),
        );
        foreach ( $sender_fields as $id => $field ) {
            register_setting( 'ke_settings_group', $id, array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
            add_settings_field( $id, $field['label'], array( __CLASS__, 'render_field' ), 'kargo-etiketi-settings', 'ke_sender_section', array_merge( $field, array( 'id' => $id ) ) );
        }

        /* ---- BÖLÜM 2: Etiket Ayarları ---- */
        add_settings_section( 'ke_label_section', __( 'Etiket Ayarları', 'kargo-etiketi' ), null, 'kargo-etiketi-settings' );

        // Ödeme tipi modu
        register_setting( 'ke_settings_group', 'ke_payment_mode',        array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ke_settings_group', 'ke_payment_manual_text', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        add_settings_field( 'ke_payment_mode', __( 'Ödeme Tipi', 'kargo-etiketi' ), array( __CLASS__, 'render_payment_mode_field' ), 'kargo-etiketi-settings', 'ke_label_section' );

        // Ürünleri göster
        register_setting( 'ke_settings_group', 'ke_show_products', array( 'sanitize_callback' => 'absint' ) );
        add_settings_field( 'ke_show_products', __( 'Sipariş Ürünleri', 'kargo-etiketi' ), array( __CLASS__, 'render_checkbox_field' ), 'kargo-etiketi-settings', 'ke_label_section',
            array( 'id' => 'ke_show_products', 'desc' => __( 'Ürün adı ve miktarını etikette göster (fiyat gösterilmez)', 'kargo-etiketi' ), 'preview' => 'products-section' )
        );

        // Sipariş notunu göster
        register_setting( 'ke_settings_group', 'ke_show_order_note', array( 'sanitize_callback' => 'absint' ) );
        add_settings_field( 'ke_show_order_note', __( 'Sipariş Notu', 'kargo-etiketi' ), array( __CLASS__, 'render_checkbox_field' ), 'kargo-etiketi-settings', 'ke_label_section',
            array( 'id' => 'ke_show_order_note', 'desc' => __( 'Müşterinin sipariş notunu etikette göster', 'kargo-etiketi' ), 'preview' => 'note-section' )
        );

        // Etiket boyutu
        register_setting( 'ke_settings_group', 'ke_label_size', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        add_settings_field( 'ke_label_size', __( 'Etiket Boyutu', 'kargo-etiketi' ), array( __CLASS__, 'render_size_field' ), 'kargo-etiketi-settings', 'ke_label_section' );

        /* ---- BÖLÜM 3: Sipariş Entegrasyonu ---- */
        add_settings_section(
            'ke_integration_section',
            __( 'Sipariş Entegrasyonu', 'kargo-etiketi' ),
            function () {
                echo '<p class="description">' . esc_html__( 'Etiket yazdırıldığında WooCommerce siparişine otomatik işlemler uygula.', 'kargo-etiketi' ) . '</p>';
            },
            'kargo-etiketi-settings'
        );

        // Otomatik durum güncelleme
        register_setting( 'ke_settings_group', 'ke_auto_status',       array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'ke_settings_group', 'ke_auto_status_value', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        add_settings_field( 'ke_auto_status', __( 'Otomatik Durum Güncelleme', 'kargo-etiketi' ), array( __CLASS__, 'render_auto_status_field' ), 'kargo-etiketi-settings', 'ke_integration_section' );

        // Kargo takip no
        register_setting( 'ke_settings_group', 'ke_show_tracking', array( 'sanitize_callback' => 'absint' ) );
        add_settings_field( 'ke_show_tracking', __( 'Kargo Takip Numarası', 'kargo-etiketi' ), array( __CLASS__, 'render_checkbox_field' ), 'kargo-etiketi-settings', 'ke_integration_section',
            array( 'id' => 'ke_show_tracking', 'desc' => __( 'Sipariş detayında takip numarası giriş alanı göster, etikete de yazdır', 'kargo-etiketi' ) )
        );

        /* ---- BÖLÜM 4: Logo ---- */
        add_settings_section( 'ke_logo_section', __( 'Firma Logosu', 'kargo-etiketi' ), null, 'kargo-etiketi-settings' );

        register_setting( 'ke_settings_group', 'ke_logo_url',        array( 'sanitize_callback' => array( __CLASS__, 'sanitize_logo_url' ) ) );
        register_setting( 'ke_settings_group', 'ke_logo_show',       array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'ke_settings_group', 'ke_logo_position',   array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ke_settings_group', 'ke_logo_max_height', array( 'sanitize_callback' => 'absint' ) );

        add_settings_field( 'ke_logo_upload',    __( 'Logo Görseli', 'kargo-etiketi' ),  array( __CLASS__, 'render_logo_field' ),    'kargo-etiketi-settings', 'ke_logo_section' );
        add_settings_field( 'ke_logo_show',      __( 'Etikette Göster', 'kargo-etiketi' ), array( __CLASS__, 'render_checkbox_field' ), 'kargo-etiketi-settings', 'ke_logo_section',
            array( 'id' => 'ke_logo_show', 'desc' => __( 'Logoyu kargo etiketinde göster', 'kargo-etiketi' ), 'preview' => 'logo-section' )
        );
        add_settings_field( 'ke_logo_position',  __( 'Konum', 'kargo-etiketi' ),         array( __CLASS__, 'render_logo_position_field' ), 'kargo-etiketi-settings', 'ke_logo_section' );
        add_settings_field( 'ke_logo_max_height', __( 'Maksimum Yükseklik', 'kargo-etiketi' ), array( __CLASS__, 'render_logo_height_field' ), 'kargo-etiketi-settings', 'ke_logo_section' );
    }

    /* ------------------------------------------------------------------
     * Alan render fonksiyonları
     * ------------------------------------------------------------------ */

    public static function render_field( $args ) {
        $id      = esc_attr( $args['id'] );
        $preview = isset( $args['preview'] ) ? 'data-ke-preview="' . esc_attr( $args['preview'] ) . '"' : '';

        if ( 'textarea' === $args['type'] ) {
            echo "<textarea id='{$id}' name='{$id}' rows='3' class='large-text' {$preview}>" . esc_textarea( get_option( $id, '' ) ) . '</textarea>';
        } else {
            $value = esc_attr( get_option( $id, '' ) );
            echo "<input type='text' id='{$id}' name='{$id}' value='{$value}' class='regular-text' {$preview} />";
        }
    }

    public static function render_checkbox_field( $args ) {
        $id      = esc_attr( $args['id'] );
        $checked = checked( 1, get_option( $id, 0 ), false );
        $desc    = esc_html( $args['desc'] );
        $preview = isset( $args['preview'] ) ? 'data-ke-preview="' . esc_attr( $args['preview'] ) . '"' : '';
        echo "<label><input type='checkbox' id='{$id}' name='{$id}' value='1' {$checked} {$preview} /> {$desc}</label>";
    }

    public static function render_payment_mode_field() {
        $mode   = get_option( 'ke_payment_mode', 'auto' );
        $manual = get_option( 'ke_payment_manual_text', 'Gönderici Ödemeli' );
        ?>
        <fieldset>
            <label>
                <input type="radio" name="ke_payment_mode" value="auto" <?php checked( $mode, 'auto' ); ?> data-ke-payment-mode />
                <?php esc_html_e( 'Siparişten otomatik çek', 'kargo-etiketi' ); ?>
                <span class="description">&mdash; <?php esc_html_e( 'Kapıda ödeme = Alıcı Ödemeli, diğerleri = Gönderici Ödemeli', 'kargo-etiketi' ); ?></span>
            </label>
            <br><br>
            <label>
                <input type="radio" name="ke_payment_mode" value="manual" <?php checked( $mode, 'manual' ); ?> data-ke-payment-mode />
                <?php esc_html_e( 'Sabit metin kullan:', 'kargo-etiketi' ); ?>
            </label>
            <input type="text" name="ke_payment_manual_text" id="ke_payment_manual_text"
                   value="<?php echo esc_attr( $manual ); ?>"
                   class="regular-text" style="margin-left:8px;"
                   data-ke-preview="payment-type"
                   <?php echo 'auto' === $mode ? 'disabled' : ''; ?> />
        </fieldset>
        <?php
    }

    public static function render_auto_status_field() {
        $enabled = get_option( 'ke_auto_status', 0 );
        $value   = get_option( 'ke_auto_status_value', 'wc-completed' );
        $statuses = wc_get_order_statuses();
        ?>
        <fieldset>
            <label>
                <input type="checkbox" name="ke_auto_status" id="ke_auto_status" value="1" <?php checked( 1, $enabled ); ?> />
                <?php esc_html_e( 'Etiket yazdırıldığında siparişi otomatik güncelle', 'kargo-etiketi' ); ?>
            </label>
            <br><br>
            <label for="ke_auto_status_value"><?php esc_html_e( 'Hedef Sipariş Durumu:', 'kargo-etiketi' ); ?></label>
            <select name="ke_auto_status_value" id="ke_auto_status_value" <?php echo $enabled ? '' : 'disabled'; ?>>
                <?php foreach ( $statuses as $slug => $label ) : ?>
                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $value, $slug ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php esc_html_e( 'Varsayılan kapalıdır. Aktif ettiğinizde her etiket yazdırma işleminde sipariş durumu otomatik değişir.', 'kargo-etiketi' ); ?>
            </p>
        </fieldset>
        <?php
    }

    public static function render_logo_field() {
        $url           = get_option( 'ke_logo_url', '' );
        $site_logo_url = self::get_site_logo_url();
        ?>

        <!-- Önizleme kutusu -->
        <div class="ke-logo-current <?php echo $url ? '' : 'ke-logo-empty'; ?>" id="ke-logo-preview-wrap" style="margin-bottom:8px;">
            <span id="ke-logo-placeholder" <?php echo $url ? 'style="display:none"' : ''; ?>>
                <?php esc_html_e( 'Logo seçilmedi', 'kargo-etiketi' ); ?>
            </span>
            <img id="ke-logo-preview-img"
                 src="<?php echo esc_url( $url ); ?>"
                 alt=""
                 <?php echo $url ? '' : 'style="display:none"'; ?> />
        </div>

        <!-- URL alanı — her zaman görünür, form'a dahil, kayıt garantili -->
        <div style="display:flex;gap:6px;align-items:center;margin-bottom:8px;">
            <input type="text"
                   id="ke_logo_url"
                   name="ke_logo_url"
                   value="<?php echo esc_attr( $url ); ?>"
                   class="large-text ke-logo-url-field"
                   placeholder="<?php esc_attr_e( 'Logo URL\'si (https://…)', 'kargo-etiketi' ); ?>"
                   style="font-size:12px;" />
            <button type="button" class="button" id="ke-logo-preview-btn">
                <?php esc_html_e( 'Önizle', 'kargo-etiketi' ); ?>
            </button>
        </div>

        <!-- Butonlar -->
        <div class="ke-logo-btn-group">
            <button type="button" class="button" id="ke-logo-upload-btn">
                📁 <?php esc_html_e( 'Medya Kütüphanesinden Seç', 'kargo-etiketi' ); ?>
            </button>

            <?php if ( $site_logo_url ) : ?>
            <button type="button" class="button" id="ke-logo-site-btn"
                    data-url="<?php echo esc_url( $site_logo_url ); ?>">
                🌐 <?php esc_html_e( 'Site Logosunu Kullan', 'kargo-etiketi' ); ?>
            </button>
            <?php else : ?>
            <button type="button" class="button" disabled
                    title="<?php esc_attr_e( 'WordPress Site Kimliği\'nde logo tanımlı değil', 'kargo-etiketi' ); ?>">
                🌐 <?php esc_html_e( 'Site Logosunu Kullan', 'kargo-etiketi' ); ?>
            </button>
            <?php endif; ?>

            <button type="button" class="button ke-logo-remove-btn" id="ke-logo-remove-btn"
                    <?php echo $url ? '' : 'style="display:none"'; ?>>
                ✕ <?php esc_html_e( 'Logoyu Kaldır', 'kargo-etiketi' ); ?>
            </button>
        </div>

        <p class="description" style="margin-top:6px;">
            <?php esc_html_e( 'URL\'yi doğrudan yazabilir, Medya Kütüphanesinden seçebilir veya siteye kayıtlı logoyu kullanabilirsiniz. Kaydetmeden önce "Önizle" ile kontrol edin.', 'kargo-etiketi' ); ?>
        </p>
        <?php
    }

    /**
     * WordPress site kimliği logosunun URL'sini döndürür.
     */
    /**
     * Logo URL sanitize — http/https geçerliyse olduğu gibi sakla,
     * değilse boş string döndür.
     */
    public static function sanitize_logo_url( $value ) {
        $value = trim( $value );
        if ( '' === $value ) {
            return '';
        }
        // Geçerli http/https URL değilse temizle
        if ( ! preg_match( '#^https?://#i', $value ) ) {
            return '';
        }
        return esc_url_raw( $value );
    }

    public static function get_site_logo_url() {
        // Önce WordPress tema logosunu dene (Özelleştirici → Site Kimliği)
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $src = wp_get_attachment_image_url( $logo_id, 'full' );
            if ( $src ) {
                return $src;
            }
        }

        // WooCommerce store logo (bazı temalarda store_logo option'ı bulunur)
        $wc_logo_id = get_option( 'site_logo' );
        if ( $wc_logo_id ) {
            $src = wp_get_attachment_image_url( $wc_logo_id, 'full' );
            if ( $src ) {
                return $src;
            }
        }

        return '';
    }

    public static function render_logo_position_field() {
        $pos = get_option( 'ke_logo_position', 'left' );
        $options = array(
            'left'   => __( 'Sol', 'kargo-etiketi' ),
            'center' => __( 'Orta', 'kargo-etiketi' ),
            'right'  => __( 'Sağ', 'kargo-etiketi' ),
        );
        echo '<select name="ke_logo_position" id="ke_logo_position" data-ke-preview="logo-position">';
        foreach ( $options as $val => $label ) {
            printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $pos, $val, false ), esc_html( $label ) );
        }
        echo '</select>';
    }

    public static function render_logo_height_field() {
        $h = absint( get_option( 'ke_logo_max_height', 60 ) );
        echo "<input type='number' name='ke_logo_max_height' id='ke_logo_max_height' value='" . esc_attr( $h ) . "' min='20' max='200' class='small-text' data-ke-preview='logo-height' /> px";
        echo '<p class="description">' . esc_html__( 'Etikette logonun maksimum yüksekliği (önerilen: 50–80 px).', 'kargo-etiketi' ) . '</p>';
    }

    public static function render_size_field() {
        $size = get_option( 'ke_label_size', 'a6' );
        $options = array(
            'a6'      => 'A6 (105×148 mm) – Standart',
            'a5'      => 'A5 (148×210 mm)',
            'thermal' => 'Termal (100×150 mm)',
        );
        echo '<select name="ke_label_size" id="ke_label_size">';
        foreach ( $options as $val => $label ) {
            printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $size, $val, false ), esc_html( $label ) );
        }
        echo '</select>';
    }

    /* ------------------------------------------------------------------
     * Sayfa render
     * ------------------------------------------------------------------ */

    /**
     * WooCommerce mağaza bilgilerini gönderici alanlarına aktar.
     * Ayarlar sayfasında "WC'den Doldur" butonuna basılınca çağrılır.
     */
    public static function sync_from_woocommerce() {
        $address  = get_option( 'woocommerce_store_address', '' );
        $address2 = get_option( 'woocommerce_store_address_2', '' );
        $city     = get_option( 'woocommerce_store_city', '' );
        $postcode = get_option( 'woocommerce_store_postcode', '' );
        $country  = get_option( 'woocommerce_default_country', '' );
        $phone    = get_option( 'woocommerce_store_phone', '' );

        $full_address = trim( $address . ( $address2 ? "\n" . $address2 : '' ) );
        $country_code = strstr( $country, ':', true ) ?: $country;
        $city_parts   = array_filter( array( $city, $postcode ) );
        if ( $country_code && 'TR' !== strtoupper( $country_code ) ) {
            $city_parts[] = $country_code;
        }

        update_option( 'ke_sender_name',    get_bloginfo( 'name' ) );
        update_option( 'ke_sender_address', $full_address );
        update_option( 'ke_sender_city',    implode( ' / ', $city_parts ) );
        update_option( 'ke_sender_phone',   $phone );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        // WC'den doldur aksiyonu
        if ( isset( $_POST['ke_sync_wc'] ) && check_admin_referer( 'ke_sync_wc_action' ) ) {
            self::sync_from_woocommerce();
            echo '<div class="notice notice-success is-dismissible"><p>'
                . esc_html__( 'Gönderici bilgileri WooCommerce mağaza ayarlarından güncellendi.', 'kargo-etiketi' )
                . '</p></div>';
        }

        $sender        = self::get_sender();
        $show_products = get_option( 'ke_show_products', 0 );
        $show_note     = get_option( 'ke_show_order_note', 0 );
        $logo_url      = get_option( 'ke_logo_url', '' );
        $logo_show     = get_option( 'ke_logo_show', 0 );
        $logo_pos      = get_option( 'ke_logo_position', 'left' );
        $logo_height   = absint( get_option( 'ke_logo_max_height', 60 ) );
        ?>
        <div class="wrap ke-settings-wrap">
            <h1><?php esc_html_e( 'Kargo Etiketi Ayarları', 'kargo-etiketi' ); ?></h1>

            <form method="post" style="display:inline-block;margin-bottom:12px;">
                <?php wp_nonce_field( 'ke_sync_wc_action' ); ?>
                <button type="submit" name="ke_sync_wc" value="1" class="button">
                    🔄 <?php esc_html_e( 'Gönderici Bilgilerini WooCommerce\'den Doldur', 'kargo-etiketi' ); ?>
                </button>
                <span class="description" style="margin-left:8px;">
                    <?php esc_html_e( 'Mağaza adı, adres ve telefonu WooCommerce ayarlarından otomatik çeker.', 'kargo-etiketi' ); ?>
                </span>
            </form>

            <div class="ke-settings-layout">

                <!-- Sol: Form -->
                <div class="ke-settings-form">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields( 'ke_settings_group' );
                        do_settings_sections( 'kargo-etiketi-settings' );
                        submit_button( __( 'Ayarları Kaydet', 'kargo-etiketi' ) );
                        ?>
                    </form>
                </div>

                <!-- Sağ: Önizleme -->
                <div class="ke-settings-preview-panel">
                    <h3 class="ke-preview-title">
                        <?php esc_html_e( 'Önizleme', 'kargo-etiketi' ); ?>
                        <span class="ke-preview-badge"><?php esc_html_e( 'Canlı', 'kargo-etiketi' ); ?></span>
                    </h3>

                    <div class="ke-preview-scale-wrapper">
                    <div class="ke-label-wrapper ke-preview-label" id="ke-live-preview">

                        <!-- Logo bölümü (önizleme) -->
                        <div class="ke-logo-section" id="prev-logo-section"
                             style="text-align:<?php echo esc_attr( $logo_pos ); ?>;<?php echo ( $logo_show && $logo_url ) ? '' : 'display:none;'; ?>">
                            <img id="prev-logo-img"
                                 src="<?php echo esc_url( $logo_url ); ?>"
                                 alt="logo"
                                 style="max-height:<?php echo esc_attr( $logo_height ); ?>px;" />
                        </div>

                        <section class="ke-section">
                            <h2 class="ke-section-title">GÖNDERİCİ</h2>
                            <table class="ke-info-table">
                                <tr><td class="ke-label-cell">Firma/Ad:</td>
                                    <td id="prev-sender-name"><?php echo esc_html( $sender['name'] ); ?></td></tr>
                                <tr><td class="ke-label-cell">Adres:</td>
                                    <td id="prev-sender-address"><?php echo nl2br( esc_html( $sender['address'] ) ); ?></td></tr>
                                <tr><td class="ke-label-cell">İlçe/İl:</td>
                                    <td id="prev-sender-city"><?php echo esc_html( $sender['city'] ); ?></td></tr>
                                <tr><td class="ke-label-cell">Telefon:</td>
                                    <td id="prev-sender-phone"><?php echo esc_html( $sender['phone'] ); ?></td></tr>
                            </table>
                        </section>

                        <section class="ke-section">
                            <h2 class="ke-section-title">GÖNDERİ DETAYLARI</h2>
                            <table class="ke-info-table">
                                <tr><td class="ke-label-cell">Sipariş No:</td><td>#1234</td></tr>
                                <tr><td class="ke-label-cell">Paket Adedi:</td><td>1</td></tr>
                                <tr><td class="ke-label-cell">Tarih:</td><td><?php echo esc_html( date_i18n( 'd.m.Y' ) ); ?></td></tr>
                                <tr><td class="ke-label-cell">Ödeme Tipi:</td>
                                    <td id="prev-payment-type"><?php echo esc_html( $sender['payment_type'] ); ?></td></tr>
                            </table>
                        </section>

                        <section class="ke-section ke-section-recipient">
                            <h2 class="ke-section-title">ALICI</h2>
                            <table class="ke-info-table">
                                <tr><td class="ke-label-cell">Ad Soyad:</td><td>Ahmet Yılmaz</td></tr>
                                <tr><td class="ke-label-cell">Adres:</td><td>Örnek Mahallesi, Test Sokak No:1</td></tr>
                                <tr><td class="ke-label-cell">Posta Kodu:</td><td>34000</td></tr>
                                <tr><td class="ke-label-cell">İlçe/İl:</td><td>Kadıköy / İstanbul</td></tr>
                                <tr><td class="ke-label-cell">Telefon:</td><td>0555 000 00 00</td></tr>
                            </table>
                        </section>

                        <section class="ke-section ke-section-products" id="prev-products-section"
                             style="<?php echo $show_products ? '' : 'display:none'; ?>">
                            <h2 class="ke-section-title">ÜRÜNLER</h2>
                            <table class="ke-info-table ke-products-table">
                                <tr><td>Örnek Ürün A</td><td class="ke-qty">×2</td></tr>
                                <tr><td>Örnek Ürün B</td><td class="ke-qty">×1</td></tr>
                            </table>
                        </section>

                        <section class="ke-section ke-section-note" id="prev-note-section"
                             style="<?php echo $show_note ? '' : 'display:none'; ?>">
                            <h2 class="ke-section-title">SİPARİŞ NOTU</h2>
                            <p class="ke-note-text">Lütfen dikkatli paketleyin.</p>
                        </section>

                    </div><!-- .ke-preview-label -->
                    </div><!-- .ke-preview-scale-wrapper -->
                </div><!-- .ke-settings-preview-panel -->

            </div><!-- .ke-settings-layout -->
        </div>
        <?php
    }

    /* ------------------------------------------------------------------
     * Yardımcı getter'lar
     * ------------------------------------------------------------------ */

    /**
     * Tüm gönderici ayarlarını tek dizide döndürür.
     */
    public static function get_sender() {
        $mode        = get_option( 'ke_payment_mode', 'auto' );
        $manual_text = get_option( 'ke_payment_manual_text', 'Gönderici Ödemeli' );

        return array(
            'name'          => get_option( 'ke_sender_name', get_bloginfo( 'name' ) ),
            'address'       => get_option( 'ke_sender_address', '' ),
            'city'          => get_option( 'ke_sender_city', '' ),
            'phone'         => get_option( 'ke_sender_phone', '' ),
            'payment_mode'  => $mode,
            'payment_type'  => 'manual' === $mode ? $manual_text : 'Gönderici Ödemeli',
            'logo_url'      => get_option( 'ke_logo_url', '' ),
            'logo_show'     => (bool) get_option( 'ke_logo_show', 0 ),
            'logo_position' => get_option( 'ke_logo_position', 'left' ),
            'logo_height'   => absint( get_option( 'ke_logo_max_height', 60 ) ),
        );
    }

    /**
     * Siparişe göre ödeme tipini tespit et.
     *
     * @param WC_Order $order
     * @return string
     */
    public static function get_payment_type( $order ) {
        $mode = get_option( 'ke_payment_mode', 'auto' );

        if ( 'manual' === $mode ) {
            return get_option( 'ke_payment_manual_text', 'Gönderici Ödemeli' );
        }

        // Otomatik mod: kapıda ödeme yöntemleri = Alıcı Ödemeli
        $cod_methods = apply_filters( 'ke_cod_payment_methods', array( 'cod', 'kapida_odeme', 'cash_on_delivery' ) );
        $method      = $order->get_payment_method();

        if ( in_array( $method, $cod_methods, true ) ) {
            return apply_filters( 'ke_payment_label_cod', __( 'Alıcı Ödemeli', 'kargo-etiketi' ), $order );
        }

        return apply_filters( 'ke_payment_label_prepaid', __( 'Gönderici Ödemeli', 'kargo-etiketi' ), $order );
    }
}
