{gt text="OpenID for Zikula Legal Notices" assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

<h2>{$templatetitle}</h2>
<ul>
    <li>{gt text="Google is a registered trademark of, and Google Apps is a trademark of Google, Inc."}</li>
    <li>{gt text="OpenID and the OpenID logo are registered trademarks of, and the OpenID icon is a trademark of the OpenID Foundation."}</li>
    <li>{gt text="VeriSign is a registered trademark of VeriSign, Inc."}</li>
    <li>{gt text="Zikula is a registered trademark of the Zikula Software Foundation."}</li>
</ul>
<p>{gt text="Without intending to be, the above list may be incomplete. All other company names, product names, service names, and icons and/or logos representing companies and/or their products or services may be trademarks of or servicemarks of the respective companies they represent, or may be protected by copyright by their respective companies."}
    {gt text="Absence of a particular trademark, servicemark or copyright from the list above does not imply that such a mark or copyright does not exist."}</p>
<p>{gt text="Use of company, product or service names, icons or logos on this site does not imply an endorsement by the respective companies of any content or service on this site."}</p>

{if !empty($return_url)}
<p><a href="{$return_url}">{gt text='Return to the previous page.'}</a></p>
{else}
<p>{gt text="Use your browser's Back button to return to the previous page."}</p>
{/if}
