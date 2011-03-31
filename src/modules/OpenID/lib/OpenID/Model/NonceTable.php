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
 * Doctrine_Table class used to implement own special entity methods.
 *
 * This class is optional, do not include it of you don't need it.
 */
class OpenID_Model_NonceTable extends Doctrine_Table
{
    public function construct()
    {
        $this->addNamedQuery('clean.nonces', Doctrine_Query::create()
            ->delete('OpenID_Model_Nonce oid_n')
            ->where('oid_n.timestamp < ?'));
    }

    public function cleanExpired($skewTime)
    {
        $v = time() - $skewTime;
        return $this->execute('clean.nonces', array($v));
    }

    public function truncateTable()
    {
        $q = Doctrine_Query::create()
            ->delete('OpenID_Model_Nonce');
        $q->execute();
    }
}