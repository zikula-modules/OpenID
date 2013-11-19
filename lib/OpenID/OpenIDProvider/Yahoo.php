<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
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
class OpenID_OpenIDProvider_Yahoo extends OpenID_OpenIDProvider_AbstractProvider
{
    /**
     * The OpenID server endpoint used by Yahoo for all OpenID authentication.
     */
    const USER_ENDPOINT = "http://yahoo.com";

    /**
     * Returns the supplied id.
     *
     * @return string The OpenID supplied by the user.
     */
    public function getSuppliedId()
    {
        return self::USER_ENDPOINT;
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
        return $this->__('Yahoo');
    }

    public function getShortDescription()
    {
        return $this->__('Yahoo');
    }

    public function getLongDescription()
    {
        return $this->__('Yahoo Account');
    }

    public function needsSsl()
    {
        return true;
    }

    public function getIcon()
    {
        return 'modules/OpenID/images/medium/yahoo-icon.png';
    }

    public function getExtraExtensionArgs()
    {
        return array(array(Auth_OpenID_BARE_NS, 'xopenid_lang_pref', ZLanguage::getLocale()));
    }
}