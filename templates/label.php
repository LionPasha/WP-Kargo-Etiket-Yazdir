<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php printf( esc_html__( 'Kargo Etiketi – Sipariş #%s', 'kargo-etiketi' ), esc_html( $data['order_number'] ) ); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url( KE_PLUGIN_URL . 'assets/css/label.css' ); ?>" />
    <style>
        <?php
        $sizes = array(
            'a5'      => 'size: A5 portrait; margin: 10mm;',
            'thermal' => 'size: 100mm 150mm; margin: 4mm;',
            'a6'      => 'size: A6 portrait; margin: 8mm;',
        );
        $page_size = isset( $sizes[ $data['label_size'] ] ) ? $sizes[ $data['label_size'] ] : $sizes['a6'];
        ?>
        @page { <?php echo esc_html( $page_size ); ?> }
    </style>
</head>
<body>
<script>
window.addEventListener('load', function () {
    var imgs = document.querySelectorAll('img');
    if (!imgs.length) { setTimeout(function(){ window.print(); }, 150); return; }
    var remaining = imgs.length;
    function tryPrint() { remaining--; if (remaining <= 0) setTimeout(function(){ window.print(); }, 150); }
    imgs.forEach(function (img) {
        if (img.complete) { tryPrint(); }
        else { img.addEventListener('load', tryPrint); img.addEventListener('error', tryPrint); }
    });
});
</script>

<div class="ke-label-wrapper ke-size-<?php echo esc_attr( $data['label_size'] ); ?>">

    <!-- ============================================================
         LOGO (opsiyonel)
    ============================================================ -->
    <?php if ( $data['sender']['logo_show'] && ! empty( $data['sender']['logo_url'] ) ) : ?>
    <div class="ke-logo-section ke-logo-<?php echo esc_attr( $data['sender']['logo_position'] ); ?>">
        <img src="<?php echo esc_url( $data['sender']['logo_url'] ); ?>"
             alt="<?php echo esc_attr( $data['sender']['name'] ); ?>"
             style="max-height:<?php echo esc_attr( $data['sender']['logo_height'] ); ?>px; width:auto;" />
    </div>
    <?php endif; ?>

    <!-- ============================================================
         GÖNDERİCİ
    ============================================================ -->
    <section class="ke-section">
        <h2 class="ke-section-title">GÖNDERİCİ</h2>
        <table class="ke-info-table">
            <tr>
                <td class="ke-label-cell">Firma/Ad:</td>
                <td><?php echo esc_html( $data['sender']['name'] ); ?></td>
            </tr>
            <?php if ( ! empty( $data['sender']['address'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Adres:</td>
                <td><?php echo nl2br( esc_html( $data['sender']['address'] ) ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( ! empty( $data['sender']['city'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">İlçe/İl:</td>
                <td><?php echo esc_html( $data['sender']['city'] ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( ! empty( $data['sender']['phone'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Telefon:</td>
                <td><?php echo esc_html( $data['sender']['phone'] ); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </section>

    <!-- ============================================================
         GÖNDERİ DETAYLARI
    ============================================================ -->
    <section class="ke-section">
        <h2 class="ke-section-title">GÖNDERİ DETAYLARI</h2>
        <table class="ke-info-table">
            <tr>
                <td class="ke-label-cell">Sipariş No:</td>
                <td>#<?php echo esc_html( $data['order_number'] ); ?></td>
            </tr>
            <tr>
                <td class="ke-label-cell">Paket Adedi:</td>
                <td><?php echo esc_html( $data['package_count'] ); ?></td>
            </tr>
            <tr>
                <td class="ke-label-cell">Tarih:</td>
                <td><?php echo esc_html( $data['order_date'] ); ?></td>
            </tr>
            <tr>
                <td class="ke-label-cell">Ödeme Tipi:</td>
                <td><strong><?php echo esc_html( $data['payment_type'] ); ?></strong></td>
            </tr>
            <?php if ( ! empty( $data['tracking_number'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Takip No:</td>
                <td><strong><?php echo esc_html( $data['tracking_number'] ); ?></strong></td>
            </tr>
            <?php endif; ?>
        </table>
    </section>

    <!-- ============================================================
         ALICI
    ============================================================ -->
    <section class="ke-section ke-section-recipient">
        <h2 class="ke-section-title">ALICI</h2>
        <table class="ke-info-table">
            <tr>
                <td class="ke-label-cell">Ad Soyad:</td>
                <td><?php echo esc_html( $data['recipient']['name'] ); ?></td>
            </tr>
            <?php if ( ! empty( $data['recipient']['address1'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Adres:</td>
                <td>
                    <?php echo esc_html( $data['recipient']['address1'] ); ?>
                    <?php if ( ! empty( $data['recipient']['address2'] ) ) : ?>
                        <br><?php echo esc_html( $data['recipient']['address2'] ); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ( ! empty( $data['recipient']['postcode'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Posta Kodu:</td>
                <td><?php echo esc_html( $data['recipient']['postcode'] ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( ! empty( $data['recipient']['city'] ) || ! empty( $data['recipient']['state'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">İlçe/İl:</td>
                <td>
                    <?php
                    $parts = array_filter( array( $data['recipient']['city'], $data['recipient']['state'] ) );
                    echo esc_html( implode( ' / ', $parts ) );
                    ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ( ! empty( $data['recipient']['phone'] ) ) : ?>
            <tr>
                <td class="ke-label-cell">Telefon:</td>
                <td><?php echo esc_html( $data['recipient']['phone'] ); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </section>

    <!-- ============================================================
         ÜRÜNLER (opsiyonel)
    ============================================================ -->
    <?php if ( ! empty( $data['items'] ) ) : ?>
    <section class="ke-section ke-section-products">
        <h2 class="ke-section-title">ÜRÜNLER</h2>
        <table class="ke-info-table ke-products-table">
            <?php foreach ( $data['items'] as $item ) : ?>
            <tr>
                <td><?php echo esc_html( $item['name'] ); ?></td>
                <td class="ke-qty">×<?php echo esc_html( $item['qty'] ); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
    <?php endif; ?>

    <!-- ============================================================
         SİPARİŞ NOTU (opsiyonel)
    ============================================================ -->
    <?php if ( ! empty( $data['order_note'] ) ) : ?>
    <section class="ke-section ke-section-note">
        <h2 class="ke-section-title">SİPARİŞ NOTU</h2>
        <p class="ke-note-text"><?php echo nl2br( esc_html( $data['order_note'] ) ); ?></p>
    </section>
    <?php endif; ?>

    <!-- ============================================================
         Alt bilgi
    ============================================================ -->
    <footer class="ke-footer">
        <?php
        printf(
            esc_html__( 'Bu etiket %s tarafından oluşturulmuştur.', 'kargo-etiketi' ),
            esc_html( get_bloginfo( 'name' ) )
        );
        ?>
        &nbsp;|&nbsp; <?php echo esc_html( date_i18n( 'd.m.Y H:i' ) ); ?>
    </footer>

</div><!-- .ke-label-wrapper -->

</body>
</html>
