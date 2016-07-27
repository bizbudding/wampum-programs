;(function( $ ) {
    'use strict';

    $('.wampum-program-progress').on( 'click', '.progress-button', function(connection) {

		// this disables href from acting like a link
		connection.preventDefault();

        var data = {
                from: $(this).attr('data-from-id'),
                to: $(this).attr('data-to-id'),
            };

        var clicked = $(this);

        clicked.toggleClass('loading').html('Working...');

        if ( clicked.is( '.connect' ) ) {
            var url = 'restful-p2p/v1/connect/';
            var text = restful_p2p_connection_vars.connected_text;
        } else if ( clicked.is( '.connected' ) ) {
            var url = 'restful-p2p/v1/disconnect/';
            var text = restful_p2p_connection_vars.connect_text;
        }

        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + url,
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success: function( response ) {
                if ( response.success == true ) {
                    clicked.toggleClass('loading connect connected').html(text);
                } else {
                    alert(response.message);
                }
            },
            fail: function( response ) {
                // What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });

    });

})( jQuery );