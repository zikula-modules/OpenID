<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class OpenID_Api_User extends Zikula_Api
{
    /**
     * Retrieves an OpenID record for the user currently logged in.
     *
     * Either a unique (database record) id, a unique claimed id, or a primary indicator must be
     * supplied in order to uniquely identify the record for this user to retrieve.
     *
     * @param array $args All parameters passed to this function.
     *                      int     $args['id']         The unique database id for the record to retrieve, which must be associated with the
     *                                                      user currently logged in; required if 'claimed_id' or 'primary' are not specified;
     *                                                      cannot be used in conjunction with 'claimed_id' or 'primary'.
     *                      string  $args['claimed_id'] The claimed OpenID to retrieve, which must be associated with the user currently
     *                                                      logged in; required if 'id' or 'primary' are not specified; cannot be
     *                                                      used in conjunction with 'id' or 'primary'.
     *                      bool    $args['primary']    Indicates that the primary OpenID for the currently logged in user should be retrieved;
     *                                                      the value of this parameter must be true; required if 'id' or 'claimed_id' are
     *                                                      not specified; cannot be used in conjunction with 'id' or 'claimed_id'.
     *
     * @return array|bool The OpenID record as specified, or an empty array if no such record is found; false on error.
     */
    public function get($args)
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        if (isset($args['id']) && (isset($args['claimed_id']) || isset($args['is_primary']))) {
            // Cannot supply more than one of id, claimed_id or is_primary
            return LogUtil::registerArgsError();
        } elseif (isset($args['claimed_id']) && isset($args['is_primary'])) {
            // Cannot supply more than one of id, claimed_id or is_primary
            return LogUtil::registerArgsError();
        }

        if (isset($args['id'])) {
            if (!is_numeric($args['id']) || ((int)$args['id'] != $args['id']) || ($args['id'] < 1)) {
                return LogUtil::registerArgsError();
            } else {
                $fieldKey = 'id';
                $value = $args['id'];
            }
        } elseif (isset($args['claimed_id'])) {
            if (empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
                return LogUtil::registerArgsError();
            } else {
                $fieldKey = 'claimed_id';
                $value = "'" . DataUtil::formatForStore($args['claimed_id']) . "'";
            }
        } elseif (isset($args['is_primary'])) {
            if (empty($args['is_primary']) || !is_bool($args['is_primary'])) {
                return LogUtil::registerArgsError();
            } else {
                $fieldKey = 'is_primary';
                $value = 1;
            }
        }

        $uid = UserUtil::getVar('uid');

        $dbTables = DBUtil::getTables();
        $openidUserColumn = $dbTables['openid_user_column'];
        $where = "WHERE ({$openidUserColumn['uid']} = {$uid}) AND ({$openidUserColumn[$fieldKey]} = {$value})";

        return DBUtil::selectObject('openid_user', $where);
    }

    /**
     * Retrieves all OpenIDs associated with the current user.
     *
     * @param <type> $args
     * @return array|bool An array of OpenID records associated with the current user; an empty array if there are no
     *                      OpenIDs associated with the current user; false on error.
     */
    public function getAll($args)
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        $returnValue = false;

        $uid = UserUtil::getVar('uid');
        if ($uid && ($uid > 0)) {
            $dbTables = DBUtil::getTables();
            $openidUserColumn = $dbTables['openid_user_column'];
            $where = "WHERE {$openidUserColumn['uid']} = {$uid}";
            $orderby = "ORDER BY {$openidUserColumn['is_primary']}, {$openidUserColumn['claimed_id']}";
            $returnValue = DBUtil::selectObjectArray('openid_user', $where, $orderby);
        }

        return $returnValue;
    }

    /**
     * Internal function to count the instances of a particular claimed id across all users.
     *
     * This is an internal, protected function because all user-level functions should operate only
     * on the user's own data. This function operates across all users. A user-level function should
     * only use this in order to confirm that the claimed id has not been claimed by another account.
     *
     * @param array $args All parameters sent to this function.
     *                      string $args['claimed_id'] The claimed OpenID to count across all user accounts.
     *
     * @return int|bool The count across all users accounts; false on error.
     */
    protected function countAllInternal($args)
    {
        if (!isset($args['claimed_id']) || empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
            return LogUtil::registerArgsError();
        } else {
            $claimedID = DataUtil::formatForStore($args['claimed_id']);
        }

        $dbTables = DBUtil::getTables();
        $openidUserColumn = $dbTables['openid_user_column'];
        $where = "WHERE {$openidUserColumn['claimed_id']} = '{$claimedID}'";

        return DBUtil::selectObjectCount('openid_user', $where);
    }

    /**
     *
     * @param array $args All parameters for this function.
     *                      string $args['claimed_id'] Counts only those records for the current user whose claimed id is equal to this; optional,
     *                                                  cannot be used in combination with args['primary'].
     *                      string $args['is_primary'] Counts only those records for the current user whose claimed id is equal to this; optional,
     *                                                  cannot be used in combination with args['primary'].
     *
     *
     * @return int|bool The count for the current user; false on error
     */
    public function countAll($args)
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        if (isset($args['claimed_id']) && isset($args['primary'])) {
            // Cannot supply more than one of claimed_id or primary
            return LogUtil::registerArgsError();
        }

        if (isset($args['claimed_id'])) {
            if (empty($args['claimed_id']) || !is_string($args['claimed_id'])) {
                return LogUtil::registerArgsError();
            } else {
                $fieldKey = 'claimed_id';
                $value = "'" . DataUtil::formatForStore($args['claimed_id']) . "'";
            }
        } elseif (isset($args['is_primary'])) {
            if (!is_bool($args['is_primary'])) {
                return LogUtil::registerArgsError();
            } else {
                $fieldKey = 'is_primary';
                $value = $args['is_primary'] ? 1 : 0;
            }
        }

        $uid = UserUtil::getVar('uid');

        $dbTables = DBUtil::getTables();
        $openidUserColumn = $dbTables['openid_user_column'];
        $whereArgs = array();
        $whereArgs[] = "{$openidUserColumn['uid']} = {$uid}";
        if (isset($fieldKey) && !empty($fieldKey)) {
            $whereArgs[] = "{$openidUserColumn[$fieldKey]} = {$value}";
        }

        $where = "WHERE (" . implode(') AND (', $whereArgs) . ")";

        return DBUtil::selectObjectCount('openid_user', $where);
    }

    /**
     * Adds a new claimed OpenID for the current user.
     *
     * @param array $args All arguments passed to the function.
     *                      string  $args['claimed_id']  A normalized and validated claimed OpenID
     *                      bool    $args['is_primary'] Whether this is now the primary ID or not.
     *
     * @return <type>
     */
    public function addOpenID($args)
    {
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->getName().'::self', '::', ACCESS_COMMENT))
        {
            return LogUtil::registerPermissionError();
        }

        if (!isset($args['claimed_id']) || empty($args['claimed_id'])) {
            return LogUtil::registerArgsError();
        }

        $openidCount = ModUtil::apiFunc($this->getName(), 'user', 'countAll');

        $claimedID = $args['claimed_id'];
        $isPrimary = (isset($args['is_primary']) && is_bool($args['is_primary']) && $args['is_primary']) || ($openidCount <= 0);

        $uid = UserUtil::getVar('uid');

        $thisUserCount = ModUtil::apiFunc($this->getName(), 'user', 'countAll', array(
            'claimed_id'    => $claimedID,
        ));
        if ($thisUserCount === false) {
            LogUtil::log($this->__f('Internal error: Unable to check for duplicate claimed id for %1$s = %2$s', array('uid', $uid)));
            return false;
        }

        $otherUserCount = $this->countAllInternal(array('claimed_id' => $claimedID));
        if ($otherUserCount === false) {
            LogUtil::log($this->__('Internal error: Unable to check for duplicate claimed id across all users'));
            return false;
        }

        $saved = false;

        if ($thisUserCount > 0) {
            return LogUtil::registerError($this->__f('The claimed OpenID \'%1$s\' is already associated with your account.', $claimedID));
        } elseif ($otherUserCount > 0) {
            return LogUtil::registerError($this->__f('The claimed OpenID \'%1$s\' is already associated with another account. If this is your OpenID, then please contact the site administrator.', $claimedID));
        } else {
            if ($isPrimary) {
                $currentPrimaryOpenID = ModUtil::apiFunc($this->getName(), 'user', 'get', array(
                    'is_primary'    => true,
                ));
                if ($currentPrimaryOpenID) {
                    // Unset primary
                    $openidObj = array(
                        'id'        => $currentPrimaryOpenID['id'],
                        'is_primary'=> false,
                    );

                    DBUtil::updateObject($openidObj, 'openid_user');
                }
            }

            $openidObj = array(
                'uid'           => $uid,
                'claimed_id'    => $claimedID,
                'is_primary'    => $isPrimary ? true : false,
            );

            $saved = DBUtil::insertObject($openidObj, 'openid_user');
        }

        return $saved;
    }
}