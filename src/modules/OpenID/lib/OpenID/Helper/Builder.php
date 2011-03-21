<?php

class OpenID_Helper_Builder
{
    public static function buildInstance($authenticationMethod, $authenticationInfo)
    {
        try {
            switch (strtolower($authenticationMethod)) {
                case 'openid':
                    return new OpenID_Helper($authenticationInfo);
                    break;
                case 'google':
                    return new OpenID_Helper_Google($authenticationInfo);
                    break;
                case 'pip':
                    return new OpenID_Helper_VeriSignPIP($authenticationInfo);
                    break;
                default:
                    return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}