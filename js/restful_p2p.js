;(function( $ ) {
    'use strict';

    $('.wampum-connection-wrap').on( 'click', '.wampum-connection', function(connection) {

		// this disables href from acting like a link
		connection.preventDefault();

        var data = {
                from_id: $(this).attr('data-from-id'),
                to_id: $(this).attr('data-to-id'),
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
                if ( true === response.success ) {
                    clicked.toggleClass('loading connect connected').html(text);
                } else if ( false === response.success ) {
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