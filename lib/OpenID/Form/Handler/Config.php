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
 * The form handler for modifying the module's configuration
 */
class OpenID_Form_Handler_Config extends Zikula_Form_AbstractHandler
{
    /**
     * @param Zikula_Form_View $view
     *
     * @return bool|void
     */
    public function initialize(Zikula_Form_View $view)
    {
        $openIdProvider = ModUtil::apiFunc($this->name, 'user', 'getAllOpenIdProvider');

        $items = array();
        foreach ($openIdProvider as $provider) {
            $items[] = array('text' => $provider->getProviderDisplayName(), 'value' => $provider->getProviderName());
        }
        $view->assign('items', $items)
                ->assign('selectedLoginProvider', $this->getVar('loginProvider'))
                ->assign('selectedRegistrationProvider', $this->getVar('registrationProvider'))
                ->assign('openIdProvider', $openIdProvider);
    }

    /**
     * @param Zikula_Form_View $view
     * @param array            $args
     */
    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            $view->redirect(ModUtil::url($this->name, 'admin', 'modifyconfig'));
        } else if ($args['commandName'] == 'submit') {
            if (!$this->view->isValid()) {
                return false;
            }

            $values = $view->getValues();
            $this->setVar('loginProvider', $values['loginProvider']);
            $this->setVar('registrationProvider', $values['registrationProvider']);

            LogUtil::registerStatus($this->__('Configuration saved.'));
        }

        return $this->view->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

}