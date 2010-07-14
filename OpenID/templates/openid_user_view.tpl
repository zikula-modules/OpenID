{gt text="My OpenIDs" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>

{insert name='getstatusmsg'}
{if !empty($openids)}
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text="OpenID"}</th>
            <th class="z-right">{gt text="Actions"}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$openids item='openid' name='openids'}
        <tr class="{cycle values='z-odd,z-even'}">
            <td>{$openid.claimed_id|safehtml}</td>
            <td>{strip}{foreach from=$actions[$openid.id] item='action'}
                {switch expr=$action.name}
                    {case expr='delete'}
                    <a href="{$action.url}">{img modname='core' set='icons/extrasmall' src='delete.gif' title=$action.title|safehtml alt=$action.title|safehtml}</a>
                    {/case}
                {/switch}
            {/foreach}{/strip}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{else}
<p class="z-informationmsg">{gt text="You do not have an OpenID associated with your account at this time."}</p>
{/if}
<div class="z-formbuttons z-buttons">{strip}
    {if !empty($openids)}{gt text="Add another OpenID" assign='addText'}{else}{gt text="Add my OpenID" assign='addText'}{/if}
    {/strip}<a href="{modurl modname=$module type='user' func='newOpenID'}" title="{$addText}">{img modname='core' src='edit_add.gif' set='icons/extrasmall' alt=$addText title=$addText} {$addText}</a>
</div>
{zdebug}
