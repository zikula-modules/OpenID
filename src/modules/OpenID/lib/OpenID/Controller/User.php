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
     * 
     * @throws Zikula_Exception_Forbidden Thrown if the user is not logged in or does not have comment access for his own record.
     */
    public function view()
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT)) {
            throw new Zikula_Exception_Forbidden();
        }

        $openIds = ModUtil::apiFunc($this->name, 'user', 'getAll');

        return $this->view->assign('openids', $openIds ? $openIds : array())
                ->fetch('openid_user_view.tpl');
    }

    /**
     * Render a form suitable for adding a new OpenID to the user's account.
     * 
     * @return
     * 
     * @throws Zikula_Exception_Forbidden Thrown if the user is not logged in, does not have comment access for his own record, or the function is access improperly.
     */
    public function newOpenID()
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }
        
        $proceedToForm = true;

        if ($this->request->isPost() || ($this->request->isGet() && $this->request->getGet()->has('reentranttoken'))) {
            if ($this->request->isPost()) {
                $this->checkCsrfToken();
                
                $authenticationMethod = $this->request->getPost()->get('authentication_method', array());
                $authenticationInfo = array();
                switch (strtolower($authenticationMethod['method'])) {
                    case 'openid':
                        $authenticationInfo['suppliedId'] = $this->request->getPost()->get('openid_identifier', '');
                        break;
                    case 'google':
                        $authenticationInfo['suppliedId'] = 'google';
                        break;
                    case 'pip':
                        $authenticationInfo['suppliedId'] = $this->request->getPost()->get('pip_username', '');
                        break;
                    default:
                        break;
                }
            } else {
                if ($this->request->getGet()->has('reentranttoken')) {
                    $reentrantTokenReceived = $this->request->getGet()->get('reentranttoken');
                } else {
                    $reentrantTokenReceived = '';
                    $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');
                }
                
                $authenticationMethod = $this->request->getSession()->get('authenticationMethod', array(), 'OpenID_Controller_User_newOpenID');
                $authenticationInfo = $this->request->getSession()->get('authenticationInfo', array(), 'OpenID_Controller_User_newOpenID');
                $reentrantToken = $this->request->getSession()->get('reentrantToken', false, 'OpenID_Controller_User_newOpenID');
                $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');
            }
            
            if (isset($authenticationInfo) && !empty($authenticationInfo) && is_array($authenticationInfo)) {
                // About to call an authmodule checkPassword function, so we must set up the ability to be reentrant.
                $this->request->getSession()->set('authenticationMethod', $authenticationMethod, 'OpenID_Controller_User_newOpenID');
                $this->request->getSession()->set('authenticationInfo', $authenticationInfo, 'OpenID_Controller_User_newOpenID');
                if (!isset($reentrantToken)) {
                    $reentrantToken = substr(SecurityUtil::generateCsrfToken(), 0, 10);
                }
                $this->request->getSession()->set('reentrantToken', $reentrantToken, 'OpenID_Controller_User_newOpenID');

                $passwordValidates = ModUtil::apiFunc($this->getName(), 'Authentication', 'checkPassword', array(
                    'authentication_info'   => $authenticationInfo,
                    'authentication_method' => $authenticationMethod,
                    'set_claimed_id'        => 'OpenID_Controller_User_newOpenID',
                    'reentrant_url'         => ModUtil::url($this->name, 'user', 'newOpenID', array('reentranttoken' => $reentrantToken), null, null, true, true, false),
                ));
                
                $authenticationInfo['claimed_id'] = $this->request->getSession()->get('claimed_id', false, 'OpenID_Controller_User_newOpenID');
                $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');

                if ($passwordValidates) {
                    if (!empty($authenticationInfo['claimed_id'])) {
                        $saved = ModUtil::apiFunc($this->getName(), 'user', 'addOpenID', array(
                            'authentication_info'   => $authenticationInfo,
                            'authentication_method' => $authenticationMethod,
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
            
        } elseif ($this->request->isGet()) {
            $this->request->getSession()->clearNamespace('OpenID_Controller_User_newOpenID');
            
            $authenticationMethod['modname'] = $this->name;
            $authenticationMethod['method'] = $this->request->getGet()->get('authentication_method', 'OpenID');
            $authenticationInfo = array();
        } else {
            throw Zikula_Exception_Forbidden();
        }

        if ($proceedToForm) {
            $supportsSsl = function_exists('openssl_open');
            // Change to OpenID if Google selected and SSL not supported
            // TODO - Do we really want to do this?
            if (!$supportsSsl && (($authenticationMethod['method'] == 'Google'))) {
                $authenticationMethod['method'] = 'OpenID';
            }

            return $this->view->assign('authenticationInfo', $authenticationInfo)
                ->assign('authenticationMethod', $authenticationMethod)
                ->assign('supportsSsl', $supportsSsl)
                ->fetch('openid_user_newopenid.tpl');
        } else {
            $this->redirect(ModUtil::url($this->name, 'user', 'view'));
        }
    }

    public function legalNotice()
    {
        $returnUrl = $this->request->getGet()->get('legalreturn', '');
        
        return $this->view->assign('returnUrl', $returnUrl)
                ->fetch('openid_user_legalnotice.tpl');
    }

}