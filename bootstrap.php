<?php
/**
 * Copyright Zikula Foundation 2013 - Zikula Application Framework
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

// Load composer generated autoload file.
$autoloaderPath = dirname(__FILE__) . '/lib/vendor/autoload.php';

if (file_exists($autoloaderPath)) {
    require($autoloaderPath);
} else {
    echo "The OpenID module could not find the required php-openid library.<br />";
    echo "If you are a developer, run \"composer update\" to add the library.<br />";
    echo "If you are a normal user, please don't download the source of this module from github. Please download a released version from ";
    echo "<a href=\"https://github.com/zikula-modules/OpenID/releases\">https://github.com/zikula-modules/OpenID/releases</a> instead.<br />";
    echo "Once you did that, you do not need to reinstall this module. Just overwrite the module's files.";
}