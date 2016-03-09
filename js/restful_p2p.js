;(function( $ ) {
    'use strict';

	$('a.p2p-connect').on('click', function(connect){

		// this disables href from acting like a link
		connect.preventDefault();

		var data = {
				from_id: $(this).attr('data-from-id'),
				to_id: $(this).attr('data-to-id'),
            };

		console.log(data);

		AddConnection(data);

	});

	$('a.p2p-disconnect').on('click', function(disconnect){

		// this disables href from acting like a link
		disconnect.preventDefault();

		var data = {
				from_id: $(this).attr('data-from-id'),
				to_id: $(this).attr('data-to-id'),
            };

		console.log(data);

		RemoveConnection(data);

	});

	function AddConnection( data ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/connect/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success : function( response ) {
                console.log( response );
                alert( 'Success!' );
            },
            fail : function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

	function RemoveConnection( data ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/disconnect/',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success : function( response ) {
                console.log( response );
                alert( 'Success!' );
            },
            fail : function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

})( jQuery );