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
 * This is the model class that define the entity structure and behaviours.
 */
class OpenID_Model_UserMap extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('openid_usermap');
        $this->hasColumn('id', 'integer', 4, array(
            'primary'       => true,
            'autoincrement' => true,
            'notnull'       => true,
        ));
        $this->hasColumn('uid', 'integer', 4, array(
            'notnull'       => true,
        ));
        $this->hasColumn('authentication_method', 'string', 16, array(
            'notnull'       => true,
        ));
        $this->hasColumn('claimed_id', 'string', 255, array(
            'notnull'       => true,
        ));
        $this->hasColumn('display_id', 'string', 255, array(
            'default'       => '',
        ));

        $this->index('claimed_id', array(
            'fields'        => array('claimed_id'),
            'type'          => 'unique',
        ));
        $this->index('uid_claimed_id', array(
            'fields'        => array('uid', 'authentication_method', 'claimed_id'),
            'type'          => 'unique',
        ));
        $this->index('type_claimed_id', array(
            'fields'        => array('authentication_method', 'claimed_id'),
        ));
    }

}
