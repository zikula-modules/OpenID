{pageaddvar name='javascript' value='jquery'}
{pageaddvar name='javascript' value='modules/OpenID/javascript/OpenID.modifyconfig.js'}
{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>
        {if isset($uid) && $uid !== false}
        {gt text="Set a random password"}
        {else}
            {gt text="Set random passwords"}
        {/if}
    </h3>
</div>

<p class="z-warningmsg">
    {if isset($uid) && $uid !== false}
    {gt text='Are you really sure to set a random password for "%s"?' tag1=$uname}
    {else}
    {gt text="Are you really sure to set random passwords?"}
    {/if}
</p>
<form class="z-form" action="{modurl modname='OpenID' type='admin' func='setpassword' uid=$uid}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}">
        <input type="hidden" name="users" value="{$users}">
        <fieldset>
            <legend>{gt text='Confirmation prompt'}</legend>
            <div class="z-formbuttons z-buttons">
                {if isset($uid) && $uid !== false}
                {button class="z-btgreen" src='button_ok.png' set='icons/extrasmall' __alt='Yes, set a random password' __title='Yes, set a random password' __text='Yes, set a random password'}
                {else}
                {button class="z-btgreen" src='button_ok.png' set='icons/extrasmall' __alt='Yes, set random passwords' __title='Yes, set random passwords' __text='Yes, set random passwords'}
                {/if}
                <a class="z-btred" href="{modurl modname='OpenID' type='admin' func='view'}" title="{gt text='No'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='No'}</a>
            </div>
        </fieldset>
    </div>
</form>

{adminfooter}
