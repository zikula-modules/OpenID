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

function OpenID_tables()
{
    $dbtables = array();

    $dbtables['openid_user'] = DBUtil::getLimitedTablename('openid_user');

    /*
     * id           The unique identifier for this record, set automatically by the database on insert.
     * uid          The Zikula user account id (uid) for the user associated with the claimed identifier
     * claimed_id   The claimed identifier, as normalized through the OpenID normalization process described in
     *                  section 7.2 of verion 2.0 of the OpenID specification. A Zikula user account may have
     *                  zero, one, or many claimed identifier(s), but no two claimed identifiers should be equivalent
     *                  either for the same uid or for different uids (claimed identifiers must be unique).
     * is_primary   A boolean value indicating whether this is the primary claimed identifier or not. If it is the only
     *                  claimed identifier for a given uid, then this must be true. If several claimed identifiers exist
     *                  for a given uid, then one (and only one) must have this flag set to true for that uid. If (in error)
     *                  no claimed identifier has the primary flag set, the one with the lowest numerical value for its id
     *                  field may be assumed to be the primary.
     */
    $dbtables['openid_user_column'] = array(
        'id'            => 'id',
        'uid'           => 'uid',
        'claimed_id'    => 'claimed_id',
        'is_primary'    => 'is_primary',
    );
    $dbtables['openid_user_column_def'] = array(
        'id'            => 'I PRIMARY AUTO',
        'uid'           => 'I NOTNULL DEFAULT 0',
        'claimed_id'    => "C(255) NOTNULL DEFAULT ''",
        'is_primary'    => 'L NOTNULL DEFAULT 0',
    );

    return $dbtables;
}