{pageaddvar name='javascript' value='jquery'}
{pageaddvar name='javascript' value='jquery-ui'}
{pageaddvar name='javascript' value='modules/OpenID/javascript/OpenID.view.js'}
{pageaddvar name="jsgettext" value="module_openid_js:OpenID"}
{pageaddvar name='stylesheet' value='javascript/jquery-ui/themes/base/jquery-ui.css'}
{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="OpenID users"}</h3>
</div>

<form class="z-form" id="oid-view-form" action="{modurl modname='OpenID' type='admin' func='setpassword'}" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}">
    {checkpermissionblock component='.*' instance='.*' level=ACCESS_ADMIN}
    <div class="z-buttons oid-button-row">
        {button type="submit" id="set-password-multiple" class="z-btgreen" src='button_ok.png' set='icons/extrasmall' __alt='Set random password for all selected users' __title='Set random password for all selected users' __text='Set random password for all selected users'}
    </div>
    {/checkpermissionblock}
    <table class="z-datatable">
        <thead>
        <tr>
            <th><input class="oid-select-user" id="selectAll" type="checkbox" /></th>
            <th>{gt text='Uid'}</th>
            <th>{gt text='Username'}</th>
            <th>{gt text='Has password'}</th>
            {assign var='cols' value=5}
            {foreach from=$openIdProvider item='provider'}
                <th>{$provider->getProviderDisplayName()|safetext}</th>
                {assign var='cols' value=$cols+1}
            {/foreach}
            <th>{gt text='Actions'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$userMapByUid item='user' key='uid'}
            <tr>
                <td><input class="oid-select-user" type="checkbox" id="user_{$user.user.uid}" name="users[{$user.user.uid}]" value="1" /></td>
                <td>{$user.user.uid|safetext}</td>
                <td>{$user.user.uname|safetext}</td>
                <td>{$user.user.hasPassword|openid_bool2pic}</td>
                {foreach from=$openIdProvider item='provider'}
                    <td>{if in_array($provider->getProviderName(), $user.registeredProviders)}{gt text='Yes'}{else}{gt text='No'}{/if}</td>
                {/foreach}
                <td class="oid-actions">
                    {if !$user.user.hasPassword}
                    {checkpermissionblock component='.*' instance='.*' level=ACCESS_ADMIN}
                    <a data-uid="{$user.user.uid}" data-uname="{$user.user.uname}" class="oid-password" title="{gt text='Set a random password for this user'}" href="{modurl modname='OpenID' type='admin' func='setpassword' uid=$user.user.uid skipCheck='%s'}">{img modname='core' set='icons/extrasmall' src='password.png'}</a>
                    {/checkpermissionblock}
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="{$cols}{*TODO*}">{gt text='There are no users associated with an OpenID.'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</form>

<div id="dialog-confirm-setpassword" title="" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>{gt text='The user will get a new random password via email.'}</span><br /><strong>{gt text='Are you really sure to proceed?'}</strong></p>
</div>

{adminfooter}
