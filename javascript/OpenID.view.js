jQuery(function() {
    jQuery('#selectAll').click(function() {
        jQuery('.oid-select-user').each(function() {
            jQuery(this).prop("checked", jQuery('#selectAll').prop("checked"));
        });
    });

    var redirectUrl = "";

    jQuery('.oid-password').click(event, function() {
        event.preventDefault();

        var title;
        title = Zikula.__("Are you really sure to set a random password for \"" + jQuery(this).data('uname') + "\"?", "module_openid");

        // Skip security confirmation.
        redirectUrl = jQuery(this).prop('href').replace('%s', '1');

        jQuery("#dialog-confirm-setpassword").dialog("option", "title", title).dialog("open");
    });

    jQuery("#dialog-confirm-setpassword").dialog({
        resizable: false,
        autoOpen: false,
        height:170,
        width: 600,
        modal: true,
        show: {
            effect: "fade",
            duration: 200
        },
        buttons: {
            "Set a random password": function() {
                window.location.href = redirectUrl;
                jQuery( this ).dialog("close");
            },
            Cancel: function() {
                jQuery( this ).dialog("close");
            }
        }
    });
});