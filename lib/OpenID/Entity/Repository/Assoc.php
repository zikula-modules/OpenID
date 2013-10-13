<?php
/**
 * Copyright Zikula Foundation 2013 - Zikula Application Framework
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

use Doctrine\ORM\EntityRepository;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 */
class OpenID_Entity_Repository_Assoc extends EntityRepository
{
    /**
     * Get all associations for an url.
     *
     * @param string $serverUrl The server url.
     *
     * @return array|OpenID_Entity_Assoc[]
     */
    public function getAllForUrl($serverUrl)
    {
        $serverUrlHash = ModUtil::apiFunc('OpenID', 'admin', 'hashServerUrl', array('serverUrl' => $serverUrl));
        $assocList = $this->findBy(array('serverUrlHash' => $serverUrlHash));

        return $assocList;
    }

    /**
     * Get the most recent association.
     *
     * @param string $serverUrl The server url.
     *
     * @return null|OpenID_Entity_Assoc
     */
    public function getMostRecentAssoc($serverUrl)
    {
        $serverUrlHash = ModUtil::apiFunc('OpenID', 'admin', 'hashServerUrl', array('serverUrl' => $serverUrl));
        $assoc = $this->findOneBy(array('serverUrlHash' => $serverUrlHash), array('issued' => 'DESC'));

        return $assoc;
    }

    /**
     * Get an association by server url and handle.
     *
     * @param string $serverUrl The server url.
     * @param string $handle    The handle.
     *
     * @return null|OpenID_Entity_Assoc
     */
    public function getAssoc($serverUrl, $handle)
    {
        $serverUrlHash = ModUtil::apiFunc('OpenID', 'admin', 'hashServerUrl', array('serverUrl' => $serverUrl));
        $assoc = $this->findOneBy(array('serverUrlHash' => $serverUrlHash, 'handle' => $handle));

        return $assoc;
    }

    /**
     * Clean expired associations.
     *
     * @return int Number of associations removed.
     */
    public function cleanExpired()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $assocsToBeRemoved = $qb->select('a')
            ->from('OpenID_Entity_Assoc', 'a')
            ->where($qb->expr()->lt('(a.issued + a.lifetime)', '?1'))
            ->setParameter(1, time())
            ->getQuery()->execute();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('OpenID_Entity_Assoc', 'a')
            ->where($qb->expr()->lt('(a.issued + a.lifetime)', '?1'))
            ->setParameter(1, time())
            ->getQuery()->execute();

        return count($assocsToBeRemoved);
    }

    /**
     * Remove an association by server url and handle.
     *
     * @param string $serverUrl The server url.
     * @param string $handle    The handle.
     *
     * @return bool True if the to be deleted entity really existed, false otherwise.
     */
    public function removeAssoc($serverUrl, $handle)
    {
        $assoc = $this->getAssoc($serverUrl, $handle);

        if ($assoc) {
            $em = $this->getEntityManager();
            $em->remove($assoc);
            $em->flush();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Truncate associations table.
     *
     * @return void
     */
    public function truncateTable()
    {
        $this->getEntityManager()->createQuery('TRUNCATE TABLE OpenID_Entity_Assoc')->execute();
    }
}