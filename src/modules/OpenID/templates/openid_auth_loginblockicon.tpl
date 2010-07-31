{assign var='supports_ssl' value='openssl_open'|function_exists}
<a id="users_block_loginwith_OpenID" class="users_block_loginwith"  href="{getcurrenturi loginwith='OpenID' openidtype='openid'}">{img modname='OpenID' src='small/openid-icon.png' __alt='OpenID' __title='OpenID'}</a>
{if $supports_ssl}
<a id="users_block_loginwith_Google" class="users_block_loginwith"  href="{getcurrenturi loginwith='OpenID' openidtype='google'}">[Google&reg; Account]</a>
{/if}
<a id="users_block_loginwith_VeriSign" class="users_block_loginwith"  href="{getcurrenturi loginwith='OpenID' openidtype='verisign'}">[VeriSign&reg; PIP]</a>