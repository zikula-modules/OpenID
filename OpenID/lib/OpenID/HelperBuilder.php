<?php

class OpenID_HelperBuilder
{
    public static function buildInstance($openidType, $authinfo)
    {
        try {
            switch ($openidType) {
                case 'openid':
                    return new OpenID_Helper($authinfo);
                    break;
                case 'google':
                    return new OpenID_Helper_Google($authinfo);
                    break;
                case 'verisign':
                    return new OpenID_Helper_VeriSignPIP($authinfo);
                    break;
                default:
                    return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}