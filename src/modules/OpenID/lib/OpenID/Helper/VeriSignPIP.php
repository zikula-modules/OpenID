<?php

class OpenID_Helper_VeriSignPIP extends OpenID_Helper_OpenID
{
    const USER_ENDPOINT = 'http://%s.pip.verisignlabs.com/';

    public function getSuppliedId()
    {
        if (strpos($this->suppliedId, 'verisignlabs.com') === false) {
            return sprintf(self::USER_ENDPOINT, $this->suppliedId);
        } else {
            return $this->suppliedId;
        }
    }

    public function getDisplayName($claimedId)
    {
        return $this->suppliedId;
    }

}