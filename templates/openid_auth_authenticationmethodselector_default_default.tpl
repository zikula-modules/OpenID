<form id="authentication_select_method_form_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" class="authentication_select_method" method="post" action="{$form_action}" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" id="authentication_select_method_csrftoken_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" id="authentication_select_method_selector_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" name="authentication_method_selector" value="1" />
        <input type="hidden" id="authentication_select_method_module_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" name="authentication_method[modname]" value="{$authentication_method.modname}" />
        <input type="hidden" id="authentication_select_method_method_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" name="authentication_method[method]" value="{$authentication_method.method}" />
        {strip}{switch expr=$authentication_method.method}
            {case expr='Google'}
                {gt text='Google Account' assign='button_text'}
            {/case}
            {case expr='myID'}
                {gt text='myID.net Account' assign='button_text'}
            {/case}
            {case expr='myOpenID'}
                {gt text='myOpenID Account' assign='button_text'}
            {/case}
            {case expr='OpenID'}
                {gt text='Any OpenID' assign='button_text'}
            {/case}
            {case expr='PIP'}
                {gt text='Symantec VeriSign PIP' assign='button_text'}
            {/case}
            {case expr='Yahoo'}
                {gt text='Yahoo! Account' assign='button_text'}
            {/case}
        {/switch}
        {/strip}<input type="submit" id="authentication_select_method_submit_{$authentication_method.modname|lower}_{$authentication_method.method|lower}" class="authentication_select_method_button{if $is_selected} authentication_select_method_selected{/if}" name="submit" value="{$button_text}" />
    </div>
</form>