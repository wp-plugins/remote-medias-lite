(function($){
    'use strict';

    function rmlAccountSettingsManager() {
        this.init();
    }

    rmlAccountSettingsManager.prototype = {
        $filterField: $('#remote_media_type'),
        $metabox: $('.rmlfields'),
        init: function () {
            var that = this;
            this.$filterField.change(function () {
                that.showFields(this.value);
            });
            this.showFields(this.$filterField.val());
        },
        showFields: function (format) {
            var that = this;
            $.each(that.$metabox, function () {
                $(this).parent().parent().hide();
            });
            this.$metabox.filter('.rmlfields-'+format).parent().parent().show()
        }
    }

    $(document).ready(function() {
        var rmlSettingsManager = new rmlAccountSettingsManager();
    });
}(jQuery));