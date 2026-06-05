<?php
defined( 'ABSPATH' ) || exit;

/**
 * Kargo etiketini render eden ve URL'sini üreten sınıf.
 */
class KE_Label {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'handle_print_request' ) );
    }

    /**
     * ?ke_print=1&order_id=X isteğini yakala ve etiketi yazdır.
     */
    public static function handle_print_request() {
        if ( ! isset( $_GET['ke_print'] ) || '1' !== $_GET['ke_print'] ) {
            return;
        }

        $order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
        if ( ! $order_id ) {
            wp_die( esc_html__( 'Geçersiz sipariş.', 'kargo-etiketi' ) );
        }

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'kargo-etiketi' ) );
        }

        if ( ! isset( $_GET['ke_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['ke_nonce'] ) ), 'ke_print_' . $order_id ) ) {
            wp_die( esc_html__( 'Güvenlik doğrulaması başarısız.', 'kargo-etiketi' ) );
        }

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            wp_die( esc_html__( 'Sipariş bulunamadı.', 'kargo-etiketi' ) );
        }

        // Yazdırma kaydı + entegrasyon işlemlerini etiket çıkmadan önce yap
        self::record_print( $order );

        self::render_label( $order );
        exit;
    }

    /**
     * Yazdırma istatistiğini kaydet ve oto-durum güncellemeyi tetikle.
     */
    private static function record_print( $order ) {
        // Yazdırma zaman damgası ve sayacı
        $order->update_meta_data( '_ke_printed_at',    current_time( 'timestamp' ) );
        $order->update_meta_data( '_ke_printed_count', (int) $order->get_meta( '_ke_printed_count' ) + 1 );
        $order->save();

        // Otomatik sipariş durumu güncelleme
        if ( get_option( 'ke_auto_status', 0 ) ) {
            $target_status = get_option( 'ke_auto_status_value', 'wc-completed' );
            // WC durumları "wc-" prefix'li saklanır ama set_status "processing" gibi alır
            $target_status = str_replace( 'wc-', '', $target_status );

            if ( $order->get_status() !== $target_status ) {
                $order->update_status(
                    $target_status,
                    __( 'Kargo Etiketi eklentisi tarafından otomatik güncellendi (etiket yazdırıldı).', 'kargo-etiketi' )
                );
            }
        }
    }

    /**
     * Belirli bir sipariş için yazdır URL'si oluştur.
     */
    public static function get_print_url( $order_id ) {
        return add_query_arg(
            array(
                'ke_print'  => '1',
                'order_id'  => absint( $order_id ),
                'ke_nonce'  => wp_create_nonce( 'ke_print_' . $order_id ),
            ),
            admin_url()
        );
    }

    /**
     * Sipariş verisini derleyerek etiketi yazdır.
     */
    public static function render_label( $order ) {
        $sender = KE_Settings::get_sender();

        $payment_type = KE_Settings::get_payment_type( $order );

        // Sipariş ürünleri
        $items = array();
        if ( get_option( 'ke_show_products', 0 ) ) {
            foreach ( $order->get_items() as $item ) {
                $items[] = array(
                    'name' => $item->get_name(),
                    'qty'  => $item->get_quantity(),
                );
            }
        }

        // Sipariş notu
        $order_note = '';
        if ( get_option( 'ke_show_order_note', 0 ) ) {
            $order_note = $order->get_customer_note();
        }

        // Kargo takip numarası
        $tracking_number = '';
        if ( get_option( 'ke_show_tracking', 0 ) ) {
            $tracking_number = $order->get_meta( '_ke_tracking_number' );
        }

        $data = array(
            'order'          => $order,
            'order_id'       => $order->get_id(),
            'order_number'   => $order->get_order_number(),
            'order_date'     => wc_format_datetime( $order->get_date_created(), 'd.m.Y' ),
            'sender'         => $sender,
            'payment_type'   => $payment_type,
            'items'          => $items,
            'order_note'     => $order_note,
            'tracking_number'=> $tracking_number,
            'label_size'     => get_option( 'ke_label_size', 'a6' ),
            'recipient'      => array(
                'name'     => trim( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ),
                'address1' => $order->get_shipping_address_1(),
                'address2' => $order->get_shipping_address_2(),
                'postcode' => $order->get_shipping_postcode(),
                'city'     => $order->get_shipping_city(),
                'state'    => $order->get_shipping_state(),
                'country'  => $order->get_shipping_country(),
                'phone'    => $order->get_billing_phone(),
            ),
            'package_count'  => apply_filters( 'ke_package_count', 1, $order ),
        );

        // Teslimat adresi boşsa fatura adresini kullan
        if ( '' === $data['recipient']['name'] ) {
            $data['recipient']['name'] = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
        }
        if ( '' === $data['recipient']['address1'] ) {
            $data['recipient']['address1'] = $order->get_billing_address_1();
            $data['recipient']['address2'] = $order->get_billing_address_2();
            $data['recipient']['postcode']  = $order->get_billing_postcode();
            $data['recipient']['city']      = $order->get_billing_city();
            $data['recipient']['state']     = $order->get_billing_state();
            $data['recipient']['country']   = $order->get_billing_country();
        }

        // Eyalet kodunu okunabilir ada çevir
        $states = WC()->countries->get_states( $data['recipient']['country'] );
        if ( $states && isset( $states[ $data['recipient']['state'] ] ) ) {
            $data['recipient']['state'] = $states[ $data['recipient']['state'] ];
        }

        $template = KE_PLUGIN_DIR . 'templates/label.php';
        if ( file_exists( $template ) ) {
            include $template;
        }
    }
}
