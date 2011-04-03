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
class OpenID_Helper_Yahoo extends OpenID_Helper_OpenID
{
    /**
     * The OpenID server endpoint used by Google for all OpenID authentication.
     */
    const USER_ENDPOINT = "http://yahoo.com";
    
    /**
     * Builds a new instance of this class, extracting the supplied OpenID from the $authenticationInfo parameter.
     *
     * @param array $authenticationInfo An array containing the authentication information, and specifically, the OpenID supplied by the user
     *                                      in the 'supplied_id' element which is used to initialize this instance.
     */
    public function __construct(array $authenticationInfo = null)
    {
        if (isset($authenticationInfo)) {
            $this->suppliedId = $authenticationInfo['supplied_id'];
        }
    }

    /**
     * Returns the supplied id.
     *
     * @return string The OpenID supplied by the user.
     */
    public function getSuppliedId()
    {
        if (isset($this->suppliedId)) {
            return $this->suppliedId;
        } else {
            return self::USER_ENDPOINT;
        }
    }

}