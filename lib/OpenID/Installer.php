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

/**
 * Installs or upgrades the OpenID module.
 */
class OpenID_Installer extends Zikula_AbstractInstaller
{
    /**
     * Initialise the template module.
     * 
     * @return void
     */
    public function install()
    {
        try {
            DoctrineUtil::createTablesFromModels('OpenID');
        } catch (Doctrine_Exception $e) {
            $message = $this->__f('An error was encountered while installing the %1$s module.', array($this->getName()));
            if (System::isDevelopmentMode()) {
                $message .= ' ' . $this->__f('The error occurred while creating the tables. The Doctrine Exception message was: %1$s', array($e->getMessage()));
            }
            $this->registerError($message);
        }
        
        // Persistent event handler registration
        EventUtil::registerPersistentModuleHandler('OpenID', 'user.account.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));
        EventUtil::registerPersistentModuleHandler('OpenID', 'user.registration.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));

        $openIdProvider = ModUtil::apiFunc($this->name, 'user', 'getAllOpenIdProvider');

        foreach ($openIdProvider as $provider) {
            $nameArray[] = $provider->getProviderName();
        }

        // Enable all providers for login and registration by default.
        $this->setVar('loginProvider', $nameArray);
        $this->setVar('registrationProvider', $nameArray);

        // Initialisation successful
        return true;
    }

    /**
     * Upgrade the OpenID module from an old version.
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param string $oldVersion Version number string from which the upgrade begins.
     * 
     * @return mixed True on success, last valid version string or false if fails.
     */
    public function upgrade($oldVersion)
    {
        switch ($oldVersion) {
            case '1.0.0':
                // Upgrade old version 1.0.0 to ?.?.?
                break;
            default:
                return $oldVersion;
                break;
        }
        
        // Update successful
        return true;
    }

    /**
     * Delete the OpenID module.
     * 
     * @return boolean True if the module was successfully uninstalled; otherwise false.
     */
    public function uninstall()
    {
        try {
            $userMapList = Doctrine_Core::getTable('OpenID_Model_UserMap')
                ->getAll(Doctrine_Core::HYDRATE_ARRAY);
        } catch (Exception $e) {
            $message = $this->__f('A database error was encountered while uninstalling the %1$s module.', array($this->getName()));
            if (System::isDevelopmentMode()) {
                $message .= ' ' . $this->__f('The Doctrine Exception message was: %1$s', array($e->getMessage()));
            }
            $this->registerError($message);
            return false;
        }

        $error = false;
        foreach ($userMapList as $user) {
            $uid = $user['uid'];
            if (UserUtil::getVar('pass', $uid) == Users_Constant::PWD_NO_USERS_AUTHENTICATION) {
                // This user has no password. He won't be able to login anymore.
                $error = true;
                $this->registerError($this->__f('The user "%s" would not be able to login anymore if you uninstall this module.', UserUtil::getVar('uname', $uid)));
            }
        }

        if ($error) {
            $this->registerError($this->__("If you really want to uninstall this module, you have two possibilities: Go to this module's admin interface and either delete the users or set random passwords for them."));
            return false;
        }

        $tables = array(
            'openid_usermap',
            'openid_assoc',
            'openid_nonce',
        );
        
        foreach ($tables as $tableName) {
            try {
                DoctrineUtil::dropTable($tableName);
            } catch (Doctrine_Exception $e) {
                $message = $this->__f('A database error was encountered while uninstalling the %1$s module.', array($this->getName()));
                if (System::isDevelopmentMode()) {
                    $message .= ' ' . $this->__f('The error occurred while dropping the %1$s table. The Doctrine Exception message was: %2$s', array($tableName, $e->getMessage()));
                }
                $this->registerError($message);
            }
        }

        $this->delVars();
        
        // Persistent event handler unregistration
        EventUtil::unregisterPersistentModuleHandler('OpenID', 'user.account.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));
        EventUtil::unregisterPersistentModuleHandler('OpenID', 'user.registration.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));

        // Deletion successful
        return true;
    }
}