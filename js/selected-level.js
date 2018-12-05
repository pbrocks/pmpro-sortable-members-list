jQuery(document).ready(function($) {
	$('#dropdown-levels').change(function() {
		$.ajax({
			type: "POST",
			url: ajaxurl,
			dataType: 'html',
			data: {
				'action' : 'selected_dash_request',
				'filter' : $('#dropdown-levels').val(),
				'selected_dashurl' : selected_dash_object.selected_dash_ajaxurl,
				'selectednonce' : selected_dash_object.selected_dash_nonce,
			},
			success:function(data) {
				user_table = data.substring(data.indexOf('<table'), data.indexOf('</table>') + 8);
				$( '#pbrx-ajax-replace table' ).html(user_table);
				console.log(data);
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(errorThrown);
			}
		});  
	});      
});