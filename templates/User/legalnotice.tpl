{gt text="OpenID for Zikula Legal Notices" assign='templatetitle'}
{pagesetvar name='title' value=$templatetitle}
<h2>{$templatetitle}</h2>
<p class="oid-fineprint">
    {gt text="Use of company, product names, service names, trademarks, servicemarks, logos (or other similar images) on this site does not imply an endorsement by the respective companies of any content or service on this site. These names, marks, and logos (or other similar images) are used only to link to or otherwise highlight products or services provided by the respective companies."}
    {gt text="Without intending to be, the following list may be incomplete. All other company names, product names, service names, and logos (or other similar images) representing companies and/or their products or services may be trademarks of or servicemarks of the respective companies they represent, or may be protected by copyright by their respective companies."}
</p>
<ul class="oid-mediumprint">
    <li>{gt text='Google is a registered trademark of, and Google Apps is a trademark of Google, Inc. The Google logo, the Google \'favicon\' icon, and other Google logos, icons or images are copyrighted by, and may be trademarks or service marks of <a href="http://www.google.com">Google, Inc.</a>'}</li>
    <li>{gt text='myID.net and the myID.net logo are copyrighted by, and may be a trademarks or servicemarks of <a href="http://www.openmaru.com">openmaru Studio</a>.'}</li>
    <li>{gt text='myOpenID is a registered trademark of, and the myOpenID logo is copyrighted by, and may be a trademark or servicemark of <a href="http://www.janrain.com">JanRain, Inc.</a>'}</li>
    <li>{gt text='OpenID and the OpenID logo are registered trademarks of, and the OpenID icon is a trademark of the OpenID Foundation. The OpenID logo and the OpenID icon are copyrighted by the <a href="http://openid.net">OpenID Foundation</a>.'}</li>
    <li>{gt text='VeriSign is a registered trademark of Symantec, Inc. The Symantec \'checkmark\' logo is copyrighted by, and may be a trademark or a servicemark of <a href="http://www.symantec.com">Symantec, Inc.</a>'}</li>
    <li>{gt text='Yahoo is a registered trademark of Yahoo, Inc. The Yahoo \'Y!\' logo (including, but not limited to the Y! dialog bubble) is copyrighted by, and may be a trademark or a servicemark of <a href="http://www.yahoo.com">Yahoo, Inc.</a>'}</li>
    <li>{gt text='Zikula is a registered trademark of, and the Zikula logo copyrighted by and is a trademark of the <a href="http://www.zikula.org">Zikula Software Foundation</a>.'}</li>
</ul>
<p class="oid-fineprint">
    {gt text="Absence of a particular trademark, servicemark or copyright notice from the list does not imply that such a mark or copyright does not exist, nor does it imply an endorsement by the respective company of any portion of this site."}
</p>

{if !empty($returnUrl)}
<p><a href="{$returnUrl}">{gt text='Return to the previous page.'}</a></p>
{else}
<p>{gt text="Use your browser's Back button to return to the previous page."}</p>
{/if}
