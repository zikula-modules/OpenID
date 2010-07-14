{gt text="My OpenIDs" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>

{insert name='getstatusmsg'}
<form id="openidViewForm" action="{modurl modname='OpenID' type='user' func='updatePrimaryOpenID'}">
    <div>
        <input type="hidden" id="openid_authid" name="authid" value="{insert name='generateauthkey' module='OpenID'}" />
        {if !empty($openids)}
        <table class="z-datatable">
            <thead>
                <tr>
                    <th>{gt text="OpenID"}</th>
                    <th class="z-center">{gt text="Primary?"}</th>
                    <th class="z-right">{gt text="Actions"}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$openids item='openid' name='openids'}
                <tr class="{cycle values='z-odd,z-even'}">
                    <td>{$openid.claimed_id|safehtml}</td>
                    <td class="z-center"><input id="openid_primary_{$openid.id}" name="openid_primary" value="{$openid.id}" type="radio"{if $openid.is_primary} checked="checked"{/if} /></td>
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
    </div>
    <div class="z-formbuttons z-buttons">{strip}
        {if !empty($openids)}{gt text="Add another OpenID" assign='addText'}{else}{gt text="Add my OpenID" assign='addText'}{/if}
        {gt text="Change primary OpenID" assign='okText'}
        {/strip}<a href="{modurl modname=$module type='user' func='newOpenID'}" title="{$addText}">{img modname='core' src='edit_add.gif' set='icons/extrasmall' alt=$addText title=$addText} {$addText}</a>
        {if !empty($openids)}{button src='button_ok.gif' set='icons/extrasmall' alt=$okText title=$okText text=$okText}{/if}
    </div>
</form>
{zdebug}