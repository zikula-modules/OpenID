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
 * The session storage class used by the Auth_Yadis_Manager.
 * 
 * This class wraps the default PHP session machinery and subclasses the default
 * machinery to make it compatible with Zikula's session management.
 */
class OpenID_PHPOpenID_SessionStore extends Auth_Yadis_PHPSession
{
    /**
     * The namespace used for all Yadis session variables.
     */
    const OPENID_YADIS_SESSION_NAMESPACE = 'OpenID_Yadis';
    
    /**
     * A reference to the Zikula service manager.
     * 
     * @var Zikula_ServiceManager
     */
    protected $serviceManager;
    
    /**
     * A reference to the Zikula HTTP request service.
     * 
     * @var Zikula_Request_Http
     */
    protected $request;
    
    /**
     * Build a new instance of this class, initializing Zikula-specific items.
     */
    public function __construct()
    {
        $this->serviceManager = ServiceUtil::getManager();
        $this->request = $this->serviceManager->getService('request');
    }

    /**
     * Set a session key/value pair.
     *
     * @param string $name  The name of the session key to add.
     * @param string $value The value to add to the session.
     * 
     * @return void
     */
    function set($name, $value)
    {
        $this->request->getSession()->set($name, $value, self::OPENID_YADIS_SESSION_NAMESPACE);
    }

    /**
     * Get a key's value from the session.
     *
     * @param string $name    The name of the key to retrieve.
     * @param string $default The optional value to return if the key is not found in the session.
     * 
     * @return string $result The key's value in the session or $default if it isn't found.
     */
    function get($name, $default=null)
    {
        return $this->request->getSession()->get($name, $default, self::OPENID_YADIS_SESSION_NAMESPACE);
    }

    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     * 
     * @return void
     */
    function del($name)
    {
        $this->request->getSession()->del($name, self::OPENID_YADIS_SESSION_NAMESPACE);
    }

    /**
     * Return the contents of the session in array form.
     * 
     * @return mixed The contents of the Yadis session store, specifically, the Zikula session management namespace contents.
     */
    function contents()
    {
        return $this->request->getSession()->getNamespaceContents(self::OPENID_YADIS_SESSION_NAMESPACE);
    }
}
