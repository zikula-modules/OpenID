OpenID Module
=============

Ready for Zikula 1.3.X.

**Important:** Due to a bug in Zikula 1.3.5 (and maybe earlier versions) and the Legal module, **the registration will not work**.
You have to manually fix three bugs, see below.

Installing
==========

#### Users ####
Please only use the https://github.com/zikula-modules/OpenID/releases page to download this module. Make sure
not to download the *Source code (zip)* but *OpenID.zip*. The source code does not contain all necessary external
libraries.

#### Developers ####
Run `composer self-update && composer update`. Composer can be downloaded from http://getcomposer.org/.

Bugs in other modules / core you need to fix before using this
==============================================================

*Core I:*

Please change the following lines (79-92) in `system/Users/lib/Users/Controller/FormData/RegistrationForm.php` from:
```php
$this->addField(new Users_Controller_FormData_Field(
        $this,
        'passreminder',
        false,
        false,
        $this->serviceManager))
    ->setNullAllowed(false)
    ->addValidator(new Users_Controller_FormData_Validator_StringType(
        $this->serviceManager,
        $this->__('The value must be a string.')))
    ->addValidator(new Users_Controller_FormData_Validator_StringMinimumLength(
        $this->serviceManager,
        1,
        $this->__('A password reminder is required, and cannot be left blank.')));
```
to (add `/*` and `*/`):
```php
$this->addField(new Users_Controller_FormData_Field(
        $this,
        'passreminder',
        false,
        false,
        $this->serviceManager))
    ->setNullAllowed(false)
    ->addValidator(new Users_Controller_FormData_Validator_StringType(
        $this->serviceManager,
        $this->__('The value must be a string.')))/*
    ->addValidator(new Users_Controller_FormData_Validator_StringMinimumLength(
        $this->serviceManager,
        1,
        $this->__('A password reminder is required, and cannot be left blank.')))*/;
```
( *see https://github.com/zikula/core/issues/900 too* )

*Core II*
Please replace the file `system/Users/lib/Users/Controller/User.php` by the File you find under the following link: https://gist.github.com/cmfcmf/6105715/raw/User.php

*Legal module (if you have it installed):*

Please change the following lines (269-272) in `modules/Legal/lib/Legal/Listener/UsersUiHandler.php` from:
```php
    if (!$this->request->isPost()) {
        // Validation is only appropriate for a post, otherwise it is probably a hack attempt.
        throw new Zikula_Exception_Forbidden();
    }
```
to:
```php
    if (!$this->request->isPost()) {
        // Check if we got here by a reentrant login method.
        $sessionVars = $this->request->getSession()->get('Users_Controller_User_login', array(), 'Zikula_Users');
        $getReentrantToken = $this->request->query->get('reentranttoken', null);
        if (!isset($sessionVars['reentranttoken']) || !isset($getReentrantToken) || $getReentrantToken != $sessionVars['reentranttoken']) {
            // Not reentrant login method,  it is probably a hack attempt.
            throw new Zikula_Exception_Forbidden();
        }
    }
