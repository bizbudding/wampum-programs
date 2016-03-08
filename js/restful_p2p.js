;(function( $ ) {
    'use strict';

	$('a.p2p-connect').on('click', function(add){

		// this disables href from acting like a link
		add.preventDefault();

		var data = {
				from_id: $(this).attr('from_id'),
				to_id: $(this).attr('to_id'),
            };

		console.log(data);

		AddConnection(data);

	});

	$('a.p2p-disconnect').on('click', function(add){

		// this disables href from acting like a link
		add.preventDefault();

		var data = {
				from_id: $(this).attr('from_id'),
				to_id: $(this).attr('to_id'),
            };

		console.log(data);

		RemoveConnection(data);

	});

	function AddConnection( data ) {
        $.ajax({
            method: "POST",
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/add-connection/'
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success : function( response ) {
                console.log( response );
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
            url: restful_p2p_connection_vars.root + 'restful-p2p/v1/remove-connection/'
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_connection_vars.nonce );
            },
            success : function( response ) {
                console.log( response );
            },
            fail : function( response ) {
            	// What to do if no response at all?
                alert( 'Sorry, something went wrong. Please try again.' );
            }
        });
	}

})( jQuery );