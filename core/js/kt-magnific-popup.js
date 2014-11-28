jQuery( document ).ready( function () {
    jQuery( '.image-link' ).magnificPopup( { type: 'image' } );
    jQuery( '.image-popup-vertical-fit' ).magnificPopup( {
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    } );
    jQuery( '.image-popup-fit-width' ).magnificPopup( {
        type: 'image',
        closeOnContentClick: true,
        image: {
            verticalFit: false
        }
    } );
    jQuery( '.image-popup-no-margins' ).magnificPopup( {
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,
        mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 300 // don't foget to change the duration also in CSS
        }
    } );
    jQuery( document ).ready( function () {
        jQuery( '.popup-gallery' ).magnificPopup( {
            delegate: 'a',
            type: 'image',
            tLoading: 'Nahrávám obrázek #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [ 0, 1 ] // Will preload 0 - before current, and 1 after the current image
            },
            image: {
                tError: '<a href="%url%">Obrázek #%curr%</a> nemůže být načten.',
                titleSrc: function ( item ) {
                    return item.el.attr( 'title' );
                }
            }
        } );
    } );
    jQuery( '.popup-modal' ).magnificPopup( {
        type: 'inline',
        preloader: false,
        focus: '#dialog'
    } );
} );