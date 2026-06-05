/* global jQuery, keAdmin */
( function ( $ ) {
    'use strict';

    /* ------------------------------------------------------------------
       Takip numarası – AJAX kaydet
    ------------------------------------------------------------------ */
    $( document ).on( 'click', '#ke-tracking-save', function () {
        var $btn      = $( this );
        var $input    = $( '#ke-tracking-input' );
        var $msg      = $( '#ke-tracking-msg' );
        var orderId   = $input.data( 'order-id' );
        var tracking  = $input.val().trim();

        $btn.prop( 'disabled', true ).text( keAdmin.saving );
        $msg.hide();

        $.post( keAdmin.ajaxUrl, {
            action   : 'ke_save_tracking',
            nonce    : keAdmin.trackingNonce,
            order_id : orderId,
            tracking : tracking,
        } )
        .done( function ( res ) {
            if ( res.success ) {
                $msg.text( keAdmin.saved ).css( 'color', '#008a00' ).show();
            } else {
                $msg.text( keAdmin.error ).css( 'color', '#d63638' ).show();
            }
        } )
        .fail( function () {
            $msg.text( keAdmin.error ).css( 'color', '#d63638' ).show();
        } )
        .always( function () {
            $btn.prop( 'disabled', false ).text( 'Kaydet' );
            setTimeout( function () { $msg.fadeOut(); }, 3000 );
        } );
    } );

    /* ------------------------------------------------------------------
       Toplu yazdırma: URL parametrelerinden etiket URL'lerini al,
       her birini yeni sekmede aç.
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

        // URL'yi temizle (yenilemede tekrar açılmasın)
        var cleanUrl = window.location.href
            .replace( /[?&]ke_bulk_print=1/, '' )
            .replace( /[?&]ke_urls=[^&]*/, '' )
            .replace( /\?$/, '' );
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
    } );

}( jQuery ) );
