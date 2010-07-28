<?php
/**
 * Copyright Zikula Foundation 2010 - Zikula Application Framework
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

/**
 * This is the model class that define the entity structure and behaviours.
 */
class OpenID_Model_Assoc extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('openid_assoc');
        $this->hasColumn('server_url_hash', 'string', 64, array(
            'primary'       => true,
            'notnull'       => true,
        ));
        $this->hasColumn('server_url', 'blob', null, array(
            'notnull'       => true,
        ));
        $this->hasColumn('handle', 'string', 255, array(
            'primary'       => true,
            'notnull'       => true,
        ));
        $this->hasColumn('secret', 'blob', null, array(
            'notnull'       => true,
        ));
        $this->hasColumn('issued', 'integer', 4, array(
            'notnull'       => true,
        ));
        $this->hasColumn('lifetime', 'integer', 4, array(
            'notnull'       => true,
        ));
        $this->hasColumn('assoc_type', 'string', 64, array(
            'notnull'       => true,
        ));

        $this->index('byIssued', array(
            'fields'    => array(
                'issued'    => array(
                    'sorting'   => 'DESC',
                ),
                'server_url_hash',
                'handle'
            ),
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
