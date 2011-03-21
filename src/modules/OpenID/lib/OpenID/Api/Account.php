<?php
/**
 * Zikula Application Framework
 *
 * @copyright 2001 Zikula Development Team
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula
 * @subpackage OpenID
 * @version $Id$
 * @link http://www.zikula.org
 */

/**
 * The Account API provides links for modules on the "user account page"; this
 * class provides those links for the Users module.
 *
 * @package Zikula
 * @subpackage OpenID
 */
class OpenID_Api_Account extends Zikula_AbstractApi
{
    /**
     * Return an array of items to show in the the user's account panel.
     *
     * @return array Indexed array of items.
     */
    public function getAll()
    {
        $items = array();

        $items[] = array(
            'url'       => ModUtil::url('OpenID', 'user', 'view'),
            'module'    => 'OpenID',
            'icon'      => 'large/openid-icon.png',
            'title'     => $this->__('OpenID manager'),
        );

        // Return the items
        return $items;
    }
}
