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
 * The Account API provides links for modules on the "user account page".
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
            'url'       => ModUtil::url($this->name, 'user', 'view'),
            'module'    => 'OpenID',
            'icon'      => 'account.png',
            'title'     => $this->__('OpenID manager'),
        );

        // Return the items
        return $items;
    }
}
