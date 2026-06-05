<?php
defined( 'ABSPATH' ) || exit;

/**
 * Sipariş listesi ve sipariş detay sayfası entegrasyonu.
 */
class KE_Admin {

    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        /* ---- Sipariş listesi – satır aksiyonu ---- */
        add_filter( 'woocommerce_admin_order_actions', array( __CLASS__, 'add_order_list_action' ), 10, 2 );

        /* ---- Sipariş listesi – SÜTUN (HPOS + Klasik) ---- */
        add_filter( 'manage_woocommerce_page_wc-orders_columns', array( __CLASS__, 'add_column' ) );
        add_filter( 'manage_edit-shop_order_columns',             array( __CLASS__, 'add_column' ) );
        add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
        add_action( 'manage_shop_order_posts_custom_column',            array( __CLASS__, 'render_column_classic' ), 10, 2 );

        /* ---- Toplu aksiyon ---- */
        add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( __CLASS__, 'add_bulk_action' ) );
        add_filter( 'bulk_actions-edit-shop_order',            array( __CLASS__, 'add_bulk_action' ) );
        add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( __CLASS__, 'handle_bulk_action' ), 10, 3 );
        add_filter( 'handle_bulk_actions-edit-shop_order',             array( __CLASS__, 'handle_bulk_action' ), 10, 3 );

        /* ---- Sipariş detayı – meta kutusu ---- */
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );

        /* ---- AJAX: Takip numarası kaydet ---- */
        add_action( 'wp_ajax_ke_save_tracking', array( __CLASS__, 'ajax_save_tracking' ) );
    }

    /* ------------------------------------------------------------------
     * Varlıklar
     * ------------------------------------------------------------------ */

    public static function enqueue_assets( $hook ) {
        $order_screens = array( 'woocommerce_page_wc-orders', 'edit.php', 'post.php', 'post-new.php' );
        if ( ! in_array( $hook, $order_screens, true ) ) {
            return;
        }
        wp_enqueue_style( 'ke-admin', KE_PLUGIN_URL . 'assets/css/admin.css', array(), KE_VERSION );
        wp_enqueue_script( 'ke-admin', KE_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), KE_VERSION, true );
        wp_localize_script( 'ke-admin', 'keAdmin', array(
            'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
            'trackingNonce' => wp_create_nonce( 'ke_tracking_nonce' ),
            'saving'        => __( 'Kaydediliyor…', 'kargo-etiketi' ),
            'saved'         => __( 'Kaydedildi ✓', 'kargo-etiketi' ),
            'error'         => __( 'Hata!', 'kargo-etiketi' ),
        ) );
    }

    /* ------------------------------------------------------------------
     * Sipariş listesi – satır aksiyonu
     * ------------------------------------------------------------------ */

    public static function add_order_list_action( $actions, $order ) {
        $actions['ke_print'] = array(
            'url'    => KE_Label::get_print_url( $order->get_id() ),
            'name'   => __( 'Kargo Etiketi Yazdır', 'kargo-etiketi' ),
            'action' => 'ke_print',
            'target' => '_blank',
        );
        return $actions;
    }

    /* ------------------------------------------------------------------
     * Sipariş listesi – Sütun
     * ------------------------------------------------------------------ */

    public static function add_column( $columns ) {
        // "actions" sütunundan önce ekle
        $new = array();
        foreach ( $columns as $key => $label ) {
            if ( 'wc_actions' === $key || 'order_actions' === $key ) {
                $new['ke_print_status'] = '<span class="ke-col-icon dashicons dashicons-printer" title="'
                    . esc_attr__( 'Kargo Etiketi', 'kargo-etiketi' ) . '"></span>';
            }
            $new[ $key ] = $label;
        }
        return $new;
    }

    /** HPOS: ($column, $order) */
    public static function render_column( $column, $order ) {
        if ( 'ke_print_status' !== $column ) {
            return;
        }
        self::render_column_content( $order );
    }

    /** Klasik posts: ($column, $post_id) */
    public static function render_column_classic( $column, $post_id ) {
        if ( 'ke_print_status' !== $column ) {
            return;
        }
        $order = wc_get_order( $post_id );
        if ( $order ) {
            self::render_column_content( $order );
        }
    }

    private static function render_column_content( $order ) {
        $printed_at = (int) $order->get_meta( '_ke_printed_at' );
        $print_url  = KE_Label::get_print_url( $order->get_id() );

        echo '<div class="ke-col-wrap">';

        // Yazdır butonu
        echo '<a href="' . esc_url( $print_url ) . '" target="_blank"
                 class="ke-col-btn tips"
                 data-tip="' . esc_attr__( 'Kargo Etiketi Yazdır', 'kargo-etiketi' ) . '">
                 <span class="dashicons dashicons-printer"></span>
              </a>';

        // Durum badge
        if ( $printed_at ) {
            $count = (int) $order->get_meta( '_ke_printed_count' );
            $date  = date_i18n( 'd.m.Y H:i', $printed_at );
            echo '<span class="ke-badge ke-badge-printed tips"
                        data-tip="' . esc_attr( sprintf( __( '%s tarihinde yazdırıldı (%d kez)', 'kargo-etiketi' ), $date, $count ) ) . '">
                      ✓ ' . esc_html( date_i18n( 'd.m', $printed_at ) ) . '
                  </span>';
        } else {
            echo '<span class="ke-badge ke-badge-pending tips"
                        data-tip="' . esc_attr__( 'Henüz yazdırılmadı', 'kargo-etiketi' ) . '">
                      —
                  </span>';
        }

        echo '</div>';
    }

    /* ------------------------------------------------------------------
     * Toplu aksiyon
     * ------------------------------------------------------------------ */

    public static function add_bulk_action( $actions ) {
        $actions['ke_print_labels'] = __( 'Kargo Etiketi Yazdır', 'kargo-etiketi' );
        return $actions;
    }

    public static function handle_bulk_action( $redirect_to, $action, $ids ) {
        if ( 'ke_print_labels' !== $action ) {
            return $redirect_to;
        }
        $urls = array();
        foreach ( $ids as $id ) {
            $urls[] = KE_Label::get_print_url( absint( $id ) );
        }
        return add_query_arg(
            array(
                'ke_bulk_print' => '1',
                'ke_urls'       => rawurlencode( wp_json_encode( $urls ) ),
            ),
            $redirect_to
        );
    }

    /* ------------------------------------------------------------------
     * Sipariş detayı – meta kutusu
     * ------------------------------------------------------------------ */

    public static function add_meta_box() {
        $screens = array( 'shop_order' );

        if ( class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) ) {
            $screens[] = wc_get_page_screen_id( 'shop-order' );
        }

        foreach ( array_unique( $screens ) as $screen ) {
            add_meta_box(
                'ke_print_meta_box',
                __( 'Kargo Etiketi', 'kargo-etiketi' ),
                array( __CLASS__, 'render_meta_box' ),
                $screen,
                'side',
                'high'
            );
        }
    }

    public static function render_meta_box( $post_or_order ) {
        $order = ( $post_or_order instanceof WP_Post )
            ? wc_get_order( $post_or_order->ID )
            : $post_or_order;

        if ( ! $order ) {
            return;
        }

        $url        = KE_Label::get_print_url( $order->get_id() );
        $printed_at = (int) $order->get_meta( '_ke_printed_at' );
        $count      = (int) $order->get_meta( '_ke_printed_count' );
        $tracking   = $order->get_meta( '_ke_tracking_number' );
        $show_track = get_option( 'ke_show_tracking', 0 );
        ?>

        <?php if ( $printed_at ) : ?>
        <p class="ke-meta-status ke-meta-printed">
            <span class="dashicons dashicons-yes-alt"></span>
            <?php
            printf(
                esc_html__( '%1$s tarihinde yazdırıldı (%2$d kez)', 'kargo-etiketi' ),
                esc_html( date_i18n( 'd.m.Y H:i', $printed_at ) ),
                $count
            );
            ?>
        </p>
        <?php else : ?>
        <p class="ke-meta-status ke-meta-pending">
            <span class="dashicons dashicons-clock"></span>
            <?php esc_html_e( 'Henüz yazdırılmadı', 'kargo-etiketi' ); ?>
        </p>
        <?php endif; ?>

        <p>
            <a href="<?php echo esc_url( $url ); ?>"
               target="_blank"
               class="button button-primary ke-print-btn">
                <span class="dashicons dashicons-printer"></span>
                <?php esc_html_e( 'Kargo Etiketi Yazdır', 'kargo-etiketi' ); ?>
            </a>
        </p>

        <?php if ( $show_track ) : ?>
        <hr style="margin:12px 0;" />
        <p><strong><?php esc_html_e( 'Kargo Takip No:', 'kargo-etiketi' ); ?></strong></p>
        <p style="display:flex;gap:4px;">
            <input type="text"
                   id="ke-tracking-input"
                   class="ke-tracking-input"
                   value="<?php echo esc_attr( $tracking ); ?>"
                   placeholder="<?php esc_attr_e( 'Takip numarasını girin', 'kargo-etiketi' ); ?>"
                   data-order-id="<?php echo esc_attr( $order->get_id() ); ?>" />
            <button type="button" class="button ke-tracking-save-btn" id="ke-tracking-save">
                <?php esc_html_e( 'Kaydet', 'kargo-etiketi' ); ?>
            </button>
        </p>
        <p id="ke-tracking-msg" style="display:none;font-size:12px;color:#008a00;margin-top:4px;"></p>
        <?php endif; ?>

        <p class="description" style="margin-top:8px;">
            <?php esc_html_e( 'Yeni sekmede açılan sayfayı Ctrl+P ile yazdırın.', 'kargo-etiketi' ); ?>
        </p>
        <?php
    }

    /* ------------------------------------------------------------------
     * AJAX – Takip numarası kaydet
     * ------------------------------------------------------------------ */

    public static function ajax_save_tracking() {
        check_ajax_referer( 'ke_tracking_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json_error( array( 'message' => 'Yetersiz yetki.' ) );
        }

        $order_id = absint( $_POST['order_id'] ?? 0 );
        $tracking = sanitize_text_field( wp_unslash( $_POST['tracking'] ?? '' ) );

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            wp_send_json_error( array( 'message' => 'Sipariş bulunamadı.' ) );
        }

        $order->update_meta_data( '_ke_tracking_number', $tracking );
        $order->save();

        // Sipariş notuna da ekle (opsiyonel — isteğe göre kaldırılabilir)
        if ( $tracking ) {
            $note = sprintf(
                /* translators: %s: takip numarası */
                __( 'Kargo takip numarası: %s', 'kargo-etiketi' ),
                $tracking
            );
            $existing = $order->get_meta( '_ke_tracking_note_added' );
            if ( ! $existing ) {
                $order->add_order_note( $note );
                $order->update_meta_data( '_ke_tracking_note_added', '1' );
                $order->save();
            }
        }

        wp_send_json_success( array( 'message' => __( 'Takip numarası kaydedildi.', 'kargo-etiketi' ) ) );
    }
}
