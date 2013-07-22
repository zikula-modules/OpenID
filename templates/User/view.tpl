{gt text="My OpenIDs" assign=templatetitle}
<h2>{$templatetitle}</h2>
{insert name='getstatusmsg'}
{if !empty($openids)}
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text="Service"}</th>
            <th>{gt text="ID"}</th>
            <th class="z-center" colspan="{$actions.count|default:'1'}">{gt text="Actions"}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$openids item='openid' name='openids'}
        <tr class="{cycle values='z-odd,z-even'}">
            <td>
                {strip}
                {switch expr=$openid.authentication_method|lower}
                {case expr='google'}
                <a href="http://www.google.com/accounts/">{img modname='OpenID' src='extrasmall/google-favicon.png' __alt='Google Account' __title='Google Account'}&nbsp;{gt text='Google Account'}</a>
                {/case}
                {case expr='myid'}
                <a href="http://www.myid.net/">{img modname='OpenID' src='extrasmall/myid-net-icon.png' __alt='myID.net Account' __title='myID.net Account'}&nbsp;{gt text='myID.net Account'}</a>
                {/case}
                {case expr='myopenid'}
                <a href="http://www.myopenid.com/">{img modname='OpenID' src='extrasmall/myopenid-icon.png' __alt='myOpenID Account' __title='myOpenID Account'}&nbsp;{gt text='myOpenID Account'}</a>
                {/case}
                {case expr='pip'}
                <a href="http://pip.verisignlabs.com/">{img modname='OpenID' src='extrasmall/symantec-checkmark-only-logo.png' __alt='Symantec Personal Identity Portal' __title='Symantec Personal Identity Portal'}&nbsp;{gt text='Symantec Personal Identity Portal'}</a>
                {/case}
                {case expr='yahoo'}
                <a href="http://me.yahoo.com/">{img modname='OpenID' src='extrasmall/yahoo-icon.png' __alt='Yahoo! Account' __title='Yahoo! Account'}&nbsp;{gt text='Yahoo! Account'}</a>
                {/case}
                {case}
                {img modname='OpenID' src='extrasmall/openid-icon.png' __alt='OpenID' __title='OpenID'}&nbsp;{gt text='OpenID'}
                {/case}
                {/switch}
                {/strip}
            </td>
            <td title="{gt text='Claimed ID: %1$s' tag1=$openid.claimed_id|safehtml}">
                {strip}
                {if $openid.authentication_method|lower == 'google'}
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
                {/strip}
            </td>
            {assign var='actionlist' value=$actions[$openid.id]}
            {if isset($actionlist.delete)}
            <td class="oid-action">
                {if ($actionlist.delete)}
                <a href="{$actionlist.delete.url}">{icon type='delete' size='extrasmall' __title='Delete' __alt='Delete' class='tooltips'}</a>
                {else}
                {icon type='delete' size='extrasmall' __title='Delete' __alt='Delete' class='tooltips' style='visibility: hidden;'}
                {/if}
            </td>
            {/if}
        </tr>
        {/foreach}
    </tbody>
</table>
{else}
<p class="z-informationmsg">{gt text="You do not have an OpenID associated with your account at this time."}</p>
{/if}
<div class="z-formbuttons z-buttons">
    {strip}
    {if !empty($openids)}{gt text="Add another OpenID" assign='addText'}{else}{gt text="Add my OpenID" assign='addText'}{/if}
    {/strip}
    <a href="{modurl modname=$module type='user' func='newOpenID'}" title="{$addText}">{icon type='add' size='extrasmall' alt=$addText title=$addText} {$addText}</a>
</div>
{include file="legalfooter.tpl"}

<script type="text/javascript">
    Zikula.UI.Tooltips($$('.tooltips'));
</script>
