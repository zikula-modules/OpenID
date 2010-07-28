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

    public function loginBlockFields()
    {
        $openidType = FormUtil::getPassedValue('openidtype', 'openid', 'GET');
        return $this->view->assign('openid_type', $openidType)
            ->fetch('openid_auth_loginblockfields.tpl');
    }

    public function loginBlockIcon()
    {
        $openidType = FormUtil::getPassedValue('openidtype', 'openid', 'GET');
        return $this->view->assign('openid_type', $openidType)
            ->fetch('openid_auth_loginblockicon.tpl');
    }

}
