<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula
 * @subpackage OpenID
 */

/**
 * Controllers provide users access to actions that they can perform on the system;
 * this class provides access to (non-administrative) user-initiated actions for the Users module.
 *
 * @package Zikula
 * @subpackage OpenID
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
     * @return string The rendered template.
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
            throw new BadMethodCallException($errorMessage);
        } elseif (!is_array($args)) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: The $args parameter was not an array on a call to %1$s.', array(__METHOD__));
            }
            throw new InvalidArgumentException($errorMessage);
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
            throw new InvalidArgumentException($errorMessage);
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
            throw new InvalidArgumentException($errorMessage);
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
     * @return string The rendered template.
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
            throw new InvalidArgumentException($this->__f('Error: An invalid formType (\'%1$s\') was received.', array(
                    isset($args['formType']) ? $args['formType'] : 'NULL')));
        }

        if (isset($args['method']) && is_string($args['method']) && $this->supportsAuthenticationMethod($args['method'])) {
            $method = $args['method'];
        } else {
            throw new InvalidArgumentException($this->__f('Error: An invalid method (\'%1$s\') was received.', array(
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
