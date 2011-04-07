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
 * A helper or utility class that provides information for an OpenID provider in expected formats for the protocol.
 */
class OpenID_Helper_OpenID extends Zikula_AbstractHelper
{
    /**
     * The OpenID supplied id, as an OpenID Server endpoint.
     *
     * @var string
     */
    protected $suppliedId;

    /**
     * Builds a new instance of this class, extracting the supplied OpenID from the $authenticationInfo parameter.
     *
     * @param array $authenticationInfo An array containing the authentication information, and specifically, the OpenID supplied by the user
     *                                      in the 'supplied_id' element which is used to initialize this instance.
     */
    public function __construct(array $authenticationInfo)
    {
        $this->suppliedId = $authenticationInfo['supplied_id'];
    }

    /**
     * Returns the supplied id.
     *
     * @return string The OpenID supplied by the user.
     */
    public function getSuppliedId()
    {
        return $this->suppliedId;
    }

    /**
     * Constructs and returns the user's claimed OpenID appropriate for human-readable on-screen display.
     *
     * @param string $claimedId The normalized, authenticated claimed OpenID for the user.
     * 
     * @return string The claimed OpenID, adjusted for display purposes--in this case formatted as an OpenID URL.
     */
    public function getDisplayName($claimedId)
    {
        if ((substr($claimedId, 0, 7) == 'http://') || (substr($claimedId, 0, 8) == 'https://')) {
            $urlParts = @parse_url($claimedId);
            if ($urlParts) {
                $displayName = $urlParts['host'];
                if (isset($urlParts['user']) && !empty($urlParts['user'])) {
                    $displayName .= ':' . $urlParts['user'];
                }
                if (isset($urlParts['pass']) && !empty($urlParts['pass'])) {
                    $displayName .= '@' . $urlParts['pass'];
                }
                if (isset($urlParts['port']) && !empty($urlParts['port']) && ($urlParts['port'] != 80)) {
                    $displayName .= ':' . $urlParts['port'];
                }
                if (isset($urlParts['path']) && !empty($urlParts['path'])) {
                    if ((!isset($urlParts['query']) || empty($urlParts['query'])) && (substr($urlParts['path'], -1) == '/')) {
                        $path = substr($urlParts['path'], 0, -1);
                    } else {
                        $path = $urlParts['path'];
                    }
                    $displayName .= $path;
                }
                if (isset($urlParts['query']) && !empty($urlParts['query'])) {
                    $displayName .= '?' . $urlParts['query'];
                }
                if (isset($urlParts['fragment']) && !empty($urlParts['fragment'])) {
                    $displayName .= '#' . $urlParts['fragment'];
                }
            } else {
                $displayName = $claimedId;
            }
        } elseif (substr($claimedId, 0, 6) == 'xri://') {
            $displayName = substr($claimedId, 6);
        } else {
            $displayName = $claimedId;
        }

        return $displayName;
    }
}