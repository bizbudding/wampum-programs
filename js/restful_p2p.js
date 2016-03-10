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

        var clicked = $(this);
		// console.log(clicked);

		AddConnection(data,clicked);
        // AddConnection( data ).done(RestfulP2PConnect);

	});

	$('a.p2p-disconnect').on('click', function(disconnect){

		// this disables href from acting like a link
		disconnect.preventDefault();


        var data = {
                from_id: $(this).attr('data-from-id'),
                to_id: $(this).attr('data-to-id'),
            };

        var clicked = $(this);
		// console.log(data);

        RemoveConnection(data,clicked);
		// RemoveConnection(data).done(RestfulP2PDisconnect);

	});

	function AddConnection( data, clicked ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/connect/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            // done : function ( response ) {
                // return response;
            // }
            success : function( response ) {
                // console.log( clicked );
                if ( true === response.success ) {
                    clicked.removeClass('p2p-connect');
                    clicked.addClass('p2p-disconnect');
                } else if ( false === response.success ) {

                }
            },
            fail : function( response ) {
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
            },
            success : function( response ) {
                // console.log( clicked );
                if ( true === response.success ) {
                    clicked.removeClass('p2p-disconnect');
                    clicked.addClass('p2p-connect');
                } else if ( false === response.success ) {

                }
            },
            fail : function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

})( jQuery );