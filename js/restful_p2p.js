;(function( $ ) {
    'use strict';

	$('.p2p-connection').on('click', function(connect){

		// this disables href from acting like a link
		connect.preventDefault();

        var data = {
                from_id: $(this).attr('data-from-id'),
                to_id: $(this).attr('data-to-id'),
            };

        var clicked = $(this);

        clicked.toggleClass('p2p-loading').html('Working...');

        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/connection/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success: function( response ) {
                if ( true === response.success ) {
                    clicked.toggleClass('p2p-loading p2p-connected').html(response.message);
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