(function($){
$(document).ready(function($) {
	$('.action_query_test').live('click', function() {
	    var $metabox = $(this).closest('.rmlfields'),
	    	data = {
		        action: 'rmlQueryTest',
		        security: rmlQueryTestParams.nonce,
		        post_id: $('#post_ID').val(),
		        user_id: $metabox.find('#remote_user_id').val(),
		        service_class: $metabox.find('#service_class').val()
	    	};

		$metabox.find('#rmlloading').css('display', 'inline-block');
		$metabox.find('#query_msg').html('');

		if (data.user_id.length < 1) {
			$metabox.find('#rmlloading').hide();
			$metabox.find('#query_msg').html('<div class="dashicons dashicons-no ocs-3x ocs-red" style="margin-top: -3px;"></div>');
			return false;
		}
		$.post(rmlQueryTestParams.ajax_url, data, function(response) {
			$metabox.find('#rmlloading').hide();
			data = $.parseJSON( response );
	        if(data.validate) {
	        	$metabox.find('#query_msg').html('<div class="dashicons dashicons-yes ocs-3x ocs-green" style="margin-top: -3px;"></div>');
	        	return true;
        	}
	        $metabox.find('#query_msg').html('<div class="dashicons dashicons-no ocs-3x ocs-red" style="margin-top: -3px;"></div>');
	        return false;
		});
		return false;
	});
});
}(jQuery));