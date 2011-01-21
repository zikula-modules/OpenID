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
class OpenID_Controller_Auth extends Zikula_Controller
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

    public function loginBlockFields($args)
    {
        if (isset($args['openidtype']) && !empty($args['openidtype'])) {
            $openidType = $args['openidtype'];
        } else {
            $openidType = FormUtil::getPassedValue('openidtype', 'openid', 'GETPOST');
        }
        $templateName = "openid_auth_loginblockfields_{$openidType}.tpl";
        if ($this->view->template_exists($templateName)) {
            return $this->view->fetch($templateName);
        } else {
            return false;
        }
    }

    public function loginBlockIcon()
    {
        $openidType = FormUtil::getPassedValue('openidtype', 'openid', 'GETPOST');
        $supportsSSL = function_exists('openssl_open');
        return $this->view->assign('openid_type', $openidType)
            ->assign('supports_ssl', $supportsSSL)
            ->fetch('openid_auth_loginblockicon.tpl');
    }

}
