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

abstract class OpenID_OpenIDProvider_AbstractProvider implements Zikula_TranslatableInterface
{
    /**
     * The OpenID supplied id, as an OpenID Server endpoint.
     *
     * @var string
     */
    protected $suppliedId;

    protected $domain;

    /**
     * Builds a new instance of this class, extracting the supplied OpenID from the $authenticationInfo parameter.
     *
     * @param array  $authenticationInfo An array containing the authentication information, and specifically, the OpenID supplied by the user
     *                                      in the 'supplied_id' element which is used to initialize this instance.
     */
    public final function __construct(array $authenticationInfo)
    {
        $this->domain = ZLanguage::getModuleDomain(OpenID_Constant::MODNAME);

        if (isset($authenticationInfo)) {
            $this->suppliedId = $authenticationInfo['supplied_id'];
        }
    }

    abstract function getSuppliedId();

    abstract function getDisplayName($claimedId);

    public final function getProviderName()
    {
        $parts = explode('_', get_class($this));

        return $parts[2];
    }

    public function getProviderDisplayName()
    {
        return $this->getProviderName();
    }

    public function getShortDescription()
    {
        return $this->getProviderDisplayName();
    }

    public function getLongDescription()
    {
        return $this->getShortDescription();
    }

    public function needsSsl()
    {
        return false;
    }

    public function registrationCapable()
    {
        return true;
    }

    /**
     * Translate.
     *
     * @param string $msgid String to be translated.
     *
     * @return string
     */
    public function __($msgid)
    {
        return __($msgid, $this->domain);
    }

    /**
     * Translate with sprintf().
     *
     * @param string       $msgid  String to be translated.
     * @param string|array $params Args for sprintf().
     *
     * @return string
     */
    public function __f($msgid, $params)
    {
        return __f($msgid, $params, $this->domain);
    }

    /**
     * Translate plural string.
     *
     * @param string $singular Singular instance.
     * @param string $plural   Plural instance.
     * @param string $count    Object count.
     *
     * @return string Translated string.
     */
    public function _n($singular, $plural, $count)
    {
        return _n($singular, $plural, $count, $this->domain);
    }

    /**
     * Translate plural string with sprintf().
     *
     * @param string       $sin    Singular instance.
     * @param string       $plu    Plural instance.
     * @param string       $n      Object count.
     * @param string|array $params Sprintf() arguments.
     *
     * @return string
     */
    public function _fn($sin, $plu, $n, $params)
    {
        return _fn($sin, $plu, $n, $params, $this->domain);
    }
}