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
 * Provides access to administrative initiated actions for the OpenID module.
 */
class OpenID_Controller_Authentication extends Zikula_Controller_AbstractAuthentication
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Set caching to false by default.
        $this->view->setCaching(false);
    }

    /**
     * Renders the template that displays the input fields for the authentication module in the Users module's login block.
     *
     * Parameters sent in the $args array:
     * -----------------------------------
     * string $args['method']      The name of the authentication method for which the fields should be rendered.
     * string $args['formType']    The type of form (or block, or plugin, etc.) on which the form fields will appear; used in
     *                                  computing the template name.
     *
     * @param array $args All parameters passed to this function.
     * 
     * @return string The rendered template.
     * 
     * @throws Zikula_Exception_Fatal If the $args array or any parameter it contains is invalid; or if a template cannot be found
     *                                      for the method and the specified form type.
     */
    public function getLoginFormFields(array $args)
    {
        // Parameter extraction and error checking
        $errorMessage = false;
        $genericErrorMessage = $this->__('An internal error has occurred while selecting a method of logging in.');
        $showDetailedErrorMessage = (System::getVar('development', false) || SecurityUtil::checkPermission($this->name . '::debug', '::', ACCESS_ADMIN));

        if (!isset($args)) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: The $args array was empty on a call to %1$s.', array(__METHOD__));
            }
            throw new Zikula_Exception_Fatal($errorMessage);
        } elseif (!is_array($args)) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: The $args parameter was not an array on a call to %1$s.', array(__METHOD__));
            }
            throw new Zikula_Exception_Fatal($errorMessage);
        }

        if (isset($args['formType']) && is_string($args['formType'])) {
            $formType = $args['formType'];
        } else {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: An invalid formType (\'%1$s\') was received on a call to %2$s.', array(
                    isset($args['formType']) ? $args['formType'] : 'NULL',
                    __METHOD__));
            }
            throw new Zikula_Exception_Fatal($errorMessage);
        }

        if (isset($args['method']) && is_string($args['method']) && $this->supportsAuthenticationMethod($args['method'])) {
            $method = $args['method'];
        } else {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: An invalid method (\'%1$s\') was received on a call to %2$s.', array(
                    isset($args['formType']) ? $args['formType'] : 'NULL',
                    __METHOD__));
            }
            throw new Zikula_Exception_Fatal($errorMessage);
        }
        // End parameter extraction and error checking
        
        $templateName = mb_strtolower("openid_auth_loginformfields_{$args['formType']}_{$method}.tpl");
        if (!$this->view->template_exists($templateName)) {
            throw new Zikula_Exception_Fatal($this->__f('A form fields template was not found for the %1$s method using form type \'%2$s\'.', array($method, $args['formType'])));
        }

        return $this->view->assign('authentication_method', $method)
                          ->fetch($templateName);
    }

    /**
     * Renders the template that displays the authentication module's icon in the Users module's login block.
     * 
     * Parameters sent in the $args array:
     * -----------------------------------
     * string $args['method']      The name of the authentication method for which a selector should be rendered.
     * string $args['is_selected'] True if the selector for this method is the currently selected selector; otherwise false.
     * string $args['formType']    The type of form (or block, or plugin, etc.) on which the selector will appear; used in
     *                                  computing the template name.
     * 
     * @param array $args All parameters passed to this function.
     *
     * @return string The rendered template.
     * 
     * @throws Zikula_Exception_Fatal If the $args array or any parameter it contains is invalid; or if a template cannot be found
     *                                      for the method and the specified form type.
     */
    public function getAuthenticationMethodSelector(array $args)
    {
        // Parameter extraction and error checking
        if (!isset($args) || !is_array($args)) {
            throw new Zikula_Exception_Fatal($this->__('The an invalid \'$args\' parameter was received.'));
        }

        if (isset($args['formType']) && is_string($args['formType'])) {
            $formType = $args['formType'];
        } else {
            throw new Zikula_Exception_Fatal($this->__f('Error: An invalid formType (\'%1$s\') was received.', array(
                    isset($args['formType']) ? $args['formType'] : 'NULL')));
        }

        if (isset($args['method']) && is_string($args['method']) && $this->supportsAuthenticationMethod($args['method'])) {
            $method = $args['method'];
        } else {
            throw new Zikula_Exception_Fatal($this->__f('Error: An invalid method (\'%1$s\') was received.', array(
                    isset($args['method']) ? $args['method'] : 'NULL')));
        }
        // End parameter extraction and error checking

        $authenticationMethod = array(
            'modname'   => $this->name,
            'method'    => $method,
        );
        $isSelected = isset($args['is_selected']) && $args['is_selected'];

        $templateName = mb_strtolower("openid_auth_authenticationmethodselector_{$formType}_{$method}.tpl");
        if (!$this->view->template_exists($templateName)) {
            throw new Zikula_Exception_Fatal($this->__f('An authentication method selector template was not found for method \'%1$s\' using form type \'%2$s\'.', array($method, $args['formType'])));
        }
        
        return $this->view->assign('authentication_method', $authenticationMethod)
                          ->assign('is_selected', $isSelected)
                          ->fetch($templateName);
    }

}
