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
class OpenID_Entity_Repository_Nonce extends EntityRepository
{
    /**
     * Remove expired nonces.
     *
     * @param int $skewTime
     *
     * @return int The number of nonces being deleted.
     *
     * @see $Auth_OpenID_SKEW
     */
    public function cleanExpired($skewTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $noncesToBeRemoved = $qb->select('n')
            ->from('OpenID_Entity_Nonce', 'n')
            ->where($qb->expr()->lt('n.timestamp', '?1'))
            ->setParameter(1, time() - $skewTime)
            ->getQuery()->execute();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('OpenID_Entity_Nonce', 'n')
           ->where($qb->expr()->lt('n.timestamp', '?1'))
           ->setParameter(1, time() - $skewTime)
           ->getQuery()->execute();

        return count($noncesToBeRemoved);
    }

    /**
     * Truncate nonces table.
     *
     * @return void
     */
    public function truncateTable()
    {
        $this->getEntityManager()->createQuery('TRUNCATE TABLE OpenID_Entity_Nonce')->execute();
    }
}