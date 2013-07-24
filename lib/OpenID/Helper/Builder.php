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
 * Builds concrete instances of OpenID_Helper_OpenID or one of its subclasses or sibling classes, based on a specified authentication method.
 */
class OpenID_Helper_Builder
{
    /**
     * Builds an instance of OpenID_Helper_OpenID or one of its subclasses or sibling classes, based on a specified authentication method.
     *
     * @param string       $authenticationMethod The authentication method for which a helper should be built and returned.
     * @param array|string $authenticationInfo   The authentication information entered by the user, and passed on to the helper.
     *
     * @return boolean|OpenID_Helper_OpenID An instance of OpenID_Helper_OpenID or one of its siblings or subclasses appropriate for the authentication
     *                                          method, and initialized with the authentication information provided; false if an error or exception
     *                                          occurs or if the authentication method is not recognized.
     */
    public static function buildInstance($authenticationMethod, array $authenticationInfo = null)
    {
        $class = "OpenID_OpenIDProvider_" . $authenticationMethod;

        return new $class($authenticationInfo);
    }
}