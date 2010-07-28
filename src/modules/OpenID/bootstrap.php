<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

$libVendorPath = dirname(__FILE__) . '/lib/vendor';
ZLoader::addAutoloader('Auth', $libVendorPath);

// JanRain OpenID Libraries use require_once, so we need to ensure their stuff is on the include_path
$includePath = ini_get('include_path');
if ((strpos($includePath, PATH_SEPARATOR . $libVendorPath) === false)
    && (strpos($includePath, $libVendorPath . PATH_SEPARATOR) === false)
    && ($includePath !== $libVendorPath))
{
    set_include_path((!empty($includePath) ? $includePath . PATH_SEPARATOR : '') . $libVendorPath);
}
define('Auth_OpenID_RAND_SOURCE', null);

// TODO - This must be old code.
//$authApi = ModUtil::getObject('OpenID_Api_Auth'); // this will additionally register the service.
//EventUtil::attachService('users.delete', new Zikula_ServiceHandler('module.openid.api.auth', 'deleteUserEvent'));