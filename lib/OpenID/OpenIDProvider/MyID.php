<?php
/**
 * Copyright Zikula Foundation 2013 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPv3 (or at your option any later version).
 * @package OpenID
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * A helper or utility class that provides information for a myID.net OpenID provider in expected formats for the protocol.
 */
class OpenID_OpenIDProvider_MyID extends OpenID_OpenIDProvider_AbstractProvider
{
    /**
     * A sprintf pattern used to construct the appropriate endpoint URL when only the user's PIP user name is supplied as the supplied id.
     */
    const USER_ENDPOINT = 'http://%s.myid.net/';

    /**
     * Returns the supplied id in the form of an OpenID endpoint, which for PIP is USER_ENDPOINT constant with the user's PIP user name in place of the parameter.
     *
     * @return string The OpenID supplied by the user in the form of an endpoint.
     */
    public function getSuppliedId()
    {
        if (strpos($this->suppliedId, 'myid.net') === false) {
            return sprintf(self::USER_ENDPOINT, $this->suppliedId);
        } else {
            return $this->suppliedId;
        }
    }

    /**
     * Constructs and returns the user's OpenID appropriate for human-readable on-screen display.
     *
     * For PIP, this is the supplied id.
     *
     * @param string $claimedId The normalized, authenticated claimed OpenID for the user; may be used in the future.
     *
     * @return string The OpenID, adjusted for display purposes--in this case formatted as an OpenID URL.
     */
    public function getDisplayName($claimedId)
    {
        return $this->suppliedId;
    }

    public function getProviderDisplayName()
    {
        return $this->__('myID.net');
    }

    public function getShortDescription()
    {
        return $this->__('myID.net');
    }

    public function getLongDescription()
    {
        return $this->__('myID.net Account');
    }

    public function needsSsl()
    {
        return false;
    }

    public function getIcon()
    {
        return 'modules/OpenID/images/medium/myid-net-icon.png';
    }
}