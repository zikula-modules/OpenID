{insert name='generateauthkey' module='OpenID' assign='openid_authkey'}
<form id="users_loginscreen_loginwith_OpenID" class="users_loginscreen_loginwith" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_OpenID" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_OpenID" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_Users" name="openidtype" value="openid" />
        <input type="submit" id="users_loginscreen_loginwith_button_OpenID" class="users_loginscreen_loginwith_button" value="OpenID" />
    </div>
</form>
{if isset($supports_ssl) && $supports_ssl}
<form id="users_loginscreen_loginwith_Google" class="users_loginscreen_loginwith" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_Google" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_Google" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_Google" name="openidtype" value="google" />
        <input type="submit" id="users_loginscreen_loginwith_button_Google" class="users_loginscreen_loginwith_button" value="Google Account" />
    </div>
</form>
{/if}
<form id="users_loginscreen_loginwith_PIP" class="users_loginscreen_loginwith" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_PIP" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_PIP" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_PIP" name="openidtype" value="pip" />
        <input type="submit" id="users_loginscreen_loginwith_button_PIP" class="users_loginscreen_loginwith_button" value="Symantec (VeriSign) PIP" />
    </div>
</form>