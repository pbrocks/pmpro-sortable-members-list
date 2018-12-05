jQuery(document).ready(function($) {
	$('#dropdown-levels').change(function() {
		$.ajax({
			type: "POST",
			// url: selected_dash_object.selected_ajaxurl,
			url: ajaxurl,
			dataType: 'html',
			// data: "pmpros_add_post=1&pmpros_series=" + seriesid + "&pmpros_post=" + $('#pmpros_post').val() + '&pmpros_delay=' + $('#pmpros_delay').val(),
			data: {
				// 'url' : "pmpros_add_post=1&pmpros_series=" + seriesid + "&pmpros_post=" + $('#pmpros_post').val() + '&pmpros_delay=' + $('#pmpros_delay').val(),
				'action' : 'selected_dash_request',
				'filter' : $('#dropdown-levels').val(),
				'selected_dashurl' : selected_dash_object.selected_dash_ajaxurl,
				'selectednonce' : selected_dash_object.selected_dash_nonce,
			},
			success:function(data) {
				//$( '#return-selected' ).html(data);
				user_table = data.substring(data.indexOf('<table'), data.indexOf('</table>') + 8);
				$( '#pbrx-ajax-replace table' ).html(user_table);
				// if ( '' !== $('#dropdown-levels').val() ) {
				// 	$('#return-levels').html(' You selected Level ' + $('#dropdown-levels').val
				// 		());
				// } else {
				// 	$('#return-levels').html('You need to select a Level');
				// }
				$( '#test-input-ajax' ).val(selected_dash_object.selected_dash_nonce);
				// This outputs the result of the ajax request
				console.log(data);
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert('There is an error');

				console.log(errorThrown);
			}
		});  
	});      
});