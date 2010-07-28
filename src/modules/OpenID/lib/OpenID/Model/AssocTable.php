<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Doctrine_Table class used to implement own special entity methods.
 *
 * This class is optional, do not include it of you don't need it.
 */
class OpenID_Model_AssocTable extends Doctrine_Table
{
    public function construct()
    {
        $this->addNamedQuery('clean.assoc', Doctrine_Query::create()
            ->delete('OpenID_Model_Assoc oid_a')
            ->where('(oid_a.issued + oid_a.lifetime) < ?')
        );

        $this->addNamedQuery('get.assoc', Doctrine_Query::create()
            ->select('oid_a.*')
            ->from('OpenID_Model_Assoc oid_a')
            ->where('oid_a.server_url_hash = ?')
            ->andWhere('oid_a.handle = ?')
        );

        $this->addNamedQuery('get.most.recent.assoc', Doctrine_Query::create()
            ->select('oid_a.*')
            ->from('OpenID_Model_Assoc oid_a')
            ->where('oid_a.server_url_hash = ?')
            ->orderBy('oid_a.issued DESC')
            ->limit(1)
        );

        $this->addNamedQuery('remove.assoc', Doctrine_Query::create()
            ->delete('OpenID_Model_Assoc oid_a')
            ->where('oid_a.server_url_hash = ?')
            ->andWhere('oid_a.handle = ?')
        );
    }

    public function getAllForUrl($server_url, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $server_url_hash = $this->hashValue($server_url);
        $assocList = $this->findBy('server_url_hash', $server_url_hash, $hydrationMode);

        return $assocList;
    }

    public function getMostRecentAssoc($server_url, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $server_url_hash = $this->hashValue($server_url);
        $assocList = $this->find('get.most.recent.assoc', array($server_url_hash), $hydrationMode);

        return empty($assocList) ? null : $assocList[0];
    }

    public function getAssoc($server_url, $handle, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $server_url_hash = $this->hashValue($server_url);
        $assocList = $this->find('get.assoc', array($server_url_hash, $handle), $hydrationMode);

        return empty($assocList) ? null : $assocList[0];
    }

    public function cleanExpired()
    {
        return $this->execute('clean.assoc', array(time()));
    }

    public function removeAssoc($server_url, $handle)
    {
        $server_url_hash = $this->hashValue($server_url);

        $assoc = $this->getAssoc($server_url, $handle);

        if ($assoc) {
            $this->execute('remove.assoc', array($server_url_hash, $handle));
            return true;
        } else {
            return false;
        }
    }

    public function truncateTable()
    {
        $q = Doctrine_Query::create()
            ->delete('OpenID_Model_Assoc');
        $q->execute();
    }

}