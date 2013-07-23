OpenID Module
=============

Ready for Zikula 1.3.X.

**Important:** Due to a bug in Zikula 1.3.5 (and maybe earlier versions), **the registration will not work**.
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

Installing
==========

#### Users ####
Please only use the https://github.com/zikula-modules/OpenID/releases page to download this modules. Make sure
not to download the *Source code (zip)* but *OpenID.zip*. The source code does not contain all necessary external
libraries.

#### Developers ####
Run `composer self-update && composer update`. Composer can be downloaded from http://getcomposer.org/.