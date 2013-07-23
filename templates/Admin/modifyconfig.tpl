{pageaddvar name='javascript' value='jquery'}
{pageaddvar name='javascript' value='modules/OpenID/javascript/OpenID.modifyconfig.js'}
{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text="OpenID configuration"}</h3>
</div>

{form cssClass='z-form'}
    {formvalidationsummary}
    <fieldset>
        <legend>{gt text='Allowed OpenID types.'}</legend>

        <div class="z-formrow">
            {formlabel for='loginProvider' text='Allowed providers for log-in'}
            {formcheckboxlist id='loginProvider' items=$items selectedValue=$selectedLoginProvider}
        </div>
        <div class="z-formrow">
            {formlabel for='registrationProvider' text='Allowed providers for registration'}
            {formcheckboxlist id='registrationProvider' items=$items selectedValue=$selectedRegistrationProvider}
        </div>
    </fieldset>

    <div class="z-buttons z-formbuttons">
        {formbutton id="submit" commandName='submit' __text='Save' class='z-bt-ok'}
        {formbutton id='cancel' commandName='cancel' __text='Cancel' class='z-bt-cancel'}
    </div>
{/form}

{adminfooter}
