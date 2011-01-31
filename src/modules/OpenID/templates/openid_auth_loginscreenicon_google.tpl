<form id="users_loginscreen_loginwith_Google" class="users_loginscreen_loginwith_button" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_Google" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_Google" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_Google" name="openidtype" value="google" />
        <input type="submit" id="users_loginscreen_loginwith_button_Google" class="users_loginscreen_loginwith_button" value="Google Account" />
    </div>
</form>