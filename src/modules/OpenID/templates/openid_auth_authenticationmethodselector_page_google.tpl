<form id="users_login_select_authentication_form_openid_google" class="users_login_select_authentication" method="post" action="{modurl modname='Users' type='user' func='login'}" enctype="application/x-www-form-urlencoded">
    <div>
        {if $modvars.ZConfig.anonymoussessions}
        <input type="hidden" id="users_login_select_authentication_openid_google_csrftoken" name="csrftoken" value="{insert name='csrftoken'}" />
        {/if}
        <input type="hidden" id="users_login_select_authentication_openid_google_module" name="authentication_method[modname]" value="OpenID" />
        <input type="hidden" id="users_login_select_authentication_openid_google_method" name="authentication_method[method]" value="Google" />
        <input type="submit" id="users_login_select_authentication_openid_google_submit" class="users_login_select_authentication_button{if $is_selected} users_login_select_authentication_selected{/if}" name="submit" value="Google Account" />
    </div>
</form>