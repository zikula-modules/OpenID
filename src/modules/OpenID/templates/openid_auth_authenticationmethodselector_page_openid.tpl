<form id="users_login_select_authentication_form_openid_openid" class="users_login_select_authentication" method="post" action="{modurl modname='Users' type='user' func='login'}" enctype="application/x-www-form-urlencoded">
    <div>
        {if $modvars.ZConfig.anonymoussessions}
        <input type="hidden" id="users_login_select_authentication_openid_openid_csrftoken" name="csrftoken" value="{insert name='csrftoken'}" />
        {/if}
        <input type="hidden" id="users_login_select_authentication_openid_openid_module" name="authentication_method[modname]" value="OpenID" />
        <input type="hidden" id="users_login_select_authentication_openid_openid_method" name="authentication_method[method]" value="OpenID" />
        <input type="submit" id="users_login_select_authentication_openid_openid_submit" class="users_login_select_authentication_button{if $is_selected} users_login_select_authentication_selected{/if}" name="submit" value="OpenID" />
    </div>
</form>