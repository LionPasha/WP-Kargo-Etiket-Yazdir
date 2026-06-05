/* global jQuery, wp, keSettings */
( function ( $ ) {
    'use strict';

    /* ------------------------------------------------------------------
       Canlı önizleme: metin alanları değişince önizleme güncellenir
    ------------------------------------------------------------------ */
    var previewMap = {
        'sender-name'    : '#prev-sender-name',
        'sender-address' : '#prev-sender-address',
        'sender-city'    : '#prev-sender-city',
        'sender-phone'   : '#prev-sender-phone',
        'payment-type'   : '#prev-payment-type',
    };

    $( '[data-ke-preview]' ).not( '[type="checkbox"]' ).on( 'input change', function () {
        var key = $( this ).data( 'ke-preview' );
        var target = previewMap[ key ];
        if ( ! target ) { return; }
        var val = $( this ).val().replace( /\n/g, '<br>' );
        $( target ).html( val || '&nbsp;' );
    } );

    // Checkbox'lar
    $( '#ke_show_products' ).on( 'change', function () {
        $( '#prev-products-section' ).toggle( this.checked );
    } );

    $( '#ke_show_order_note' ).on( 'change', function () {
        $( '#prev-note-section' ).toggle( this.checked );
    } );

    $( '#ke_logo_show' ).on( 'change', function () {
        var hasUrl = $( '#ke_logo_url' ).val().trim() !== '';
        if ( this.checked && hasUrl ) {
            $( '#prev-logo-section' ).show();
        } else {
            $( '#prev-logo-section' ).hide();
        }
    } );

    /* ------------------------------------------------------------------
       Otomatik durum – checkbox/select bağlantısı (inline script kaldırıldı)
    ------------------------------------------------------------------ */
    var $autoStatusCheck  = $( '#ke_auto_status' );
    var $autoStatusSelect = $( '#ke_auto_status_value' );

    if ( $autoStatusCheck.length ) {
        $autoStatusCheck.on( 'change', function () {
            $autoStatusSelect.prop( 'disabled', ! this.checked );
        } );
    }

    /* ------------------------------------------------------------------
       Ödeme modu radyo butonları
    ------------------------------------------------------------------ */
    $( '[data-ke-payment-mode]' ).on( 'change', function () {
        var isManual = $( 'input[name="ke_payment_mode"]:checked' ).val() === 'manual';
        var $textField = $( '#ke_payment_manual_text' );

        $textField.prop( 'disabled', ! isManual );

        if ( isManual ) {
            $( '#prev-payment-type' ).text( $textField.val() || 'Gönderici Ödemeli' );
        } else {
            $( '#prev-payment-type' ).text( 'Siparişten otomatik' );
        }
    } );

    $( '#ke_payment_manual_text' ).on( 'input', function () {
        if ( $( 'input[name="ke_payment_mode"]:checked' ).val() === 'manual' ) {
            $( '#prev-payment-type' ).text( $( this ).val() || 'Gönderici Ödemeli' );
        }
    } );

    /* ------------------------------------------------------------------
       Logo konum ve yükseklik önizleme
    ------------------------------------------------------------------ */
    $( '#ke_logo_position' ).on( 'change', function () {
        var $sec = $( '#prev-logo-section' );
        $sec.css( 'text-align', $( this ).val() );
        $sec.removeClass( 'ke-logo-left ke-logo-center ke-logo-right' )
            .addClass( 'ke-logo-' + $( this ).val() );
    } );

    $( '#ke_logo_max_height' ).on( 'input change', function () {
        var h = parseInt( $( this ).val(), 10 ) || 60;
        $( '#prev-logo-img' ).css( 'max-height', h + 'px' );
    } );

    /* ------------------------------------------------------------------
       WordPress Medya Kütüphanesi – Logo Seç
    ------------------------------------------------------------------ */
    var mediaFrame;

    $( document ).on( 'click', '#ke-logo-upload-btn', function ( e ) {
        e.preventDefault();

        if ( mediaFrame ) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media( {
            title:    keSettings.mediaTitle,
            button:   { text: keSettings.mediaButton },
            library:  { type: [ 'image' ] },
            multiple: false,
        } );

        mediaFrame.on( 'select', function () {
            var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
            applyLogoUrl( attachment.url );
        } );

        mediaFrame.open();
    } );

    $( document ).on( 'click', '#ke-logo-remove-btn', function ( e ) {
        e.preventDefault();
        applyLogoUrl( '' );
    } );

    /* ------------------------------------------------------------------
       Site logosunu kullan
    ------------------------------------------------------------------ */
    $( document ).on( 'click', '#ke-logo-site-btn', function ( e ) {
        e.preventDefault();
        var url = $( this ).data( 'url' );
        if ( url ) {
            applyLogoUrl( url );
        }
    } );

    /* ------------------------------------------------------------------
       Önizle butonu – URL alanındaki değeri önizlemeye yansıt
    ------------------------------------------------------------------ */
    $( document ).on( 'click', '#ke-logo-preview-btn', function ( e ) {
        e.preventDefault();
        var url = $( '#ke_logo_url' ).val().trim();
        applyLogoUrl( url );
    } );

    // URL alanından ayrılınca da önizlemeyi güncelle
    $( document ).on( 'blur', '#ke_logo_url.ke-logo-url-field', function () {
        applyLogoUrl( $( this ).val().trim() );
    } );

    /**
     * Verilen URL'yi logo olarak uygula:
     * — görünür text input'a yazar (form'a dahil → otomatik kayıt)
     * — küçük önizleme kutusunu günceller
     * — canlı etiket önizlemesini günceller
     */
    function applyLogoUrl( url ) {
        $( '#ke_logo_url' ).val( url );

        if ( url ) {
            $( '#ke-logo-preview-wrap' ).removeClass( 'ke-logo-empty' );
            $( '#ke-logo-placeholder' ).hide();
            $( '#ke-logo-preview-img' ).attr( 'src', url ).show();
            $( '#ke-logo-remove-btn' ).show();
        } else {
            $( '#ke-logo-preview-wrap' ).addClass( 'ke-logo-empty' );
            $( '#ke-logo-placeholder' ).show();
            $( '#ke-logo-preview-img' ).attr( 'src', '' ).hide();
            $( '#ke-logo-remove-btn' ).hide();
        }

        // Canlı etiket önizlemesi
        $( '#prev-logo-img' ).attr( 'src', url );
        if ( url && $( '#ke_logo_show' ).is( ':checked' ) ) {
            $( '#prev-logo-section' ).show();
        } else {
            $( '#prev-logo-section' ).hide();
        }
    }

    /* ------------------------------------------------------------------
       Toplu yazdırma: URL parametrelerinden etiket URL'lerini al
    ------------------------------------------------------------------ */
    function handleBulkPrint() {
        var params = new URLSearchParams( window.location.search );
        if ( params.get( 'ke_bulk_print' ) !== '1' ) { return; }

        var raw = params.get( 'ke_urls' );
        if ( ! raw ) { return; }

        var urls;
        try {
            urls = JSON.parse( decodeURIComponent( raw ) );
        } catch ( e ) { return; }

        if ( ! Array.isArray( urls ) || ! urls.length ) { return; }

        var cleanUrl = window.location.href
            .replace( /[?&]ke_bulk_print=1/, '' )
            .replace( /[?&]ke_urls=[^&]*/, '' );
        window.history.replaceState( {}, document.title, cleanUrl );

        $.each( urls, function ( i, url ) {
            setTimeout( function () { window.open( url, '_blank' ); }, i * 350 );
        } );

        $( '<div class="notice notice-success is-dismissible"><p>' +
            urls.length + ' adet kargo etiketi yeni sekmelerde açıldı.' +
            '</p></div>' )
            .insertBefore( '.wp-list-table' )
            .delay( 5000 ).fadeOut();
    }

    $( document ).ready( function () {
        handleBulkPrint();

        // Sayfa yüklenince mevcut logo URL'sini önizlemeye yansıt
        var existingUrl = $( '#ke_logo_url' ).val().trim();
        if ( existingUrl ) {
            applyLogoUrl( existingUrl );
        }
    } );

}( jQuery ) );
