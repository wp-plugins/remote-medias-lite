jQuery(document).ready( function($) {
    $.each(pointersRML.pointers, function () {
        var that = this;
        $(that.targetId).pointer({
            content: that.content,
            position: that.position,
            close: function() {
                $.post( ajaxurl, {
                      pointer: that.pointerId,
                      action: 'dismiss-wp-pointer'
                });
            }
        }).pointer('open');
    });
});