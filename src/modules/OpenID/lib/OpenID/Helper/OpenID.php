<?php

class OpenID_Helper_OpenID extends Zikula_AbstractBase
{
    protected $suppliedId;

    public function __construct(array $authenticationInfo)
    {
        $this->suppliedId = $authenticationInfo['suppliedId'];
    }

    public function getSuppliedId()
    {
        return $this->suppliedId;
    }

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