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
 * Converts a boolean value to a picture.
 */
function smarty_modifier_openid_getdisplayname($method)
{
    $provider = OpenID_Helper_Builder::buildInstance($method);

    return $provider->getShortDescription();
}