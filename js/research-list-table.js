jQuery(document).ready(function($) {
	$('#research-levels').change(function() {
        // We'll pass this variable to the PHP function example_ajax_request
        var fruit = 'Banana';
        // // This does the ajax request
        $.ajax({
            type: "GET",
            url: frontend_ajax_object.frontend_ajaxurl,
            // url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                'action': 'frontend_ajax_request',
                'filename': 'research-list-table.js',
                'fruit' : fruit,
                'chosenlevel' : $('#research-levels').val(),
                'frontend_ajaxurl' : frontend_ajax_object.frontend_ajaxurl,
                'frontend_nonce' : frontend_ajax_object.frontend_nonce,
                'random_number' : frontend_ajax_object.random_number,
            },
            success:function(data) {
		// if ( '' !== $('#research-levels').val() ) {
		// 	$('#return-research').html(' You selected Level ' + data.fruit);
		// } else {
		// 	$('#return-research').html('You need to select a Level');
		// }
                $( '#return-frontend' ).html(data);
                $( '#test-input-ajax' ).val(frontend_ajax_object.frontend_nonce);
                // This outputs the result of the ajax request
                console.log(data);
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
            }
        });  
    });      
});