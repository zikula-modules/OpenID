<?php

class OpenID_Helper_VeriSignPIP extends OpenID_Helper_OpenID
{
    const USER_ENDPOINT = 'http://%s.pip.verisignlabs.com/';

    protected $supplied_id;

    public function __construct(array $authenticationInfo)
    {
        $this->supplied_id = $authenticationInfo['supplied_id'];
    }

    public function getSuppliedId()
    {
        if (strpos($this->supplied_id, 'verisignlabs.com') === false) {
            return sprintf(self::USER_ENDPOINT, $this->supplied_id);
        } else {
            return $this->supplied_id;
        }
    }

    public function getDisplayName($claimedID)
    {
        return $this->supplied_id;
    }

}