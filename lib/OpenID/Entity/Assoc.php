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
     * "Octifies" a binary string by returning a string with escaped
     * octal bytes.
     *
     * @param The string to "octify".
     *
     * @return string The string with escaped octal bytes.
     */
    private function octify($str)
    {
        $result = "";
        for ($i = 0; $i < Auth_OpenID::bytes($str); $i++) {
            $ch = substr($str, $i, 1);
            if ($ch == "\\") {
                $result .= "\\\\\\\\";
            } else if (ord($ch) == 0) {
                $result .= "\\\\000";
            } else {
                $result .= "\\" . strval(decoct(ord($ch)));
            }
        }
        return $result;
    }

    /**
     * "Unoctifies" octal-escaped data and returns the
     * resulting ASCII (possibly binary) string.
     *
     * @param The string to "unoctify".
     *
     * @return string The ASCII (and possibly binary) string.
     */
    private function unoctify($str)
    {
        $result = "";
        $i = 0;
        while ($i < strlen($str)) {
            $char = $str[$i];
            if ($char == "\\") {
                // Look to see if the next char is a backslash and
                // append it.
                if ($str[$i + 1] != "\\") {
                    $octal_digits = substr($str, $i + 1, 3);
                    $octal_digits = explode("\\", $octal_digits);
                    $octal_digits = $octal_digits[0];
                    $dec = octdec($octal_digits);
                    $char = chr($dec);
                    $i += strlen((string)$octal_digits) + 1;
                } else {
                    $char = "\\";
                    $i += 2;
                }
            } else {
                $i += 1;
            }

            $result .= $char;
        }

        return $result;
    }
}