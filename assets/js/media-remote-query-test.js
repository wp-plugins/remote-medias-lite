(function($){
$(document).ready(function($) {
	$('#rml_service_selection,#rml_account_settings select').live('change', function () {
		$('.rml-status-field.working').hide();
	    $('.rml-status-field.notvalid').hide();
		$('.rml-status-field.unknown').show();
	});
	$('#rml_account_settings input').live('keyup', function () {
		$('.rml-status-field.working').hide();
	    $('.rml-status-field.notvalid').hide();
		$('.rml-status-field.unknown').show();
	});
	$('.action_query_test').live('click', function() {
	    var $metabox = $(this).closest('.rmlfields'),
	    	$currentFields = $('.rmlfields-'+$('#remote_media_type').val()),
	    	data = {
		        action: 'rmlQueryTest',
		        security: rmlQueryTestParams.nonce,
		        post_id: $('#post_ID').val(),
		        user_id: $currentFields.find('#remote_user_id').val(),
		        service_class: $currentFields.find('#service_class').val()
	    	};

		$('#rmlloading').css('display', 'inline-block');
		$('#query_msg').html('');
		$('.rml-status-field.working').hide();
	    $('.rml-status-field.notvalid').hide();
		$('.rml-status-field.unknown').show();

		if (data.user_id.length < 1) {
			$('#rmlloading').hide();
			$('.rml-status-field.notvalid').show();
        	$('.rml-status-field.working').hide();
        	$('.rml-status-field.unknown').hide();
			$('#query_msg').html('<div class="dashicons dashicons-no ocs-3x ocs-red" style="margin-top: -3px; padding: 0 10px;"></div>');
			return false;
		}
		$.post(rmlQueryTestParams.ajax_url, data, function(response) {
			$('#rmlloading').hide();
			data = $.parseJSON( response );
	        if(data.validate) {
	        	$('.rml-status-field.working').show();
	        	$('.rml-status-field.notvalid').hide();
	        	$('.rml-status-field.unknown').hide();
	        	$('#query_msg').html('<div class="dashicons dashicons-yes ocs-3x ocs-green" style="margin-top: -3px; padding: 0 10px;"></div>');
	        	return true;
        	}
        	$('.rml-status-field.notvalid').show();
        	$('.rml-status-field.working').hide();
        	$('.rml-status-field.unknown').hide();
	        $('#query_msg').html('<div class="dashicons dashicons-no ocs-3x ocs-red" style="margin-top: -3px; padding: 0 10px;"></div>');
	        return false;
		});
		return false;
	});
});
}(jQuery));