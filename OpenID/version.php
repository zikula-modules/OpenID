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


$modversion['name']           = 'OpenID';
$modversion['displayname']    = __('OpenID Authentication Provider');
$modversion['description']    = __('Provides OpenID authentication.');
//! module name that appears in URL
$modversion['url']            = __('OpenID');

$modversion['version']        = '0.0.1';

$modversion['author']         = 'Drak';
$modversion['contact']        = 'drak@zikula.org';

$modversion['securityschema'] = array('OpenID::' => '::');
