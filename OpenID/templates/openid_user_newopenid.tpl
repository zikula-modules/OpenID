{gt text="Add an OpenID" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>
{insert name='getstatusmsg'}
<form id="openidNewForm" class="z-form" action="{modurl modname=$module type='user' func='addOpenID'}" method="post">
    <div>
        <input type="hidden" id="openid_authid" name="authid" value="{insert name='generateauthkey' module='OpenID'}" />
        <fieldset>
            <legend>{gt text='New OpenID'}</legend>
            <div class="z-formrow">
                {* Per OpenID specification: the field for the identifier should have a name attribute of "openid_identifier" *}
                <label for="openid_identifier">{img modname=$module src='small/openid-logo.png' __alt='OpenID' __title='OpenID'}</label>
                <input id="openid_identifier" name="openid_identifier" type="text" maxlength="255" value="{$supplied_id}" />
            </div>
        </fieldset>
    </div>
    <div class="z-formbuttons z-buttons">
        {button src='button_ok.gif' set='icons/extrasmall' __alt='Submit' __title='Submit' __text='Submit'}
        <a href="{modurl modname=$module type='user' func='view'}" title={gt text='Cancel'}>{img modname='core' src='button_cancel.gif' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    </div>
</form>
{* zdebug *}