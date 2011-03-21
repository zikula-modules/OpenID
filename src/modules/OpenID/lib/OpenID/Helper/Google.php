<?php

class OpenID_Helper_Google extends OpenID_Helper_OpenID
{
    const USER_ENDPOINT = "https://www.google.com/accounts/o8/id";

    public function __construct(array $authenticationInfo)
    {
    }

    public function getSuppliedId()
    {
        return self::USER_ENDPOINT;
    }

    public function getDisplayName($claimedID)
    {
        return $this->__('(Google does not return a displayable name for your account)');
    }

}