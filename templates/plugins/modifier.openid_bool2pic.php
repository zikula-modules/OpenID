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

/**
 * Converts a boolean value to a picture.
 */
function smarty_modifier_openid_bool2pic($bool)
{
    $view = Zikula_View::getInstance();
    require_once $view->_get_plugin_filepath('function', 'img');

    if ($bool) {
        return smarty_function_img(array('modname' => 'core', 'set' => 'icons/extrasmall', 'src' => 'button_ok.png'), $view);
    }

    return smarty_function_img(array('modname' => 'core', 'set' => 'icons/extrasmall', 'src' => 'button_cancel.png'), $view);
}