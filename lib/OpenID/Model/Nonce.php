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
class OpenID_Model_Nonce extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('openid_nonce');
        $this->hasColumn('server_url_hash', 'string', 64, array(
            'primary'       => true,
            'notnull'       => true,
        ));
        $this->hasColumn('server_url', 'string', 2047, array(
            'notnull'       => true,
        ));
        $this->hasColumn('timestamp', 'integer', 4, array(
            'primary'       => true,
            'notnull'       => true,
        ));
        $this->hasColumn('salt', 'string', 40, array(
            'primary'       => true,
            'notnull'       => true,
        ));
    }

    public function preSave($event)
    {
        parent::preSave($event);
        $this->hashFieldInto('server_url', 'server_url_hash');
    }

    public function  preSerialize($event)
    {
        parent::preSerialize($event);
        $this->hashFieldInto('server_url', 'server_url_hash');
    }

    public function setUp()
    {
        $this->actAs('OpenID_Model_Behavior_HashedField');
    }
}
