{switch expr=$openid_type}
    {case expr='google'}
            <input id="openid_openid_type" type="hidden" name="authinfo[openid_type]" value="google" />
            <legend>{gt text='Google&reg; Account'}</legend>
            <p>{gt text="Click 'Log in' to log in with your Google Account."}</p>
    {/case}
    {case expr='verisign'}
            <input id="openid_openid_type" type="hidden" name="authinfo[openid_type]" value="verisign" />
            <legend>{gt text='VeriSign&reg; PIP'}</legend>
            <div><label for="openid_supplied_id">{gt text='PIP user name'}</label></div>
            <div><input id="openid_supplied_id" name="authinfo[supplied_id]" type="text" maxlength="255" value="" /></div>
    {/case}
    {case}
            <input id="openid_openid_type" type="hidden" name="authinfo[openid_type]" value="openid" />
            <legend><label for="openid_identifier">{img modname=$module src='small/openid-logo.png' __alt='OpenID' __title='OpenID'}</label></legend>
            <div><input id="openid_identifier" name="authinfo[supplied_id]" type="text" maxlength="255" value="" /></div>
    {/case}
{/switch}
