<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Users
 * @subpackage Listeners
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides listeners (handlers) Users module user and registration delete events.
 */
class OpenID_Listener_UsersDelete
{
    /**
     * Removes an account's OpenID association(s) when the account (either a user or a registration) is deleted.
     *
     * @param Zikula_Event $event The event that triggered this handler.
     *
     * @return void
     */
    public static function deleteAccountListener(Zikula_Event $event)
    {
        $userObj = $event->getSubject();

        if (isset($userObj) && isset($userObj['uid']) && !empty($userObj['uid']) && ($userObj['uid'] > 2)) {
            $sm = ServiceUtil::getManager();

            /** @var Doctrine\ORM\EntityManager $em */
            $em = $sm->get('doctrine.entitymanager');

            $qb = $em->createQueryBuilder();
            $qb->delete('OpenID_Entity_UserMap', 'u');
            $qb->where($qb->expr()->eq('u.uid', ':uid'));
            $qb->setParameter(':uid',  $userObj['uid']);
            $qb->getQuery()->execute();
        }
    }
}
