(function($){
    'use strict';

    function rmlAccountSettingsManager() {
        this.init();
    }

    rmlAccountSettingsManager.prototype = {
        $filterField: $('#remote_media_type'),
        $metabox: $('#rml_account_settings'),
        init: function () {
            var that = this;
            this.$filterField.change(function () {
                that.showFields(this.value);
            });
            this.showFields(this.$filterField.val());
        },
        showFields: function (format) {
            this.$metabox.find('ul.wpform-fieldset li.wpform-field').hide();
            this.$metabox.find('ul.wpform-fieldset li.wpform-field.all').show();
            this.$metabox.find('ul.wpform-fieldset li.wpform-field.'+format).show();

            //Update Metabox title
            $('#rml-hndle-extend').remove();
            this.$metabox.find('.hndle').append('<span id="rml-hndle-extend"> - '+this.$filterField.find("option:selected").text()+'</span>');
        }
    }

    $(document).ready(function() {
        var rmlSettingsManager = new rmlAccountSettingsManager();
    });
}(jQuery));