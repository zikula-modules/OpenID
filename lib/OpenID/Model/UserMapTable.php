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
class OpenID_Model_UserMapTable extends Doctrine_Table
{
    public function construct()
    {
        $this->addNamedQuery('get.by.id', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.id = ?'));

        $this->addNamedQuery('get.by.claimed_id', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.claimed_id = ?'));

        $this->addNamedQuery('get.map.by.id', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.uid = ?')
            ->andWhere('um.id = ?'));

        $this->addNamedQuery('get.map.by.claimed_id', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.uid = ?')
            ->andWhere('um.claimed_id = ?'));

        $this->addNamedQuery('get.all', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->orderBy('um.uid, um.claimed_id'));

        $this->addNamedQuery('get.all.for.uid', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.uid = ?')
            ->orderBy('um.claimed_id'));

        $this->addNamedQuery('get.all.type.for.uid', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.uid = ?')
            ->andWhere('um.openid_type = ?')
            ->orderBy('um.claimed_id'));

        $this->addNamedQuery('get.all.for.claimed_id', Doctrine_Query::create()
            ->select('um.*')
            ->from('OpenID_Model_UserMap um')
            ->where('um.claimed_id = ?')
            ->orderBy('um.claimed_id'));

        $this->addNamedQuery('remove.by.id', Doctrine_Query::create()
            ->delete()
            ->from('OpenID_Model_UserMap um')
            ->where('um.id = ?'));

        $this->addNamedQuery('remove.by.uid', Doctrine_Query::create()
            ->delete()
            ->from('OpenID_Model_UserMap um')
            ->where('um.uid = ?'));
    }

    public function getById($id, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $userMapList = $this->find('get.by.id', array($id), $hydrationMode);
        return empty($userMapList) ? null : $userMapList[0];
    }

    public function getByClaimedId($claimed_id, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $userMapList = $this->find('get.by.claimed_id', array($claimed_id), $hydrationMode);
        return empty($userMapList) ? null : $userMapList[0];
    }

    public function getMapById($uid, $id, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $userMapList = $this->find('get.map.by.id', array($uid, $id), $hydrationMode);
        return empty($userMapList) ? null : $userMapList[0];
    }

    public function getMapByClaimedId($uid, $claimed_id, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $userMapList = $this->find('get.map.by.claimed_id', array($uid, $claimed_id), $hydrationMode);
        return empty($userMapList) ? null : $userMapList[0];
    }

    public function getAll($hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        $userMapList = $this->find('get.all', array(), $hydrationMode);
        return empty($userMapList) ? null : $userMapList;
    }

    public function getAllForUid($uid, $openid_type = null, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
    {
        if (!isset($openidType)) {
            $userMapList = $this->find('get.all.for.uid', array($uid), $hydrationMode);
        } else {
            $userMapList = $this->find('get.all.type.for.uid', array($uid, $openid_type), $hydrationMode);
        }

        return empty($userMapList) ? null : $userMapList;
    }

    public function countAll($uid = null, $openid_type = null)
    {
        if (isset($uid) && isset($openid_type)) {
            $theCount = $this->createNamedQuery('get.all.type.for.uid')
                ->count(array($uid, $openid_type));
        } elseif (isset($uid)) {
            $theCount = $this->createNamedQuery('get.all.for.uid')
                ->count(array($uid));
        } else {
            $theCount = $this->createNamedQuery('get.all')
                ->count();
        }

        return $theCount;
    }

    public function countClaimedId($clamied_id, $uid = null)
    {
        if (isset($uid)) {
            $theCount = $this->createNamedQuery('get.map.by.claimed_id')
                ->count(array($uid, $clamied_id));
        } else {
            $theCount = $this->createNamedQuery('get.all.for.claimed_id')
                ->count(array($clamied_id));
        }

        return $theCount;
    }

    public function removeById($id)
    {
        $this->execute('remove.by.id', array($id), Doctrine::HYDRATE_NONE);
    }

    public function removeByUserId($uid)
    {
        $this->execute('remove.by.uid', array($uid), Doctrine::HYDRATE_NONE);
    }
}