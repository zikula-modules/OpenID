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
 * Provides access to (non-administrative) user-initiated actions for the OpenID module.
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
     * 
     * @throws Zikula_Exception_Forbidden Thrown if the user is not logged in or does not have comment access for his own record.
     */
    public function view()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name.'::self', '::', ACCESS_COMMENT));

        $openIds = ModUtil::apiFunc($this->name, 'user', 'getAll');

        if ($this->allowOpenIdDelete()) {
            $actions = array('count' => 1);
            foreach ($openIds as $key => $openId) {
                $actions[$openId['id']]['delete'] = array(
                    'url'   => ModUtil::url($this->name, 'user', 'removeOpenID', array('id' => $openId['id'])),
                );
            }
        } else {
            $actions = array('count' => 0);
        }

        return $this->view->assign('openids', $openIds ? $openIds : array())
                ->assign('actions', $actions)
                ->fetch('User/view.tpl');
    }

    /**
     * Render a form suitable for adding a new OpenID to the user's account.
     *
     * GET Parameters Used:
     * --------------------
     * string 'reentranttoken' A nonce used to indicate that the user is returning to this function from a reentrant external call to
     *                              an external authentication server of some sort (for this module, an OpenID Provider).
     *
     * POST Parameters Used:
     * ---------------------
     * string 'authentication_method' The name of one of the authentication methods supported by this module.
     * string 'openid_identifier'     If the authentication method is set to 'openid' then this contains the identifier supplied by the user.
     * string 'pip_username'          If the authentication method is set to 'pip' then this is either the PIP user name for the user, or
     *                                      the full OpenID URL pointing to the user's PIP account.
     *
     * SESSION Variables Used, 'OpenID_Controller_User_newOpenID' Namespace:
     * ---------------------------------------------------------------------
     * array  'authenticationMethod' An array containing the module name ('modname') and method name ('method') that identifies the authentication
     *                                  method being used. 'modname' will be set to 'OpenID', and 'method' will be set to the value received in
     *                                  the 'authentication_method' POST parameter.
     * array  'authenticationInfo'   An array containing the authentication information entered by the user, including the supplied id.
     * string  'claimed_id'          A normalized and authenticated version of the supplied id which represents the OpenID URL claimed by the user
     *                                  as identifying him. This is set and returned by the authentication process.
     * string 'reentrantToken'       The nonce used to signal that the user has exited to and is reentering this function from areentrant external
     *                                  call to an OpenID server.
     *
     * @return string The rendered template.
     *
     * @throws Zikula_Exception_Forbidden Thrown if the user is not logged in; does not have comment access for his
     *                                          own record; or the function is access improperly.
     */
    public function newOpenID()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT));
        
        $proceedToForm = true;
        $changeAuthenticatonMethod = false;

        if ($this->request->isPost() || ($this->request->isGet() && $this->request->query->has('reentranttoken'))) {
            if ($this->request->isPost()) {
                $this->checkCsrfToken();
                
                $selectedAuthenticationMethod = $this->request->request->get('authentication_method', array());
                $authenticationInfo = array();
                
                if ($this->request->request->has('changeto')) {
                    $changeAuthenticatonMethod = true;
                } else {
                    switch (strtolower($selectedAuthenticationMethod['method'])) {
                        case 'google':
                            $authenticationInfo['supplied_id'] = 'google';
                            break;
                        case 'yahoo':
                            $authenticationInfo['supplied_id'] = 'yahoo';
                            break;
                        case 'openid':
                            $authenticationInfo['supplied_id'] = $this->request->request->get('openid_identifier', '');
                            break;
                        default:
                            $authenticationInfo['supplied_id'] = $this->request->request->get('username', '');
                            break;
                    }
                }
            } else {
                $reentrantTokenReceived = $this->request->query->get('reentranttoken');
                $selectedAuthenticationMethod = $this->request->getSession()->get('authenticationMethod', array(), 'OpenID_Controller_User_newOpenID');
                $authenticationInfo = $this->request->getSession()->get('authenticationInfo', array(), 'OpenID_Controller_User_newOpenID');
                $reentrantToken = $this->request->getSession()->get('reentrantToken', false, 'OpenID_Controller_User_newOpenID');
                $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');
            }
            
            if (!$changeAuthenticatonMethod) {
                if (isset($authenticationInfo) && !empty($authenticationInfo) && is_array($authenticationInfo)) {
                    // About to call an authmodule checkPassword function, so we must set up the ability to be reentrant.
                    $this->request->getSession()->set('authenticationMethod', $selectedAuthenticationMethod, 'OpenID_Controller_User_newOpenID');
                    $this->request->getSession()->set('authenticationInfo', $authenticationInfo, 'OpenID_Controller_User_newOpenID');
                    if (!isset($reentrantToken)) {
                        $reentrantToken = substr(SecurityUtil::generateCsrfToken(), 0, 10);
                    }
                    $this->request->getSession()->set('reentrantToken', $reentrantToken, 'OpenID_Controller_User_newOpenID');

                    $passwordValidates = ModUtil::apiFunc($this->getName(), 'Authentication', 'checkPassword', array(
                        'authentication_info'   => $authenticationInfo,
                        'authentication_method' => $selectedAuthenticationMethod,
                        'set_claimed_id'        => 'OpenID_Controller_User_newOpenID',
                        'reentrant_url'         => ModUtil::url($this->name, 'user', 'newOpenID', array('reentranttoken' => $reentrantToken), null, null, true, true, false),
                    ));

                    $authenticationInfo['claimed_id'] = $this->request->getSession()->get('claimed_id', false, 'OpenID_Controller_User_newOpenID');
                    $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');

                    if ($passwordValidates) {
                        if (!empty($authenticationInfo['claimed_id'])) {
                            $saved = ModUtil::apiFunc($this->getName(), 'authentication', 'register', array(
                                'uid'                   => UserUtil::getVar('uid'),
                                'authentication_info'   => $authenticationInfo,
                                'authentication_method' => $selectedAuthenticationMethod,
                            ));

                            if (!$saved && !LogUtil::hasErrors()) {
                                $this->registerError($this->__('There was a problem saving your new OpenID.'));
                            } else {
                                $proceedToForm = false;
                            }
                        } else {
                            $this->registerError($this->__('There was an internal error processing your request to save a new OpenID.'));
                            $proceedToForm = false;
                        }
                    } else {
                        $this->registerError($this->__('Your OpenID was not authorized by the OpenID provider, or the provider could not be contacted.'));
                    }
                } else {
                    $this->registerError($this->__('Your must supply the requested information.'));
                }
            }
        } elseif ($this->request->isGet()) {
            $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');
            
            $selectedAuthenticationMethod['modname'] = $this->name;
            $selectedAuthenticationMethod['method'] = $this->request->query->get('authentication_method', 'OpenID');
            $authenticationInfo = array();
        } else {
            throw new Zikula_Exception_Forbidden();
        }

        if ($proceedToForm) {
            $supportsSSL = function_exists('openssl_open');
            // Change to OpenID if Google selected and SSL not supported
            // TODO - Do we really want to do this?
            if (!$supportsSSL && (($selectedAuthenticationMethod['method'] == 'Google'))) {
                $selectedAuthenticationMethod['method'] = 'OpenID';
            }
            
            $getAuthenticationMethodsArgs = array(
                'filter' => Zikula_Api_AbstractAuthentication::FILTER_ENABLED,
            );
            $authenticationMethodList = ModUtil::apiFunc($this->name, 'Authentication', 'getAuthenticationMethods', $getAuthenticationMethodsArgs, 'Zikula_Api_AbstractAuthentication');

            // TODO - The order and availability should be set by configuration
            $authenticationMethodDisplayOrder = array();
            foreach ($authenticationMethodList as $authenticationMethod) {
                $authenticationMethodDisplayOrder[] = array(
                    'modname'   => $authenticationMethod->modname,
                    'method'    => $authenticationMethod->method,
                );
            }

            $viewArgs = array(
                'authentication_info'                   => $authenticationInfo,
                'selected_authentication_method'        => $selectedAuthenticationMethod,
                'supports_ssl'                          => $supportsSSL,
                'authentication_method_display_order'   => $authenticationMethodDisplayOrder,
            );

            return $this->view->assign($viewArgs)
                ->fetch('User/newopenid.tpl');
        } else {
            $this->redirect(ModUtil::url($this->name, 'user', 'view'));
        }
    }
    
    public function removeOpenID()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT));

        if (!$this->allowOpenIdDelete()) {
            return LogUtil::registerError($this->__("You cannot delete this open id, because you won't be able to login anymore."), null, ModUtil::url($this->name, 'user', 'view'));
        }

        $proceedToForm = true;
        
        if ($this->request->isPost()) {
            $this->checkCsrfToken();
            
            $id = $this->request->request->get('id', null);
            if (!isset($id) || ((string)((int)$id) != $id)) {
                throw new Zikula_Exception_Fatal($this->__f('An invalid id was recevied: \'%1$s\'.', array($id)));
            }
            
            $confirmed = $this->request->request->get('confirmed', null);
            if (!isset($confirmed)) {
                throw new Zikula_Exception_Fatal($this->__('An invalid confirmation flag was recevied.'));
            }
            
            $proceedToForm = false;
            
            if ($confirmed) {
                try {
                    $userMap = Doctrine_Core::getTable('OpenID_Model_UserMap')
                        ->getById($id);
                    
                    $uid = UserUtil::getVar('uid');
                    
                    if ($userMap['uid'] != $uid) {
                        throw new Zikula_Exception_Forbidden();
                    }
                    
                    if ($userMap) {
                        Doctrine_Core::getTable('OpenID_Model_UserMap')
                            ->removeById($id);
                    } else {
                        throw new Zikula_Exception_Fatal($this->__f('An OpenID with the id \'%1$s\' was not found.', array($id)));
                    }
                } catch (Doctrine_Exception $e) {
                    $message = $this->__('An error occurred while retrieving the selected OpenID.');
                    if (System::isDevelopmentMode()) {
                        $message .= ' ' . $this->__f('Doctrine exception message: %1$s', array($e->errorMessage()));
                    }
                    throw new Zikula_Exception_Fatal($message);
                }
            }
        } elseif ($this->request->isGet()) {
            $id = $this->request->query->get('id', null);
            if (!isset($id) || ((string)((int)$id) != $id)) {
                throw new Zikula_Exception_Fatal($this->__f('An invalid id was recevied: \'%1$s\'.', array($id)));
            }
            
            try {
                $userMap = Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->getById($id);
                
                if (!$userMap) {
                    throw new Zikula_Exception_Fatal($this->__f('An OpenID with the id \'%1$s\' was not found.', array($id)));
                }
                    
                $uid = UserUtil::getVar('uid');

                if ($userMap['uid'] != $uid) {
                    throw new Zikula_Exception_Forbidden();
                }
            } catch (Doctrine_Exception $e) {
                $message = $this->__('An error occurred while retrieving the selected OpenID.');
                if (System::isDevelopmentMode()) {
                    $message .= ' ' . $this->__f('Doctrine exception message: %1$s', array($e->errorMessage()));
                }
                throw new Zikula_Exception_Fatal($message);
            }
        } else {
            throw new Zikula_Exception_Forbidden();
        }
        
        if ($proceedToForm) {
            return $this->view->assign('openid', $userMap)
                    ->fetch('User/removeopenid.tpl');
        } else {
            $this->redirect(ModUtil::url($this->name, 'user', 'view'));
        }
    }

    /**
     * Renders and returns the legal notice for the OpenID pages, specifying copyrights, trademarks, etc.
     * 
     * GET Parameters used:
     * --------------------
     * string 'legalreturn' The URL to which the user should be returned when clicking on the appropriate link.
     *
     * @return string The rendered template.
     */
    public function legalNotice()
    {
        $returnUrl = $this->request->query->get('legalreturn', '');
        
        return $this->view->assign('returnUrl', $returnUrl)
                ->fetch('User/legalnotice.tpl');
    }

    private function allowOpenIdDelete()
    {
        $openIds = ModUtil::apiFunc($this->name, 'user', 'getAll');

        if (count($openIds) <= 1) {
            // The user only has one openid. Check if he has a password.
            $password = UserUtil::getVar('pass');
            if ($password == Users_Constant::PWD_NO_USERS_AUTHENTICATION) {
                // The user does not have a password set. If he now deletes his last openid, he won't
                // be able to login anymore.
                return false;
            }
        }

        return true;
    }

}