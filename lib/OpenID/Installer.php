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
 * Installs, uninstalls or upgrades the OpenID module.
 */
class OpenID_Installer extends Zikula_AbstractInstaller
{
    /**
     * Initialise the template module.
     *
     * @return boolean
     */
    public function install()
    {
        try {
            DoctrineHelper::createSchema($this->entityManager, array('OpenID_Entity_UserMap', 'OpenID_Entity_Nonce', 'OpenID_Entity_Assoc'));
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            return LogUtil::registerError($this->__f('An error was encountered while creating the tables for the %s module.', array($this->getName())));
        }

        // Persistent event handler registration
        EventUtil::registerPersistentModuleHandler('OpenID', 'user.account.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));
        EventUtil::registerPersistentModuleHandler('OpenID', 'user.registration.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));

        // Do not use an api function here: The api is not loaded yet.
        $openIdProvider = OpenID_Util::getAllOpenIdProvider();

        $nameArray = array();
        /** @var OpenID_OpenIDProvider_AbstractProvider $provider */
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
            $userMapList = $this->entityManager->getRepository('OpenID_Entity_UserMap')->findAll();
        } catch (Exception $e) {
            $message = $this->__f('A database error was encountered while uninstalling the %1$s module.', array($this->getName()));
            if (System::isDevelopmentMode()) {
                $message .= ' ' . $this->__f('The Doctrine Exception message was: %1$s', array($e->getMessage()));
            }
            LogUtil::registerError($message);
            return false;
        }

        $error = false;
        if (!empty($userMapList)) {
            foreach ($userMapList as $user) {
                $uid = $user['uid'];
                if (UserUtil::getVar('pass', $uid) == Users_Constant::PWD_NO_USERS_AUTHENTICATION) {
                    // This user has no password. He won't be able to login anymore.
                    $error = true;
                    LogUtil::registerError($this->__f('The user "%s" would not be able to login anymore if you uninstall this module.', UserUtil::getVar('uname', $uid)));
                }
            }
        }

        if ($error) {
            LogUtil::registerError($this->__("If you really want to uninstall this module, you have two possibilities: Either delete the users via the Users module or set random passwords for them via the OpenID admin interface"));
            return false;
        }

        try {
            DoctrineHelper::dropSchema($this->entityManager, array('OpenID_Entity_UserMap', 'OpenID_Entity_Nonce', 'OpenID_Entity_Assoc'));
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            return LogUtil::registerError($this->__f('An error was encountered while dropping the tables for the %s module.', array($this->getName())));
        }

        $this->delVars();

        // Persistent event handler unregistration
        EventUtil::unregisterPersistentModuleHandler('OpenID', 'user.account.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));
        EventUtil::unregisterPersistentModuleHandler('OpenID', 'user.registration.delete', array('OpenID_Listener_UsersDelete', 'deleteAccountListener'));

        // Deletion successful
        return true;
    }
}