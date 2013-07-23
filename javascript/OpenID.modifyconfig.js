jQuery(function() {
    jQuery('[id^=registrationProvider_]').click(function() {
        checkState(this);
    }).each(function() {
        checkState(this);
    })
});

function checkState(elem) {
    if (jQuery(elem).prop("checked")) {
        // A registration provider was enabled.
        var index = jQuery(elem).prop("id").split('_')[1];
        jQuery('#loginProvider_' + index).prop("checked", true).prop('disabled', true);
    } else {
        // A registration provider was disabled.
        var index = jQuery(elem).prop("id").split('_')[1];
        jQuery('#loginProvider_' + index).prop('disabled', false);
    }
}