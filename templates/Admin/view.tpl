{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="OpenID users"}</h3>
</div>
<table class="z-datatable">
    <thead>
    <tr>
        <th>{gt text='Uid'}</th>
        <th>{gt text='Username'}</th>
        <th>{gt text='Has password'}</th>
        {assign var='cols' value=4}
        {foreach from=$openIdProvider item='provider'}
            <th>{$provider->getProviderDisplayName()}</th>
            {assign var='cols' value=$cols+1}
        {/foreach}
        <th>{gt text='Actions'}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$users item='user'}
        <tr>
            <td>{$user.uid}</td>
            <td>{$user.uname}TODO</td>
            <td>{$user.hasPassword|openid_bool2pic}</td>
            {foreach from=$openIdProvider item='provider'}
                <td>TODO</td>
            {/foreach}
            <td class="oid-actions">TODO</td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="{$cols}{*TODO*}">{gt text='There are no users associated with an OpenID.'}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
{adminfooter}
