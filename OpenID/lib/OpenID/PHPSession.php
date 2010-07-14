<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 * @subpackage OpenID
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

// Auth_Yadis_PHPSession is defined in Auth_Yadis_Manager.php
ZLoader::autoload('Auth_Yadis_Manager');

/**
 * The session class used by the Auth_Yadis_Manager.  This
 * class wraps the default PHP session machinery and should be
 * subclassed if your application doesn't use PHP sessioning.
 *
 * @package OpenID
 */
class OpenID_PHPSession extends Auth_Yadis_PHPSession
{
    /**
     * The path used with SessionUtil for all Yadis session variables
     */
    const OPENID_SESSIONPATH = '/OpenID/Yadis';

    /**
     * Set a session key/value pair.
     *
     * @param string $name The name of the session key to add.
     * @param string $value The value to add to the session.
     */
    function set($name, $value)
    {
        SessionUtil::setVar($name, $value, self::OPENID_SESSIONPATH, true, true);
    }

    /**
     * Get a key's value from the session.
     *
     * @param string $name The name of the key to retrieve.
     * @param string $default The optional value to return if the key
     * is not found in the session.
     * @return string $result The key's value in the session or
     * $default if it isn't found.
     */
    function get($name, $default=null)
    {
        return SessionUtil::getVar($name, $default, self::OPENID_SESSIONPATH, false, false);
    }

    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     */
    function del($name)
    {
        SessionUtil::delVar($name, null, self::OPENID_SESSIONPATH);
    }

    /**
     * Return the contents of the session in array form.
     */
    function contents()
    {
        return SessionUtil::getVar(self::OPENID_SESSIONPATH, array(), '/');
    }
}
