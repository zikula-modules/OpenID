{gt text="Add an OpenID" assign=templatetitle}
<h2>{$templatetitle}</h2>
{insert name='getstatusmsg'}
<form id="openid_users_newopenid_form" class="z-form" action="{modurl modname=$module type='user' func='addOpenID'}" method="post">
    <div>
        <input type="hidden" id="openid_users_newopenid_csrftoken" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" id="openid_users_newopenid_authentication_method" name="authentication_method" value="{$authentication_method.method|default:'OpenID'}" />
        <fieldset>
            <legend>Use my...</legend>
            {if $supports_ss}
            <a href="{modurl modname='OpenID' func='newOpenID' authenticationMethod='Google'}">[Google Account]</a>
            {/if}
            <a href="{modurl modname='OpenID' func='newOpenID' authenticationMethod='OpenID'}">{img modname='OpenID' src='extrasmall/openid-logo.png' __alt='OpenID' __title='OpenID'}</a>
            <a href="{modurl modname='OpenID' func='newOpenID' authenticationMethod='PIP'}">[Symantec (VeriSign) PIP]</a>
        </fieldset>

        {switch expr=$authentication_method.method|lower}
        {case expr='google'}
        <p class="z-informationmsg">
            {gt text='If you are currently logged into a Google Account, ensure that it is the account you would like to add at this time. If you are not logged into your account, you will be asked to do so.'}
            <a href="http://www.google.com/accounts/" target="_blank">{gt text='Click here to open Google Accounts in a new page.'}</a>
            {gt text='(You can use this to see if you are logged into a Google Account, and if so, which one.)'}
        </p>
        <fieldset>
            <legend>{gt text='New Google Account'}</legend>
            <div class="z-formrow">
                <p>{gt text='Click on \'Submit\' to sign into and add your Google Account.'}</p>
            </div>
        </fieldset>
        {/case}
        {case expr='pip'}
        <fieldset>
            <legend>{gt text='VeriSign Personal Identity Portal'}</legend>
            <div class="z-formrow">
                <label for="openid_users_newopenid_pip_username">PIP user name</label>
                <input id="openid_users_newopenid_pip_username" name="supplied_id" type="text" maxlength="255" value="{$authenticationIinfo.supplied_id}" />
            </div>
        </fieldset>
        {/case}
        {case}
        <fieldset>
            <legend>{gt text='New OpenID'}</legend>
            <div class="z-formrow">
                {* Per OpenID specification: the field for the identifier should have a name attribute of "openid_identifier" *}
                <label for="openid_users_newopenid_openid_identifier">{img modname=$module src='small/openid-logo.png' __alt='OpenID' __title='OpenID'}</label>
                <input id="openid_users_newopenid_openid_identifier" name="openid_identifier" type="text" maxlength="255" value="{$authentication_info.supplied_id}" />
            </div>
        </fieldset>
        {/case}
        {/switch}
    </div>

    <div class="z-formbuttons z-buttons">
        {button src='button_ok.gif' set='icons/extrasmall' __alt='Submit' __title='Submit' __text='Submit'}
        <a href="{modurl modname=$module type='user' func='view'}" title="{gt text='Cancel'}">{img modname='core' src='button_cancel.gif' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    </div>
</form>
{include file="openid_common_legalfooter.tpl"}
