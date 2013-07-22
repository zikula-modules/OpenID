{gt text='Remove an OpenID' assign='templatetitle'}
{pagesetvar name='title' value=$templatetitle}

<h2>{gt text='Remove an OpenID from your account'}</h2>

<form id="openid_user_removeopenid" class="z-form" action="{modurl modname='OpenID' type='user' func='removeOpenID'}" method="post" enctype="application/x-www-form-urlencoded">
    <input id="openid_user_removeopenid_csrftoken" type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    <input id="openid_user_removeopenid_id" type="hidden" name="id" value="{$openid.id|safetext}" />
    <input id="openid_user_removeopenid_confirmed" type="hidden" name="confirmed" value="1" />
    <fieldset>
        <legend>{gt text='Do you really want to remove this OpenID from your account?'}</legend>
        <div class="z-formrow">
            <label>{gt text='OpenID:'}</label>
            <span>{$openid.claimed_id}</span>
        </div>
    </fieldset>
    {notifydisplayhooks eventname='openid.ui_hooks.openid.form_delete' subject=$openid id=$openid.id}
    <div class="z-formbuttons z-buttons">
        {button class="z-btgreen" src='button_ok.png' set='icons/extrasmall' __alt='Remove OpenID' __title='Remove OpenID' __text='Remove OpenID'}
        <a class="z-btred" href="{modurl modname='OpenID' type='user' func='view'}" title="{gt text='Cancel'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    </div>
</form>
{include file="legalfooter.tpl"}
