<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
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

use Symfony\Component\Finder\Finder;

/**
 * User-oriented API function for the OpenID module.
 */
class OpenID_Api_User extends Zikula_AbstractApi
{
    /**
     * Retrieves an OpenID record for the user currently logged in.
     *
     * Either a unique (database record) id, or a unique claimed id must be
     * supplied in order to uniquely identify the record for this user to retrieve.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * int    $args['id']         The unique database id for the record to retrieve, which must be associated with the
     *                                  user currently logged in; required if 'claimed_id' is not specified;
     *                                  cannot be used if 'claimed_id' is specified.
     * string $args['claimed_id'] The claimed OpenID to retrieve, which must be associated with the user currently
     *                                  logged in; required if 'id' is not specified; cannot be used if 'id' is specified.
     * 
     * @param array $args All parameters passed to this function.
     *
     * @return array|boolean The OpenID record as specified, or an empty array if no such record is found; false on error.
     */
    public function get($args)
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT)) {
            throw new Zikula_Exception_Fatal();
        }

        if (isset($args['id']) && isset($args['claimed_id'])) {
            // Cannot supply more than one of id or claimed_id
            throw new Zikula_Exception_Fatal($this->__('Either an id or a claimed id should be specified, not both.'));
        }

        $uid = UserUtil::getVar('uid');

        try {
            if (isset($args['id'])) {
                if (!is_numeric($args['id']) || ((string)((int)$args['id']) != $args['id']) || ($args['id'] < 1)) {
                    throw new Zikula_Exception_Fatal($this->__f('An invalid user id was received: \'%1$s\'.', array($args['id'])));
                } else {
                    $userMap = Doctrine_Core::getTable('OpenID_Model_UserMap')
                        ->getMapById($uid, $args['id']);
                }
            } elseif (isset($args['claimed_id'])) {
                if (empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
                    throw new Zikula_Exception_Fatal($this->__f('An invalid claimed id was received: \'%1$s\'.', array($args['claimed_id'])));
                } else {
                    $userMap = Doctrine_Core::getTable('OpenID_Model_UserMap')
                        ->getMapByClaimedId($uid, $args['claimed_id']);
                }
            }
        } catch (Exception $e) {
            // TODO - Probably a Doctrine error. Throw an exception?
            return false;
        }

        return isset($userMap) ? $userMap : array();
    }

    /**
     * Retrieves all OpenIDs associated with the current user.
     *
     * @param array $args All parameters passed to this function; not currently used.
     * 
     * @return array|boolean An array of OpenID records associated with the current user; an empty array if there are no
     *                          OpenIDs associated with the current user; false on error.
     */
    public function getAll($args)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT));

        $userMapList = false;

        $uid = UserUtil::getVar('uid');

        if ($uid && ($uid > 1)) {
            try {
                $userMapList = Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->getAllForUid($uid);
            } catch (Exception $e) {
                // TODO - Throw an exception?
                return false;
            }
        }

        return isset($userMapList) ? $userMapList : array();
    }

    /**
     * Either counts all claimed open IDs, or counts the occurrences of the specified claimed_id.
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     * string  $args['claimed_id'] Counts only those records whose claimed id is equal to this; optional, if not specifed then all claimed IDs are counted.
     *
     * @param array $args All parameters for this function.
     *
     * @return boolean|integer The count; false on error.
     */
    public function countAll($args)
    {
        if (isset($args['claimed_id'])) {
            if (empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
                throw new Zikula_Exception_Fatal($this->__('An invalid claimed ID was specified.'));
            }
        }
        
        try {
            if (isset($args['claimed_id'])) {
                return Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->countClaimedId($args['claimed_id']);
            } else {
                return Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->countAll();
            }
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                return LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            } else {
                return false;
            }
        }
    }

    /**
     * Either counts all claimed open IDs for the specifed user, or counts the specified claimed_id for the specifed user.
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     * numeric $args['uid']        The user id of the user for whom the count is to be performed; optional, if not specified then the current user is assumed.
     * string  $args['claimed_id'] Counts only those records for the specified user whose claimed id is equal to this; optional, if not specifed then all 
     *                                  claimed IDs for the specified user are counted.
     *
     * @param array $args All parameters for this function.
     *
     * @return boolean|integer The count for the current user; false on error.
     */
    public function countAllForUser($args)
    {
        if (isset($args['claimed_id'])) {
            if (empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
                throw new Zikula_Exception_Fatal($this->__('An invalid claimed ID was specified.'));
            }
        }
        
        if (isset($args['uid'])) {
            if (empty($args['uid']) || !is_numeric($args['uid']) || ((string)((int)$args['uid']) != $args['uid'])) {
                throw new Zikula_Exception_Fatal($this->__('An invalid user ID was received.'));
            }
            $uid = $args['uid'];
        } else {
            $uid = UserUtil::getVar('uid');
        }

        try {
            if (isset($args['claimed_id'])) {
                return Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->countClaimedId($args['claimed_id'], $uid);
            } else {
                return Doctrine_Core::getTable('OpenID_Model_UserMap')
                    ->countAll($uid);
            }
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                return LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            } else {
                return false;
            }
        }
    }

    public function getAllOpenIdProvider($args)
    {
        $finder = new Finder();
        $finder->files()
                ->in(__DIR__ . "/../Helper")
                ->name('*.php')
                ->notName('OpenID.php')
                ->notName('Builder.php')
                ->notName('AuthenticationMethod.php')
                ->depth('== 0')
                ->sortByName();

        $provider = array();

        foreach ($finder as $file) {
            $classname =  'OpenID_Helper_' . substr($file->getRelativePathname(), 0, -4);
            $provider[] = new $classname(new stdClass());
        }

        return $provider;
    }
}