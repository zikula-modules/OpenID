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

ZLoader::addAutoloader('Auth', dirname(__FILE__) . '/lib/vendor');

$authApi = ModUtil::getObject('OpenID_Api_Auth'); // this will additionally register the service.
EventUtil::attachService('users.delete', new Zikula_ServiceHandler('module.openid.api.auth', 'deleteUserEvent'));