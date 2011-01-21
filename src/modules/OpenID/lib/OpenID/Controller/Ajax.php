<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
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

/**
 * Access to actions initiated through AJAX for the OpenID module.
 */
class OpenID_Controller_Ajax extends Zikula_Controller
{
    /**
     * Post setup.
     *
     * @return void
     */
    public function _postSetup()
    {
        // no need for a Zikula_View so override it.
    }
    
    public function getLoginBlockFields()
    {
        if (!SecurityUtil::confirmAuthKey()) {
            LogUtil::registerAuthidError();
            throw new Zikula_Exception_Fatal();
        }

        $openidType = FormUtil::getPassedValue('openidtype', 'OpenID', 'GETPOST');
        $loginBlockFields = ModUtil::func('OpenID', 'Auth', 'loginBlockFields', array(
            'openidtype'    => strtolower($openidType),
        ));

        $result = array(
            'content'   => $loginBlockFields,
            'openidType'=> $openidType,
        );

        return new Zikula_Response_Ajax($result);
    }
}
