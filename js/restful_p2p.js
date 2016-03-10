;(function( $ ) {
    'use strict';

	$('a.p2p-connect').on('click', function(connect){

		// this disables href from acting like a link
		connect.preventDefault();

        var data = {
                // clicked: $(this).data( $(this) ),
                from_id: $(this).attr('data-from-id'),
                to_id: $(this).attr('data-to-id'),
            };

        var clickedg = $(this);

        clickedg.html('Working...')

		AddConnection(data,clickedg);

	});

	$('a.p2p-disconnect').on('click', function(disconnect){

		// this disables href from acting like a link
		disconnect.preventDefault();

        var data = {
                from_id: $(this).attr('data-from-id'),
                to_id: $(this).attr('data-to-id'),
            };

        var clicked = $(this);

        clicked.html('Working...')

        RemoveConnection(data,clicked);

	});

	function AddConnection( data, clickedg ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/connect/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
                // clicked.text('Working...')
           },
            success: function( response ) {
                // console.log( clicked );
                if ( true === response.success ) {
                    clickedg.removeClass('p2p-connect').addClass('p2p-disconnect').html('Mark Unread');
                } else if ( false === response.success ) {
                    alert(response.message);
                }
            },
            fail: function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

	function RemoveConnection( data, clicked ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/disconnect/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
                // clicked.text('Working...')
            },
            success: function( response ) {
                // console.log( clicked );
                if ( true === response.success ) {
                    // clicked.removeClass('p2p-disconnect');
                    // clicked.addClass('p2p-connect');
                    clicked.removeClass('p2p-disconnect').addClass('p2p-connect').html('Mark Read');
                } else if ( false === response.success ) {
                    alert(response.message);
                }
            },
            fail: function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

})( jQuery );