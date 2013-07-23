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
    public function postInitialize()
    {
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }

    public function main()
    {
        $this->redirect(ModUtil::url($this->name, 'admin', 'modifyconfig'));
    }

    public function view()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN));

        // TODO Join uname when refactored to Doctrine 2.
        try {
            $userMapList = Doctrine_Core::getTable('OpenID_Model_UserMap')
                ->getAll(Doctrine_Core::HYDRATE_ARRAY);
        } catch (Exception $e) {
            throw new Zikula_Exception_Fatal($e->getMessage());
        }

        $openIdProvider = ModUtil::apiFunc($this->name, 'user', 'getAllOpenIdProvider');

        return $this->view->assign('users', $userMapList)
                ->assign('openIdProvider', $openIdProvider)
                ->fetch('Admin/view.tpl');
    }

    public function modifyConfig()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN));

        $view = FormUtil::newForm($this->name, $this);

        // build form handler class name
        $handlerClass = $this->name . '_Form_Handler_Config';

        // execute form using supplied template and page event handler
        return $view->execute('Admin/modifyconfig.tpl', new $handlerClass());
    }

    public function delete()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN));

        if ($this->request->isGet()) {

            /*
             * TODO Params!
             */

            return $this->view->fetch('Admin/delete.tpl');

        } elseif ($this->request->isPost()) {

            /*
             * TODO Delete!
             */

            $this->redirect(ModUtil::url($this->name, 'admin', 'view'));
        } else {
            throw new Zikula_Exception_Forbidden();
        }
    }

    public function setPassword()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN));

        if ($this->request->isGet()) {

            /*
             * TODO Params!
             */

            return $this->view->fetch('Admin/setpassword.tpl');

        } elseif ($this->request->isPost()) {

            /*
             * TODO Set password!
             */

            $this->redirect(ModUtil::url($this->name, 'admin', 'view'));
        } else {
            throw new Zikula_Exception_Forbidden();
        }
    }

    private function getRandomPassword()
    {
        /*
         * TODO StringUtil!
         */
        return "test";
    }
}