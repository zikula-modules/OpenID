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

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * @ORM\Entity(repositoryClass="OpenID_Entity_Repository_Nonce")
 * @ORM\Table(name="openid_nonce")
 * @ORM\HasLifecycleCallbacks
 */
class OpenID_Entity_Nonce extends Zikula_EntityAccess
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=64, name="serverUrlHash")
     * @var string $serverUrlHash.
     */
    private $serverUrlHash;

    /**
     * @ORM\Column(type="string", length=2047, name="serverUrl")
     * @var string $serverUrl.
     */
    private $serverUrl;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=4, name="timestamp")
     * @var string $timestamp.
     */
    private $timestamp;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=40, name="salt")
     * @var string $salt.
     */
    private $salt;

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $serverUrl
     */
    public function setServerUrl($serverUrl)
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * @param string $serverUrlHash
     */
    public function setServerUrlHash($serverUrlHash)
    {
        $this->serverUrlHash = $serverUrlHash;
    }

    /**
     * @return string
     */
    public function getServerUrlHash()
    {
        return $this->serverUrlHash;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->serverUrlHash = ModUtil::apiFunc('OpenID', 'admin', 'hashServerUrl', array('serverUrl' => $this->serverUrl));
    }
}