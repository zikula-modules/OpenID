<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 * @subpackage OpenID
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class OpenID_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta = array();
        $meta['name']           = 'OpenID';
        $meta['displayname']    = __('OpenID for Zikula');
        $meta['description']    = __('Provides OpenID authentication for Zikula user accounts.');
        //! module name that appears in URL
        $meta['url']            = __('openid');
        $meta['capabilities'] = array('authentication' => array('version' => '1.0'));

        $meta['version']        = '0.0.1';

        $meta['author']         = 'RMBurkhead';
        $meta['contact']        = 'http://code.zikula.org/OpenID';

        $meta['securityschema'] = array(
            'OpenID::self'  => '::',
            'OpenID::'      => 'User ID::',
        );
        
        return $meta;
    }
}