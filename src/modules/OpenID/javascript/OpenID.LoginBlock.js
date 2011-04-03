// Copyright Zikula Foundation 2011 - license GNU/LGPLv3 (or at your option, any later version).

if (typeof(OpenID) == 'undefined') {
    OpenID = {};
}

OpenID.LoginBlock =
{
    init: function()
    {
        if ($('authentication_select_method_form_openid_google') != null) {
            $('authentication_select_method_form_openid_google').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_google'); });
        }
        if ($('authentication_select_method_form_openid_myid') != null) {
            $('authentication_select_method_form_openid_myid').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_myid'); });
        }
        if ($('authentication_select_method_form_openid_myopenid') != null) {
            $('authentication_select_method_form_openid_myopenid').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_myopenid'); });
        }
        if ($('authentication_select_method_form_openid_openid') != null) {
            $('authentication_select_method_form_openid_openid').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_openid'); });
        }
        if ($('authentication_select_method_form_openid_pip') != null) {
            $('authentication_select_method_form_openid_pip').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_pip'); });
        }
        if ($('authentication_select_method_form_openid_yahoo') != null) {
            $('authentication_select_method_form_openid_yahoo').observe('submit', function(event) { Zikula.Users.LoginBlock.onSubmitSelectAuthenticationMethod(event, 'authentication_select_method_form_openid_yahoo'); });
        }
    }

}

// Load and execute the initialization when the DOM is ready.
// This must be below the definition of the init function!
document.observe("dom:loaded", OpenID.LoginBlock.init);
