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


class OpenID_Helper_AuthenticationMethod extends Users_Helper_AuthenticationMethod
{
    public function tryToEnableForAuthentication()
    {
        $loginProvider = ModUtil::getVar(OpenID_Constant::MODNAME, 'loginProvider');

        if (!is_array($loginProvider)) {
            $loginProvider = array();
        }
        if (in_array($this->method, $loginProvider)) {
            $this->enabledForAuthentication;
        } else {
            $this->disableForAuthentication();
        }
    }

    public function tryToEnableForRegistration()
    {
        $registrationProvider = ModUtil::getVar(OpenID_Constant::MODNAME, 'registrationProvider');

        if (!is_array($registrationProvider)) {
            $registrationProvider = array();
        }
        if (in_array($this->method, $registrationProvider)) {
            $this->enableForRegistration();
        } else {
            $this->disableForRegistration();
        }
    }

    public function getProvider()
    {
        $classname =  'OpenID_OpenIDProvider_' . $this->getMethod();
        return new $classname();
    }
}