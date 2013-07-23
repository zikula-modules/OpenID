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

use Symfony\Component\Finder\Finder;

class OpenID_Util
{
    public static function getAllOpenIdProvider()
    {
        $finder = new Finder();
        $finder->files()
                ->in(dirname(__FILE__) . "/Helper")
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