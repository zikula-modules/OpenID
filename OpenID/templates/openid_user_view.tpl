{gt text="My OpenIDs" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>

{insert name='getstatusmsg'}
{if !empty($openids)}
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text="Service"}</th>
            <th>{gt text="ID"}</th>
            <th class="z-right">{gt text="Actions"}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$openids item='openid' name='openids'}
        <tr class="{cycle values='z-odd,z-even'}">
            <td>{strip}
                {switch expr=$openid.openid_type}
                    {case expr='google'}
                    <a href="http://www.google.com/accounts/">{gt text='Google Account'}</a>
                    {/case}
                    {case expr='googleapp'}
                    <a href="http://www.google.com/a/">{gt text='Google Apps Hosted Account'}</a>
                    {/case}
                    {case expr='verisign'}
                    <a href="http://pip.verisignlabs.com/">{gt text='VeriSign Personal Identity Portal'}</a>
                    {/case}
                    {case}
                    {img modname='OpenID' src='extrasmall/openid-logo.png' __alt='OpenID' __title='OpenID'}
                    {/case}
                {/switch}
            {/strip}</td>
            <td title="{gt text='Claimed ID: %1$s' tag1=$openid.claimed_id|safehtml}">{strip}
                {if $openid.openid_type == 'google'}
                    {assign var='linkopen' value='<a href="http://www.google.com/accounts/">'}
                    {assign var='linkclose' value='</a>'}
                {elseif substr($openid.claimed_id, 0, 4) == 'http'}
                    {assign var='linkopen' value='<a href="%s">'|sprintf:$openid.claimed_id}
                    {assign var='linkclose' value='</a>'}
                {else}
                    {assign var='linkopen' value=''}
                    {assign var='linkclose' value=''}
                {/if}
                {$linkopen}{if !empty($openid.display_id)}{$openid.display_id|safehtml}{else}{$openid.claimed_id|safehtml}{/if}{$linkclose}
            {/strip}</td>
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
{include file="openid_common_legalfooter.tpl"}
