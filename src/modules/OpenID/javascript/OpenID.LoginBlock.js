// Copyright Zikula Foundation 2011 - license GNU/LGPLv3 (or at your option, any later version).

if (typeof(OpenID) == 'undefined') {
    OpenID = {};
}

OpenID.LoginBlock =
{
    init: function()
    {
        if ($('users_loginblock_select_authentication_form_openid_openid') != null) {
            $('users_loginblock_select_authentication_form_openid_openid').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'users_loginblock_select_authentication_form_openid_openid'); });
        }
        if ($('users_loginblock_select_authentication_form_openid_google') != null) {
            $('users_loginblock_select_authentication_form_openid_google').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'users_loginblock_select_authentication_form_openid_google'); });
        }
        if ($('users_loginblock_select_authentication_form_openid_pip') != null) {
            $('users_loginblock_select_authentication_form_openid_pip').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'users_loginblock_select_authentication_form_openid_pip'); });
        }
    }

}

// Load and execute the initialization when the DOM is ready.
// This must be below the definition of the init function!
document.observe("dom:loaded", OpenID.LoginBlock.init);
