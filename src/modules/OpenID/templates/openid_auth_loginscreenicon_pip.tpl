<form id="users_loginscreen_loginwith_PIP" class="users_loginscreen_loginwith" method="post" action="{modurl modname='Users' type='user' func='loginScreen'}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="openid_authid_PIP" name="authid" value="{$openid_authkey}" />
        <input type="hidden" id="loginwith_PIP" name="loginwith" value="OpenID" />
        <input type="hidden" id="openidtype_PIP" name="openidtype" value="pip" />
        <input type="submit" id="users_loginscreen_loginwith_button_PIP" class="users_loginscreen_loginwith_button" value="Symantec (VeriSign) PIP" />
    </div>
</form>