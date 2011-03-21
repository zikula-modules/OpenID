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

class OpenID_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array(
            // Module name
            'name'          => 'OpenID',

            // Current module version
            'version'       => '0.0.1',

            // How the module name appears in URL
            'url'           => __('openid'),

            // Display name (mostly for Extensions)
            'displayname'   => __('OpenID for Zikula'),

            // Description (mostly for Extensions)
            'description'   => __('Provides OpenID authentication for Zikula user accounts.'),

            // Advertised module capabilities
            'capabilities'  => array(
                'authentication'    => array(
                    'version'   => '1.0.0'
                )
            ),
            
            // Security schema help
            'securityschema' => array(
                'OpenID::self'  => '::',
                'OpenID::'      => 'User ID::',
            ),
        );
        return $meta;
    }
}