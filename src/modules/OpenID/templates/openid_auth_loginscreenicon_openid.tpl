<form id="users_loginscreen_loginwith_OpenID" class="users_loginscreen_loginwith_button" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_OpenID" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_OpenID" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_Users" name="openidtype" value="openid" />
        <input type="submit" id="users_loginscreen_loginwith_button_OpenID" class="users_loginscreen_loginwith_button" value="OpenID" />
    </div>
</form>