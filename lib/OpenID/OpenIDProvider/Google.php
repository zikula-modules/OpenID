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
 * A helper or utility class that provides information for a Google Account OpenID in expected formats for the protocol.
 */
class OpenID_OpenIDProvider_Google extends OpenID_OpenIDProvider_AbstractProvider
{
    /**
     * The OpenID server endpoint used by Google for all OpenID authentication.
     */
    const USER_ENDPOINT = "https://www.google.com/accounts/o8/id";

    /**
     * Returns the supplied id in the form of an OpenID endpoint, which for Google is always the same endpoint.
     *
     * @return string The OpenID supplied by the user.
     */
    public function getSuppliedId()
    {
        return self::USER_ENDPOINT;
    }

    /**
     * Constructs and returns the user's claimed OpenID appropriate for human-readable on-screen display.
     *
     * @param string $claimedId The normalized, authenticated claimed OpenID for the user.
     *
     * @return string The claimed OpenID, adjusted for display purposes--in this case formatted as an OpenID URL.
     */
    public function getDisplayName($claimedID)
    {
        return $this->__('(Google does not return a displayable name for your account)');
    }

    public function getProviderDisplayName()
    {
        return $this->__('Google');
    }

    public function getShortDescription()
    {
        return $this->__('Google');
    }

    public function getLongDescription()
    {
        return $this->__('Google Account');
    }

    public function needsSsl()
    {
        return true;
    }

    public function getIcon()
    {
        return 'fa-google-plus';
    }
}