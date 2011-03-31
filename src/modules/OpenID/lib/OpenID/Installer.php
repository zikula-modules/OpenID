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

class OpenID_Installer extends Zikula_AbstractInstaller
{
    /**
     * initialise the template module
     * This function is only ever called once during the lifetime of a particular
     * module instance
     */
    public function install()
    {
        try {
            //Doctrine_Core::generateModelsFromDb('ztemp', array(Doctrine_Manager::connection()->getName()), array('generateTableClasses' => true));

            DoctrineUtil::createTablesFromModels('OpenID');
        } catch (Doctrine_Exception $e) {
            $message = $this->__f('An error was encountered while installing the %1$s module.', array($this->getName()));
            if (System::isDevelopmentMode()) {
                $message .= ' ' . $this->__f('The error occurred while creating the tables. The Doctrine Exception message was: %1$s', array($e->getMessage()));
            }
            $this->registerError($message);
        }

        $this->defaultData();

        // Initialisation successful
        return true;
    }

    protected function defaultData()
    {
//        $assoc = new OpenID_Model_OpenIDAssoc();
//        $assoc->server_url = "http://www.example.com";
//        $assoc->handle = "first handle";
//        $assoc->secret = "secret";
//        $assoc->issued = time();
//        $assoc->lifetime = 1;
//        $assoc->assoc_type = "assoc_type";
//        $assoc->save();
//
//        $assoc = new OpenID_Model_OpenIDAssoc();
//        $assoc->server_url = "http://www.example.com";
//        $assoc->handle = "second handle";
//        $assoc->secret = "secret";
//        $assoc->issued = time();
//        $assoc->lifetime = 1;
//        $assoc->assoc_type = "assoc_type";
//        $assoc->save();
//
//        $assoc = new OpenID_Model_OpenIDAssoc();
//        $assoc->server_url = "http://another.example.com";
//        $assoc->handle = "first other handle";
//        $assoc->secret = "secret";
//        $assoc->issued = time();
//        $assoc->lifetime = 1;
//        $assoc->assoc_type = "assoc_type";
//        $assoc->save();
//
//        $assocTable = Doctrine_Core::getTable('OpenID_Model_OpenIDAssoc');
//
//        $assoc = $assocTable->getAssoc('http://www.example.com', 'second handle');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc = " . $dump, 'DEBUG');
//
//        $assoc = $assocTable->getAllForUrl('http://www.example.com');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc for www.example.com = " . $dump, 'DEBUG');
//
//        $assoc = $assocTable->getAllForUrl('http://another.example.com');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc for another.example.com = " . $dump, 'DEBUG');
//
//        $assocTable->removeAssoc('http://www.example.com', 'first handle');
//
//        $assoc = $assocTable->getAllForUrl('http://www.example.com');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc after remove = " . $dump, 'DEBUG');
//
//        sleep(3);
//
//        $assocTable->cleanExpired();
//
//        $assoc = $assocTable->getAllForUrl('http://www.example.com');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc for www.example.com after clean = " . $dump, 'DEBUG');
//
//        $assoc = $assocTable->getAllForUrl('http://www.example.com');
//        $dump = var_export($assoc, true);
//        LogUtil::log("assoc for another.example.com after clean = " . $dump, 'DEBUG');
    }

    /**
     * Upgrade the errors module from an old version
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param        string   $oldVersion   version number string to upgrade from
     * @return       mixed    true on success, last valid version string or false if fails
     */
    public function upgrade($oldversion)
    {
        // Update successful
        return true;
    }

    /**
     * delete the errors module
     * This function is only ever called once during the lifetime of a particular
     * module instance
     */
    public function uninstall()
    {
        $tables = array(
            'openid_usermap',
            'openid_assoc',
            'openid_nonce',
        );
        
        foreach ($tables as $tableName) {
            try {
                DoctrineUtil::dropTable($tableName);
            } catch (Doctrine_Exception $e) {
                $message = $this->__f('A database error was encountered while uninstalling the %1$s module. The installation was allowed to proceed.', array($this->getName()));
                if (System::isDevelopmentMode()) {
                    $message .= ' ' . $this->__f('The error occurred while dropping the %1$s table. The Doctrine Exception message was: %2$s', array($tableName, $e->getMessage()));
                }
                $this->registerError($message);
            }
        }

        $this->delVars();

        // Deletion successful
        return true;
    }
}