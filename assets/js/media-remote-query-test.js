window.ocs = window.ocs || {};
ocs.rml = ocs.rml || {};

(function($){
    'use strict';
    ocs.rml.AccountStatusManager = function () {
    	this.init();
    }

    ocs.rml.AccountStatusManager.prototype = {
        $statusField: $('.rml-status-field'),
        $querymsg: $('#query_msg'),
        $loading: $('#rmlloading'),
        $currentFields: null,
        authInterval: null,
        init: function () {
        },
        hookevents: function () {
        	var that = this;
        	$('#rml_service_selection,#rml_account_settings select').live('change', function () {
				that.setStatus('unknown');
			});
			$('#rml_account_settings input').live('keyup', function () {
				that.setStatus('unknown');
			});
			$('.action_query_test').live('click', function() {
			    that.validate();
			});
        },
        fetchActiveFields: function () {
        	this.$currentFields = $('.rmlfields-'+$('#remote_media_type').val());
        },
        getServiceClass: function () {
        	this.fetchActiveFields();
        	return this.$currentFields.find('#service_class').val() || '';
        },
        getPostId: function () {
        	return $('#post_ID').val();
        },
        setStatus: function (status) {
        	var $statusmsg = $('#rmlaccountstatus');

            $statusmsg.html(status);
        	this.$querymsg.html('');
        	if (status === rmlQueryTestParams.status.invalid) {
        		this.$querymsg.html('<div class="dashicons dashicons-no ocs-3x ocs-red" style="margin-top: -3px; padding: 0 10px;"></div>');
        	}

        	if (status === rmlQueryTestParams.status.enabled) {
        		this.$querymsg.html('<div class="dashicons dashicons-yes ocs-3x ocs-green" style="margin-top: -3px; padding: 0 10px;"></div>');
        	}
        	
        	return;
        },
        getCurrentSettings: function () {
            var settings = {},
                elements = $('.rmlfields-'+$('#remote_media_type').val()+'').find('input');
            $.each(elements, function () {
                var start = this.name.indexOf('[', this.name.indexOf('[')+1)+1,
                    end   = this.name.indexOf(']', this.name.indexOf(']')+1),
                    name = this.name;
                name = name.substr(start,end-start);
                settings[name] = $(this).val();
            })
            return settings;
        },
        authenticate: function (url) {
            var that = this,
                authwindow;

            this.$loading.css('display', 'inline-block');
            this.setStatus(rmlQueryTestParams.status.authProcessing);
            if (url) {
                authwindow = window.open(url,'rmlauthprocess','height=400,width=800');
                this.authInterval = window.setInterval(function() {
                    try {
                        if (authwindow === null || authwindow.closed) {
                            that.authdone(0);
                        }
                    }
                    catch (e) {
                    }
                }, 1000);
            }
            
        },
        authdone: function (status) {
            var status = parseInt(status) || 0;
            window.clearInterval(this.authInterval);
            this.$loading.hide();
            if (status === 1) {
                this.setStatus(rmlQueryTestParams.status.enabled);
                $('.rmlnotice').hide();
                $('#rmlstatusbutton').html(rmlQueryTestParams.button.reauth);
            } else {
                this.setStatus(rmlQueryTestParams.status.authfailed);
            }
            this.$loading.hide();
        },
        validate: function () {
        	var that = this,
		    	data = {
			        action: 'rmlQueryTest',
			        security: rmlQueryTestParams.nonce,
			        post_id: this.getPostId(),
                    account: this.getCurrentSettings()
		    	};
		    
			that.$loading.css('display', 'inline-block');

			$.post(rmlQueryTestParams.ajax_url, data, function(response) {
				that.$loading.hide();
				data = $.parseJSON(response);

                if (data.authneeded && data.authurl) {
                    that.authenticate(data.authurl);
                    return true;
                }
		        if(!data.authneeded && parseInt(data.validate) === 1) {
		        	that.setStatus(rmlQueryTestParams.status.enabled);
		        	return true;
	        	}

	        	that.setStatus(rmlQueryTestParams.status.invalid);
		        return false;
			});
			return false;
        }
    }
    ocs.rml.statusManager = new ocs.rml.AccountStatusManager();

	$(document).ready(function($) {
		ocs.rml.statusManager.hookevents();
	});
}(jQuery));