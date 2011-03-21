<?php

class OpenID_Exception_InternalError extends Zikula_Exception_Fatal
{
    public function __construct($detailedMessage = null, $standardMessage = null, $code = 500, $debug = null)
    {
        if (empty($standardMessage)) {
            $message = __('An internal error has occurred in the OpenID module.');
        } else {
            $message = $standardMessage;
        }

        if (!empty($detailedMessage)
                && (System::getVar('development', false) || SecurityUtil::checkPermission('OpenID::debug', '::', ACCESS_ADMIN))
                ) {
            $message .= ' ' . $detailedMessage;
        }

        parent::__construct($message, $code, $debug);
    }
}