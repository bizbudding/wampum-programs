jQuery(function( $ ){

    // Set menu/content variables
    var Menu    = '.' + jivedigcontentswap.name + '-menu';
    var Content = '.' + jivedigcontentswap.name + '-content';

	$(Menu + ' a').on('click', function(e){

		// Disable page refresh
		e.preventDefault();

        // Loading
        $(Content).addClass('loading').html(jivedigcontentswap.loading);;

        // Get the item that was clicked
        var Item = $(this).parent();
        // Set remove/add active class
        $(Menu + ' li').removeClass('active');
        Item.addClass('active');

        var Key = Item.attr('data-item');


        $.ajax({
            method: "GET",
            url: jivedigcontentswap.root + jivedigcontentswap.json_dir,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', jivedigcontentswap.nonce );
            },
            success : function( response ) {
                $(Content).empty().append(response[Key]).removeClass('loading');
                // Update query parameters
                // window.history.pushState("object or string", window.document.title, window.location.protocol + "?" + jivedigcontentswap.name + "=" + Item);
            },
            fail : function( response ) {
            	console.log( response );
				alert( 'FAIL!' );
            }
        });

    });
});