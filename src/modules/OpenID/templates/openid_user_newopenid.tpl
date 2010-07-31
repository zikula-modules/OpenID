{gt text="Add an OpenID" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>
{insert name='getstatusmsg'}
<form id="openidNewForm" class="z-form" action="{modurl modname=$module type='user' func='addOpenID'}" method="post">
    <div>
        <input type="hidden" id="openid_authid" name="authid" value="{insert name='generateauthkey' module='OpenID'}" />
        <input type="hidden" id="openid_type" name="authinfo[openid_type]" value="{$authinfo.openid_type|default:'openid'}" />
        <fieldset>
            <legend>Use my...</legend>
            {if $supports_ssl}
            <a href="{modurl modname='OpenID' func='newOpenID' openidtype='google'}">[Google Account]</a>
            {/if}
            <a href="{modurl modname='OpenID' func='newOpenID' openidtype='openid'}">{img modname='OpenID' src='extrasmall/openid-logo.png' __alt='OpenID' __title='OpenID'}</a>
            <a href="{modurl modname='OpenID' func='newOpenID' openidtype='verisign'}">[VeriSign PIP]</a>
        </fieldset>
        <fieldset>
            {switch expr=$authinfo.openid_type}
            {case expr='google'}
            <legend>{gt text='New Google Account'}</legend>
            <div class="z-formrow">
                <p class="z-informationmsg">{gt text='If you are currently logged into a Google Account, ensure that it is the account you would like to add at this time. If you are not logged into your account, you will be asked to do so.'}
                    <a href="http://www.google.com/accounts/">{gt text='Click here to open Google Accounts in a new page.'}</a> {gt text='(You can use this to see if you are logged into a Google Account, and if so, which one.)'}</p>
                <p>{gt text='Click on \'Submit\' to sign into and add your Google Account.'}</p>
            </div>
            {/case}
            {case expr='verisign'}
            <legend>{gt text='VeriSign Personal Identity Portal'}</legend>
            <div class="z-formrow">
                <label for="pip_username">PIP user name</label>
                <input id="pip_username" name="authinfo[supplied_id]" type="text" maxlength="255" value="{$authinfo.supplied_id}" />
            </div>
            {/case}
            {case}
            <legend>{gt text='New OpenID'}</legend>
            <div class="z-formrow">
                {* Per OpenID specification: the field for the identifier should have a name attribute of "openid_identifier" *}
                <label for="openid_identifier">{img modname=$module src='small/openid-logo.png' __alt='OpenID' __title='OpenID'}</label>
                <input id="openid_identifier" name="authinfo[supplied_id]" type="text" maxlength="255" value="{$authinfo.supplied_id}" />
            </div>
            {/case}
            {/switch}
        </fieldset>
    </div>

    <div class="z-formbuttons z-buttons">
        {button src='button_ok.gif' set='icons/extrasmall' __alt='Submit' __title='Submit' __text='Submit'}
        <a href="{modurl modname=$module type='user' func='view'}" title={gt text='Cancel'}>{img modname='core' src='button_cancel.gif' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    </div>
</form>
{include file="openid_common_legalfooter.tpl"}
{zdebug}