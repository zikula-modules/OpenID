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
class OpenID_Entity_Repository_UserMap extends EntityRepository
{
    /**
     * Count the number of mappings.
     *
     * @return int The number of mappings.
     */
    public function countAll()
    {
        return count($this->getEntityManager()->getRepository('OpenID_Entity_UserMap')->findAll());
    }

    /**
     * Count the number of mappings by where condition.
     *
     * @param array $where An array of fieldnames and values to be used as where condition.
     *
     * @return int The number of mappings matching the where clause.
     */
    public function countBy($where)
    {
        return count($this->getEntityManager()->getRepository('OpenID_Entity_UserMap')->findBy($where));
    }
}