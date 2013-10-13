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
 * @ORM\Entity(repositoryClass="OpenID_Entity_Repository_UserMap")
 * @ORM\Table(name="openid_usermap")
 */
class OpenID_Entity_UserMap extends Zikula_EntityAccess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id.
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var integer $uid.
     */
    private $uid;

    /**
     * @ORM\Column(type="string",name="authentication_method")
     * @var string $authentication_method
     */
    private $authentication_method;

    /**
     * @ORM\Column(type="string",name="claimed_id")
     * @var string $claimed_id
     */
    private $claimed_id;

    /**
     * @ORM\Column(type="string",name="display_id")
     * @var string $display_id
     */
    private $display_id;

    /**
     * @param string $authentication_method
     */
    public function setauthentication_method($authentication_method)
    {
        $this->authentication_method = $authentication_method;
    }

    /**
     * @return string
     */
    public function getauthentication_method()
    {
        return $this->authentication_method;
    }

    /**
     * @param string $claimed_id
     */
    public function setclaimed_id($claimed_id)
    {
        $this->claimed_id = $claimed_id;
    }

    /**
     * @return string
     */
    public function getclaimed_id()
    {
        return $this->claimed_id;
    }

    /**
     * @param string $display_id
     */
    public function setdisplay_id($display_id)
    {
        $this->display_id = $display_id;
    }

    /**
     * @return string
     */
    public function getdisplay_id()
    {
        return $this->display_id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * This is a helper function to receive the username of a mapped user.
     *
     * @return string The username.
     */
    public function getUname()
    {
        return UserUtil::getVar('uname', $this->uid);
    }

    /**
     * This is a helper function to check if the mapped user has a password set.
     *
     * @return bool True if he has a password.
     */
    public function getHasPassword()
    {
        return UserUtil::getVar('pass', $this->uid) != Users_Constant::PWD_NO_USERS_AUTHENTICATION;
    }

}