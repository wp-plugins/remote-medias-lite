jQuery(document).ready(function($) {

	$('.action_get_user_info').live('click', function() {
	    //jQuery('#remoteMediaUploader :submit').hide();
	    var data = {
	        action: 'remotemediasUserInfo',
	        security: rmlUserInfoParams.nonce,
	        post_id: $('#post_ID').val(),
	        user_id: $('#remote_user_id').val()
	    };
		
		$.post(rmlUserInfoParams.ajax_url, data, function(response) {
			data = jQuery.parseJSON( response );
	        if(data.error)
	        {
	        	return false;
	        }
			return true;
		});
	});
});
