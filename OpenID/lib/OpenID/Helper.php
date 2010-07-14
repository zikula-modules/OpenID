<?php

class OpenID_Helper extends Zikula_Base
{
    protected $supplied_id;

    public function __construct(array $authinfo)
    {
        $this->supplied_id = $authinfo['supplied_id'];
    }

    public function getSuppliedId()
    {
        return $this->supplied_id;
    }

    public function getDisplayName($claimedID)
    {
        if ((substr($claimedID, 0, 7) == 'http://') || (substr($claimedID, 0, 8) == 'https://')) {
            $urlParts = @parse_url($claimedID);
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
                $displayName = $claimedID;
            }
        } elseif (substr($claimedID, 0, 6) == 'xri://') {
            $displayName = substr($claimedID, 6);
        } else {
            $displayName = $claimedID;
        }

        return $displayName;
    }
}