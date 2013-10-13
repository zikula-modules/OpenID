<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 * @subpackage Users
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides storage services for OpenID protocol housekeeping information maintained by the php-openid library.
 */
class OpenID_PHPOpenID_OpenIDStore extends Auth_OpenID_OpenIDStore
{
    /** @var Doctrine\ORM\EntityManager */
    private $entityManager;

    private function getEntityManager()
    {
        if (!isset($this->entityManager)) {
            $this->entityManager = ServiceUtil::get('doctrine.entitymanager');
        }
        return $this->entityManager;
    }

    /**
     * This method puts an Association object into storage, retrievable by server URL and handle.
     *
     * @param string      $serverUrl  The URL of the identity server that this association is with. Because of the way the server portion
     *                                      of the library uses this interface, don't assume there are any limitations on the character set 
     *                                      of the input string. In particular, expect to see unescaped non-url-safe characters in
     *                                      the server_url field.
     * @param Auth_OpenID_Association $association The Association to store.
     * 
     * @return void
     */
    public function storeAssociation($serverUrl, Auth_OpenID_Association $association)
    {
        $openidAssoc = new OpenID_Entity_Assoc();
        $openidAssoc->setServerUrl($serverUrl);
        $openidAssoc->setHandle($association->handle);
        $openidAssoc->setSecret($association->secret);
        $openidAssoc->setIssued($association->issued);
        $openidAssoc->setLifetime($association->lifetime);
        $openidAssoc->setAssocType($association->assoc_type);
        $this->getEntityManager()->persist($openidAssoc);
        $this->getEntityManager()->flush();
    }

    /**
     * Remove expired nonces from the store.
     *
     * Discards any nonce from storage that is old enough that its
     * timestamp would not pass useNonce().
     *
     * This method is not called in the normal operation of the
     * library.  It provides a way for store admins to keep their
     * storage from filling up with expired data.
     *
     * @return integer The number of nonces expired.
     */
    public function cleanupNonces()
    {
        global $Auth_OpenID_SKEW;

        /** @var OpenID_Entity_Repository_Nonce $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Nonce');

        return $repository->cleanExpired($Auth_OpenID_SKEW);
    }

    /**
     * Remove expired associations from the store.
     *
     * This method is not called in the normal operation of the
     * library.  It provides a way for store admins to keep their
     * storage from filling up with expired data.
     *
     * @return integer The number of associations expired.
     */
    public function cleanupAssociations()
    {
        /** @var OpenID_Entity_Repository_Assoc $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Assoc');

        return $repository->cleanExpired();
    }

    /**
     * Shortcut for cleanupNonces(), cleanupAssociations().
     *
     * This method is not called in the normal operation of the
     * library.  It provides a way for store admins to keep their
     * storage from filling up with expired data.
     * 
     * @return array An array containing the results of {@link cleanupNonces()} and {@link cleanupAssociations()}.
     */
    public function cleanup()
    {
        return array($this->cleanupNonces(),
                     $this->cleanupAssociations());
    }

    /**
     * Report whether this storage supports cleanup.
     * 
     * @return boolean True.
     */
    public function supportsCleanup()
    {
        return true;
    }

    /**
     * This method returns an Association object from storage that matches the server URL and, if specified, handle. 
     * 
     * It returns null if no such association is found or if the matching
     * association is expired.
     *
     * If no handle is specified, the store may return any association
     * which matches the server URL. If multiple associations are
     * valid, the recommended return value for this method is the one
     * most recently issued.
     *
     * This method is allowed (and encouraged) to garbage collect
     * expired associations when found. This method must not return
     * expired associations.
     *
     * @param string $serverUrl The URL of the identity server to get the association for. Because of the way the server portion of
     *                              the library uses this interface, don't assume there are any limitations on the character set of 
     *                              the input string.  In particular, expect to see unescaped non-url-safe characters in
     *                              the server_url field.
     * @param mixed  $handle     This optional parameter is the handle of the specific association to get. If no specific handle is
     *                              provided, any valid association matching the server URL is returned.
     *
     * @return null|Auth_OpenID_Association The Association for the given identity server.
     */
    public function getAssociation($serverUrl, $handle = null)
    {
        /** @var OpenID_Entity_Repository_Assoc $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Assoc');

        $repository->cleanExpired();

        if ($handle !== null) {
            $assoc = $repository->getAssoc($serverUrl, $handle);
        } else {
            $assoc = $repository->getMostRecentAssoc($serverUrl);
        }

        if (!isset($assoc)) {
            return null;
        } else {
            return new Auth_OpenID_Association(
                $assoc->getHandle(),
                $assoc->getSecret(),
                $assoc->getIssued(),
                $assoc->getLifetime(),
                $assoc->getAssocType()
            );
        }
    }

    /**
     * This method removes the matching association if it's found, and returns whether the association was removed or not.
     *
     * @param string $serverUrl The URL of the identity server the association to remove belongs to. Because of the way the server
     *                              portion of the library uses this interface, don't assume there are any limitations on the 
     *                              character set of the input string. In particular, expect to see unescaped non-url-safe 
     *                              characters in the server_url field.
     * @param string $handle     This is the handle of the association to remove. If there isn't an association found that matches both
     *                              the given URL and handle, then there was no matching handle found.
     *
     * @return mixed Returns whether or not the given association existed.
     */
    public function removeAssociation($serverUrl, $handle)
    {
        /** @var OpenID_Entity_Repository_Assoc $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Assoc');

        return $repository->removeAssoc($serverUrl, $handle);
    }

    /**
     * Called when using a nonce.
     *
     * This method should return C{True} if the nonce has not been
     * used before, and store it for a while to make sure nobody
     * tries to use the same value again.  If the nonce has already
     * been used, return C{False}.
     *
     * Change: In earlier versions, round-trip nonces were used and a
     * nonce was only valid if it had been previously stored with
     * storeNonce.  Version 2.0 uses one-way nonces, requiring a
     * different implementation here that does not depend on a
     * storeNonce call.  (storeNonce is no longer part of the
     * interface.
     *
     * @param string  $server_url The URL of the OpenID Server.
     * @param integer $timestamp  The UNIX timestamp associated with the nonce. 
     * @param string  $salt       The salt string associated with the nonce.
     *
     * @return bool True if the nonce was valid (not used) and stored; otherwise false.
     */
    public function useNonce($serverUrl, $timestamp, $salt)
    {
        global $Auth_OpenID_SKEW;

        if ( abs($timestamp - time()) > $Auth_OpenID_SKEW ) {
            return false;
        }

        try {
            $nonce = new OpenID_Entity_Nonce();
            $nonce->setServerUrl($serverUrl);
            $nonce->setTimestamp($timestamp);
            $nonce->setSalt($salt);
            $this->getEntityManager()->persist($nonce);
            $this->getEntityManager()->flush();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Removes all entries from the store.
     * 
     * Implementation is optional.
     * 
     * @return void
     */
    public function reset()
    {
        /** @var OpenID_Entity_Repository_Assoc $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Assoc');
        $repository->truncateTable();

        /** @var OpenID_Entity_Repository_Nonce $repository */
        $repository = $this->getEntityManager()->getRepository('OpenID_Entity_Nonce');
        $repository->truncateTable();
    }

}
