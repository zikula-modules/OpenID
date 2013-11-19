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
 * @ORM\Entity(repositoryClass="OpenID_Entity_Repository_Assoc")
 * @ORM\Table(name="openid_assoc",indexes={@ORM\Index(name="byIssued", columns={"serverUrlHash", "handle"})})
 * @ORM\HasLifecycleCallbacks
 */
class OpenID_Entity_Assoc extends Zikula_EntityAccess
{
    /**
     * @Orm\Id
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
     * @Orm\Id
     * @ORM\Column(type="string", length=255, name="handle")
     * @var string $handle.
     */
    private $handle;

    /**
     * @ORM\Column(type="text", name="secret")
     * @var string $secret.
     */
    private $secret;

    /**
     * @ORM\Column(type="integer", length=4, name="issued")
     * @var string $issued.
     */
    private $issued;

    /**
     * @ORM\Column(type="integer", length=4, name="lifetime")
     * @var string $lifetime.
     */
    private $lifetime;

    /**
     * @ORM\Column(type="string", length=64, name="assoc_type")
     * @var string $assoc_type.
     */
    private $assoc_type;

    /**
     * @param string $assoc_type
     */
    public function setAssocType($assoc_type)
    {
        $this->assoc_type = $assoc_type;
    }

    /**
     * @return string
     */
    public function getAssocType()
    {
        return $this->assoc_type;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $issued
     */
    public function setIssued($issued)
    {
        $this->issued = $issued;
    }

    /**
     * @return string
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * @param string $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @return string
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $this->octify($secret);
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->unoctify($this->secret);
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->serverUrlHash = ModUtil::apiFunc('OpenID', 'admin', 'hashServerUrl', array('serverUrl' => $this->serverUrl));
    }

    /**
     * "Octifies" a binary string by returning a comma seperated string with the corresponding ASCII values.
     *
     * @param The string to "octify".
     *
     * @return string The string with the corresponding ASCII values.
     */
    private function octify($str)
    {
        $result = array();
        for ($i = 0; $i < strlen($str); $i++) {
            $char = substr($str, $i, 1);
            $result[] = ord($char);
        }

        return implode(',', $result);
    }

    /**
     * "Unoctifies" a comma seperated string with ASCII values into an ASCII (possibly binary) string.
     *
     * @param The string to "unoctify".
     *
     * @return string The ASCII (and possibly binary) string.
     */
    private function unoctify($str)
    {
        $result = '';
        foreach (explode(',', $str) as $char) {
            $result .= chr($char);
        }

        return $result;
    }
}