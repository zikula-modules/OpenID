// Copyright Zikula Foundation 2011 - license GNU/LGPLv3 (or at your option, any later version).
/*
 Disabled, see https://github.com/zikula/core/issues/907.

if (typeof(OpenID) == 'undefined') {
    OpenID = {};
}

OpenID.Login =
{
    init: function()
    {


        if ($('users_login_select_authentication_form_openid_openid') != null) {
            $('users_login_select_authentication_form_openid_openid').observe('submit', function(event) { Zikula.Users.Login.onSubmitSelectAuthenticationMethod(event, 'users_login_select_authentication_form_openid_openid'); });
        }
        if ($('users_login_select_authentication_form_openid_google') != null) {
            $('users_login_select_authentication_form_openid_google').observe('submit', function(event) { Zikula.Users.Login.onSubmitSelectAuthenticationMethod(event, 'users_login_select_authentication_form_openid_google'); });
        }
        if ($('users_login_select_authentication_form_openid_pip') != null) {
            $('users_login_select_authentication_form_openid_pip').observe('submit', function(event) { Zikula.Users.Login.onSubmitSelectAuthenticationMethod(event, 'users_login_select_authentication_form_openid_pip'); });
        }

    }

}

// Load and execute the initialization when the DOM is ready.
// This must be below the definition of the init function!
document.observe("dom:loaded", OpenID.Login.init);
 */