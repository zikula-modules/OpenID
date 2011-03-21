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

class OpenID_Controller_User extends Zikula_AbstractController
{
    /**
     * Redirects the user to the main OpenID module function; in this case, the view function.
     */
    public function main()
    {
        $this->redirect(ModUtil::url($this->name, 'user', 'view'));
    }

    /**
     * Displays the primary user-level OpenID management page, listing OpenIDs associated with the account and actions available to the user.
     *
     * @return string|bool The rendered output; false on error.
     */
    public function view()
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        $openIds = ModUtil::apiFunc($this->name, 'user', 'getAll');

        $this->view->add_core_data();
        $this->view->assign('openids', $openIds ? $openIds : array());
        return $this->view->fetch('openid_user_view.tpl');
    }

    /**
     * Render a form suitable for adding a new OpenID to the user's account.
     *
     * @return string|bool The rendered page; or false if error.
     */
    public function newOpenID()
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        $authenticationInfo = SessionUtil::getVar('authentication_info', array(), '/OpenID_Controller_User_newOpenID', false);
        SessionUtil::delVar('OpenID_Controller_User_newOpenID', false, '/');

        if (!isset($authenticationInfo['authentication_method']) || empty($authenticationInfo['authentication_method'])) {
            $authenticationInfo['authentication_method'] = FormUtil::getPassedValue('authentication_method', 'OpenID', 'GET');
        }

        $supportsSSL = function_exists('openssl_open');
        // TODO - Huh? Wha...? Change from Google to OpenID?
        if (!$supportsSSL && (($authenticationInfo['authentication_method'] == 'Google'))) {
            $authenticationInfo['authentication_method'] = 'OpenID';
        }

        $this->view->add_core_data()
            ->assign('authentication_info', $authenticationInfo)
            ->assign('supports_ssl', $supportsSSL);
        return $this->view->fetch('openid_user_newopenid.tpl');
    }

    public function addOpenID()
    {
        if (!SecurityUtil::confirmAuthKey($this->getName())) {
            return LogUtil::registerAuthidError(ModUtil::url($this->getName(), 'user', 'view'));
        }

        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError(ModUtil::url($this->getName(), 'user', 'view'));
        }

        if (System::serverGetVar('REQUEST_METHOD', false) == 'POST') {
            $authenticationInfo = FormUtil::getPassedValue('authentication_info', array());
        } else {
            // This function calls an authmodule's checkPassword function, which requires that 
            // this controller function be reentrant.
            $authenticationInfo = SessionUtil::getVar('authenticationInfo', null, '/OpenID/addOpenID', false, false);
            SessionUtil::delVar('/OpenID/addOpenID');
        }

        if (!isset($authenticationInfo) || empty($authenticationInfo) || !is_array($authenticationInfo)) {
            return LogUtil::registerError($this->__('Your must supply the requested information.'), null,
                ModUtil::url($this->getName(), 'user', 'newOpenID'));
        }

        // About to call an authmodule checkPassword function, so we must set up the ability to be reentrant.
        SessionUtil::requireSession();
        SessionUtil::setVar('authenticationInfo', $authenticationInfo, '/OpenID/addOpenID', true, true);

        $passwordValidates = ModUtil::apiFunc($this->getName(), 'Authentication', 'checkPassword', array(
            'authentication_info'   => $authenticationInfo,
            'authentication_method' => $authenticationMethod,
            'set_claimed_id'        => '/OpenID_Controller_User_addOpenID',
            'reentrant_url'         => System::getCurrentUrl(array('authid' => SecurityUtil::generateAuthKey($this->getName()))),
        ));
        SessionUtil::delVar('authenticationInfo', false, '/OpenID_Controller_addOpenID');

        if ($passwordValidates) {
            $authenticationInfo['claimed_id'] = SessionUtil::getVar('claimed_id', false, '/OpenID_Controller_User_addOpenID', false);
            SessionUtil::delVar('OpenID_Controller_User_addOpenID');

            if (!empty($authenticationInfo['claimed_id'])) {
                $saved = ModUtil::apiFunc($this->getName(), 'user', 'addOpenID', array(
                    'authenticationInfo' => $authenticationInfo,
                ));

                if (!$saved && !LogUtil::hasErrors()) {
                    LogUtil::registerError($this->__('There was a problem saving your new OpenID.'));
                }
                return System::redirect(ModUtil::url($this->getName(), 'user', 'view'));
            } else {
                return LogUtil::registerError($this->__('There was an internal error processing your request to save a new OpenID.'), null,
                    ModUtil::url($this->getName(), 'user', 'view'));
            }
        } else {
            SessionUtil::setVar('authenticationInfo', $authenticationInfo, '/OpenID/newOpenID');
            return LogUtil::registerError($this->__('Your OpenID was not authorized by the OpenID provider, or the provider could not be contacted.'), null,
                ModUtil::url($this->getName(), 'user', 'newOpenID'));
        }
    }

    public function legalNotice()
    {
        $returnURL = FormUtil::getPassedValue('legalreturn', '', 'GET');
        return $this->view->assign('returnURL', $returnURL)
            ->fetch('openid_user_legalnotice.tpl');
    }

}