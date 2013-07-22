{pageaddvar name='stylesheet' value='system/Users/style/style.css'}
{gt text="Add an OpenID" assign='templatetitle'}
{pagesetvar name='title' value=$templatetitle}
{insert name='csrftoken' assign='csrftoken'}
{insert name='getstatusmsg'}

<h2>{$templatetitle}</h2>
<div>
    <p>Use my...</p>
    <div class="authentication_select_method_bigbutton">
    {modurl modname='OpenID' type='user' func='newOpenId' assign='form_action'}
    {foreach from=$authentication_method_display_order item='authentication_method' name='authentication_method_display_order'}
        {authentication_method_selector form_type='newopenid' form_action=$form_action authentication_method=$authentication_method selected_authentication_method=$selected_authentication_method}
    {/foreach}
    </div>
</div>

<form id="openid_users_newopenid_form" class="z-form z-clearer z-gap" action="{modurl modname='OpenID' type='user' func='newOpenID'}" method="post">
    <div>
        <input type="hidden" id="openid_user_newopenid_csrftoken" name="csrftoken" value="{$csrftoken}" />
        <input type="hidden" id="openid_user_newopenid_authentication_method_modname" name="authentication_method[modname]" value="{$selected_authentication_method.modname|default:'OpenID'}" />
        <input type="hidden" id="openid_user_newopenid_authentication_method_method" name="authentication_method[method]" value="{$selected_authentication_method.method|default:'OpenID'}" />

        {switch expr=$selected_authentication_method.method|lower}
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
        {case expr='yahoo'}
        <p class="z-informationmsg">
            {gt text='If you are currently logged into a Yahoo! Account, ensure that it is the account you would like to add at this time. If you are not logged into your account, you will be asked to do so.'}
            <a href="http://www.yahoo.com/account/" target="_blank">{gt text='Click here to open your current Yahoo! Account in a new page.'}</a>
            {gt text='(You can use this to see if you are logged into a Yahoo! Account, and if so, which one.)'}
        </p>
        <fieldset>
            <legend>{gt text='New Yahoo! Account'}</legend>
            <div class="z-formrow">
                <p>{gt text='Click on \'Submit\' to sign into and add your Yahoo! Account.'}</p>
            </div>
        </fieldset>
        {/case}
        {case expr='pip'}
        <fieldset>
            <legend>{gt text='VeriSign Personal Identity Portal'}</legend>
            <div class="z-formrow">
                <label for="openid_users_newopenid_pip_username">{gt text='PIP user name'}</label>
                <input id="openid_users_newopenid_pip_username" name="username" type="text" maxlength="255" value="{$authentication_info.suppliedId|default:''}" />
            </div>
        </fieldset>
        {/case}
        {case expr='myid'}
        <fieldset>
            <legend>{gt text='myID.net'}</legend>
            <div class="z-formrow">
                <label for="openid_users_newopenid_myid_username">{gt text='myID.net user name'}</label>
                <input id="openid_users_newopenid_myid_username" name="username" type="text" maxlength="255" value="{$authentication_info.suppliedId|default:''}" />
            </div>
        </fieldset>
        {/case}
        {case expr='myopenid'}
        <fieldset>
            <legend>{gt text='myOpenID'}</legend>
            <div class="z-formrow">
                <label for="openid_users_newopenid_myopenid_username">{gt text='myOpenID user name'}</label>
                <input id="openid_users_newopenid_myopenid_username" name="username" type="text" maxlength="255" value="{$authentication_info.suppliedId|default:''}" />
            </div>
        </fieldset>
        {/case}
        {case}
        <fieldset>
            <legend>{gt text='OpenID'}</legend>
            <div class="z-formrow">
                {* Per OpenID specification: the field for the identifier should have a name attribute of "openid_identifier" *}
                <label for="openid_users_newopenid_openid_identifier">{img modname=$module src='small/openid-logo.png' __alt='OpenID' __title='OpenID'}</label>
                <input id="openid_users_newopenid_openid_identifier" name="openid_identifier" type="text" maxlength="255" value="{$authentication_info.suppliedId|default:''}" />
            </div>
        </fieldset>
        {/case}
        {/switch}
    </div>

    <div class="z-formbuttons z-buttons">
        {button src='button_ok.png' set='icons/extrasmall' __alt='Submit' __title='Submit' __text='Submit'}
        <a href="{modurl modname=$module type='user' func='view'}" title="{gt text='Cancel'}">{icon type='cancel' size='extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    </div>
</form>
{include file="legalfooter.tpl"}
