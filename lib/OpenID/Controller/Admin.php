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
 * The admin controller.
 */
class OpenID_Controller_Admin extends Zikula_AbstractController
{
    /**
     * Disable caching in admin area.
     */
    public function postInitialize()
    {
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }

    /**
     * Redirect to modifyConfig action.
     */
    public function main()
    {
        $this->redirect(ModUtil::url($this->name, 'admin', 'modifyconfig'));
    }

    public function view()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('OpenID::', '::', ACCESS_ADMIN));

        $users = $this->entityManager->getRepository('OpenID_Entity_UserMap')->findAll();

        $openIdProvider = ModUtil::apiFunc($this->name, 'user', 'getAllOpenIdProvider');

        return $this->view->assign('users', $users)
                ->assign('openIdProvider', $openIdProvider)
                ->fetch('Admin/view.tpl');
    }

    public function modifyConfig()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('OpenID::', '::', ACCESS_ADMIN));

        $view = FormUtil::newForm($this->name, $this);

        // build form handler class name
        $handlerClass = $this->name . '_Form_Handler_Config';

        // execute form using supplied template and page event handler
        return $view->execute('Admin/modifyconfig.tpl', new $handlerClass());
    }

    public function setPassword()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('.*', '.*', ACCESS_ADMIN));

        $uid = $this->request->query->filter('uid', false, FILTER_VALIDATE_INT);
        $skipCheck = $this->request->query->filter('skipCheck', false, FILTER_VALIDATE_BOOLEAN);

        $url = ModUtil::url($this->name, 'admin', 'view');

        if ($uid !== false && ($skipCheck || $this->request->isPost())) {
            // We get here if the user has selected one single user to set a password for
            // and he has javascript activated ($skipCheck is true)
            // or he has javascript disabled and comes from the "set password" confirmation page.

            if (!$skipCheck) {
                // The user comes from the "set password" confirmation page.
                $this->checkCsrfToken();
            }

            $passwordChanged = $this->setRandomPassword($uid);

            $username = UserUtil::getVar('uname', $uid);

            if ($passwordChanged) {
                return LogUtil::registerStatus($this->__f('The password for %s was set to a random password.', $username), $url);
            } else {
                return LogUtil::registerError($this->__f('The password for %s could not be set.', $username), null, $url);
            }
        } else {
            $users = $this->request->request->get('users', array());

            if ($this->request->isGet() || (is_array($users) && !$skipCheck)) {

                if ($this->request->isPost()) {
                    $this->checkCsrfToken();
                }

                if ($uid !== false) {
                    // The password of one user only shall be changed.
                    $this->view->assign('uid', $uid)
                            ->assign('uname', UserUtil::getVar('uname', $uid));
                } else {
                    if (empty($users)) {
                        // No user selected.
                        return LogUtil::registerError($this->__('You have to select at least one user.'), null, $url);
                    }
                    $this->request->getSession()->set('OpenID_users', $users);
                }
                return $this->view->fetch('Admin/setpassword.tpl');

            } elseif ($this->request->isPost()) {
                // We get here if the user comes from the "set password" confirmation page.

                $this->checkCsrfToken();

                $sessionUsers = $this->request->getSession()->get('OpenID_users', array());
                if (!empty($sessionUsers)) {
                    $users = $sessionUsers;
                }

                if (empty($users)) {
                    // No user selected.
                    return LogUtil::registerError($this->__('You have to select at least one user.'), null, $url);
                }

                foreach ($users as $uid => $setPassword) {
                    if ($setPassword) {
                        $passwordChanged = $this->setRandomPassword($uid);

                        $username = UserUtil::getVar('uname', $uid);
                        if ($passwordChanged) {
                            return LogUtil::registerStatus($this->__f('The password for %s was set to a random password.', $username), $url);
                        } else {
                            return LogUtil::registerError($this->__f('The password for %s could not be set.', $username), null, $url);
                        }
                    }
                }
            } else {
                throw new Zikula_Exception_Forbidden();
            }
        }
    }

    /**
     * Sets a random password for a user.
     * @param int $uid The user id.
     *
     * @return bool True on success.
     * @throws Zikula_Exception_Forbidden If the $uid is invalid or the user has a password set.
     */
    private function setRandomPassword($uid = 0)
    {
        if (!is_numeric($uid) || $uid < 2) {
            throw new Zikula_Exception_Forbidden('$uid is not valid');
        }

        $currentPassword = UserUtil::getVar('pass', $uid);

        if ($currentPassword != Users_Constant::PWD_NO_USERS_AUTHENTICATION) {
            // The user has a password set already!
            throw new Zikula_Exception_Forbidden("The user already has a password set!");
        }
        $password = RandomUtil::getStringForPassword(10, 10);
        $success = UserUtil::setPassword($password, $uid);

        $mail = Zikula_View::getInstance($this->name, Zikula_View::CACHE_DISABLED);

        $subject = $this->__f('Important information for your account at %s', System::getVar('sitename'));
        $sitename = System::getVar('sitename');
        $mail->assign('password', $password)
                ->assign('uname', UserUtil::getVar('uname', $uid))
                ->assign('sitename', $sitename)
                ->assign('subject', $subject);

        ModUtil::apiFunc('Mailer', 'user', 'sendmessage',  array(
                'fromaddress' => System::getVar('adminmail'),
                'from'        => System::getVar('adminmail'),
                'fromname'    => $sitename,
                'toname'      => UserUtil::getVar('email', $uid),
                'toaddress'   => UserUtil::getVar('email', $uid),
                'subject'     => $subject,
                'body'        => $mail->fetch('User/passwordchangemail.tpl'),
                'html'        => true
        ));

        return $success;
    }
}