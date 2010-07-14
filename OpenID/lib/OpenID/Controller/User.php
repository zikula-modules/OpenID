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

class OpenID_Controller_User extends Zikula_Controller
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

        $suppliedID = SessionUtil::getVar('supplied_id', '', 'OpenID_newOpenID', false);
        SessionUtil::delVar('OpenID_newOpenID');

        $this->view->add_core_data()
                    ->assign('supplied_id', $suppliedID);
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

        $suppliedID = FormUtil::getPassedValue('openid_identifier', null);

        if (empty($suppliedID)) {
            SessionUtil::setVar('supplied_id', $suppliedID, 'OpenID_newOpenID');
            return LogUtil::registerError($this->__('Your new OpenID cannot be blank.'), null,
                ModUtil::url($this->getName(), 'user', 'newOpenID'));
        } else {
            $passwordValidates = ModUtil::apiFunc($this->getName(), 'auth', 'checkPassword', array(
                'authinfo'          => array('supplied_id' => $suppliedID),
                'set_claimed_id'    => true,
            ));

            if ($passwordValidates) {
                $claimedID = SessionUtil::getVar('claimed_id', false, '/OpenID', false);
                SessionUtil::delVar('OpenID');

                if (!empty($claimedID)) {
                    $saved = ModUtil::apiFunc($this->getName(), 'user', 'addOpenID', array(
                        'claimed_id' => $claimedID,
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
                SessionUtil::setVar('supplied_id', $suppliedID, 'OpenID_newOpenID');
                return LogUtil::registerError($this->__('Your OpenID was not authorized by the OpenID provider, or the provider could not be contacted.'), null,
                    ModUtil::url($this->getName(), 'user', 'newOpenID'));
            }
        }
    }

}