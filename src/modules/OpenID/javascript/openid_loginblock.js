// Copyright Zikula Foundation 2011 - license GNU/LGPLv3 (or at your option, any later version).

if (typeof(OpenID) == 'undefined') {
    OpenID = {};
}

OpenID.LoginBlock =
{
    init: function()
    {
        if ($('users_loginblock_loginwith_OpenID') != null) {
            $('users_loginblock_loginwith_OpenID').observe('submit', function(event) { OpenID.LoginBlock.onSubmitLoginWithOpenID(event, 'OpenID'); });
        }
        if ($('users_loginblock_loginwith_Google') != null) {
            $('users_loginblock_loginwith_Google').observe('submit', function(event) { OpenID.LoginBlock.onSubmitLoginWithOpenID(event, 'Google'); });
        }
        if ($('users_loginblock_loginwith_PIP') != null) {
            $('users_loginblock_loginwith_PIP').observe('submit', function(event) { OpenID.LoginBlock.onSubmitLoginWithOpenID(event, 'PIP'); });
        }
    },

    onSubmitLoginWithOpenID: function(event, openidType)
    {
        Zikula.Users.LoginBlock.changingLoginBlockFields(true);

        var r = new Zikula.Ajax.Request(
            Zikula.Config.baseURL + "ajax.php?module=OpenID&func=getLoginBlockFields&openidtype=" + openidType,
            {
                method: 'post',
                authid: 'openid_authid',
                onComplete: OpenID.LoginBlock.getLoginWithOpenIDResponse
            });

        event.stop();
    },

    getLoginWithOpenIDResponse: function(req)
    {
        if (!req.isSuccess()) {
            $('users_loginblock_waiting').addClassName('z-hide');
            Zikula.showajaxerror(req.getMessage());
            return;
        }

        var data = req.getData();

        Element.update('users_loginblock_fields', data.content);
        
        Zikula.Users.LoginBlock.changingLoginBlockFields(false);

        $('users_loginblock_loginwith_' + data.openidType).addClassName('z-hide');
    }

}

// Load and execute the initialization when the DOM is ready.
// This must be below the definition of the init function!
document.observe("dom:loaded", OpenID.LoginBlock.init);
